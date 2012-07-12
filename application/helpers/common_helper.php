<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//自定义的common_help函数
//定义了一些常用函数
/*
函数列表：
1.getRow
2.getToRec
3.IsRecordExist4Add
4.updateRecords
5.insertRecord
6.RandomName
7.selectBox
8.selectBoxQuery
9.createDay
10.createMon
11.createMonth
12.createYear
13.replaceSpVariables
14.sendMail
15.isValidEmail
16.isFileExists
17.

*/



function getRow($table, $fieldList, $condition='')
{
	if($condition !='')
		$condition = ' WHERE '.$condition;
		//echo "SELECT ".$fieldList." FROM ".$table.$condition;
	$res		= mysql_query("SELECT ".$fieldList." FROM ".$table.$condition);
	if($res)
		$rs		= mysql_fetch_array($res);
	return $rs;
}
	
function getTotRec($fieldName,$table, $condition='')
{
	if($condition !='')
		$condition = " WHERE ".$condition;
		
	$res	= mysql_query("SELECT COUNT(".$fieldName.") AS tot_rec FROM ".$table.$condition);
	if($res)
		{
			$rs		= mysql_fetch_object($res);
			if($rs->tot_rec>0)
				return $rs->tot_rec;
			else
				return 0; 
		}
	else
		return 0;
}

function IsRecordExist4Add($table,$fieldName,$condition)
{
	$flag = false;
	$query	= "SELECT COUNT($fieldName) AS total_rows FROM $table WHERE $condition";
	//$res	= mysql_query($query);
	if($res=mysql_query($query))
	{
		$rs		= mysql_fetch_object($res);
		if($rs->total_rows>0)
			$flag = true;
	}
	return $flag;
}

function updateRecords($table,$fieldList,$condition='')
{
	if($condition !='')
		$condition =" WHERE ".$condition;
	$query	= "UPDATE ".$table." SET ".$fieldList.$condition;
	//mysql_query($query);
	if(mysql_query($query))
		return true;
	else
		return false;
}

function insertRecord($table,$fieldList)
{
	$query	= "INSERT INTO ".$table." SET ".$fieldList;
	//mysql_query($query);
	if(mysql_query($query))
		return true;
	else
		return false;
}

function RandomName( $nameLength ) 
{
 $NameChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
 $Vouel = 'AEIOU';
 $Name = "";
 for ($index = 1; $index <= $nameLength; $index++) 
 { 
    if ($index % 3 == 0)
    {
      $randomNumber = rand(1,strlen($Vouel));
      $Name .= substr($Vouel,$randomNumber-1,1); 
    }else
      {
        $randomNumber = rand(1,strlen($NameChars));
        $Name .= substr($NameChars,$randomNumber-1,1);
      } 
 }
 return $Name;
}

function selectBox($name, $valueArray, $selected='', $extras='') 
{
	//echo $selected;
	$content = '<select name="' . $name . '" id="' . $name . '" ' . $extras . '>';
	//print_r($valueArray);
	if(is_array($valueArray)) 
		{
			foreach($valueArray as $key=>$value)
				{
					//echo $key."<br>";
					if($key == $selected) 
						{
							//echo $value;
							$isselected='selected';
						} 
					else 
						{
							$isselected='';
						}
					$content .= "\n\t";
					$content .= '<option value="' . $key . '" ' . $isselected . '>' . $value . '</option>';
				}
		}
    $content .= "\n";
    $content .= '</select>';
	
	return $content;
}

function selectBoxQuery($query,$selectBoxName,$selected='',$extra='',$default='')
{
	//create event category select box
	$res			= mysql_query($query);
	$selectArry		= array();
	if($default!='')
		$selectArry[0]	= $default;
	if($res)
	while($rs=mysql_fetch_array($res))
		$selectArry[$rs[0]]	= $rs[1];
	//print_r($selectArry);
	return selectBox($selectBoxName, $selectArry,$selected,$extra);
}//selectBoxQuery()

function createDay()
{
	$day	= array();
	$day	= array("-1"=>"Day","01"=>"01","02"=>"02","03"=>"03","04"=>"04","05"=>"05","06"=>"06","07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20","21"=>"21","22"=>"22","23"=>"23","24"=>"24","25"=>"25","26"=>"26","27"=>"27","28"=>"28","29"=>"29","30"=>"30","31"=>"31");
	return $day;
}
function createMon()
{
	$month	= array();
	$month	= array("-1"=>"Month","01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
	return $month;
}

function createMonth()
{
	$month	= array();
	$month	= array(""=>"","01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
	return $month;
}

function createYear()
{
	$year	= array();
	$year	= array("2007"=>"2007","2006"=>"2006","2005"=>"2005","2004"=>"2004","2003"=>"2003","2002"=>"2002","2001"=>"2001","2000"=>"2000","1999"=>"1999","1998"=>"1998","1997"=>"1997","1996"=>"1996","1995"=>"1995","1994"=>"1994","1993"=>"1993","1992"=>"1992","1991"=>"1991","1990"=>"1990","1989"=>"1989","1988"=>"1988","1987"=>"1987","1986"=>"1986","1985"=>"1985","1984"=>"1984","1983"=>"1983","1982"=>"1982","1981"=>"1981","1980"=>"1980","1979"=>"1979","1978"=>"1978","1977"=>"1977","1976"=>"1976","1975"=>"1975","1974"=>"1974","1973"=>"1973","1972"=>"1972","1971"=>"1971","1970"=>"1970","1969"=>"1969","1968"=>"1968","1967"=>"1967","1966"=>"1966","1965"=>"1965","1964"=>"1964","1963"=>"1963","1962"=>"1962","1961"=>"1961","1960"=>"1960","1959"=>"1959","1958"=>"1958","1957"=>"1957","1956"=>"1956","1955"=>"1955","1954"=>"1954","1953"=>"1953","1952"=>"1952","1951"=>"1951","1950"=>"1950","1949"=>"1949","1948"=>"1948","1947"=>"1947","1946"=>"1946","1945"=>"1945","1944"=>"1944","1943"=>"1943","1942"=>"1942","1941"=>"1941",
					"1940"=>"1940","1939"=>"1939","1938"=>"1938","1937"=>"1937","1936"=>"1936","1935"=>"1935","1934"=>"1934","1933"=>"1933","1932"=>"1932","1931"=>"1931","1930"=>"1930","1929"=>"1929","1928"=>"1928","1927"=>"1927","1926"=>"1926","1925"=>"1925","1924"=>"1924","1923"=>"1923","1922"=>"1922","1921"=>"1921","1920"=>"1920","1919"=>"1919","1918"=>"1918","1917"=>"1917","1916"=>"1916","1915"=>"1915","1914"=>"1914","1913"=>"1913","1912"=>"1912","1911"=>"1911","1910"=>"1910","-1"=>"Year");
	return $year;
}

function replaceSpVariables($spArray,$content)
{
	foreach ($spArray as $key => $value)
		$content	= str_replace($key,$value,$content);
	return $content;
}

function sendMail($content,$subject,$from,$to)
{
	
	/* message */ 
	$message = $content;
	
	/* To send HTML mail, you can set the Content-type header. */ 
	$headers  = "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
	
	/* additional headers */ 
	$headers .= "From: $from\r\n"; 
	/* and now mail it */ 
	if(mail($to, $subject, $message, $headers))
		return true;
	else
		return false;
}

function isValidEmail($strEmail)
{
	$pattern="/^([\w\.-]+)@([a-zA-Z0-9-]+)(\.[a-zA-Z\.]+)$/i";
	if(preg_match($pattern,$strEmail,$matches)){ 
		return true;
	}
	else
		return false;
	//$result=ereg("^[^@ ]+@[^@ ]+\.[^@ ]+$",$strEmail);
	//if($result)
		//return true;
	//else
		//return false;
}

function isFileExists($path)
{
	//To check if the directory exists before calling mkdir(), use files_exists, e.g.
	if (file_exists($path)) 
		return true;
	else
		return false;
}