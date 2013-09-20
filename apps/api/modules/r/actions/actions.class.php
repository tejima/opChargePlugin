<?php

/**
 * r actions.
 *
 * @package    OpenPNE
 * @subpackage r
 * @author     Your name here
 */
class rActions extends opJsonApiActions
{

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
    $api_key = $member->generateApiKey();
    $member->save();

    $member->generateRegisterToken();
    $result = $member->getRegisterToken();

    $subject = "pneコミュニティ集金サービスの登録";
    $body = "◯◯会の登録申し込み\n\nこのURLをクリックして登録作業を継続してください。 " . sfConfig::get('op_base_url') . "/r/mailclick?token=" . $result;
    error_log(time().": $subject \n\n\n\n $body \n", 3, "/tmp/loglog");

    //$mail = new opMailSend();
    //$mail->execute("登録継続","tejima@gmai.com","tejima@tejimaya.com","登録継続してください");
    return $this->renderText(json_encode(array("status"=>"success","message"=>"メールを送ったので、そちらから手続きを続けてください。")));    
  }



  public function executeCreditcard(sfWebRequest $request)
  {
    if (!isset($request['token']))
    {
      $this->forward400('token parameter not specified.');
    }
    $member = Doctrine::getTable("Member")->findByRegisterToken($request["token"]);
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
    $year_list = array("2013","2014","2015","2016","2017");
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
    $result = Stripe_Customer::create(array(
      "card"=>
      array("number"=>$request['number'],
       "exp_month"=>$request['month'],
       "exp_year"=>$request['year'],
       "cvc"=>$request['cvc'],
       "name"=>$request['name'])
    ));
    if(!$result){
      $this->forward400('invalid card. try again.');
      //FIXME forward to admin with server error info.
    }else{
      $member->setConfig('webpay_customer_id',$result['id']);
      $member->setIsActive(true);
      $password = opToolkit::generatePasswordString();
      $member->setConfig("password",md5($password));
      $member->setConfig('register_token',null);
      $data = array("password" => $password,"email" => $member->getConfig("pc_address"));
      $message = print_r($data,true);
      error_log(time().": $message \n", 3, "/tmp/loglog");
      //$mail = new opMailSend();
      //$mail->execute("登録継続","tejima@gmai.com","tejima@tejimaya.com","登録継続してください");

      return $this->renderText(json_encode(array("status"=>"success","message"=>"Customer registration done.","data"=>$data)));
    }
  }
}
