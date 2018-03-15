<?php
if($_GET["DateTime"]){
	?><img src="graph.php?DateTime=<?php echo(str_replace("\"","",$_GET["DateTime"]));?>"><?php
}else{
	?><img src="graph.php"><?php
}?>
