<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class errorController extends Zend_Controller_Action
{
    //parodom Prisijungimo langa
    public function errorAction()
    {
        $error = $this->_getParam('error_handler');
        echo $error->exception->getMessage();
    }
}
?>
