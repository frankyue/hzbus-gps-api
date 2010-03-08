<?php
//$bus_data_file = "station.xml";
//$api = "http://127.0.0.1/hzbus/api.php";
require_once(dirname(__FILE__).'/config.php');
function LS_getdata($info)
{
	GLOBAL $bus_data_file;

	//0->line 1->type 2->sx 3->sy 4->id
	$data = array();
	$data[0] = $info[0];
	$data[1] = $info[1];
	$dom = new DOMDocument();
	$dom->load($bus_data_file);

	$buslist = $dom->getElementsByTagName('Bus');
	$linelist = $buslist->item(0)->getElementsByTagName('Line');
	foreach($linelist as $line)
	{
		if($line->getAttribute('name') == $info[0])
		{
			$data[4]=$line->getAttribute('id');
			$station_type =	$line->getElementsByTagName('Type');
			foreach($station_type->item(0)->getElementsByTagName('Station') as $sta)
			{
				if($sta->nodeValue == $info[5])
				{
					$data[2] = $sta->getAttribute('SX');
					$data[3] = $sta->getAttribute('SY');
					return $data;
				}
			}
		}
	}

}

function line_get()
{
	GLOBAL $bus_data_file;
	$dom =  new DOMDocument();
	$dom->load($bus_data_file);
	$bus = $dom->getElementsByTagName("Bus");
	$Line = $bus->item(0)->getElementsByTagName("Line");
	$num = 0;
	foreach($Line as $element)
	{
		$line[$num] = $element->getAttribute('name');
		//		$line[$num][0] = $element->getAttribute('name');
		//		$line[$num][1] = $element->getAttribute('id');
		$num++;
	}
	return $line;
}

function bus_data($info,$select)
{
	GLOBAL $bus_data_file;
	if($select == 1)
	{
		$dom = new DOMDocument();
		$dom->load($bus_data_file);
		$buslist = $dom->getElementsByTagName('Line');
		foreach($buslist as $bus)
		{
			if($bus->getAttribute('name') == $info[0])
			{
				foreach($bus->getElementsByTagName('Type') as $type)
				{	
					if($type->getAttribute('id')==$info[1])
						return $type->getAttribute('direct');
				}
				return -1;
			}

		}
	}
	else if($select == 2)
	{
		$data_info = Gps_get($info);
		$num = 0;
		if(is_array($data_info))
		{
			foreach($data_info as $data)
			{
				echo "<div class=\"list\"><ul><li style=\"width:320px\" class=\"e N T\">";
				echo "公交".$data[0]." 时间".$data[3]."分 距离".$data[2]."米 速度".round($data[1])."km/h"."<br>";
				echo "</li></ul></div>";
			}
		}
		else
		{
			echo "<div class=\"list\"><ul><li style=\"width:320px;text-align:center;\" class=\"e N T\">";
			echo "No Gps info!";
			echo "</li></ul></div>";
		}

	}

}


//$line[0]=bus
//$line[1]=id
function station_get($line)
{
	GLOBAL $bus_data_file;
	$dom =  new DOMDocument();
	$dom->validateOnParse = true;
	$dom->load($bus_data_file);
	//	$station_id = $dom->getElementById("busnode")->tagName;//$line[1]);
	$station_id = $dom->getElementsByTagName('Line');
	foreach($station_id as $id)
	{
		if($id->getAttribute('name') == $line)
		{
			$amount = 0;
			$station_type =	$id->getElementsByTagName('Type');
			foreach($station_type->item(0)->getElementsByTagName('Station') as $sta)
			{
				$station[$amount] = $sta->nodeValue;
				//				$station[$amount][0] = $sta->nodeValue;
				//				$station[$amount][1] = $sta->getAttribute('SX');
				//				$station[$amount][2] = $sta->getAttribute('SY');
				$amount++;
			}
			return $station;
		}

	}
}

//function Gps_get($bus,$lineid,$x,$y,$type)
function Gps_get($data)
{
	//return bus_data($bus,$type,$x,$y,$lineid);	
	return bus_data_get($data[0],$data[1],$data[2],$data[3],$data[4]);	
}

function bus_data_get($car,$type,$x,$y,$lineid)	
{
	GLOBAL $api;
	//	$web = $api."?&bus=10%u8DEF&type=".$type."&x=".$x."&y=".$y."&lineid=".$lineid;
	$web = $api."?&bus=".$car."&type=".$type."&x=".$x."&y=".$y."&lineId=".$lineid;
	$doc = new DOMDocument();
	@$doc->load($web);
	$num = 1;

	$bus = $doc->getElementsByTagName('bus');

	if( $bus->length <=0 )
	{
		return FALSE;
	}
	else
	{
		$bus_data = array();
		foreach($bus as $buses)
		{	
			$bus_data[$num-1][0] = $num;
			$bus_data[$num-1][1] = $buses->getElementsByTagName('speed')->item(0)->nodeValue;
			$bus_data[$num-1][2] = $buses->getElementsByTagName('distance')->item(0)->nodeValue;
			$bus_data[$num-1][3] = $buses->getElementsByTagName('time')->item(0)->nodeValue;
			$num++;
		}
		return $bus_data;
	}
}

?>
