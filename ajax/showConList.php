<table border=1>
<tr align=\"center\"><td colspan="3" align="center"><b>Ping Results</b></td></tr>
<tr><th>#</th><th>Date</th><th>Seconds</th></tr>
<?php
include '../config.php';
$dirtyNum=$_GET["Start"];
$pattern="/[0-9]*/";
preg_match($pattern,$dirtyNum,$match);
$startNum=$match[0];
$newStart=$startNum-1;
if($_GET["DateTime"]){
        $sDateTime=$_GET["DateTime"];
	if($newStart>0)
	        $sql="Select * FROM Ping WHERE `Date` BETWEEN \"$sDateTime\" AND ADDTIME(\"$sDateTime\",\"0 5:00:00\") ORDER BY Date DESC LIMIT $newStart,31";
	else
	        $sql="Select * FROM Ping WHERE `Date` BETWEEN \"$sDateTime\" AND ADDTIME(\"$sDateTime\",\"0 5:01:00\") ORDER BY Date DESC LIMIT $startNum,31";
}else{
        $DateTime="NOW()";
	if($newStart>0)
	        $sql="Select * FROM Ping WHERE DATE_SUB(NOW(), INTERVAL 5 HOUR) <= `Date` ORDER BY Date DESC LIMIT $newStart,31";
	else
	        $sql="Select * FROM Ping WHERE DATE_SUB(NOW(), INTERVAL 5 HOUR) <= `Date` ORDER BY Date DESC LIMIT $startNum,30";
}
$db = mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name,$db);
$result=mysql_query($sql,$db);
$NumOfRow = mysql_num_rows($result);
$OldTime="";
$current=1;
while ($NumOfRow >= $current){
        $row = mysql_fetch_array($result);
        $DateTime =  $row["Date"];
	if(($newStart>0 || $sDateTime) && $current==1){
        	$OldTime=strtotime($DateTime);
		$current++;
		continue;
	}elseif($current==1)
                $OldTime=strtotime('now');
        $DisplayTime=$OldTime-strtotime($DateTime);
	if($newStart>0 || $sDateTime)
		$disNum=$current+$startNum-1;
	else
		$disNum=$current+$startNum;
        if($DisplayTime>70)
                echo("<tr align=\"center\"><td>$disNum</td><td>$DateTime</td><td><font color=\"red\">$DisplayTime</font></td></tr>");
        else
                echo("<tr align=\"center\"><td>$disNum</td><td>$DateTime</td><td>$DisplayTime</td></tr>");
        $OldTime=strtotime($DateTime);
        $current++;
}
?>
<tr>
<td colspan=3 align="center"><?php 
if($startNum==0)
	echo("&lt;");
else{
	$preNum=$startNum-30;
	echo("<a href=\"javascript:showConList('$sDateTime',$preNum);stopRefresh();\">&lt;</a>");
}
$nexNum=$startNum+30;
if($nexNum>=300)
	echo(" &gt;");
else
	echo(" <a href=\"javascript:showConList('$sDateTime',$nexNum);stopRefresh();\">&gt;</a>");
?>
</tr>
</table>
