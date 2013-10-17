<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class opChargeTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
    $this->namespace        = 'cqc.jp';
    $this->name             = 'charge';
    $this->briefDescription = 'do Monthly Payment';
    $this->detailedDescription = <<< EOF
  [./symfony cqc.jp:charge]
EOF;

    $this->addOption('month', null,sfCommandOption::PARAMETER_NONE, 'month', null);
    /*
    $this->addOption('year', null,sfCommandOption::PARAMETER_NONE, 'year', null);
    $this->addOption('onetime', null, sfCommandOption::PARAMETER_NONE, 'onetime', null);
    */
    $this->addOption('targetmonth', null, sfCommandOption::PARAMETER_REQUIRED, 'targetmonth', null);

    $this->addOption('start', null, sfCommandOption::PARAMETER_OPTIONAL, 'start', null);
    $this->addOption('end', null, sfCommandOption::PARAMETER_OPTIONAL, 'end', null);
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->configuration = parent::createConfiguration("pc_backend",null);
    new sfDatabaseManager($this->configuration);

    $start = (int)$options['start'] ? (int)$options['start']: 1;
    $end = (int)$options['end'] ? (int)$options['start']: 1;

    if(!preg_match('/^20[0-9][0-9][1-9][0-9]/',$options['targetmonth'])){
      echo "invalid targetmonth parameter";
      return 1;
    }

    $q = Doctrine::getTable("Member")->createQuery('m')
      ->where('m.id >= ?', $start)
      ->andWhere('m.id <= ?',$end)
      ->andWhere('m.is_active = ?',1)
      ->limit(100);
    $member_list = $q->execute();
    echo "load " . count($member_list) . " members\n";

    foreach($member_list as $member){
      $status = $member->getConfig("cqc_jp_charge_status");
      $charge_status = $status ? json_decode($status,true) : array($options['targetmonth']=>"READY");
      switch($charge_status[$options['targetmonth']]){
        case "READY": //ready to charge
          try {
            Stripe::setApiKey(sfConfig::get('app_opchargeplugin_webpaysecret'));
            Stripe::$apiBase = "https://api.webpay.jp";
            Stripe_Charge::create(array(
               "amount"=>sfConfig::get('app_opchargeplugin_amount'),
               "currency"=>"jpy",
               "customer"=>$member->getConfig('webpay_customer_id'),
               "description"=>$options['targetmonth']."定期チャージ"
            ));
            echo "READY-STRIPE\n";
            $ad = new ActivityData();
            $ad->member_id = $member->getId();
            $ad->public_flag = false;
            $ad->body = $options['targetmonth']."の定期支払いを実行しました。";
            $ad->source = "CHARGE";
            $ad->save();
            echo $member->getId() . " charge complete\n";
            $charge_status[$options['targetmonth']] = "DONE";
          } catch(Stripe_CardError $e) {
            // カードが拒否された場合
            $body = $e->getJsonBody();
            $err = $body['error'];
            print('Status is:' . $e->getHttpStatus() . "\n");
            print('Type is:' . $err['type'] . "\n");
            print('Code is:' . $err['code'] . "\n");
            print('Param is:' . $err['param'] . "\n");
            print('Message is:' . $err['message'] . "\n");

            $ad = new ActivityData();
            $ad->member_id = $member->getId();
            $ad->public_flag = false;
            $ad->body = $options['targetmonth']."の定期支払い時、カードが利用できませんでした。有効期限、カード番号などを見なおしてください。問い合わせコード（1022".$err['code']."）";
            $ad->source = "CHARGE";
            $ad->save();
            $charge_status[$options['targetmonth']] = "CARD";
          } catch (Stripe_InvalidRequestError $e) {
            echo "リクエストで指定したパラメータが不正な場合\n";
            $charge_status[$options['targetmonth']] = "FAIL";
          } catch (Stripe_AuthenticationError $e) {
            echo "認証に失敗した場合\n";
            $charge_status[$options['targetmonth']] = "FAIL";
          } catch (Stripe_ApiConnectionError $e) {
            echo "APIへの接続エラーが起きた場合\n";
            $charge_status[$options['targetmonth']] = "FAIL";
          } catch (Stripe_Error $e) {
            echo "WebPayのサーバでエラーが起きた場合\n";
            $charge_status[$options['targetmonth']] = "FAIL";
          } catch (Exception $e) {
            echo "WebPayとは関係ない例外の場合\n";
            $charge_status[$options['targetmonth']] = "FAIL";
          }
          $member->setConfig("cqc_jp_charge_status",json_encode($charge_status));
          break;
        case "DONE": //charge aleady DONE
        case "CARD": //charge failed by incorrect CARD 
        case "STOP": //charge STOP for some reason
          echo $member->getId() . " is ".$charge_status[$options['targetmonth']]."\n";
          break;
        default:
          echo "switch\n";
      }
    }
  }
}