<?php

/**
 * r actions.
 *
 * @package    OpenPNE
 * @subpackage r
 * @author     Your name here
 */
class rActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeMailclick(sfWebRequest $request)
  {
    $this->register_token = $request['token'];
    
  }
}
