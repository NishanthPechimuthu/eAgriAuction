<?php
$files = glob('../auction-app/includes/*.php');
foreach ($files as $file) {
  include_once($file);
}
?>