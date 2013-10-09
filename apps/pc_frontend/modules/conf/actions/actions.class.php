<?php

/**
 * config actions.
 *
 * @package    OpenPNE
 * @subpackage conf
 * @author     Your name here
 */
class confActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeCreditcard(sfWebRequest $request)
  {
    $this->regist_mode = false;
  }
  public function executeLog(sfWebRequest $request)
  {
  }
}
