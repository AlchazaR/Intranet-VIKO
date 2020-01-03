<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class HomepageController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe == 2) //neaktivuotas
        {
            $this->_redirect('/user/loginform');
            //$ns = new Zend_Session_Namespace('login_error');
            //$ns->yourLoginRequest = 2;
        }
        $this->view->assign('title', 'Armitana.lt');
	$this->view->assign('logo', 'Pradžia');
    }

    public function blogAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        $this->view->assign('title', 'Armitana.lt');
	$this->view->assign('logo', 'Blog');
    }
}
?>
