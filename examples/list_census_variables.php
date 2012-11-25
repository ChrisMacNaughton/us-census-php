<?php

require('config.php');
require('../Census.php');

$census = new Census($api_key);

?>
<html>
<head>
  <?php include 'header.php'; ?>
</head>
<body>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span6">
        <h1>American Community Survey</h1>
        <pre>
        <?php var_dump($census->getOptions("ACS5")); ?>
        </pre>
      </div>
      <div class="span6">
        <h1>Census Summary File</h1>
        <pre>
        <?php var_dump($census->getOptions("SF1")); ?>
        </pre>
      </div>
    </div>
  </div>
</body>
</html>