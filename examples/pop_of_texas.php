<?php

require('config.php');
require('../Census.php');

$census = new Census($api_key);
//$texas = $census->getPlaceNamesByState("Texas");
$texas = $census->getCityDataByState("Texas", "ACS5", "B00001_001E");
uasort($texas, function($a, $b){
  if($a['Total'] == $b['Total']){
    return 0;
  }
  return ($a['Total'] > $b['Total'])? -1: 1;
});
?>
<html>
  <head>
    <?php include 'header.php'; ?>
  </head>
  <body>
    <div class="container-fluid">
      <h1>Cities in Texas, sorted by population</h1>
      <?php foreach($texas as $city): ?>
      <h3><?php echo $city['name'] . ' ' . $city['class'].', '.$city['state']; ?></h3>
      Population: <?php echo number_format($city['Total']);?>
      <hr />
      <?php endforeach ?>
    </div>
  </body>
</html>