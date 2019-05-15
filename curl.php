<?php
require_once('function.php');


$url = 'http://test.local/api.php';
$data = call_api($url);


if(!empty($data)){
  echo 'val1 = '.$data->type;
  echo '<br />';
  echo 'val2 = '.$data->today;
}


 ?>
