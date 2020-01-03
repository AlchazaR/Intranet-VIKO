<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
include_once '../application/configs/db_connect.php';

class mygroupsController extends Zend_Controller_Action
{
    /**
     * mano grupes - grupiu sarasas
     */
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);
        $user_id = $auth->getIdentity()->vart_id;

        $sql_groups =  "SELECT mgr_pavadinimas,
                               mgr_aprasymas,
                               mgr_id
                        FROM l_manogrupes
                        WHERE mgr_vartotojas = '".$user_id."'
                        ORDER BY mgr_pavadinimas";
        $group_list = $DB->fetchAssoc($sql_groups);

        $sql_places =  "SELECT sk_id, sk_pavad
                        FROM `l_skyriai`
                        ORDER BY sk_pavad ASC";
        $places_list = $DB->fetchAssoc($sql_places);

        $sql_pos = "SELECT par_id, par_pavadinimas
                    FROM `l_pareigos`
                    ORDER BY par_pavadinimas ASC";
        $pos_list = $DB->fetchAssoc($sql_pos);

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $this->view->assign('logo','Mano grupės');
        $this->view->assign('label_my_groups','Grupių sąrašas');
        $this->view->assign('label_newgroup','Sukurti nauja grupė');
        $this->view->assign('path', $path);
        $this->view->assign('group_list', $group_list);

        $this->view->assign('select_user_place', $places_list);
        $this->view->assign('select_user_pos', $pos_list);

        $this->view->assign('label_newgroup_name', 'Grupės pavadinimas *');
        $this->view->assign('label_newgroup_info', 'Grupės aprašymas');
        $this->view->assign('label_newgroup_fnd_user', 'Įtraukti vartotoją į grupė');
        $this->view->assign('label_newgroup_fnd_name', 'Vardas');
        $this->view->assign('label_newgroup_fnd_surname', 'Pavardė');
        $this->view->assign('label_newgroup_fnd_place', 'Skyrius');
        $this->view->assign('label_newgroup_fnd_pos', 'Pareigos');
       // echo $sql_groups;
        $this->view->assign('title', 'Armitana.lt >> Mano grupės');
    }

    /**
     * sukurti nauja grupe - puslapis
     */
    public function newgroupAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $sql_places =  "SELECT sk_id, sk_pavad
                        FROM `l_skyriai`
                        ORDER BY sk_pavad ASC";
        $places_list = $DB->fetchAssoc($sql_places);

        $sql_pos = "SELECT par_id, par_pavadinimas
                    FROM `l_pareigos`
                    ORDER BY par_pavadinimas ASC";
        $pos_list = $DB->fetchAssoc($sql_pos);


        $this->view->assign('logo','Mano grupės');
        $this->view->assign('label_my_groups','Grupių sąrašas');
        $this->view->assign('label_newgroup','Sukurti nauja grupė');
        $this->view->assign('path', $path);
        $this->view->assign('select_user_place', $places_list);
        $this->view->assign('select_user_pos', $pos_list);

        $this->view->assign('label_newgroup_name', 'Grupės pavadinimas *');
        $this->view->assign('label_newgroup_info', 'Grupės aprašymas');
        $this->view->assign('label_newgroup_fnd_user', 'Rasti vartotoją');
        $this->view->assign('label_newgroup_fnd_name', 'Vardas');
        $this->view->assign('label_newgroup_fnd_surname', 'Pavardė');
        $this->view->assign('label_newgroup_fnd_place', 'Skyrius');
        $this->view->assign('label_newgroup_fnd_pos', 'Pareigos');

        $this->view->assign('title', 'Armitana.lt >> Sukurti nauja grupė');
    }

    /**
     * vartotoju pajeska pries itrukiant vartotoja i grupe
     */
    public function finduserAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $surname = trim(htmlspecialchars(stripcslashes($request->getParam('surname'))));
        $pos = trim(htmlspecialchars(stripcslashes($request->getParam('pos'))));
        $place = trim(htmlspecialchars(stripcslashes($request->getParam('place'))));

        if ($name == "")
            $name = 'IS NOT NULL';
        else
            $name = "LIKE '".$name."%'";

        if ($surname == "")
            $surname = 'IS NOT NULL';
        else
            $surname = "LIKE '".$surname."%'";

        if ($pos == "all")
            $pos = 'IS NOT NULL';
        else
            $pos = "='".$pos."'";

        if ($place == "all")
            $place = 'IS NOT NULL';
        else
            $place = "='".$place."'";

        $sql = "SELECT  vart_id,
                        vart_vardas,
                        vart_pavarde,
                        vart_skyrius,
                        vart_pareigos,
                        sk_pavad,
                        par_pavadinimas
                FROM l_vartotojai vart

                INNER JOIN l_skyriai sk
                ON vart.vart_skyrius = sk.sk_id

                INNER JOIN l_pareigos par
                ON vart.vart_pareigos = par.par_id

                WHERE vart_vardas  ".$name." AND
                      vart_pavarde ".$surname." AND
                      vart_pareigos ".$pos." AND
                      vart_skyrius ".$place." AND
                      vart_grupe != 2";
        //echo $sql;
        $user_list = $DB->fetchAssoc($sql);
        $dep_data_transfer = new XMLWriter();
        $dep_data_transfer->openUri('php://output');
        $dep_data_transfer->startDocument('1.0','UTF-8');
        $dep_data_transfer->setIndent(4);
        $dep_data_transfer->startElement('root');

        foreach ($user_list as $user)
        {
            $user_name= $user['vart_vardas'];
            if ($user_name=="") $user_name = "-";

            $user_surname= $user['vart_pavarde'];
            if ($user_surname=="") $user_surname = "-";

            $user_place= $user['sk_pavad'];
            if ($user_place=="") $user_place = "-";

            $user_position= $user['par_pavadinimas'];
            if ($user_position=="") $user_position = "-";

            $dep_data_transfer->startElement('user');
                $dep_data_transfer->writeElement('id', $user['vart_id']);
                $dep_data_transfer->writeElement('name', $user_name);
                $dep_data_transfer->writeElement('surname', $user_surname);
                $dep_data_transfer->writeElement('place', $user_place);
                $dep_data_transfer->writeElement('position', $user_position);
                $dep_data_transfer->writeElement('foto', 'http:/'.$path.'/readimage?type=vart_foto&id='.$user['vart_id']);
            $dep_data_transfer->endElement();
        }
        // END root
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
    }

    /**
    *  sukurti nauja grupe ir irasyti i ja vartotojus
    */
    public function adduserAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $user_id = $auth->getIdentity()->vart_id;

        $request = $this->getRequest();
        $gr_id = trim(htmlspecialchars(stripcslashes($request->getParam('gr_id'))));
        $added_user_id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $gr_name = trim(htmlspecialchars(stripcslashes($request->getParam('gr_name'))));
        $gr_info = trim(htmlspecialchars(stripcslashes($request->getParam('gr_info'))));

        if ($gr_id == 'new')
        {
            // sukuriam nauja grupe
            $sql = "INSERT INTO l_manogrupes (mgr_pavadinimas, mgr_vartotojas, mgr_aprasymas)
                    VALUES ('".$gr_name."','".$user_id."', '".$gr_info."')";
            $DB->query($sql);
            // suzinom naujos grupes ID
            $sql = "SELECT mgr_id
                    FROM l_manogrupes
                    WHERE mgr_vartotojas ='".$user_id."'
                    ORDER BY mgr_id DESC LIMIT 1";
            $id = $DB->fetchOne($sql);
            // irasom i nauja grupe varotoja
            $sql = "INSERT INTO l_gr_nariai
                    VALUES ('".$added_user_id."','".$id."')";
            $DB->query($sql);
            // grazinam naujos gr ID
            echo $id;
        }
        else
        {
             $sql = "INSERT INTO l_gr_nariai
                    VALUES ('".$added_user_id."', '".$gr_id."')";
             $DB->query($sql);
            // echo $sql;
             echo "ok";
        }
    }
    
    /**
     * patikrinti ar nesikartoja grupes pavadinimas
     */
    public function checkgrnameAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $user_id = $auth->getIdentity()->vart_id;

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));

        $sql = "SELECT COUNT(*)
                FROM l_manogrupes
                WHERE mgr_vartotojas ='".$user_id."'
                AND mgr_pavadinimas ='".$name."'";
        $gr_name = $DB->fetchOne($sql);
        echo $gr_name;
    }

    /**
     * pasalinti vartotoja is grupes
     */
    public function removeuserAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $user_id = $auth->getIdentity()->vart_id;
        $request = $this->getRequest();
        $gr_id = trim(htmlspecialchars(stripcslashes($request->getParam('gr_id'))));
        $del_id = trim(htmlspecialchars(stripcslashes($request->getParam('del_id'))));

        // patikrinam, ar vartotojas redaguoja savo grupe
        $sql = "SELECT COUNT(*)
                FROM l_manogrupes
                WHERE mgr_vartotojas = '".$user_id."'
                AND mgr_id = '".$gr_id."'";
        $count = $DB->fetchOne($sql);
        if ($count > 0)
        {
            $sql = "DELETE
                    FROM l_gr_nariai
                    WHERE grn_nario_id = '".$del_id."'
                    AND grn_grupes_id = '".$gr_id."'";
            $DB->query($sql);
            echo "ok";
        }
        //else echo "Klaida, jus bandot redaguoti ne savo grupe.";
    }

    /**
     * pries itrukiant vartotoja i grupe, patikriman
     *  ar jis nera jau itrauktas i grupe
     */
    public function chkuserAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        //$user_id = $auth->getIdentity()->vart_id;
        $request = $this->getRequest();
        $gr_id = trim(htmlspecialchars(stripcslashes($request->getParam('gr_id'))));
        $chk_id = trim(htmlspecialchars(stripcslashes($request->getParam('user_id'))));

        $sql = "SELECT COUNT(*)
                FROM l_gr_nariai
                WHERE grn_nario_id = '".$chk_id."'
                AND grn_grupes_id = '".$gr_id."'";
        $count = $DB->fetchOne($sql);
        if ($count < 1)
        {
            echo "ok";
        }
    }

    /**
     * rasti visus grupes vartotojus
     */
    public function findgroupusersAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        //$user_id = $auth->getIdentity()->vart_id;
        $request = $this->getRequest();
        $gr_id = trim(htmlspecialchars(stripcslashes($request->getParam('gr_id'))));

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $sql = "SELECT grn_nario_id,
                       vart_vardas,
                       vart_pavarde,
                       sk_pavad,
                       par_pavadinimas
                FROM l_gr_nariai gr_nar

                INNER JOIN l_vartotojai vart
                ON vart.vart_id = gr_nar.grn_nario_id

                INNER JOIN l_skyriai sk
                ON vart.vart_skyrius = sk.sk_id

                INNER JOIN l_pareigos par
                ON vart.vart_pareigos = par.par_id

                WHERE grn_grupes_id = '".$gr_id."'";
        $user_list = $DB->fetchAssoc($sql);
        $dep_data_transfer = new XMLWriter();
        $dep_data_transfer->openUri('php://output');
        $dep_data_transfer->startDocument('1.0','UTF-8');
        $dep_data_transfer->setIndent(4);
        $dep_data_transfer->startElement('root');

        foreach ($user_list as $user)
        {
            $user_name= $user['vart_vardas'];
            if ($user_name=="") $user_name = "-";

            $user_surname= $user['vart_pavarde'];
            if ($user_surname=="") $user_surname = "-";

            $user_place= $user['sk_pavad'];
            if ($user_place=="") $user_place = "-";

            $user_position= $user['par_pavadinimas'];
            if ($user_position=="") $user_position = "-";

            $dep_data_transfer->startElement('user');
                $dep_data_transfer->writeElement('user_id', $user['grn_nario_id']);
                $dep_data_transfer->writeElement('name', $user_name);
                $dep_data_transfer->writeElement('surname', $user_surname);
                $dep_data_transfer->writeElement('place', $user_place);
                $dep_data_transfer->writeElement('position', $user_position);
                $dep_data_transfer->writeElement('foto', 'http:/'.$path.'/readimage?type=vart_foto&id='.$user['grn_nario_id']);
            $dep_data_transfer->endElement();
        }
        // END root
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
    }

    public function delgroupAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $user_id = $auth->getIdentity()->vart_id;
        $request = $this->getRequest();
        $gr_id = trim(htmlspecialchars(stripcslashes($request->getParam('group_id'))));

        $sql = "DELETE
                FROM l_manogrupes
                WHERE mgr_vartotojas = '".$user_id."'
                AND mgr_id = '".$gr_id."'";
        if ($DB->query($sql))
        {
            $sql = "DELETE
                    FROM l_gr_nariai
                    WHERE grn_grupes_id = '".$gr_id."'";
            $DB->query($sql);
        }
        echo "ok";
    }
}
?>
