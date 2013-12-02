<?php

/**
 * r actions.
 *
 * @package    OpenPNE
 * @subpackage e
 * @author     Your name here
 */
class registActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeCreditcard(sfWebRequest $request)
  {
  }
  public function executeMailVerify(sfWebRequest $request)
  {
    if (!isset($request['token']))
    {
      $this->message = "token not specified.";
      return sfView::ERROR;
    }
    $member = Doctrine::getTable("Member")->findByRegisterToken($request["token"]);
    if(!$member){
      $this->message = "invalid token.";
      return sfView::ERROR;
    }
    if($member->is_active){
      $this->message = "user exists."
      return sfView::ERROR;    
    }
    //1.トークンの確認 メンバーの復元
    //2.クレジットカードの登録処理

  }
}
