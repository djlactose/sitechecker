<?php include 'config.php';?>
<html>
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<title><?php echo($PageTitle);?></title>
<script lang="JavaScript">
var RefTime;
var interval;
var dtTracker="";

function enableRefresh(str){
	stopRefresh();
	if(str)
		RefTime=str;
	else
		RefTime=<?php echo($PageReload);?>;
	interval = setInterval(function(){change()}, RefTime);
}

function stopRefresh(){
	clearInterval(interval);
}

function change(){
	showGraph();
	showConList("",0);
	checkSite();
}

function showGraph(str){
        //AJAX call to get the facility data and then populate the fields found on the form
        xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById("graphDiv").innerHTML=xmlhttp.responseText
			document.getElementById("dtTable").style.width="100%";
                }
        }
        //Let the user know which fields are going to be populated if data is found, also lets them know when the check is done.
	document.getElementById("graphDiv").innerHTML="Loading..."
	if(str){
        	xmlhttp.open("GET","ajax/showGraph.php?DateTime="+str,true);
	}else
        	xmlhttp.open("GET","ajax/showGraph.php",true);
        xmlhttp.send();
}

function showConList(str,str2){
        //AJAX call to get the facility data and then populate the fields found on the form
	dtTracker=str;
        xmlhttp2=new XMLHttpRequest();
        xmlhttp2.onreadystatechange=function(){
                if (xmlhttp2.readyState==4 && xmlhttp2.status==200){
                        document.getElementById("conList").innerHTML=xmlhttp2.responseText
			document.getElementById("dtTable").style.width="100%";
                }
        }
        //Let the user know which fields are going to be populated if data is found, also lets them know when the check is done.
	document.getElementById("conList").innerHTML="Loading..."
	var script;
	if(document.getElementById('pingReport').checked == true)
		script='showConList.php';
	else
		script='showSiteCk.php';
	if(str!=="")
        	xmlhttp2.open("GET","ajax/"+script+"?DateTime="+str+"&Start="+str2,true);
	else
        	xmlhttp2.open("GET","ajax/"+script+"?Start="+str2,true);
        xmlhttp2.send();
}

function checkSite(){
        //AJAX call to get the facility data and then populate the fields found on the form
        xmlhttp3=new XMLHttpRequest();
        xmlhttp3.onreadystatechange=function(){
                if (xmlhttp3.readyState==4 && xmlhttp3.status==200){
                        document.getElementById("statusDiv").innerHTML=xmlhttp3.responseText
                }
        }
        //Let the user know which fields are going to be populated if data is found, also lets them know when the check is done.
        document.getElementById("statusDiv").innerHTML="Checking..."
        xmlhttp3.open("GET","ajax/showStatus.php",true);
        xmlhttp3.send();
}

function validateForm(){
	var logDT;
	var logDate=document.getElementById("logDate").value;
	var logTime=document.getElementById("logTime").value;
	var error;
	if(logDate)
		logDT=logDate;
	else
		error="No Date Provided";
	if(logTime)
		logDT=logDT + " " + logTime;
	else
		error=error + ("\nNo Time Provided");
	if(error)
		alert(error);
	else{
		showConList(logDT,0);
		showGraph(logDT);
		stopRefresh();
	}
}

function resetForm(){
	showGraph();
	showConList("",0);
	checkSite();
	enableRefresh();
	document.getElementById("logDate").value="";
	document.getElementById("logTime").value="";
}
</script>
</head>
<body onLoad='showGraph();showConList(dtTracker,0);checkSite();enableRefresh();' style="background: -webkit-linear-gradient(<?php echo($PageGrad1);?>, <?php echo($PageGrad2);?>);background: -o-linear-gradient(<?php echo($PageGrad1);?>, <?php echo($PageGrad2);?>);background: -moz-linear-gradient(<?php echo($PageGrad1);?>, <?php echo($PageGrad2);?>);background: linear-gradient(<?php echo($PageGrad1);?>, <?php echo($PageGrad2);?>);">
<div id="tableDiv" align="center">
<table style="border: 2px solid;border-radius: 25px;background-color: <?php echo($PageInnerBGColor);?>;">
<tr>
<td colspan=2><table width="100%" id="dtTable">
<tr>
<td style="vertical-align: middle;" align="right">Check Date and Time:</td>
<td style="vertical-align: middle;"> <input type="date" id="logDate"/> <input type="time" id="logTime"/><input type="button" value="View Log" onClick="validateForm();"><input type="button" value="Reset" onClick="resetForm();"></td>
<td style="vertical-align: middle;" align="center"><div id="statusDiv"/></td>
</tr>
</table>
</td>
</tr>
<tr>
<td valign="top"><div id="graphDiv"/></td>
<td>Report: <input type="radio" name="dsplayType" value="1" id="pingReport" onclick="document.getElementById('siteReport').checked = false;showConList(dtTracker,0);" checked/> Ping <input type="radio" name="displayType" value="2" id="siteReport" onclick ="document.getElementById('pingReport').checked = false;showConList(dtTracker,0);" />Site <br /><div id="conList"/></td>
</tr>
</table></div>
</body>
</html>
