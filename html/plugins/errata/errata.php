<?php

$xml_string = file_get_contents('./plugins/errata/xml.xml');

$xml = simplexml_load_string($xml_string);
//$xml = xmlstr_to_array($xml_string);
$json = json_encode($xml, JSON_FORCE_OBJECT);
$array = json_decode($json, TRUE);
$array_index = array_keys($array);

for ($i=0;$i<count($array_index);$i++) {
  echo $array_index[$i]."<br>";
  print_r($array[$array_index[$i]]['@attributes']['type']);
  echo "<br>";
  print_r($array[$array_index[$i]]['packages']);
  echo "<br>";
  if (count($array[$array_index[$i]]['cves']) > 0) {
        for ($b=0;$b<count($array[$array_index[$i]]['cves']);$b++) {
        echo $array[$array_index[$i]]['cves'][$b];
        }
  }
  echo "<hr>";
}


?>
