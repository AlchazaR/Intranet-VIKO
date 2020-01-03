<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
include_once '../application/configs/db_connect.php';

class SettingsController extends Zend_Controller_Action
{
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

        $sql_places = "SELECT sk_id,
                              sk_pavad
                       FROM `l_skyriai`
                       ORDER BY sk_pavad ASC";
        $place_list = $DB->fetchAssoc($sql_places);

        $sql_pos = "SELECT * 
                    FROM l_pareigos
                    ORDER BY par_pavadinimas ASC";
        $pos_list = $DB->fetchAssoc($sql_pos);

        $id = $auth->getIdentity()->vart_id;

        $sql_info = "SELECT vart_vardas,
                            vart_pavarde,
                            vart_tel,
                            vart_pastas,
                            vart_pareigos,
                            vart_skyrius
                     FROM l_vartotojai
                     WHERE vart_id = '".$id."'";
        $user_info = $DB->fetchAssoc($sql_info);
        foreach ($user_info as $user)
        {
            $name = $user['vart_vardas'];
            $surname = $user['vart_pavarde'];
            $tel = $user['vart_tel'];
            $mail = $user['vart_pastas'];
            $pos = $user['vart_pareigos'];
            $place = $user['vart_skyrius'];
        }
        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;

        $this->view->assign('label_user_settings','Asmens duomenis');
        $this->view->assign('path', $path);
        $this->view->assign('edit_action', 'http:/'.$path.'/settings/edit');
        $this->view->assign('user_foto', 'http:/'.$path.'/readimage?type=vart_foto&id='.$auth->getIdentity()->vart_id);
        $this->view->assign('label_user_name','Vardas');
        $this->view->assign('label_user_surname', 'Pavardė');
        $this->view->assign('label_add_foto', 'Pakeisti nuotrauką');
        $this->view->assign('label_user_tel', 'Telefono nr.');
        $this->view->assign('label_user_mail', 'El. paštas');
        $this->view->assign('label_user_pos', 'Pareigos');
        $this->view->assign('select_user_pos', $pos_list);
        $this->view->assign('label_user_place', 'Skyrius');
        $this->view->assign('select_user_place', $place_list);
        $this->view->assign('submit_user_save', 'Išsaugoti pakitimus');
        $this->view->assign('submit_browse', '...');
        $this->view->assign('val_user_name', $name);
        $this->view->assign('val_user_surname', $surname);
        $this->view->assign('val_user_tel', $tel);
        $this->view->assign('val_user_mail', $mail);
        $this->view->assign('val_user_pos', $pos);
        $this->view->assign('val_user_place', $place);

        $this->view->assign('label_change_pass', 'Slaptažodžio keitimas');
        $this->view->assign('label_old_pass', 'Senas slaptažodis');
        $this->view->assign('label_new_pass', 'Naujas slaptažodis');
        $this->view->assign('label_new_pass_confirm', 'Pakartoti slaptažodį');
        $this->view->assign('submit_pass', 'Pakeisti slaptažodį');
        

        $this->view->assign('title', 'Armitana.lt >> Nustatimai');
	$this->view->assign('logo', 'Nustatimai');
    }

    public function editAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $request = $this->getRequest();
        $id = $auth->getIdentity()->vart_id;
        $name = trim(htmlspecialchars(stripcslashes($request->getParam('user_name'))));
        $surname = trim(htmlspecialchars(stripcslashes($request->getParam('user_surname'))));
        $tel = trim(htmlspecialchars(stripcslashes($request->getParam('user_tel'))));
        $mail = trim(htmlspecialchars(stripcslashes($request->getParam('user_mail'))));
        $pos = trim(htmlspecialchars(stripcslashes($request->getParam('user_pos'))));
        $place = trim(htmlspecialchars(stripcslashes($request->getParam('user_place'))));
        // jeigu keiciama nuotrauka
        if($_FILES['user_foto']['size'] >  0)
        {
            $fileName = $_FILES['user_foto']['name'];
            $tmpName  = $_FILES['user_foto']['tmp_name'];

            // suamzinam nuotrauka
            $tmpName = $this->imageResize($tmpName, 65, 65, 'new_img');

            $fp      = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            $content = addslashes($content);
            fclose($fp);

            $sql = "UPDATE l_vartotojai SET vart_foto = '".$content."',
                                            vart_vardas = '".$name."',
                                            vart_pavarde = '".$surname."',
                                            vart_tel = '".$tel."',
                                            vart_pastas = '".$mail."',
                                            vart_pareigos = '".$pos."',
                                            vart_skyrius = '".$place."'
                    WHERE vart_id = '".$id."'";
           mysql_query($sql) or die(mysql_error());
        }
        // jeigu nuotrauka nesikeicia
        else
        {
            $sql = "UPDATE l_vartotojai SET vart_vardas = '".$name."',
                                            vart_pavarde = '".$surname."',
                                            vart_tel = '".$tel."',
                                            vart_pastas = '".$mail."',
                                            vart_pareigos = '".$pos."',
                                            vart_skyrius = '".$place."'
                    WHERE vart_id = '".$id."'";
            mysql_query($sql) or die(mysql_error());
        }
        // griztam atgal
        $config = new Zend_Config_Ini('../application/config.ini', 'host');
        $path = $config->webhost;
        header('Location: http:/'.$path.'/settings'); 
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

    public function changepassAction()
    {
        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}

        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $DB->setFetchMode(Zend_Db::FETCH_OBJ);

        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('empty');

        $request = $this->getRequest();
        $id = $auth->getIdentity()->vart_id;
        $old_pass = trim(htmlspecialchars(stripcslashes($request->getParam('old_pass'))));
        $new_pass = trim(htmlspecialchars(stripcslashes($request->getParam('new_pass'))));

        $sql ="SELECT vart_password
               FROM l_vartotojai
               WHERE vart_id ='".$id."'";
        $pass = $DB->fetchOne($sql);

        if (MD5($old_pass) == $pass)
        {
            $sql = "UPDATE l_vartotojai
                    SET vart_password = '".MD5($new_pass)."'
                    WHERE vart_id = '".$id."'";
             $DB->query($sql);
             echo "ok";
        }
        else
            echo "pass_error";

    }
}

?>
