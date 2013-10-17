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
  public function executeMailclick(sfWebRequest $request)
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
    $this->token = $request["token"];
  }
  public function executeMail(sfWebRequest $request)
  {
  }
}