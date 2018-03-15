<?php
include 'config.php';
$db = mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name,$db);
if($checkNetwork){
	echo("Checking Network...<br>");
	$sql="Select * FROM Ping WHERE DATE_SUB(NOW(), INTERVAL 5 MINUTE) <= `Date` ORDER BY Date DESC";
	$result=mysql_query($sql,$db);
	$NumOfRow = mysql_num_rows($result);
	$FirstRun=true;
	$Warning=0;
	$current=1;
	if($NumOfRow<1){
		$Message="No Connections within the last 5 Minutes";
		$Warning=5;
	}
	while ($NumOfRow >= $current){
       		$row = mysql_fetch_array($result);
	        $DateTime =  $row["Date"];
		if($FirstRun){
			$FirstRun=false;
			$DTDiff=strtotime(date("Y-m-d H:i:s")) - strtotime($DateTime);
		}else
			$DTDiff=$OldDiff-strtotime($DateTime);
		if($DTDiff>210 && $current<2){
			$Warning=5;
	                $Message="Ping not received";
	        }else if($DTDiff>70){
			$Message=$Message . "$DateTime delay of $DTDiff\n";
			$Warning++;
		}
		$OldDiff=strtotime($DateTime);
		$current++;
	}
}
if($checkSite){
	echo("Checking Site...<br>");
	$curlInit=curl_init($siteURL);
	curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
	curl_setopt($curlInit,CURLOPT_HEADER,true);
	curl_setopt($curlInit,CURLOPT_NOBODY,true);
	curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
	if(curl_exec($curlInit)){
		$sql="INSERT INTO SiteCk (Date) Values (NOW())";
		$result=mysql_query($sql,$db);
	}
	curl_close($curlInit);
	$sql="Select * FROM SiteCk WHERE DATE_SUB(NOW(), INTERVAL 5 MINUTE) <= `Date` ORDER BY Date DESC";
	$result=mysql_query($sql,$db);
	$NumOfRow = mysql_num_rows($result);
	$current=1;
	if($NumOfRow<1){
		$Message=$Message . " " . $siteURL . " unreachable for the last 5 Minutes";
		$Warning=5;
	}
}
if($sendNotice){
	$sql="Select * FROM Notice WHERE DATE_SUB(NOW(), INTERVAL 5 MINUTE) <= `Date`";
	$result=mysql_query($sql,$db);
	$NumOfRow = mysql_num_rows($result);
	if($Warning>3){
		$url = "http://www.google.com";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($retcode==200){
			if($NumOfRow<1){
				mail($emailList,$emailSubject,"The Following Issue(s) has been Detected: \n\n" . $Message . "\n\nAnother page will be sent in 5 minutes if this issue has not been resolved");
				$sql="INSERT INTO Notice (Date) Values (NOW())";
				$result=mysql_query($sql,$db);
				echo("Notice sent");
			}else
				echo("Notice sent within last 5 minutes");
		}else
			echo("Internet is down in the office");
	}else
		echo("No Problem Detected");
}
?>
