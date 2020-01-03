<?php
/*error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);
date_default_timezone_set('Europe/Vilnius');

$rootDir = dirname(dirname(__FILE__));
set_include_path($rootDir . '/library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// загружаем конфигурацию
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Registry.php';
$config = new Zend_Config_Ini('../application/config.ini', 'database');
$params = $config->database->toArray();
$registry = Zend_Registry::getInstance();
$registry->set('config', $config);

$DB = new Zend_Db_Adapter_Pdo_Mysql($params);
$DB->setFetchMode(Zend_Db::FETCH_OBJ);
Zend_Registry::set('DB',$DB);
Zend_registry::set('app_title', 'Armitana');
require_once 'Zend/Controller/Front.php';

// настраиваем контроллер
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
$frontController->setControllerDirectory('../application/controllers');

Zend_Layout::startMvc(array('layoutPath'=>'../application/layouts'));
// запускаем
$frontController->getInstance()->dispatch();*/


// Step 1: APPLICATION_PATH is a constant pointing to our
// application/subdirectory. We use this to add our "library" directory
// to the include_path, so that PHP can find our Zend Framework classes.

//ini_set('error_log','phperror.log');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '../../application/'));

define('PUBLIC_PATH', realpath(dirname(__FILE__)));
set_include_path(APPLICATION_PATH . '/../library' . PATH_SEPARATOR . get_include_path() );
// Step 2: AUTOLOADER - Set up autoloading.
// This is a nifty trick that allows ZF to load classes automatically so
// that you don't have to litter your code with 'include' or 'require'
// statements. require_once "Zend/Loader.php";

require_once "Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance();

//**************************/
/*$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
$frontController->setControllerDirectory('../application/controllers');

Zend_Layout::startMvc(array('layoutPath'=>'../application/layouts'));*/
//**************************/
// Step 3: REQUIRE APPLICATION BOOTSTRAP: Perform application-specific setup
// This allows you to setup the MVC environment to utilize. Later you
// can re-use this file for testing your applications.
// The try-catch block below demonstrates how to handle bootstrap
// exceptions. In this application, if defined a different
// APPLICATION_ENVIRONMENT other than 'production', we will output the
// exception and stack trace to the screen to aid in fixing the issue
try {
  require APPLICATION_PATH.'/bootstrap.php';
}
catch (Exception $exception)   {
  echo 'An exception occured while bootstrapping the application.';
  if (defined('APPLICATION_ENVIRONMENT') && APPLICATION_ENVIRONMENT != 'production'  ) {
    echo '<br /><br />' . $exception->getMessage() . '<br />'  . '<div align="left">Stack Trace:' . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
  }
  exit(1);
} 

// Step 4: DISPATCH: Dispatch the request using the front controller.
// The front controller is a singleton, and should be setup by now. We
// will grab an instance and call dispatch() on it, which dispatches the
// current request.
try {
  //Zend_Debug::dump(Zend_Controller_Front::getInstance());
  //Zend_Layout::startMvc(array('layoutPath'=>'../application/layouts'));
  Zend_Controller_Front::getInstance()->dispatch();
}
catch (Exception $exception)   {
  echo 'An exception occured while dispatching the application.';
  if (defined('APPLICATION_ENVIRONMENT') && APPLICATION_ENVIRONMENT != 'production'  ) {
    echo '<br /><br />' . $exception->getMessage() . '<br />'  . '<div align="left">Stack Trace:' . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
  }
  echo '</center></body></html>'; exit(1);
}

?>



