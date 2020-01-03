<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
include_once '../application/configs/db_connect.php';

class readimageController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');
        
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $request = $this->getRequest();
        $type = trim(htmlspecialchars(stripcslashes($request->getParam('type'))));
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));

        switch ($type)
        {
            case "vart_foto":
                $sql = "SELECT vart_foto FROM l_vartotojai WHERE vart_id ='".$id."'";
            break;
        }
        $image = mysql_query($sql) or die(mysql_error());
        header("Content-type: image/jpeg");
        echo mysql_result($image, 0);
    }
}