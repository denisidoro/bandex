<!DOCTYPE html>

<head>
<meta charset='utf-8'>
</head>

<?php

require_once('bandejao.php');

$band = new Bandejao();
$options = array('time' => 'name', 'day' => 'name');
print_r($band->get(array(0), $options));