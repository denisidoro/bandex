<?

require_once("bandexUSP.php");

$bandexUSP = new BandexUSP();

$options = array(
	'days' => -1,
	'meals' => -1,
	'meal_format' => 'numeric',
	'implode' => TRUE
);

echo "<h3>Cardápio do bandejão central da USP para a refeição atual</h3>";
echo "<pre>";
print_r($bandexUSP->get('central', $options));
echo "</pre>";
echo "<hr>";


require_once("bandexECP.php");

$bandexECP = new BandexECP();

echo "<h3>Cardápio do RU da ECP para a refeição atual</h3>";
echo "<pre>";
print_r($bandexECP->get('ru', $options));
echo "</pre>";