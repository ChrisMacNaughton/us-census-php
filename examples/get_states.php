<?php

require_once('config.php');

include '../Census.php';

$census = new Census($api_key);

$states = $census->getStates();

?>
<html>
  <head>
    <?php include 'header.php'; ?>
  </head>
  <body>
    <h1 class="span6 offset3">US States</h1>
    <div id="states" class="well span6 offset3">
      <input class="search" placholder="Search States">
      <span class="sort btn" data-sort="state">Sort States</span>
      <ul class="list">
        <?php foreach($states as $id=>$state){ ?>
        <li>
          <strong class="state"><?php echo $state; ?></strong>
          <small class="id"><?php echo $id; ?></small>
        </li>
        <?php } ?>
      </ul>
    </div>

<?php include 'footer.php'; ?>
    <script type="text/javascript" src="http://listjs.com/src/list.js"></script>
    <script type="text/javascript">
      var options = {
        valueNames: ['state', 'id']
      };
      var stateList  = new List('states', options);
    </script>
  </body>
</html>