<?php

require('config.php');
require('../Census.php');

$census = new Census($api_key);

?>
<div style="width: 49%; float: left;">
<h1>American Community Survey</h1>
<pre>
<?php var_dump($census->getOptions("ACS5")); ?>
</pre>
</div>
<div style="width: 49%; float: left;">
<h1>Census Summary File</h1>
<pre>
<?php var_dump($census->getOptions("SF1")); ?>
</pre>
</div>