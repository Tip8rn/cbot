<?php

function call_api($url=null){
  if(empty($url)) return false;

  $handle = curl_init();
  // Set the url
  curl_setopt($handle, CURLOPT_URL, $url);
  // Set the result output to be a string.
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

  $output = curl_exec($handle);
  $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
  curl_close($handle);

  $data = json_decode($output);

  return $data;
}


 ?>
