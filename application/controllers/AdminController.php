<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql_places = "SELECT sk_id,
                              sk_pavad
                       FROM `l_skyriai`
                       ORDER BY sk_pavad ASC";
        $place_list = $DB->fetchAssoc($sql_places);

        $sql_groups = "SELECT pGrupes_id,
                              pGrupes_pavadinimas
                       FROM `l_pagr_grupes`
                       ORDER BY pGrupes_pavadinimas ASC";
        $group_list = $DB->fetchAssoc($sql_groups);

        $this->view->assign('label_search','Vartotojo paieška');
        $this->view->assign('label_src_name','Vardas');
        $this->view->assign('label_src_surname', 'Pavardė');
        $this->view->assign('label_src_group', 'Grupė');
        $this->view->assign('select_src_group', $group_list);
        $this->view->assign('label_src_place', 'Skyrius');
        $this->view->assign('select_src_place', $place_list);
        $this->view->assign('submit_find', 'Rasti');

        $this->view->assign('title', 'Armitana.lt >> Administravimas');
	$this->view->assign('logo', 'Administravimas');
    }

    public function finduserAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        //echo "header('Content-Type: text/xml')";
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');
        
        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $surname = trim(htmlspecialchars(stripcslashes($request->getParam('surname'))));
        $group = trim(htmlspecialchars(stripcslashes($request->getParam('group'))));
        $place = trim(htmlspecialchars(stripcslashes($request->getParam('place'))));

        if ($name == "" )
            $name = 'IS NOT NULL';
        else
            $name = " = '".$name."'";

        if ($surname == "" )
            $surname = 'IS NOT NULL';
        else
            $surname = " = '".$surname."'";

        if ($group == 'all')
            $group = 'IS NOT NULL';
        else
            $group = " = '".$group."'";

        if ($place == 'all')
            $place = 'IS NOT NULL';
        else 
            $place = " = '".$place."'";

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);
    
        $sql = "SELECT vart_id, vart_vardas, vart_pavarde, vart_pastas, vart_tel,
                sk_pavad,
                par_pavadinimas,
                pGrupes_pavadinimas
                FROM l_vartotojai vart

                INNER JOIN l_skyriai sk
                ON vart.vart_skyrius = sk.sk_id

                INNER JOIN l_pagr_grupes pgr
                ON vart.vart_grupe = pgr.pGrupes_id

                INNER JOIN l_pareigos par
                ON vart.vart_pareigos = par.par_id

                WHERE vart_vardas  ".$name." AND
                      vart_pavarde ".$surname." AND
                      vart_grupe   ".$group." AND
                      vart_skyrius ".$place;

        $user_list = $DB->fetchAssoc($sql);
        $dep_data_transfer = new XMLWriter();
        $dep_data_transfer->openUri('php://output');
        $dep_data_transfer->startDocument('1.0','UTF-8');
        $dep_data_transfer->setIndent(4);
        $dep_data_transfer->startElement('root');

        foreach ($user_list as $user)
        {
            $user_tel = $user['vart_tel'];
            if ($user_tel=="") $user_tel = "-";

            $user_name= $user['vart_vardas'];
            if ($user_name=="") $user_name = "-";

            $user_surname= $user['vart_pavarde'];
            if ($user_surname=="") $user_surname = "-";

            $user_place= $user['sk_pavad'];
            if ($user_place=="") $user_place = "-";

            $user_group= $user['pGrupes_pavadinimas'];
            if ($user_group=="") $user_group = "-";

            $user_position= $user['par_pavadinimas'];
            if ($user_position=="") $user_position = "-";
            
            $user_mail= $user['vart_pastas'];
            if ($user_mail=="") $user_mail = "-";

            $dep_data_transfer->startElement('user');
                $dep_data_transfer->writeElement('id', $user['vart_id']);
                $dep_data_transfer->writeElement('name', $user_name);
                $dep_data_transfer->writeElement('surname', $user_surname);
                $dep_data_transfer->writeElement('place', $user_place);
                $dep_data_transfer->writeElement('position', $user_position);
                $dep_data_transfer->writeElement('tel', $user_tel);
                $dep_data_transfer->writeElement('group', $user_group);
                $dep_data_transfer->writeElement('mail', $user_mail);
                $dep_data_transfer->writeElement('foto', 'http:/'.$path.'/readimage?type=vart_foto&id='.$user['vart_id']);
            $dep_data_transfer->endElement();
        }
        // END root
        
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
        
    }

    public function listsAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $list_name = trim(htmlspecialchars(stripcslashes($request->getParam('info'))));

        switch ($list_name)
        {
            case 'position':
                $db_table = 'l_pareigos';
                $db_id = 'par_id';
                $db_col = 'par_pavadinimas';
            break;
            case 'group':
                $db_table = 'l_pagr_grupes';
                $db_id = 'pGrupes_id';
                $db_col = 'pGrupes_pavadinimas';
            break;
            case 'place':
                $db_table = 'l_skyriai';
                $db_id = 'sk_id';
                $db_col = 'sk_pavad';
            break;
            case 'city':
                $db_table = 'l_miestai';
                $db_id = 'm_id';
                $db_col = 'm_pavadinimas';
            break;
            case 'net':
                $db_table = 'l_tinklai';
                $db_id = 'tink_id';
                $db_col = 'tink_pavadinimas';
            break;
        }

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "SELECT * FROM ". $db_table;
        $pos_list = $DB->fetchAssoc($sql);

        $dep_data_transfer = new XMLWriter();
        $dep_data_transfer->openUri('php://output');
        $dep_data_transfer->startDocument('1.0','UTF-8');
        $dep_data_transfer->setIndent(4);
        $dep_data_transfer->startElement('root');

        foreach ($pos_list as $pos)
        {
            $dep_data_transfer->startElement('list');
                $dep_data_transfer->writeElement('id', $pos[$db_id]);
                $dep_data_transfer->writeElement('name', $pos[$db_col]);
            $dep_data_transfer->endElement();
        }
        // END root
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
    }

    public function saveAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $surname = trim(htmlspecialchars(stripcslashes($request->getParam('surname'))));
        $tel = trim(htmlspecialchars(stripcslashes($request->getParam('tel'))));
        $position = trim(htmlspecialchars(stripcslashes($request->getParam('position'))));
        $mail = trim(htmlspecialchars(stripcslashes($request->getParam('mail'))));
        $group = trim(htmlspecialchars(stripcslashes($request->getParam('group'))));
        $place = trim(htmlspecialchars(stripcslashes($request->getParam('place'))));

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "UPDATE l_vartotojai SET vart_vardas = '".$name."',
                                        vart_pavarde ='".$surname."',
                                        vart_skyrius ='".$place."',
                                        vart_grupe   ='".$group."',
                                        vart_pastas  ='".$mail."',
                                        vart_tel     ='".$tel."',
                                        vart_pareigos='".$position."'
                WHERE vart_id = '".$id."'";
       // echo $sql;
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 2, "'".$sql."'"); // vartotojo grupes pakeitymas

        echo "done";
    }

    public function deluserAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "DELETE FROM l_vartotojai WHERE vart_id =".$id;
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 12, $sql); // pašalintas vartotojas
        echo "done";
    }

    // ivykiu sarasas
    public function logAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql_events = "SELECT * FROM l_ivykiu_tipai ORDER BY logt_aprasymas ASC";
        $event_list = $DB->fetchAssoc($sql_events);

        $sql_places = "SELECT sk_id, sk_pavad FROM `l_skyriai` ORDER BY sk_pavad ASC";
        $place_list = $DB->fetchAssoc($sql_places);

        $sql_groups = "SELECT pGrupes_id, pGrupes_pavadinimas FROM `l_pagr_grupes` ORDER BY pGrupes_pavadinimas ASC";
        $group_list = $DB->fetchAssoc($sql_groups);
// ivykio pajeska
        $this->view->assign('label_search','Įvykių paješka');
        $this->view->assign('label_src_name','Varotojo vardas');        //1
        $this->view->assign('label_src_surname', 'Varotojo pavardė');   //2
        $this->view->assign('label_src_group', 'Vartotojo grupė');      //3
        $this->view->assign('select_src_group', $group_list);
        $this->view->assign('label_src_place', 'Skyrius');              //4
        $this->view->assign('select_src_place', $place_list);
        $this->view->assign('label_src_event', 'Įvykio tipas');         //5
        $this->view->assign('select_src_event', $event_list);
        $this->view->assign('label_src_date1', 'Data nuo');             //6
        $this->view->assign('label_src_date2', 'Data iki');             //7
        $this->view->assign('submit_find', 'Rasti');
// naujo ivykio tipo irasymas
        $this->view->assign('label_new','Naujas įvykio tipas');
        $this->view->assign('label_new_event','Įvykio pavadinimas');
        $this->view->assign('submit_enter', 'Sukurti');
// logo
        $this->view->assign('title', 'Armitana.lt >> Administravimas');
	$this->view->assign('logo', 'Administravimas');
        
    }
    
    public function findeventsAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $surname = trim(htmlspecialchars(stripcslashes($request->getParam('surname'))));
        $group = trim(htmlspecialchars(stripcslashes($request->getParam('group'))));
        $place = trim(htmlspecialchars(stripcslashes($request->getParam('place'))));
        $d_from = trim(htmlspecialchars(stripcslashes($request->getParam('from'))));
        $d_to = trim(htmlspecialchars(stripcslashes($request->getParam('to'))));
        $e_type = trim(htmlspecialchars(stripcslashes($request->getParam('type'))));
        //echo $e_type."  >>  ";
        if ($name == "" )
            $name = 'IS NOT NULL';
        else
            $name = " = '".$name."'";

        if ($surname == "" )
            $surname = 'IS NOT NULL';
        else
            $surname = " = '".$surname."'";

        if ($group == 'all')
            $group = 'IS NOT NULL';
        else
            $group = " = '".$group."'";

        if ($place == 'all')
            $place = 'IS NOT NULL';
        else
            $place = " = '".$place."'";

        if ($d_from == "")
            $d_from = "IS NOT NULL";
        else
            $d_from = " > '" . $d_from . "' - INTERVAL 1 DAY";

        if ($d_to == "")
            $d_to = "IS NOT NULL";
        else
            $d_to = " < '" . $d_to . "' + INTERVAL 1 DAY";

        if ($e_type == 'all')
            $e_type = 'IS NOT NULL';
        else
            $e_type = " = '".$e_type."'";

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "SELECT  log_id, vart_id, vart_vardas, vart_pavarde,
                        sk_pavad,
                        par_pavadinimas,
                        pGrupes_pavadinimas,
                        logt_aprasymas,
                        log_data


                FROM l_log log

                INNER JOIN l_vartotojai vart
                ON log.log_vartotojas = vart.vart_id

                INNER JOIN l_ivykiu_tipai logt
                ON log.log_ivykioId = logt.logt_id

                INNER JOIN l_skyriai sk
                ON vart.vart_skyrius = sk.sk_id 

                INNER JOIN l_pagr_grupes pgr
                ON vart.vart_grupe = pgr.pGrupes_id

                INNER JOIN l_pareigos par
                ON vart.vart_pareigos = par.par_id

                WHERE vart.vart_vardas  "   .$name." AND
                      vart.vart_pavarde "   .$surname." AND
                      vart.vart_grupe   "   .$group." AND
                      vart.vart_skyrius "   .$place." AND
                      logt.logt_id "        .$e_type." AND
                      log_data "            .$d_from." AND
                      log_data "            .$d_to."
                LIMIT 100";

        //echo $sql;
        $events_list = $DB->fetchAssoc($sql);
        $dep_data_transfer = new XMLWriter();
        $dep_data_transfer->openUri('php://output');
        $dep_data_transfer->startDocument('1.0','UTF-8');
        $dep_data_transfer->setIndent(4);
        $dep_data_transfer->startElement('root');

        foreach ($events_list as $event)
        {
            $e_type = $event['logt_aprasymas'];
            if ($e_type=="") $e_type = "-";

            $user_name= $event['vart_vardas'];
            if ($user_name=="") $user_name = "-";

            $user_surname= $event['vart_pavarde'];
            if ($user_surname=="") $user_surname = "-";

            $user_place= $event['sk_pavad'];
            if ($user_place=="") $user_place = "-";

            $user_group= $event['pGrupes_pavadinimas'];
            if ($user_group=="") $user_group = "-";

            $user_position= $event['par_pavadinimas'];
            if ($user_position=="") $user_position = "-";

            $date= $event['log_data'];
            if ($date=="") $date = "-";

            $dep_data_transfer->startElement('event');
                $dep_data_transfer->writeElement('log_id', $event['log_id']);
                $dep_data_transfer->writeElement('id', $event['vart_id']);
                $dep_data_transfer->writeElement('name', $user_name);
                $dep_data_transfer->writeElement('surname', $user_surname);
                $dep_data_transfer->writeElement('place', $user_place);
                $dep_data_transfer->writeElement('position', $user_position);
                $dep_data_transfer->writeElement('e_type', $e_type);
                $dep_data_transfer->writeElement('group', $user_group);
                $dep_data_transfer->writeElement('date', $date);
            $dep_data_transfer->endElement();
        }
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
    }

    public function neweventAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');
        
        $request = $this->getRequest();
        $e_name = trim(htmlspecialchars(stripcslashes($request->getParam('e_name'))));

        $sql = "INSERT INTO l_ivykiu_tipai(logt_aprasymas) VALUES ('".$e_name."')";
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 13, $sql); // sukurtas naujas įvykio tipas
        echo "ok";
    }


    public function settingsAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql_city = "SELECT * FROM l_miestai ORDER BY m_pavadinimas ASC";
        $city_list = $DB->fetchAssoc($sql_city);

        $sql_net = "SELECT * FROM l_tinklai ORDER BY tink_pavadinimas ASC";
        $net_list = $DB->fetchAssoc($sql_net);

        $sql_pos = "SELECT * FROM l_pareigos ORDER BY par_pavadinimas ASC";
        $pos_list = $DB->fetchAssoc($sql_pos);

        $this->view->assign('label_settings','Kiti nustatymai');
        $this->view->assign('label_places', 'Skyriai');
        // skyriaus pajeska
        $this->view->assign('label_place_find', 'Skyriaus paieška');
        $this->view->assign('label_net_find', 'Tinklas');
        $this->view->assign('select_net_find', $net_list);
        $this->view->assign('label_city_find', 'Miestas');
        $this->view->assign('select_city_find', $city_list);
        $this->view->assign('label_name_find', 'Pavadinimas');
        $this->view->assign('submit_place_find', 'Rasti');
        // naujas skyrius
        $this->view->assign('label_place_new', 'Naujas skyrius');
        $this->view->assign('label_addr_new', 'Adresas');
        $this->view->assign('label_name_new', 'Pavadinimas');
        $this->view->assign('label_tel_new', 'Telefono nr.');
        $this->view->assign('label_mail_new', 'El. paštas');
        $this->view->assign('submit_place_new', 'Sukurti nauja skyrių');

        // tinklai
        $this->view->assign('label_net', 'Tinklai');
        $this->view->assign('label_net_new', 'Naujas tinklas');
        $this->view->assign('label_net_new_name', 'Tinklo pavadinimas');
        $this->view->assign('submit_net_new', 'Sukurti nauja tinklą');
        $this->view->assign('label_net_id', 'Tinklo id');
        // pareigos
        $this->view->assign('label_pos', 'Pareigos');
        $this->view->assign('label_pos_new', 'Naujos pareigos');
        $this->view->assign('label_pos_new_name', 'Pareigų pavadinimas');
        $this->view->assign('submit_pos_new', 'Sukurti naujas pareigas');
        $this->view->assign('label_pos_id', 'Pareigų id');
        $this->view->assign('select_pos_find', $pos_list);
        // kita
        $this->view->assign('label_other', 'Kita');
        $this->view->assign('label_other_mail', 'Kontaktinis el. paštas');
        $this->view->assign('submit_others', 'Išsaugoti pakeitimus');
        // logo
        $this->view->assign('title', 'Armitana.lt >> Administravimas');
	$this->view->assign('logo', 'Administravimas');
    }

    public function findplacesAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $city = trim(htmlspecialchars(stripcslashes($request->getParam('city'))));
        $net = trim(htmlspecialchars(stripcslashes($request->getParam('net'))));

        if ($name == "" )
            $name = 'IS NOT NULL';
        else
            $name = " = '".$name."'";

        if ($city == "all" )
            $city = 'IS NOT NULL';
        else
            $city = " = '".$city."'";

        if ($net == "all" )
            $net = 'IS NOT NULL';
        else
            $net = " = '".$net."'";

        $sql = "SELECT sk_id, sk_pavad, sk_adresas, sk_tel, sk_mail,
                       m_pavadinimas,
                       tink_pavadinimas
                FROM l_skyriai skr

                INNER JOIN l_miestai mst
                ON mst.m_id = skr.sk_miestoID
                
                INNER JOIN l_tinklai tnk
                ON tnk.tink_id = skr.sk_tinklas

                WHERE 
                    skr.sk_tinklas  "   .$net." AND
                    skr.sk_miestoID "    .$city." AND
                    skr.sk_pavad "      .$name;
        //echo $sql;
        $place_list = $DB->fetchAssoc($sql);
        $dep_data_transfer = new XMLWriter();
        $dep_data_transfer->openUri('php://output');
        $dep_data_transfer->startDocument('1.0','UTF-8');
        $dep_data_transfer->setIndent(4);
        $dep_data_transfer->startElement('root');

        foreach ($place_list as $place)
        {
            $net = $place['tink_pavadinimas'];
            if ($net=="") $net = "-";
            
            $city = $place['m_pavadinimas'];
            if ($city=="") $city = "-";
            
            $name = $place['sk_pavad'];
            if ($name=="") $name = "-";
            
            $adr = $place['sk_adresas'];
            if ($adr=="") $adr = "-";

            $tel = $place['sk_tel'];
            if ($tel=="") $tel = "-";

            $mail = $place['sk_mail'];
            if ($mail=="") $mail = "-";
            
            $dep_data_transfer->startElement('place');
                $dep_data_transfer->writeElement('id', $place['sk_id']);
                $dep_data_transfer->writeElement('name', $name);
                $dep_data_transfer->writeElement('net', $net);
                $dep_data_transfer->writeElement('city', $city);
                $dep_data_transfer->writeElement('adr', $adr);
                $dep_data_transfer->writeElement('tel', $tel);
                $dep_data_transfer->writeElement('mail', $mail);
            $dep_data_transfer->endElement();
        }
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
    }

    //skyriaus redagavimas
    public function saveeditAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $city = trim(htmlspecialchars(stripcslashes($request->getParam('city'))));
        $net = trim(htmlspecialchars(stripcslashes($request->getParam('net'))));
        $adr = trim(htmlspecialchars(stripcslashes($request->getParam('adr'))));
        $tel = trim(htmlspecialchars(stripcslashes($request->getParam('tel'))));
        $mail = trim(htmlspecialchars(stripcslashes($request->getParam('mail'))));
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));

        $sql = "UPDATE l_skyriai SET sk_pavad   ='".$name."',
                                     sk_miestoID='".$city."',
                                     sk_adresas ='".$adr."',
                                     sk_tel     ='".$tel."',
                                     sk_mail    ='".$mail."',
                                     sk_tinklas ='".$net."'
                WHERE sk_id = '".$id."'";
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 11, $sql); // skyriaus duomenu redagavimas
        echo "done";
    }

    public function delplaceAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "DELETE FROM l_skyriai WHERE sk_id =".$id;
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 12, $sql); // pašalintas skyrius
        echo "done";
    }

    public function newplaceAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $adr = trim(htmlspecialchars(stripcslashes($request->getParam('adr'))));
        $net = trim(htmlspecialchars(stripcslashes($request->getParam('net'))));
        $tel = trim(htmlspecialchars(stripcslashes($request->getParam('tel'))));
        $mail = trim(htmlspecialchars(stripcslashes($request->getParam('mail'))));
        $city = trim(htmlspecialchars(stripcslashes($request->getParam('city'))));

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "INSERT INTO l_skyriai (sk_pavad, sk_miestoID, sk_adresas, sk_tel, sk_mail, sk_tinklas)
                       VALUES('".$name."',
                              '".$city."',
                              '".$adr."',
                              '".$tel."',
                              '".$mail."',
                              '".$net."')";
        $DB->query($sql);
       // echo $sql;
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 14, $sql); // sukurtas naujas skyrius
        echo "done";
    }

    public function newnetAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "INSERT INTO l_tinklai (tink_pavadinimas) VALUES ('".$name."')";
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 15, $sql); // sukurtas naujas tinklas
        echo "done";
    }

    public function saveednetAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "UPDATE l_tinklai SET tink_pavadinimas ='".$name."' WHERE tink_id ='".$id."'";
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 16, $sql); // pakeisstas tinklo pavadinimas
        echo "done";
    }

    public function delnetAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "DELETE FROM l_tinklai WHERE tink_id =".$id;
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 17, $sql); // pašalintas tinklas
        echo "done";
    }

    public function newposAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "INSERT INTO l_pareigos (par_pavadinimas) VALUES ('".$name."')";
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 18, $sql); // sukurtos naujos pareigos
        echo "done";
    }

    public function saveedposAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('name'))));
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "UPDATE l_pareigos SET par_pavadinimas ='".$name."' WHERE par_id ='".$id."'";
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 19, $sql); // pakeistas pareigu pavadinimas
        echo "done";
    }

    public function delposAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        if ($auth->getIdentity()->vart_grupe != 1) //admin
        {
            $this->_redirect('/user');
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $id = trim(htmlspecialchars(stripcslashes($request->getParam('id'))));
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $sql = "DELETE FROM l_pareigos WHERE par_id =".$id;
        $DB->query($sql);
        $user_id = $auth->getIdentity()->vart_id;
        logs($user_id, 20, $sql); // pašalintos pareigos
        echo "done";
    }


}
?>