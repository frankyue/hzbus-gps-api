<?php
if(!@$_GET['Station'])
{
	echo "please select a station!";
	exit;
}
require_once(dirname(__FILE__).'/function/function.php');
if(!@$_GET['type'])
	$data[1] = '1';
else
	$data[1] = $_GET['type'];

$data[0] = $_GET['Line'];
$data[5] = $_GET['Station'];
$info = LS_getdata($data);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BUS GPS</title>
<style type="text/css">
@import url(res/html.css);
</style>
</head>

<body>
<div style="width:320px;height:240px;">
	<div class="title">
		<ul ><li style="width:260px" >
			<div class="w u"><a href="index.php"><?php GLOBAL $info;echo $info[0];?></a></div></li>
		<li style="width:25px;" class="v u" >
		<a href="station.php?Line=<?php GLOBAL $info;echo $info[0]?>">站点</a></li>
		</ul>
	</div>

	<div class=list><ul><li style="width:320px;text-align:center;color:#5078C8" class="T e">
<?php GLOBAL $info;
$direct = bus_data($data,1);
if($info[1]==2)
	$type=1;
else	
	$type=2;
if($direct == -1)
	$show = "No Type Exist";
else 
{
	$show = $direct;
}
echo "<a href=\"show.php?Station=$data[5]&Line=$info[0]&type=$type\">";
echo $show;
echo  "</a>";
?>
	</li></ul></div>

	<?php GLOBAL $info;bus_data($info,2);?>
</div>
</body>
</html>
