<?

require_once("bandejao.php");

$bandejao = new Bandejao();

$options = array(
	'days' => -1,
	'meals' => -1
);

echo "<h3>Cardápio do bandejão central para a refeição atual</h3>";
echo "<pre>";
print_r($bandejao->get('central', $options));
echo "</pre>";