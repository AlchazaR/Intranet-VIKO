<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
include_once '../application/configs/db_connect.php';

class UserController extends Zend_Controller_Action
{
    
    //parodom Prisijungimo langa
    public function loginformAction()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('login');

        $request = $this->getRequest();

        $ns = new Zend_Session_Namespace('login_error');
        if(isset($ns->yourLoginRequest))
        {
            if ($ns->yourLoginRequest == 1)
                $this->view->assign('error', 'Neteisingas slaptažodis arba vartotojo vardas.');
            if ($ns->yourLoginRequest == 2)
                $this->view->assign('error', 'Jūsų tapatybe dar nepatvirtinta. <br /> Iškilus klausimams kreipkitės į sistemos administratorių.');
        }
       
        $this->view->assign('action', $request->getBaseURL()."/user/auth");
        $this->view->assign('title', 'intranet.armitana.lt');
        $this->view->assign('username', 'Vartotojo vardas');
        $this->view->assign('password', 'Slaptažodis');
    }
    
    //naujo vartotojo registracija
    public function registerAction()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('login');

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql_places = "SELECT sk_id, sk_pavad FROM `l_skyriai` ORDER BY sk_pavad ASC";
        $places_list = $DB->fetchAssoc($sql_places);

        $sql_positions = "SELECT par_id, par_pavadinimas FROM `l_pareigos` ORDER BY par_pavadinimas ASC";
        $positions_list = $DB->fetchAssoc($sql_positions);

        $request = $this->getRequest();

        //$this->view->assign('action','process');
        $this->view->assign('title','Naujo vartotojo registracija');
        $this->view->assign('label_fname','Vardas *');
        $this->view->assign('label_lname','Pavardė *');
        $this->view->assign('label_place', 'Skyrius *');
        $this->view->assign('select_places', $places_list);
        $this->view->assign('label_mail', 'El. paštas *');
        $this->view->assign('label_tel', 'Telefono nr.');
        $this->view->assign('label_position', 'Pareigos *');
        $this->view->assign('select_positions', $positions_list);
        $this->view->assign('label_login','Prisijungimo vardas *');
        $this->view->assign('label_pass','Slaptažodis *');
        $this->view->assign('label_pass_confirm','Pakartoti slaptažodį *');
        $this->view->assign('label_submit','Registruotis');
    }
    
    public function loginchkAction()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $login = $request->getParam('login');

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];
        $sql ="SELECT COUNT(*) FROM l_vartotojai WHERE vart_login ='".$login."'";

        $log_chk = $DB->fetchOne($sql);
        echo $log_chk;
    }

    public function processAction()
    {
        $request = $this->getRequest();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $filename = "../public/images/User.png";
        $handle = fopen($filename, 'r');
        $file_content = fread($handle,filesize($filename));
        $file_content = addslashes($file_content);
        fclose($handle);
        $sql ="INSERT INTO l_vartotojai (
                            vart_login,
                            vart_vardas,
                            vart_pavarde,
                            vart_skyrius,
                            vart_grupe,
                            vart_pastas,
                            vart_tel,
                            vart_password,
                            vart_pareigos,
                            vart_foto)
               VALUES('".trim(htmlspecialchars(stripcslashes($request->getParam('user_login'))))."',
                      '".trim(htmlspecialchars(stripcslashes($request->getParam('first_name'))))."',
                      '".trim(htmlspecialchars(stripcslashes($request->getParam('last_name'))))."',
                      '".trim(htmlspecialchars(stripcslashes($request->getParam('user_place'))))."',
                      '2',
                      '".trim(htmlspecialchars(stripcslashes($request->getParam('user_mail'))))."',
                      '".trim(htmlspecialchars(stripcslashes($request->getParam('user_tel'))))."',
                      '".trim(htmlspecialchars(stripcslashes(MD5($request->getParam('user_pass')))))."',
                      '".trim(htmlspecialchars(stripcslashes($request->getParam('user_position'))))."',
                      '".$file_content."')";
        mysql_query($sql) or die(mysql_error());
        $this->view->assign('title','Vartotojas užregistrotas');
        $this->view->assign('description','Po domenų patvirtinimo Jus galėsite prisijungti.');
        logs('last', 1, 'none'); // uzregistruotas naujas vartotojas
    }

    // patikrinamas slaptazodis ir vartotojo vardas
    public function authAction()
    {
        $request    = $this->getRequest();

        $uname = trim(htmlspecialchars(stripcslashes($request->getParam('username'))));
        $paswd = trim(htmlspecialchars(stripcslashes($request->getParam('password'))));

        if ($paswd == '' || $uname == '')
        {
            $this->_redirect('/user/loginform');
        }
        else
        {
            $registry   = Zend_Registry::getInstance();
            $auth       = Zend_Auth::getInstance();

            $DB = $registry['DB'];

            $authAdapter = new Zend_Auth_Adapter_DbTable($DB);
            $authAdapter->setTableName('l_vartotojai')
                   ->setIdentityColumn('vart_login')
                   ->setCredentialColumn('vart_password');
            // Set the input credential values
            $authAdapter->setIdentity($uname);
            $authAdapter->setCredential(md5($paswd));
            // Perform the authentication query, saving the result
            $result = $auth->authenticate($authAdapter);
            $ns = new Zend_Session_Namespace('login_error');
            if($result->isValid())
            {
                $data = $authAdapter->getResultRowObject(null,'password');
                $auth->getStorage()->write($data);
                $ns->yourLoginRequest = 0;
                $this->_redirect('/homepage');
            }
            else
            {
                $ns->yourLoginRequest = 1;
                $this->_redirect('/user/loginform');
            }
        }
    }

    // prisijungimas sekmingas, parodomas pagrindinis darbo langas
    public function userpageAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

       // $test = $this->navigation();
        $request = $this->getRequest();
	$user       = $auth->getIdentity();
	$real_name  = $user->vart_vardas;
	$username   = $user->vart_login;
	$logoutUrl  = $request->getBaseURL().'/user/logout';

	$this->view->assign('username', $real_name);

        $this->view->assign('title', 'Armitana.lt');
	$this->view->assign('urllogout',$logoutUrl);


        
    }

    // atsijungti
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/user/loginform');
    }





}


?>