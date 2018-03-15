<?php
include 'config.php';
$db = mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name,$db);
$sql="INSERT INTO Ping (Date) Values (NOW())";
$result=mysql_query($sql,$db);
echo("Oh hello there");
?>
