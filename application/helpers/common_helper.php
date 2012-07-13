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

function removeFiles($dir)
{
	$current_dir = opendir($dir);
	while($entryname = readdir($current_dir))
		{
			if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!=".."))
				{
				   deldir("${dir}/${entryname}");
				}
			elseif($entryname != "." and $entryname!="..")
				{
				   unlink("${dir}/${entryname}");
				}
		}
	closedir($current_dir);
	//rmdir(${dir});
}


// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
 // of course PHP has to have the rights to delete the directory
 // you specify and all files and folders inside the directory
 // ------------------------------------------------------------

 // to use this function to totally remove a directory, write:
// recursive_remove_directory('path/to/directory/to/delete');

 // to use this function to empty a directory, write:
// recursive_remove_directory('path/to/full_directory',TRUE);

function recursive_remove_directory($directory, $empty=FALSE)
{
    // if the path has a slash at the end we remove it here
    if(substr($directory,-1) == '/')
     {
         $directory = substr($directory,0,-1);
     }
 
    // if the path is not valid or is not a directory ...
    if(!file_exists($directory) || !is_dir($directory))
    {
         // ... we return false and exit the function
         return FALSE;
 
     // ... if the path is not readable
    }elseif(!is_readable($directory))
    {
        // ... we return false and exit the function
         return FALSE;
  
   // ... else if the path is readable
     }else{
  
         // we open the directory
        $handle = opendir($directory);
  
         // and scan through the items inside
         while (FALSE !== ($item = readdir($handle)))
        {
             // if the filepointer is not the current directory
           // or the parent directory
             if($item != '.' && $item != '..')
            {
                 // we build the new path to delete
                $path = $directory.'/'.$item;
 
                // if the new path is a directory
                 if(is_dir($path)) 
                {
                    // we call this function with the new path
                     recursive_remove_directory($path);
 
               // if the new path is a file
                }else{
                    // we remove the file
                    unlink($path);
                }
             }
        }
         // close the directory
         closedir($handle);
 
         // if the option to empty is not set to true
         if($empty == FALSE)
         {
            // try to delete the now empty directory
            if(!rmdir($directory))
            {
                 // return false if not possible
               return FALSE;
             }
        }
        // return success
         return TRUE;
     }
}

function createSchoolYear()
{
	$year	= array();
	$year	= array("-1"=>"Select Year","2010"=>"2010","2009"=>"2009","2008"=>"2008","2007"=>"2007","2006"=>"2006","2005"=>"2005","2004"=>"2004","2003"=>"2003","2002"=>"2002","2001"=>"2001","2000"=>"2000","1999"=>"1999","1998"=>"1998","1997"=>"1997","1996"=>"1996","1995"=>"1995","1994"=>"1994","1993"=>"1993","1992"=>"1992","1991"=>"1991","1990"=>"1990","1989"=>"1989","1988"=>"1988","1987"=>"1987","1986"=>"1986","1985"=>"1985","1984"=>"1984","1983"=>"1983","1982"=>"1982","1981"=>"1981","1980"=>"1980","1979"=>"1979","1978"=>"1978","1977"=>"1977","1976"=>"1976","1975"=>"1975","1974"=>"1974","1973"=>"1973","1972"=>"1972","1971"=>"1971","1970"=>"1970","1969"=>"1969","1968"=>"1968","1967"=>"1967","1966"=>"1966","1965"=>"1965","1964"=>"1964","1963"=>"1963","1962"=>"1962","1961"=>"1961","1960"=>"1960","1959"=>"1959","1958"=>"1958","1957"=>"1957","1956"=>"1956","1955"=>"1955","1954"=>"1954","1953"=>"1953","1952"=>"1952","1951"=>"1951","1950"=>"1950","1949"=>"1949","1948"=>"1948","1947"=>"1947","1946"=>"1946",
					"1945"=>"1945","1944"=>"1944","1943"=>"1943","1942"=>"1942","1941"=>"1941","1940"=>"1940","1939"=>"1939","1938"=>"1938","1937"=>"1937","1936"=>"1936","1935"=>"1935","1934"=>"1934","1933"=>"1933","1932"=>"1932","1931"=>"1931","1930"=>"1930","1929"=>"1929","1928"=>"1928","1927"=>"1927","1926"=>"1926","1925"=>"1925","1924"=>"1924","1923"=>"1923","1922"=>"1922","1921"=>"1921","1920"=>"1920","1919"=>"1919","1918"=>"1918","1917"=>"1917","1916"=>"1916","1915"=>"1915","1914"=>"1914","1913"=>"1913","1912"=>"1912","1911"=>"1911","1910"=>"1910");
	return $year;
}

function createWorkYear()
{
	$year	= array();
	$year	= array("-1"=>"Year","2010"=>"2010","2009"=>"2009","2008"=>"2008","2007"=>"2007","2006"=>"2006","2005"=>"2005","2004"=>"2004","2003"=>"2003","2002"=>"2002","2001"=>"2001","2000"=>"2000","1999"=>"1999","1998"=>"1998","1997"=>"1997","1996"=>"1996","1995"=>"1995","1994"=>"1994","1993"=>"1993","1992"=>"1992","1991"=>"1991","1990"=>"1990","1989"=>"1989","1988"=>"1988","1987"=>"1987","1986"=>"1986","1985"=>"1985","1984"=>"1984","1983"=>"1983","1982"=>"1982","1981"=>"1981","1980"=>"1980","1979"=>"1979","1978"=>"1978","1977"=>"1977","1976"=>"1976","1975"=>"1975","1974"=>"1974","1973"=>"1973","1972"=>"1972","1971"=>"1971","1970"=>"1970","1969"=>"1969","1968"=>"1968","1967"=>"1967","1966"=>"1966","1965"=>"1965","1964"=>"1964","1963"=>"1963","1962"=>"1962","1961"=>"1961","1960"=>"1960","1959"=>"1959","1958"=>"1958","1957"=>"1957","1956"=>"1956","1955"=>"1955");
	foreach ($year as $key=>$value)
		if($key<= date("Y"))
			$newYear[$key]	= $value;	
	return $newYear;
}

function createCountry()
{
 	$country	= array();
 	$country	= array('0'=>'Select Country:','US'=>'United States',"AF"=>"Afghanistan","AL"=>"Albania","DZ"=>"Algeria",
						"AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla",
						"AG"=>"Antigua and Barbuda","AR"=>"Argentina","AM"=>"Armenia","AW"=>"Aruba",
						"AU"=>"Australia","AT"=>"Austria","AZ"=>"Azerbaijan","BS"=>"Bahamas","BH"=>"Bahrain",
						"BD"=>"Bangladesh","BB"=>"Barbados","BY"=>"Belarus","BE"=>"Belgium","BZ"=>"Belize",
						"BJ"=>"Benin","BM"=>"Bermuda","BT"=>"Bhutan","BO"=>"Bolivia","BA"=>"Bosnia and Herzegovina",
						"BW"=>"Botswana","BV"=>"Bouvet Island","BR"=>"Brazil","IO"=>"British Indian Ocean Territory",
						"VG"=>"British Virgin Islands","BN"=>"Brunei","BG"=>"Bulgaria","BF"=>"Burkina Faso","BI"=>"Burundi",
						"KH"=>"Cambodia","CM"=>"Cameroon","CA"=>"Canada","CV"=>"Cape Verde","KY"=>"Cayman Islands",
						"CF"=>"Central African Republic","TD"=>"Chad","CL"=>"Chile","CN"=>"China","CX"=>"Christmas Island",
						"CC"=>"Cocos (Keeling) Islands","CO"=>"Colombia","KM"=>"Comoros","CG"=>"Congo","CD"=>"Congo - Democratic Republic of",
						"CK"=>"Cook Islands","CR"=>"Costa Rica","CI"=>"Cote d'Ivoire","HR"=>"Croatia","CU"=>"Cuba","CY"=>"Cyprus",
						"CZ"=>"Czech Republic","DK"=>"Denmark","DJ"=>"Djibouti","DM"=>"Dominica","DO"=>"Dominican Republic",
						"TP"=>"East Timor","EC"=>"Ecuador","EG"=>"Egypt","SV"=>"El Salvador","GQ"=>"Equitorial Guinea",
						"ER"=>"Eritrea","EE"=>"Estonia","ET"=>"Ethiopia","FK"=>"Falkland Islands (Islas Malvinas)",
						"FO"=>"Faroe Islands","FJ"=>"Fiji","FI"=>"Finland","FR"=>"France","GF"=>"French Guyana","PF"=>"French Polynesia",
						"TF"=>"French Southern and Antarctic Lands","GA"=>"Gabon","GM"=>"Gambia","GZ"=>"Gaza Strip","GE"=>"Georgia",
						"DE"=>"Germany","GH"=>"Ghana","GI"=>"Gibraltar","GR"=>"Greece","GL"=>"Greenland","GD"=>"Grenada",
						"GP"=>"Guadeloupe","GU"=>"Guam","GT"=>"Guatemala","GN"=>"Guinea","GW"=>"Guinea-Bissau","GY"=>"Guyana",
						"HT"=>"Haiti","HM"=>"Heard Island and McDonald Islands","VA"=>"Holy See (Vatican City)",
						"HN"=>"Honduras","HK"=>"Hong Kong","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia",
						"IR"=>"Iran","IQ"=>"Iraq","IE"=>"Ireland","IL"=>"Israel","IT"=>"Italy","JM"=>"Jamaica","JP"=>"Japan",
						"JO"=>"Jordan","KZ"=>"Kazakhstan","KE"=>"Kenya","KI"=>"Kiribati","KW"=>"Kuwait","KG"=>"Kyrgyzstan",
						"LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LS"=>"Lesotho","LR"=>"Liberia","LY"=>"Libya",
						"LI"=>"Liechtenstein","LT"=>"Lithuania","LU"=>"Luxembourg","MO"=>"Macau",
						"MK"=>"Macedonia - The Former Yugoslav Republic of","MG"=>"Madagascar","MW"=>"Malawi","MY"=>"Malaysia",
						"MV"=>"Maldives","ML"=>"Mali","MT"=>"Malta","MH"=>"Marshall Islands","MQ"=>"Martinique","MR"=>"Mauritania",
						"MU"=>"Mauritius","YT"=>"Mayotte","MX"=>"Mexico","FM"=>"Micronesia - Federated States of","MD"=>"Moldova",
						"MC"=>"Monaco","MN"=>"Mongolia","MS"=>"Montserrat","MA"=>"Morocco","MZ"=>"Mozambique","MM"=>"Myanmar",
						"NA"=>"Namibia","NR"=>"Naura","NP"=>"Nepal","NL"=>"Netherlands","AN"=>"Netherlands Antilles",
						"NC"=>"New Caledonia","NZ"=>"New Zealand","NI"=>"Nicaragua","NE"=>"Niger","NG"=>"Nigeria","NU"=>"Niue",
						"NF"=>"Norfolk Island","KP"=>"North Korea","MP"=>"Northern Mariana Islands","NO"=>"Norway","OM"=>"Oman",
						"PK"=>"Pakistan","PW"=>"Palau","PA"=>"Panama","PG"=>"Papua New Guinea","PY"=>"Paraguay","PE"=>"Peru",
						"PH"=>"Philippines","PN"=>"Pitcairn Islands","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico",
						"QA"=>"Qatar","RE"=>"Reunion","RO"=>"Romania","RU"=>"Russia","RW"=>"wanda","KN"=>"Saint Kitts and Nevis",
						"LC"=>"Saint Lucia","VC"=>"Saint Vincent and the Grenadines","WS"=>"Samoa","SM"=>"San Marino",
						"ST"=>"Sao Tome and Principe","SA"=>"Saudi Arabia","SN"=>"Senegal","CS"=>"Serbia and Montenegro",
						"SC"=>"Seychelles","SL"=>"Sierra Leone","SG"=>"Singapore","SK"=>"Slovakia","SI"=>"Slovenia",
						"SB"=>"Solomon Islands","SO"=>"Somalia","ZA"=>"South Africa","GS"=>"South Georgia and the South Sandwich Islands",
						"KR"=>"South Korea","ES"=>"Spain","LK"=>"Sri Lanka","SH"=>"St. Helena","PM"=>"St. Pierre and Miquelon","SD"=>"Sudan",
						"SR"=>"Suriname","SJ"=>"Svalbard","SZ"=>"Swaziland","SE"=>"Sweden","CH"=>"Switzerland","SY"=>"Syria","TW"=>"Taiwan",
						"TJ"=>"Tajikistan","TZ"=>"Tanzania","TH"=>"Thailand","TG"=>"Togo","TK"=>"Tokelau","TO"=>"Tonga","TT"=>"Trinidad and Tobago",
						"TN"=>"Tunisia","TR"=>"Turkey","TM"=>"Turkmenistan","TC"=>"Turks and Caicos Islands","TV"=>"Tuvalu","UG"=>"Uganda",
						"UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","VI"=>"United States Virgin Islands","UY"=>"Uruguay",
						"UZ"=>"Uzbekistan","VU"=>"Vanuatu","VE"=>"Venezuela","VN"=>"Vietnam","WF"=>"Wallis and Futuna","PS"=>"West Bank",
						"EH"=>"Western Sahara","YE"=>"Yemen","ZM"=>"Zambia","ZW"=>"Zimbabwe");
	return $country;
}

function createState($country)
{
	$states	= array();
	if($country=='US')
		{
			$states	= array("0"=>"select state",
							"AL" => "Alabama",
							"AK" => "Alaska",
							"AZ" => "Arizona",
							"AR" => "Arkansas",
							"CA" => "California",
							"CO" => "Colorado",
							"CT" => "Connecticut",
							"DE" => "Delaware",
							"DC" => "District Of Columbia",
							"FL" => "Florida",
							"GE" => "Georgia",
							"GUAM" => "Guam",
							"HI" => "Hawaii",
							"ID" => "Idaho",
							"IL" => "Illinois",
							"IN" => "Indiana",
							"IA" => "Iowa",
							"KS" => "Kansas",
							"KY" => "Kentucky",
							"LA" => "Louisiana",
							"ME" => "Maine",
							"MD" => "Maryland",
							"MA" => "Massachusetts",
							"MI" => "Michigan",
							"MN" => "Minnesota",
							"MS" => "Mississippi",
							"MO" => "Missouri",
							"MT" => "Montana",
							"NE" => "Nebraska",
							"NV" => "Nevada",
							"NH" => "New Hampshire",
							"NJ" => "New Jersey",
							"NM" => "New Mexico",
							"NY" => "New York",
							"NC" => "North Carolina",
							"ND" => "North Dakota",
							"OH" => "Ohio",
							"OK" => "Oklahoma",
							"OR" => "Oregon",
							"PA" => "Pennsylvania",
							"PR" => "Puerto Rico",
							"RI" => "Rhode Island",
							"SC" => "South Carolina",
							"SD" => "South Dakota",
							"TN" => "Tennessee",
							"TX" => "Texas",
							"UT" => "Utah",
							"VT" => "Vermont",
							"VA" => "Virginia",
							"WA" => "Washington",
							"WV" => "West Virginia",
							"WI" => "Wisconsin",
							"WY" => "Wyoming");
		}
	elseif($country=='CA')
		{
			$states	= array("0"=>"select province",
							"AB" => "Alberta",
							"BC" => "British Columbia",
							"MB" => "Manitoba",
							"NB" => "New Brunswick",
							"NL" => "Newfoundland",
							"NT" => "Northwest Territories",
							"NS" => "Nova Scotia",
							"NU" => "Nunavut",
							"ON" => "Ontario",
							"PE" => "Prince Edward Island",
							"QC" => "Quebec",
							"SK" => "Saskatchewan",
							"YT" => "Yukon Territory");
		}
	return $states;
}
