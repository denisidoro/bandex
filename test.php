<?php 

//header("Content-Type: application/json; charset=ISO-8859-1");

require_once('bandejao.php');

$band = new Bandejao();
$options = array('time' => 'name', 'day' => 'name');
$menu = $band->get(array(2), $options);

//echo "<h1>Menu</h1><pre>";
//print_r($menu);

echo "</pre><br><h1>Saldo</h1>";
echo $band->balance('7630980', 'cr3atesa');
