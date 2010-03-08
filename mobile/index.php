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
			<div style="text-align:center;font-size:15px;padding:10px">HZ Bus Gps</div>	
		</ul>
	</div>

	<div class=list><ul><li style="width:320px;text-align:center;color:#5078C8" class="T e">
	 <form name="Line" action="station.php" method="GET">
		<select name="Line" size="0">
<?php
  require_once(dirname(__FILE__).'/function/function.php');
  $line = line_get();
  $num = 0;
  foreach($line as $name)
  {
          echo "<option >";
          echo $name;
          echo "</option>";
          $num++;
  
  }
  ?>
  </select>
  <input type="submit" value="sumbit" />

  </form>

	</li></ul></div>
	<div class=list><ul><li style="width:320px;text-align:center;color:#5078C8" class="T e">
	数据来源www.hzbus.cn	
	</li></ul></div>

	<div class=list><ul><li style="width:320px;text-align:center;color:#5078C8" class="T e">
	提醒：公交网站GPS已恢复，可以继续使用
	</li></ul></div>

</div>
</body>
</html>
