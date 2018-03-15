<?php
include '../config.php';
$curlInit=curl_init($siteURL);
curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
curl_setopt($curlInit,CURLOPT_HEADER,true);
curl_setopt($curlInit,CURLOPT_NOBODY,true);
curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
if(curl_exec($curlInit))
	echo("Server Status: <font color=\"Green\">Up</font>");
else
	echo("Server Status: <font color=\"Red\">DOWN</font>");
curl_close($curlInit);
?>
