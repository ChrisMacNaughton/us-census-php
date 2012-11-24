<?php

class Census {
  protected $key;
  protected $census = "http://api.census.gov/data/2010/";
  protected $sf = "sf1";
  protected $acs = "acs5";
  protected static $states = array();
  protected static $ACS_get=array(
    'median_household_income'=>'B19013_001E',
    'name'=>"NAME",
    "population"=>'B01003_001E',
    'agregate_travel_time_to_work'=>'B08135_001E',
    "less_than_10_minutes"=>'B08135_002E',
    "10-14"=>'B08135_003E',
    "15-19"=>'B08135_004E',
    "20-24"=>'B08135_005E',
    "25-29"=>'B08135_006E',
    "30-34"=>'B08135_007E',
    "35-44"=>'B08135_008E',
    "45-59"=>'B08135_009E',
    "60+"=>'B08135_010E',

  );
  protected $census_data = array();
  public function __construct($api_key, $cache=false){
    $this->key = $api_key;
    $this->cache = $cache;
    if($cache){
      if(file_exists("cache/states_cache.json")){
        $f = fopen("cache/states_cache.json", "r");
        self::$states = json_decode(fread($f, filesize("cache/states_cache.json")), true);
        fclose($f);
      } else {
        $this->getStateIds();
        $f = fopen("cache/states_cache.json", "w");
        fwrite($f, json_encode(self::$states));
        fclose($f);
      }
    } else {
      $this->getStateIds();
    }
    $this->names = array();
    foreach(self::$ACS_get as $key=>$id){
      $this->names[$id]=$key;
    }
  }
  public function getStates(){
    foreach(self::$states as $state=>$id){
      $data[] = $state;
    }
    return $data;
  }
  protected function getStateIds(){

    if(count(self::$states) == 0){
      $url = $this->census . $this->sf . "?key=" . $this->key . "&get=NAME&for=state:*";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $states = json_decode(curl_exec($ch), true);
      $ch = null;
      $data = array();
      unset($states[0]);
      foreach($states as $state){
        $data[$state[0]] = $state[1];
      }
      self::$states = $data;
    }
  }
  public function getCityDataByState($state){
    $state = $state;
    $id = self::$states[$state];
    if($this->cache){
      if(file_exists("cache/state_".$state."_cache.json")){
        $f = fopen("cache/state_".$state."_cache.json", "r");
        $this->census_data[$state] = json_decode(fread($f, filesize("cache/state_".$state."_cache.json")), true);
        fclose($f);
      }
    }
    if(!isset($this->census_data[$state])){
      $args1 = array_slice(self::$ACS_get, 0, count(self::$ACS_get)+1, true);

      $url = $this->census . $this->acs . "?key=" . $this->key . "&get=".implode(',',$args1)."&for=place:*&in=state:".$id;
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $cities = json_decode(curl_exec($ch), true);
      $ch = null;
      //print_r($cities);

      //$keys = array_merge($cities[0], $cities2[0], $cities3[0]);
      //print_r($keys);
      $keys = $cities[0];
      unset($cities[0]);//unset($cities2[0]);unset($cities3[0]);
      $data = array();
      $class="";
      foreach($cities as $key=>$city){
        $name = $city[1];
        if(strpos($name, 'city')){
          $class="City";

          $name = str_replace(" city", "", $name);
        }
        if(strpos($name, 'CDP')){
          $class="CDP";

          $name = str_replace(" CDP", "", $name);
        }
        if(strpos($name, 'town')){
          $class="Town";

          $name = str_replace(" town", "", $name);
        }
        if(strpos($name, 'village')){
          $class="Village";

          $name = str_replace(" village", "", $name);
        }

        foreach($city as $i=>$value){
          if($keys[$i] == 'state' OR $keys[$i] == 'place'){
            $k = ($keys[$i] == 'state')?'state':'place';
          } else
            $k = $this->names[$keys[$i]];
          //print($k." - ");
          $tmp[$k]=$value;
        }
        //exit();
        //print_r($tmp);
        $city = $tmp;
        //$class = substr($name, -1, strlen($name) - strrpos($name, ' '));
        //$name = substr($name, 0, strrpos($name, ''));
        //print_r($city);
        //exit();
        $data[$city['place']] = array(
//          'id'=>hash('sha256',$state.$name),
          'place_id'=>$city['place'],
          'state_id'=>$city['state'],
          'state'=>$state,
          'state_safe'=>str_replace(' ', '-', $state),
          'name'=>$name,
          'name_safe'=>str_replace(' ', '-', $name),
          'class'=>$class,
          'population'=>(int)$city['population'],
          'median_income'=>(int)$city['median_household_income'],
          'travel'=>array(
            '10-14'=>(int)($city['10-14'] != 'null')?$city['10-14']:0,
            '15-19'=>(int)($city['15-19'] != 'null')?$city['15-19']:0,
            '20-24'=>(int)($city['20-24'] != 'null')?$city['20-24']:0,
            '25-29'=>(int)($city['25-29'] != 'null')?$city['25-29']:0,
            '30-34'=>(int)($city['30-34'] != 'null')?$city['30-34']:0,
            '35-44'=>(int)($city['35-44'] != 'null')?$city['35-44']:0,
            '45-59'=>(int)($city['45-59'] != 'null')?$city['45-59']:0,
            '60+'=>(int)($city['60+'] != 'null')?$city['60+']:0
          )
        );
        //print_r($data[$city['place']]);
        $city = null;
      }
      $this->census_data[$state] = $data;
      $f = fopen("cache/state_".$state."_cache.json", "w");
      fwrite($f, json_encode($data));
      fclose($f);
    } else {
      $data = $this->census_data[$state];
    }
    return $data;
  }
}
/*
This piece lets users test run the library's functions without having to actually set it up in their own application

Coming Soon: Examples using the library!
*/

if(isset($argv) && $argv[1] == "demo"){
  include ('config.php');
  header("Content-Type: text/plain");

  $census = new Census($api_key, true);

  $state = (isset($argv[2]))?$argv[2]:"Texas";
  $cities = $census->getCityDataByState($state);
  print_r($cities);
}