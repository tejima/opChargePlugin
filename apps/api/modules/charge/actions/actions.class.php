<?php

/**
 * charge actions.
 *
 * @package    OpenPNE
 * @subpackage charge
 * @author     Your name here
 */
class chargeActions extends opJsonApiActions
{
  /*
  public function executeInvite(sfWebRequest $request){
    $member = $this->getUser()->getMember();
    if(!$member){
      $this->forward400('login required.');      
    }
    //FROM TOLIST BODY
    $invitation_message = "welcome!";
    $address_list = array("a@b.com","c@d.com");
    foreach($address_list as $address){
      $member_pre = Doctrine::getTable('Member')->createPre();
      $member_pre->invite_member_id($member->getId());

      $member_pre->setName($request["email"]);
      $member_pre->setConfig("pc_address",$request["email"]);
      $member->setConfig("password",md5("password")); //FIXME
      $member_pre->save();
      $result = $member_pre->generateRegisterToken();


      $url = sfConfig::get('op_base_url') . "/regist/mailclick?token=" . $result;
      $mailbody = <<< EOT
舞踏クラブ会員登録
舞踏クラブへの入会申し込みありがとうございました。

このURLをクリックして登録作業を継続してください。

{$url}

舞踏クラブ運営事務局
〒169-0051
東京都新宿区西早稲田2-18-20
ECORICH高田馬場5階
EOT;

      $mailbody_list = explode("\n",$mailbody,2);
      $subject = $mailbody_list[0];
      $body = $mailbody_list[1];

      $mail = new opMailSend();
      $mail->execute($subject,$request['email'],sfConfig::get("mail_smtp_config_username"),$body);

    }
  }
  */
  public function executeMail(sfWebRequest $request)
  {

    if (!isset($request['email']))
    {
      $this->forward400('email parameter not specified.');
    }
    $result = Doctrine::getTable('MemberConfig')->findOneByNameAndValue("pc_address", $request['email']);
    if($result){
      return $this->renderText(json_encode(array("status"=>"error","message"=> "メールアドレスはすでに登録されています。")));
    }
    $member = Doctrine::getTable('Member')->createPre();
    $member->setName($request["email"]);
    $member->setConfig("pc_address",$request["email"]);
    $member->setConfig("password",md5("password")); //FIXME
    $member->save();
    $result = $member->generateRegisterToken();
    $url = sfConfig::get('op_base_url') . "/regist/mailclick?token=" . $result;

      $mailbody = <<< EOT
[舞踏倶楽部]メールアドレス踏力完了（仮登録）
舞踏倶楽部へご登録していただきありがとうございます。

まだ仮登録の状態です。以下をクリックして登録を完了させてください。

{$url}

このメールに返信はできません。
ご不明な点がある場合には以下にお問い合わせください。

舞踏倶楽部事務局
contact@dance-navi.jp
EOT;
    $mailbody_list = explode("\n",$mailbody,2);
    $subject = $mailbody_list[0];
    $body = $mailbody_list[1];


    $mail = new opMailSend();
    $mail->execute($subject,$request['email'],sfConfig::get("mail_smtp_config_username"),$body);
    $this->logMessage("SEND REGISTER MAIL :" . $equest['email'], "info");
    return $this->renderText(json_encode(array("status"=>"success","message"=>"メールを送ったので、そちらから手続きを続けてください。")));    
  }
  public function executeCreditcardUpdate(sfWebRequest $request){
    $member = $this->getUser()->getMember();
    if(!$member){
      $this->forward403("auth");
    }    

    $customer = Stripe_Customer::retrieve($member->getConfig('webpay_customer_id'));
    $customer->card = array(
        "number"=>$request['number'],
        "exp_month"=>$request['month'],
        "exp_year"=>$request['year'],
        "cvc"=>$request['cvc'],
        "name"=>$request['name']
    );
    $result = $customer->save();
    if(!$result){
      $this->forward400('invalid card. try again.');
      //FIXME forward to admin with server error info. クレジットカードの認証失敗は特別なエラーコードを返す。
    }else{
      $ad = new ActivityData();
      $ad->member_id = $member->getId();
      $ad->public_flag = false;
      $ad->body = "クレジットカード情報を更新しました";
      $ad->source = "CHARGE";
      $ad->save();
      return $this->renderText(json_encode(array("status"=>"success","message"=>"card regist success.")));
    }
  }
  public function executeCreditcard(sfWebRequest $request)
  {
    if (!isset($request['token']))
    {
      $this->forward400('token parameter not specified.');
    }
    $member = Doctrine::getTable("Member")->findByRegisterToken($request["token"]);
    //１．カード情報、メールアドレス情報の取得
    //２．メンバープレオブジェクトの作成
    //３．プレオブジェクトに、カード情報、セッショントークンを格納
    //４．確認メールの送信

    if(!$member){
      $this->forward400('session token not valid');
    }
    if (!isset($request['number']))
    {
      $this->forward400('number parameter not specified.');
    }
    $valid = new Zend_Validate_CreditCard(array(
        Zend_Validate_CreditCard::AMERICAN_EXPRESS,
        Zend_Validate_CreditCard::VISA,
        Zend_Validate_CreditCard::JCB,
        Zend_Validate_CreditCard::MASTERCARD,
        Zend_Validate_CreditCard::DINERS_CLUB
    ));
    if(!$valid->isValid($request['number'])){
      $this->forward400('invalid card number.');
    }
    if (!isset($request['month']))
    {
      $this->forward400('month parameter not specified.');
    }
    $month_list = array("01","02","03","04","05","06","07","08","09","10","11","12");
    if(!in_array($request['month'],$month_list))
    {
      $this->forward400('invalid month.');
    }
    if (!isset($request['year']))
    {
      $this->forward400('year parameter not specified.');
    }
    $year_list = array("2013","2014","2015","2016","2017","2018","2019","2020","2021","2022");
    if(!in_array($request['year'],$year_list))
    {
      $this->forward400('invalid year.');
    }
    if (!isset($request['cvc']))
    {
      $this->forward400('cvc parameter not specified.');
    }

    if (!isset($request['name']))
    {
      $this->forward400('name parameter not specified.');
    }

    if (!preg_match('/[a-zA-Z\s]+/',$request['name'])) {
      $this->forward400('invalid name.');
    }

    Stripe::setApiKey(sfConfig::get('app_opchargeplugin_webpaysecret'));
    Stripe::$apiBase = "https://api.webpay.jp";

    $this->forward400($regist_mode . ":mode");

    try{
      $result = Stripe_Customer::create(array(
        "card"=>
        array("number"=>$request['number'],
         "exp_month"=>$request['month'],
         "exp_year"=>$request['year'],
         "cvc"=>$request['cvc'],
         "name"=>$request['name'])
      ));
    }catch(Stripe_Error $e){
      $result = false;
      $this->logMessage('Stripe_Error', 'notice');
    }catch(Exception $e){
      $this->logMessage('Exception', 'notice');
      $result = false;
    }

    if(!$result){
      $this->forward400('invalid card. try again.');
      //FIXME forward to admin with server error info. クレジットカードの認証失敗は特別なエラーコードを返す。
    }else{
      $member->setConfig('webpay_customer_id',$result['id']);
      $member->setIsActive(true);
      $member->save();
      $password = opToolkit::generatePasswordString();
      $member->setConfig("password",md5($password));
      $member->setConfig('register_token',null);

      $email = $member->getConfig("pc_address");
      $password = $password;
      $mailbody = <<< EOT
【登録完了】舞踏クラブ会員登録

舞踏クラブへの申し込みが完了しました。

このメールは登録変更の際に必要ですので必ず保存してください。
会員ID：{$email}
会員パスワード：{$password}

舞踏クラブ運営事務局
〒169-0051
東京都新宿区西早稲田2-18-20
ECORICH高田馬場5階
EOT;
      $mailbody_list = explode("\n",$mailbody,2);
      $subject = $mailbody_list[0];
      $body = $mailbody_list[1];


      $mail = new opMailSend();
      $mail->execute($subject,$member->getConfig("pc_address"),sfConfig::get("mail_smtp_config_username"),$body);
      //FIXME
      $ad = new ActivityData();
      $ad->member_id = $member->getId();
      $ad->public_flag = false;
      $ad->body = "クレジットカード情報を新規登録しました";
      $ad->source = "CHARGE";
      $ad->save();

      return $this->renderText(json_encode(array("status"=>"success","message"=>"card regist success.","data"=>$mailbody)));
    }
  }
}
