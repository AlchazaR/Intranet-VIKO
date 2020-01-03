<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class IndexController extends Zend_Controller_Action
{
  public function indexAction()
  {
    $request = $this->getRequest();
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
        }
        if ($auth->getIdentity()->vart_grupe == 2) //admin
        {
            $this->_redirect('/user/loginform');
        }
        else
        {
             $this->_redirect('/user/homepage');
        }
  }

}
?>
