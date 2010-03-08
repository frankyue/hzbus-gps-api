<?php
//------struct ------
//	bus_data = array(array(FCarNumber,TX,TY,FSpeed,distance,Minutes),...)
//	shape = array(array(x,y),...)
//------struct ------
require_once(dirname(__FILE__).'/lineencoding.php');
$mapunit = 111194.651894933;


$bus = $_GET['bus'];
$type = $_GET['type'];
$x = $_GET['x'];
$y = $_GET['y'];
$lineid = $_GET['lineId'];

bus_statue($bus,$type,$x,$y,$lineid);


function station_get($xml,$string)
//xml want to use
//array return and the count in index 0
{
	//get xml name and return the data
	$dom = new DOMDocument();
	$dom->load("$xml");
	$node = $dom->getElementsByTagName("Row");
	$count = 1;
	foreach($node as $post)
	{
		$station = $node->item($count-1)->getAttribute("FName");
		$str[$count] = $station;
		echo "$station ";
		$count++;
	}
	$str[0] = $count-1;
	return $str;
}

function bus_get($xml,$string)
//xml want to use
//array return and the count in index 0
{
	//get xml name and return the data
	$dom = new DOMDocument();
	$dom->load("$xml");
	$node = $dom->getElementsByTagName("Row");
	$count = 1;
	foreach($node as $post)
	{
		$station = $node->item($count-1)->getAttribute("TX");
		$str[$count] = $station;
		echo "$station ";
		$count++;
	}
	$str[0] = $count-1;
	return $str;
}

//xml_ana return the gps of bus in array
function xml_ana($bus,$type,$SX,$SY,$lineID)
{//	$bus $type $i $x $y $lineID

	$url = "http://www.hzbus.cn/Page/linegps.axd?ln=".$bus."&tpe=".$type."&rnd=0&x=".$SX."&y=".$SY."&lineId=".$lineID;
	$strin = @file_get_contents($url);
	$dom = new DOMDocument();
	$dom->loadXML($strin);

	$node = $dom->getElementsByTagName("Row");
	$num = 0;
	foreach($node as $bus_dom)
		//	for($i=1;$i<4;$i++)
	{
		//	return data (FCarNumber TX TY FSpeed )

		$temp_carn = $node->item($num)->getAttribute("FCarNumber");
		$temp_x = $node->item($num)->getAttribute("TX");
		$temp_y = $node->item($num)->getAttribute("TY");
		$temp_speed = $node->item($num)->getAttribute("FSpeed");

		$data[$num][0] = trim($temp_carn);
		$data[$num][1] = $temp_x;
		$data[$num][2] = $temp_y;
		$data[$num][3] = $temp_speed;

		$num++;
	}
	if($num == 1)
		return NULL;
	return $data;	

}

//line's ID and bus's type
//return the line shape
function xml_shape($id,$type)
{
	$dom = new DOMDocument();
	//--------test------
	//	$dom->load("shape.xml");
	//--------test-------
	$dom->load("http://www.hzbus.cn/Page/linestop.axd?id=".$id."&type=".$type);
	$node = $dom->getElementsByTagName("Line");
	$seprate = $node->item(0)->getAttribute("shape");
	$sep_count = explode(',',$seprate);
	$count = 0;
	$num = 0;
	foreach($sep_count as $recl)
	{
		$check = strlen($sep_count[$num]);
		if( $check > 0)
		{
			$shape[$count] = explode(' ',$sep_count[$num]);
			$count++;
		}	
		$num++;
	}
	return $shape;
}

//calculator the distance from the bus positon to bus station
//return the distance
function distance($shape,$BX,$BY,$SX,$SY)
{
	GLOBAL $mapunit;
	$sp = get_index($BX,$BY,$shape);
	$ep = get_index($SX,$SY,$shape);

	$distance = 0;
	if($sp > $ep)
	{
		$temp = $sp;
		$sp = $ep;
		$ep = $temp;
	}

	//	echo "sp=".$sp."--";
	//	echo "ep=".$ea."--";

	for($m=$sp;$m<$ep;$m++)
	{
		$tmp = cal_twopoints($m,$shape);
		if($tmp != -1)
		{
			$distance = $distance + $tmp;
		}
	}

	if($distance)
		return round($distance * $mapunit);
	else
		return -1;
}

function Min_distance($distance)
{
	//JS	Math.round(Math.ceil(distance / 1000) * 3 - 0.5)　分钟
	if($distance)
	{
		return round(ceil("$distance"/1000)*3-0.5);
	}
	else
	{
		return -1;
	}
}

//return the distance of two points
function cal_twopoints($index_s,$shape)
{	
	//sqrt((x1-x2)^2+(y1-y2)^2)

	if($index_s != NULL && $shape != NULL)
	{
		$X=$shape[$index_s][0]-$shape[$index_s+1][0];
		$Y=$shape[$index_s][1]-$shape[$index_s+1][1];
		return sqrt(pow($X,2)+pow($Y,2));
	}
	else
	{
		return -1;
	}
}

//Get the index of the coordinate in shape
function get_index($x,$y,$shape)
{
	$count = 0;
	foreach($shape as $recl)
	{
		if($x == $shape[$count][0] && $y == $shape[$count][1])
			return $count;
		$count++;
	}
	return -1;
}

//print the statue car(distance,speed,time) by xml
//$x and $y means the station's coordinate
function bus_statue($line,$type,$SX,$SY,$lineID)
{
	$line_encoding = line_to_unicode(trim($line));
	$bus_data = xml_ana($line_encoding,$type,$SX,$SY,$lineID);
	$shape = xml_shape($lineID,$type);

	if($bus_data != NULL)
	{
		$num = 0;
		$c_sx = $bus_data[0][1];
		$c_sy = $bus_data[0][2];
		foreach($bus_data as $bus)
		{
			//calculator the distance from the bus positon to bus station
			//function distance($shape,$BX,$BY,$SX,$SY)
			//bus_data = array(array(FCarNumber,TX,TY,FSpeed,distance,Minutes),...)
			$temp_D = distance($shape,$bus_data[$num][1],$bus_data[$num][2],$c_sx,$c_sy);
			if($temp_D)
				$bus_data[$num][4] = $temp_D;
			else
				return -1;

			$temp_M = Min_distance($temp_D);
			if($temp_M)
				$bus_data[$num][5] = $temp_M;
			else
				return -1;

			$num++;
		}
	}

	xml_out($bus_data);
 
}	

function xml_out($bus_data)
{
	$doc = new DOMDocument();
	$doc->formatOutput = true;
	$doc->encoding = "UTF-8";
	$bus_amount = 0;

	if($bus_data == NULL)
		$bus_amount = 0;
	else
		$bus_amount = count($bus_data)-1;


	$buses = $doc->createElement("Buses");
	$doc->appendChild($buses);
	$amount = $doc->createAttribute("amount");
	$buses->appendChild($amount);
	$amount->appendChild($doc->createTextNode($bus_amount));

	$num = 0;
	
	if($bus_amount > 0)
	{
		for($num=1;$num<$bus_amount+1;$num++)
		{
			//bus_data = array(array(FCarNumber,TX,TY,FSpeed,distance,Minutes),...)
			//return the statue car(distance,speed,time)

			$bus = $doc->createElement("bus");
			$number = $doc->createElement("number");
			$number->appendChild($doc->createTextNode($bus_data[$num][0]));
			$bus->appendChild($number);

			$speed = $doc->createElement("speed");
			$speed->appendChild($doc->createTextNode($bus_data[$num][3]));
			$bus->appendChild($speed);

			$distance = $doc->createElement("distance");
			$distance->appendChild($doc->createTextNode($bus_data[$num][4]));
			$bus->appendChild($distance);

			$time = $doc->createElement("time");
			$time->appendChild($doc->createTextNode($bus_data[$num][5]));
			$bus->appendChild($time);

			$buses->appendChild($bus);
		}
	}
	echo $doc->saveXML();
}
?>
