<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
include_once '../application/configs/db_connect.php';

class fotoController extends Zend_Controller_Action
{
     /**
     * foto galerija - sarasas
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

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $this->view->assign('logo','Foto galerija');
        $this->view->assign('label_all_albums','Visi albumai');
        $this->view->assign('label_my_albums','Mano albumai');
        $this->view->assign('label_new_album','Sukurti nauja albumą');
        $this->view->assign('path', $path);
       // $this->view->assign('group_list', $group_list);
         $this->view->assign('title', 'Armitana.lt >> Foto galerija');

    }

    /**
     * foto galerija - naujas albumas
     */
    public function newalbumAction()
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

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $sql_mygroups = "SELECT *
                         FROM l_manogrupes
                         WHERE mgr_vartotojas= '".$user_id."'";
        $mygroups_list = $DB->fetchAssoc($sql_mygroups);

        $sql_places =  "SELECT sk_id, sk_pavad
                        FROM `l_skyriai`
                        ORDER BY sk_pavad ASC";
        $places_list = $DB->fetchAssoc($sql_places);

        $sql_pos = "SELECT par_id, par_pavadinimas
                    FROM `l_pareigos`
                    ORDER BY par_pavadinimas ASC";
        $pos_list = $DB->fetchAssoc($sql_pos);

        $this->view->assign('logo','Foto galerija');
        $this->view->assign('label_all_albums','Visi albumai');
        $this->view->assign('label_my_albums','Mano albumai');
        $this->view->assign('label_new_album','Sukurti nauja albumą');
        $this->view->assign('label_album_name','Albumo pavadinimas *');
        $this->view->assign('label_album_info','Albumo aprašymas');
        $this->view->assign('label_add_group','Pasirinkti grupės');
        $this->view->assign('label_add_users','Pasirinkti vartotojus');
        $this->view->assign('label_can_view_list','Pasirinkit grupės/vartotojus kurie galės matyti albumą');

        $this->view->assign('select_user_place', $places_list);
        $this->view->assign('select_user_pos', $pos_list);

        $this->view->assign('label_album_fnd_user', 'Rasti vartotoją');
        $this->view->assign('label_album_fnd_name', 'Vardas');
        $this->view->assign('label_album_fnd_surname', 'Pavardė');
        $this->view->assign('label_album_fnd_place', 'Skyrius');
        $this->view->assign('label_album_fnd_pos', 'Pareigos');

        $this->view->assign('label_add_fotos', 'Įkelti nuotraukas į albumą');

        $this->view->assign('mygroups_list', $mygroups_list);
        $this->view->assign('path', $path);

        $this->view->assign('label_applyed_user_list', 'Vartotojai, kurie galės matyti albumą ');
        
        $this->view->assign('title', 'Armitana.lt >> Foto galerija');
    }

    /**
     * itraukti grupes vartotojus i galinciu
     * matyti sarasa
     */
    public function addgroupAction()
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

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $group_id = trim(htmlspecialchars(stripcslashes($request->getParam('group_id'))));

        $sql = "SELECT vart_id,
                       vart_vardas,
                       vart_pavarde,
                       grn_nario_id,
                       sk_pavad,
                       par_pavadinimas
                FROM l_gr_nariai grn

                INNER JOIN l_vartotojai vart
                ON vart.vart_id = grn.grn_nario_id

                INNER JOIN l_skyriai sk
                ON vart.vart_skyrius = sk.sk_id

                INNER JOIN l_pareigos par
                ON vart.vart_pareigos = par.par_id

                WHERE grn_grupes_id = '".$group_id."'";

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
            $dep_data_transfer->endElement();
        }
        // END root
        header("Content-type: text/xml");
        $dep_data_transfer->endElement();
        $dep_data_transfer->endDocument();
        $dep_data_transfer->flush();
    }

    public function fotouploadAction()
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

        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        //$info = " empty ";
        // jeigu ikeliama nuotrauka
        if($_FILES['album_foto_upload']['size'] >  0)
        {
            $fileName = $_FILES['album_foto_upload']['name'];
            $tmpName  = $_FILES['album_foto_upload']['tmp_name'];

            $nr = 0;
            //$info = "";
            $dir = opendir ("../public/temp");
            while (false !== ($file = readdir($dir)))
            {
                
                if (stripos($file, '.png')||stripos($file, '.jpg')||stripos($file, '.jpeg'))
                {
                    //$info = $info." file name - ".$file." \n";

                    $f_name = $user_id."_";
                    $pos = strpos($file, $f_name);
                    if ($pos !== false)
                    {
                        $down = strpos($file, '_');
                        $dot = strpos($file, '.');
                        $pr = strpos($file, '_pr');
                        if ($pr)
                            $answer = substr($file, $down+1, $dot-$down-4);
                        else
                            $answer = substr($file, $down+1, $dot-$down-1);
                        if ($answer > $nr) $nr = $answer;
                      //  $info = $info." nr ".$nr. ", answer = ".$answer." \n ";
                    }
                }
            }
            $dot = strpos($fileName, '.');
            $extension = substr($fileName, $dot);
            $nr = $nr+1;
            $foto_link = '../public/temp/'.$user_id.'_'.$nr.$extension;
            $preview_link = '../public/temp/'.$user_id.'_'.$nr.'_pr'.$extension;

            // sumazinam nuotrauka ir pakeiciam pavadinima
            $newName = $this->imageResize($tmpName, 800, 600, $foto_link);
            $preview = $this->imageResize($tmpName, 100, 100, $preview_link);
            $preview_link = '../temp/'.$user_id.'_'.$nr.'_pr'.$extension;

           // echo $info.' ../public/temp/'.$user_id.'_'.$nr.$extension;
           echo 'success'.$preview_link;
        }
        else echo "Klaida. ".$_FILES['album_foto_upload']['size'];
    }


    public function imageResize($img, $thumb_width, $max_height, $newfilename)
    {
        $max_width=$thumb_width;

    //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2'))
        {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

    //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type)
        {
            case 1: $im = imagecreatefromgif($img); break;
            case 2: $im = imagecreatefromjpeg($img);  break;
            case 3: $im = imagecreatefrompng($img); break;
            default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
        }

    /*** calculate the aspect ratio ***/
        $aspect_ratio = (float) $height_orig / $width_orig;

    /*** calulate the thumbnail width based on the height ***/
        $thumb_height = round($thumb_width * $aspect_ratio);


        while(($thumb_height>$max_width) AND ($thumb_height>$max_height))
        {
            $thumb_width-=10;
            $thumb_height = round($thumb_width * $aspect_ratio);
        }

        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

    /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) OR ($image_type==3))
        {
            imagealphablending($newImg, false);
            imagesavealpha($newImg,true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);

    //Generate the file, and rename it to $newfilename
        switch ($image_type)
        {
            case 1: imagegif($newImg,$newfilename); break;
            case 2: imagejpeg($newImg,$newfilename);  break;
            case 3: imagepng($newImg,$newfilename); break;
            default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
        }

        return $newfilename;
    }

}