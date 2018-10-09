<?php
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
$wsdl = "sso/EmployeeEmployments.wsdl";
$options = array(
	"trace"         => 1, 
	"encoding"	=> "utf-8",
	'location' => 'https://wsg.sso.go.th/DBforService/services/EmployeeEmployments'	
);

$username = "deptest";
$password = "vLNfg0cS";
$ssoNum = "1100700135135";
//$ssoNum = "3101400567703";

$client = new SoapClient($wsdl,$options);
try {
	$result = $client->getServ38($username,$password,$ssoNum);
	print_xml("REQUEST",$client->__getLastRequest());
	print_xml("RESPONSE",$client->__getLastResponse());
}
catch(SoapFault  $e){
	echo $e->getMessage;
	print_xml("REQUEST",$client->__getLastRequest());
	print_xml("RESPONSE",$client->__getLastResponse());
}
//var_dump($result);
echo "<br><hr><br><b>RESULT:</b></br><hr>";
echo show($result,"lastIncremental");
echo show($result,"message");
echo show($result,"status");
echo "result: <br>";
echo " -> employments: <br>";
foreach($result->result->employments as $employment){
	echo "&nbsp;&nbsp; =>".show($employment,"accBran");
	echo "&nbsp;&nbsp; =>".show($employment,"accNo");
	echo "&nbsp;&nbsp; =>".show($employment,"companyName");
	echo "&nbsp;&nbsp; =>".show($employment,"empResignDate");
	echo "&nbsp;&nbsp; =>".show($employment,"employStatus");
	echo "&nbsp;&nbsp; =>".show($employment,"employStatusDesc");
	echo "&nbsp;&nbsp; =>".show($employment,"expStartDate");
	echo "&nbsp;&nbsp; ---------------- <br>";

}
echo " -> person: <br>";
echo "&nbsp;&nbsp; =>".show($result->result->person,"activeStatus");
echo "&nbsp;&nbsp; =>".show($result->result->person,"activeStatusDesc");
echo "&nbsp;&nbsp; =>".show($result->result->person,"empBirthDate");
echo "&nbsp;&nbsp; =>".show($result->result->person,"expirationDate");
echo "&nbsp;&nbsp; =>".show($result->result->person,"firstName");
echo "&nbsp;&nbsp; =>".show($result->result->person,"gender");
echo "&nbsp;&nbsp; =>".show($result->result->person,"genderDesc");
echo "&nbsp;&nbsp; =>".show($result->result->person,"idDesc");
echo "&nbsp;&nbsp; =>".show($result->result->person,"idType");
echo "&nbsp;&nbsp; =>".show($result->result->person,"lastName");
echo "&nbsp;&nbsp; =>".show($result->result->person,"ssoNum");
echo "&nbsp;&nbsp; =>".show($result->result->person,"titleCode");
echo "&nbsp;&nbsp; =>".show($result->result->person,"titleCodeDesc");


function print_xml($title,$xml){
	echo "<b>$title</b></br><hr>";
	echo xml_highlight($xml);
	echo "<br><br>";

}

function show($obj,$k) {
	return "$k: ".$obj->$k."<br>";

}

function xml_highlight($in)
{        
    	$dom = new DOMDocument();	
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;

	$dom->loadXML($in);
	$s = $dom->saveXML();

    $s = htmlspecialchars($s);
    $s = preg_replace("#&lt;([/]*?)(.*)([\s]*?)&gt;#sU",
        "<font color=\"#0000FF\">&lt;\\1\\2\\3&gt;</font>",$s);
    $s = preg_replace("#&lt;([\?])(.*)([\?])&gt;#sU",
        "<font color=\"#800000\">&lt;\\1\\2\\3&gt;</font>",$s);
    $s = preg_replace("#&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)#iU",
        "&lt;<font color=\"#808000\">\\1\\2</font>\\3",$s);
    $s = preg_replace("#&lt;([/])([^\s]*?)([\s\]]*?)&gt;#iU",
        "&lt;\\1<font color=\"#808000\">\\2</font>\\3&gt;",$s);
    $s = preg_replace("#([^\s]*?)\=(&quot;|')(.*)(&quot;|')#isU",
        "<font color=\"#800080\">\\1</font>=<font color=\"#FF00FF\">\\2\\3\\4</font>",$s);
    $s = preg_replace("#&lt;(.*)(\[)(.*)(\])&gt;#isU",
        "&lt;\\1<font color=\"#800080\">\\2\\3\\4</font>&gt;",$s);
    return nl2br($s);
}

?>
