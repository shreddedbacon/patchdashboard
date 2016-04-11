<?php

$xml_string = file_get_contents(BASE_PATH.'plugins/errata/xml.xml');

$xml = simplexml_load_string($xml_string);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

echo $json;
print_r($array);

?>
