<?php
/*
 * globalios funkcijos
 */

// isaugo vartotoju veiksmus
function logs($id, $logt, $sql_text)
{
    $registry = Zend_Registry::getInstance();
    $DB = $registry['DB'];

    $DB->setFetchMode(Zend_Db::FETCH_OBJ);

    if ($id == 'last')
    {
        $sql = "SELECT MAX(vart_id) FROM l_vartotojai";
        $id = $DB->fetchOne($sql);
    }
   
    $sql = "INSERT INTO l_log(log_vartotojas, log_ivykioId)  VALUES (".$id.",".$logt.")";
    // echo "globa log: ".$sql;

    $DB->query($sql);
}
?>
