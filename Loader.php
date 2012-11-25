<?php

$xml = simplexml_load_file('http://www.census.gov/developers/data/2010acs5_variables.xml');
$counter = 0;

$concepts = $xml->concept;
$limit = 10;

for($i=0; $i<= $limit; $i++){
  $variables = $concepts[$i]->variable;
  foreach($variables as $key=>$variable){
    //print_r($variables[$n]);
    $entry = $variable;
    //print_r($entry);
    $entry = array(
      'key'=>(string)$entry['name'],
      'description'=>(string)$entry['concept'],
      'name'=>htmlentities((string)$entry)
    );
    //print_r($entry); exit();
    $entries["ACS5"][$entry['key']] = $entry;
  }
}
print("Finished loading ACS5, starting SF1\n");
$xml = simplexml_load_file('http://www.census.gov/developers/data/sf1.xml');

$counter = 0;

$concepts = $xml->concept;
$limit = 10;

for($i=0; $i<= $limit; $i++){
  $variables = $concepts[$i]->variable;
  foreach($variables as $key=>$variable){
    //print_r($variables[$n]);
    $entry = $variable;
    //print_r($entry);
    $entry = array(
      'key'=>(string)$entry['name'],
      'description'=>(string)$entry['concept'],
      'name'=>htmlentities((string)$entry)
    );
    //print_r($entry); exit();
    $entries["SF1"][$entry['key']] = $entry;
  }
}
$xml = null;
print("Finished SF1!\n");
$write = false;
if(file_exists("data/SF1.json")){
  $f = fopen("data/SF1.json", "r");
  if(json_encode($entries["SF1"]) != fread($f, filesize("data/SF1.json"))){
    fclose($f);
    unlink("data/SF1.json");
    $write = true;
  }
} else {
  $write = true;
}
if($write){
  $f = fopen("data/SF1.json", "w");
  fwrite($f, json_encode($entries["SF1"]));
  fclose($f);
}
$write = false;

if(file_exists("data/ACS5.json")){
  $f = fopen("data/ACS5.json", "r");
  if(json_encode($entries["ACS5"]) != fread($f, filesize("data/ACS5.json"))){
    fclose($f);
    unlink("data/ACS5.json");
    $write = true;
  }
} else {
  $write = true;
}
if($write){
  $f = fopen("data/ACS5.json", "w");
  fwrite($f, json_encode($entries["ACS5"]));
  fclose($f);
}
print("Done!");