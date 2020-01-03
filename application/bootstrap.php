<?php
// APPLICATION CONSTANTS - Set the constants to use in this application.
// These constants are accessible throughout the application, even in ini
// files. We optionally set APPLICATION_PATH here in case our entry point
// isn't index.php (e.g., if required from our test suite or a script).
defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__).'application/');

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');

defined('FILE_UPLOAD_TMP_DIR') or define('FILE_UPLOAD_TMP_DIR', APPLICATION_PATH . '/tmp/uploads');

// FRONT CONTROLLER - Get the front controller.
// The Zend_Front_Controller class implements the Singleton pattern, which is a
// design pattern used to ensure there is only one instance of
// Zend_Front_Controller created on each request.
$frontController = Zend_Controller_Front::getInstance();

// CONTROLLER DIRECTORY SETUP - Point the front controller to your action
// controller directory.
$frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');

// APPLICATION ENVIRONMENT - Set the current environment
// Set a variable in the front controller indicating the current environment --
// commonly one of development, staging, testing, production, but wholly
// dependent on your organization and site's needs.
$frontController->setParam('env', APPLICATION_ENVIRONMENT);


// LAYOUT SETUP - Setup the layout component
// The Zend_Layout component implements a composite (or two-step-view) pattern
// In this call we are telling the component where to find the layouts scripts.
$layout = Zend_Layout::startMvc(APPLICATION_PATH . '/layouts');
$layout->getView()->addHelperPath(APPLICATION_PATH .'/views/helpers', 'menu');
// VIEW SETUP - Initialize properties of the view object
// The Zend_View component is used for rendering views. Here, we grab a "global"
// view instance from the layout object, and specify the doctype we wish to
// use -- in this case, XHTML1 Strict.
$view = Zend_Layout::getMvcInstance()->getView();
$view->doctype('XHTML1_STRICT');

// CONFIGURATION - Setup the configuration object
// The Zend_Config_Ini component will parse the ini file, and resolve all of
// the values for the given section.  Here we will be using the section name
// that corresponds to the APP's Environment
//$configuration = new Zend_Config_Ini(APPLICATION_PATH . '/config.ini', APPLICATION_ENVIRONMENT);

$config = new Zend_Config_Ini('../application/config.ini', 'database');
$params = $config->database->toArray();



// DATABASE ADAPTER - Setup the database adapter
// Zend_Db implements a factory interface that allows developers to pass in an
// adapter name and some parameters that will create an appropriate database
// adapter object.  In this instance, we will be using the values found in the
// "database" section of the configuration obj.
//$dbAdapter = Zend_Db::factory($configuration->database);
$DB = new Zend_Db_Adapter_Pdo_Mysql($params);
$DB->setFetchMode(Zend_Db::FETCH_OBJ);

// DATABASE TABLE SETUP - Setup the Database Table Adapter
// Since our application will be utilizing the Zend_Db_Table component, we need
// to give it a default adapter that all table objects will be able to utilize
// when sending queries to the db.
//Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

// REGISTRY - setup the application registry
// An application registry allows the application to store application
// necessary objects into a safe and consistent (non global) place for future
// retrieval.  This allows the application to ensure that regardless of what
// happends in the global scope, the registry will contain the objects it
// needs.
//$registry = Zend_Registry::getInstance();
//$registry->configuration = $configuration;
//$registry->dbAdapter     = $dbAdapter;

$registry = Zend_Registry::getInstance();
$registry->set('config', $config);

Zend_Registry::set('DB',$DB);
Zend_registry::set('app_title', 'Armitana');

/*$sessionOptions = array(
  'save_path' => APPLICATION_PATH . '/sessions',
  'name' => 'loyaltyMailer',
  'cookie_lifetime' => '0',
  'gc_maxlifetime' => '86400',
  'gc_probability' => '10',
  'remember_me_seconds' => '0'
);

Zend_Session::setOptions($sessionOptions);
$ns = new Zend_Session_Namespace ('loyaltyMailer');
if ($ns->authenticated) {
  if ($ns->rememberMeDone != true || $ns->clicksSinceLastRememberMe>100) {
    Zend_Session::rememberMe(30*60);
    $ns->rememberMeDone = true;
    $ns->clicksSinceLastRememberMe = 0;
  }
  $ns->clicksSinceLastRememberMe += 1;
}
else Zend_Session::regenerateId();
Zend_Session::start();
$ns->source_ip = $_SERVER['REMOTE_ADDR'];
*/
/*
Include table mappings
*/
//require_once(APPLICATION_PATH . '/gTable.php');

// User Menu



//$navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
//$navContainer = new Zend_Navigation($navContainerConfig);
//Zend_Registry::set('navMenu',$navContainer);
//
//$configNav = new Zend_Config_Xml(APPLICATION_PATH.'/configs/navigation.xml');
//$navigation = new Zend_Navigation($configNav);

// CLEANUP - remove items from global scope
// This will clear all our local boostrap variables from the global scope of
// this script (and any scripts that called bootstrap).  This will enforce
// object retrieval through the Applications's Registry
require_once 'global.php';
unset($frontController,$view,$config,$registry,$sessionOptions);




