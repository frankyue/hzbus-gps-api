<?php
if(!@$_GET['Line'])
{
	echo "please select a line!";
	exit;
}
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
		<ul >
		<div style="text-align:center;font-size:15px;padding:10px"><a href="index.php"><?php echo $_GET['Line']?></a></div>	
		</ul>
	</div>

	<div class=list><ul><li style="width:320px;text-align:center;color:#5078C8" class="T e">
	 <form name="Line" action="show.php" method="GET">
		<select name="Station" size="0">
<?php
require_once(dirname(__FILE__).'/function/function.php');

  $station = station_get($_GET['Line']);
  $num = 0;
  foreach($station as $name)
  {
          echo "<option >";
          echo $name;
          echo "</option>";
          $num++;
  
  }
  ?>
  </select>
  <input type="submit" name='Line' value="<?php echo $_GET['Line'] ?>" />

  </form>

	</li></ul></div>
	<div class=list><ul><li style="width:320px;text-align:center;color:#5078C8" class="T e">
	数据来源www.hzbus.cn	
	</li></ul></div>

</div>
</body>
</html>
