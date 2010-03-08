<?php

function utf8tounicode($string,$count,$type,$sep)
{
	$word = array();
	$unicode = array();
	$str = array();
	if($type == 0)
	{
		$str[0] = $string[$count];
		$str[1] = $string[$count+1];
		$str[2] = $string[$count+2];
		$unicode[0] = $sep;
		$unicode[1] = getUnicodeFromOneUTF8($str);
	}
	else
	{
		$num = 0;
		$amount = 0;
	//	echo count($string);
		for($i=$count;$i<count($string)-2;$i+=3,$amount+=2)
		{
			$word = array();
			$word[0] = $string[$i];
			$word[1] = $string[$i+1];
			$word[2] = $string[$i+2];
			$unicode[$amount] = $sep;
			$unicode[$amount+1] = getUnicodeFromOneUTF8($word);
		}
 	}
	return implode("",$unicode);
 
}

//get the function from the web without the author name
function getUnicodeFromOneUTF8($word) 
{   
	//获取其字符的内部数组表示，所以本文件应用utf-8编码！   
	if (is_array( $word))   
		$arr = $word;   
	else  
		$arr = str_split($word);   
	//此时，$arr应类似array(228, 189, 160)   
	//定义一个空字符串存储   
	$bin_str = '';   
	//转成数字再转成二进制字符串，最后联合起来。   
	foreach ($arr as $value)   
		$bin_str .= decbin(ord($value));   
	//此时，$bin_str应类似111001001011110110100000,如果是汉字"你"   
	//正则截取   
	$bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/','$1$2$3', $bin_str);   
	//此时， $bin_str应类似0100111101100000,如果是汉字"你"   
	return dechex(bindec($bin_str)); //如想返回十六进制4f60，用这句   
}  

function line_to_unicode($line)
{
	$string = str_split($line);
	$amount = 0;
	$unicode = '';
	$final = array();
	for($count=0;$count<count($string);$count++)
	{
		if($string[$count] == 'K')
			continue;

		if(bin2hex($string[$count]) == 'e6')//E6 is special for bus line B branch
		{
			$unicode = utf8tounicode($string,$count,0,"%u");
			$final[$amount] = $unicode;
			$amount++;
			$count+=2;
			continue;
		}

		if(bin2hex($string[$count]) >= '80')//if $string[$count] > 80 mean it is a element of UTF-8;for all character
		{
			$unicode = utf8tounicode($string,$count,1,"%u");
			$final[$amount] = $unicode;
			$amount++;
			break;
		}
 
		$final[$amount] = $string[$count];
		$amount++;

	}
	
	$str = implode("",$final);
	if(($sta = strpbrk($str,'/')))
	{
		$sta = explode('/',$sta);
		return $sta[1];
	}
	else
 		return $str;
}

?>
