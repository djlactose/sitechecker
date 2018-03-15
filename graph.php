<?php
include 'config.php';
$db = mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name,$db);
if($checkNetwork){
	if($_GET["DateTime"]){
		$sql="Select * FROM Ping WHERE `Date` BETWEEN \"".$_GET["DateTime"]."\" AND ADDTIME(\"".$_GET["DateTime"]."\",\"0 5:00:00\") ORDER BY Date";
	}else{
		$sql="Select * FROM Ping WHERE DATE_SUB(NOW(), INTERVAL 5 HOUR) <= `Date` ORDER BY Date";
	}
	$result=mysql_query($sql,$db);
	$NumOfRow = mysql_num_rows($result);
	$current=1;
	while ($NumOfRow >= $current){
		$row = mysql_fetch_array($result);
		$DateTime =  $row["Date"];
		$DisDateTime = date("H:i",strtoTime($DateTime));
		if($current==2)
			$DTArray=$DisDateTime;
		else
			$DTArray=$DTArray . "," . $DisDateTime;
		$DTDiff=strtotime($DateTime)-$OldDiff;
		if($current==2)
			$Points=$DTDiff;
		else
			$Points=$Points . "," . $DTDiff;
		$Bar=$Bar . "|70";
		$OldDiff=strtotime($DateTime);
	        $current++;
	}
}
if ($checkSite){
	if($_GET["DateTime"]){
		$sql="Select * FROM SiteCk WHERE `Date` BETWEEN \"".$_GET["DateTime"]."\" AND ADDTIME(\"".$_GET["DateTime"]."\",\"0 5:00:00\") ORDER BY Date";
	}else{
		$sql="Select * FROM SiteCk WHERE DATE_SUB(NOW(), INTERVAL 5 HOUR) <= `Date` ORDER BY Date";
	}
	$result=mysql_query($sql,$db);
	$NumOfRow = mysql_num_rows($result);
	$current2=1;
	while ($NumOfRow >= $current2){
		$row = mysql_fetch_array($result);
		$DateTime =  $row["Date"];
		$DisDateTime = date("H:i",strtoTime($DateTime));
		if($current2==2)
			$DTArray2=$DisDateTime;
		else
			$DTArray2=$DTArray2 . "," . $DisDateTime;
		$DTDiff=strtotime($DateTime)-$OldDiff;
		if($current2==2)
			$Points2=$DTDiff;
		else
			$Points2=$Points2 . "," . $DTDiff;
		$Bar=$Bar . "|70";
		$OldDiff=strtotime($DateTime);
	        $current2++;
	}
}
header("Content-type: image/png");
$height = 800;
$width = 800;
$im = imagecreate($width,$height);
$white = imagecolorallocate($im,255,255,255);
$gray = imagecolorallocate($im,200,200,200);
$black = imagecolorallocate($im,0,0,0);
$red = imagecolorallocate($im,255,0,0);
$blue = imagecolorallocate($im,0,0,255);
$green = imagecolorallocate($im,0,199,20);
$VertLines=10;
while($VertLines<$height-10){
	imageline($im, 20, $VertLines, $width-20, $VertLines, $gray);
	$VertLines=$VertLines+($height/20);
	if(($VertLines/5)<155)
		imagestring($im,2,0,$height-$VertLines,$VertLines/5,$black);
}
$HorLines=20;
while($HorLines<$width-10){
        imageline($im, $HorLines, $height-30, $HorLines, 10, $gray);
        $HorLines=$HorLines+($width/20);
} 
if($checkNetwork){
	$Step=($width-40)/$current;
	$Start=20;
	$oldvalues=$height-30;
	$FirstRun=true;
	$count=0;
	$arrval = preg_split("/,/",$Points);
	$DArray = preg_split("/,/",$DTArray);
	foreach($arrval as $values){
		if($FirstRun){
			imageline($im,$Start,$width-($values*5),$Start+$Step,$width-($values*5),$blue);
			$FirstRun=false;
		}else
			imageline($im,$Start,$oldvalues,$Start+$Step,$width-($values*5),$blue);
		$oldvalues=$width-($values*5);
		$Start=$Start+$Step;
		$count++;
	}
}
if($checkSite){
	$Step=($width-40)/$current2;
	$Start=20;
	$oldvalues=$height-30;
	$FirstRun=true;
	$count=0;
	$arrval = preg_split("/,/",$Points2);
	$DArray = preg_split("/,/",$DTArray2);
	foreach($arrval as $values){
		if($FirstRun){
			imageline($im,$Start,$width-($values*5),$Start+$Step,$width-($values*5),$green);
			$FirstRun=false;
		}else
			imageline($im,$Start,$oldvalues,$Start+$Step,$width-($values*5),$green);
		if($count%60==0)
			imagestring($im,2,$Start,750,$DArray[$count],$black);
		$oldvalues=$width-($values*5);
		$Start=$Start+$Step;
		$count++;
	}
}
imageline($im, 20, $height-(70*5), $width-20, $height-(70*5), $red);
imageline($im, 20, $height-(50*5), $width-20, $height-(50*5), $red);
imageline($im, 20, $height-30, 20, 10, $black);
imageline($im, 20, $height-30, $width-20, $height-30, $black);
imagestring($im,5,300,780,"Graph of last 5 hours",$black);
imagestring($im,2,20,780,"Load at: " . date("m/d/Y H:i"),$black);
imagestring($im,3,525,780,"- Site Status",$green);
imagestring($im,3,675,780,"- Ping Status",$blue);
imagepng($im);
?>
