<?php
$db = mysql_connect("localhost", "root", "");
mysql_select_db("armi_intranet", $db) or die(mysql_errno() . ": " . mysql_error() . "<br>");
?>
