<?php
/*
try{
  $sClient = new SoapClient('http://61.19.50.29/ws/services/QueryWebService.wsdl');
  
  $params = "Aqila";
  $response = $sClient->executeQuery(array('user' => 'test','password' => 'test123','queryCode' => 'FUND03','paramMap' => (object) array('CARD_ID'=>'1234567890123')));
  
  var_dump($response);
  
  
} catch(SoapFault $e){
  var_dump($e);
}

exit();*/
?><?php 

error_reporting(1);

$client = new SoapClient("http://61.19.50.29/ws/services/QueryWebService.wsdl");
//var_dump($client->__getFunctions()); 
//var_dump($client->__getTypes()); 
//$client->executeQuery();
//
//http://61.19.50.29/ws/wsjson?user=test&password=test123&queryCode=FUND03&CARD_ID=5521200018059
//http://61.19.50.29/ws/wsjson?user=test&password=test123&queryCode=FUND03&CARD_ID=3101701636968

echo "--";

/*$data = $client->QueryRequest(array('user' => 'test','password' => 'test123','queryCode' => 'FUND03','CARD_ID' => '5521200018059'));
print_r($data);*/
class Person {
    function Person($card_id) 
    {
        $this->CARD_ID = $card_id;
    }
}

$person = new Person('5521200018059');

//print_r($person);
//$person2 = array();

$hash = array();
$values = array();

//unset $values[$hash[$key]];
/*
$key = "CARD_ID";
$value = "5521200018059";
$hash[$key] = $value;
$values[$value] = $key;
print_r($values[$hash[$key]]);
*/

$params = array(
  
  
  'user' => 'test'
  ,'password' => 'test123'
  ,'queryCode' => 'FUND03'
  
  //,'paramMap' => (object) array('CARD_ID'=>array('5521200018059'))
  //,'paramMap' => array('CARD_ID'=>array('5521200018059'))
  //,'paramMap' => $person
  //,'paramMap' => $person2
  //,'paramMap'=>'5521200018059'
  ,'paramMap'=>array(array('CARD_ID'=>'5521200018059'))
 // ,'paramMap'=>$values[$hash[$key]]
  //,'CARD_ID'=>'5521200018059'
  
);



$response = $client->__soapCall("executeQuery", array($params));

/* Print webservice response */
var_dump($response);

echo "--";
?>