<?php 

header("Content-Type: application/json; charset=ISO-8859-1");

require_once('bandejao.php');

$band = new Bandejao();
$options = array('time' => 'name', 'day' => 'name');
$menu = $band->get(array(2), $options);

//print_r($menu);
echo json_encode($menu);
