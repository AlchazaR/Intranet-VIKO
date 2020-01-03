<?php

class menu_navigation extends Zend_View_Helper_Abstract
{
    public function navigation()
    {
        $registry = Zend_Registry::getInstance();
        $DB = $registry['DB'];

        $auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity())
        {
            $this->_redirect('/user/loginform');
	}
        else
        {
            $config = new Zend_Config_Ini('../application/config.ini', 'host');
            $path = $config->webhost;

            $name = $auth->getIdentity()->vart_vardas;
            $surname = $auth->getIdentity()->vart_pavarde;
            $menu = " ". $name . "<br />" . $surname ."<br /><br />";
            $menu = $menu . '<UL >
                    <li class="menu"><a href=/'.$path.'/homepage class="menu">      Pradžia         </a> </li>
                    <li class="menu"><a href=/'.$path.'/homepage class="menu">      Straipsniai     </a></li>
                    <li class="menu"><a href=/'.$path.'/homepage/blog class="menu"> Dienoraštis     </a></li>
                    <li class="menu"><a href=/'.$path.'/homepage class="menu">      Kalendorius     </a> </li>
                    <li class="menu"><a href=/'.$path.'/mygroups class="menu">      Mano grupės     </a></li>
                    <li class="menu"><a href=/'.$path.'/homepage class="menu">      Failų talpykla  </a></li>
                    <li class="menu"><a href=/'.$path.'/foto class="menu">      Foto galerija   </a></li>
                    <li class="menu"><a href=/'.$path.'/settings/ class="menu">      Nustatymai     </a> </li>';
            if ($auth->getIdentity()->vart_grupe == 1) //admin
            {
                $menu = $menu . '<li class="menu"><a href=/'.$path.'/admin/ class="menu"> Adiminstravimas </a> </li>';
            }
            $menu = $menu . '<li class="menu"><a href=/'.$path.'/user/logout class="menu"> Išeiti </a> </li></UL>';
        }
        return $menu;
     }
}



?>
