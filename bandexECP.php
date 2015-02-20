<?php

require_once("bandex.php");
include_once("selector.php");

class BandexECP extends Bandex {

	public function BandexECP() {
		date_default_timezone_set('Europe/Paris');
		setlocale(LC_ALL, 'fr_FR');
	}
	
	const MENU_BASE_URL = 'www.crous-versailles.fr/restaurant/';
	public $options;
	
	public function get($ids, $options = array()) {

		// initialize output array
		$menu = array();

		// get options with default values filled and treat IDs
		$options = $this->sanitize($options);
		$this->options = $options;
		$ids = $this->treatIDs($ids);

		// get menu for each restaurant
		foreach ($ids as $id) {

			// get html code
			$html = $this->curl(bandexECP::MENU_BASE_URL . $id . '-ecp/');

			// break if html code is invalid
			if (strlen($html) < 40)
				continue;

			// select divs
			$dom = new SelectorDOM($html);
			$info = $dom->select('#menu-repas')[0];

			// parse, select what's desired and populate $menu
			$r = $this->parse($info);
			$menu[$id] = $this->select($r);

		}

		// if only one restaurant was selected, show its menu directly
		// if (count($menu) == 1) {
		// 	$menu = array_values($menu);
		// 	$menu = array_shift($menu);
		// }

		return $menu;

	}

	// extract info from the graph leaves
	private function parse($parent, $id = 0, $r = array(), $i = array()) {

		foreach ($parent['children'] as $child) {
			if (count($child['children']) == 0) {
				$t = utf8_decode($child['text']);
				if ($id == 2) {
					$i[0] = $this->format($t);
					$r[$i[0]] = array();
				}
				else if ($id == 4) {
					$i[1] = ($this->options['meal_format'] == 'numeric') ? count($r[$i[0]]) : $t;
					$r[$i[0]][$i[1]] = array();
				}
				else if ($id > 4) {
					if (!($t == 'Les plats' || strlen($t) < 2))
						$r[$i[0]][$i[1]][] = $t;
				}
			}
			$r = $this->parse($child, $id + 1, $r, $i);
		}
		
		return $r;

	}

	// strtotime and time format
	private function format($t) {
		$d = explode(" ", $t);
		$d = implode(" ", array_slice($d, 3, 3));
		$date = strtotime(strtr(strtolower($d), array('janvier'=>'jan','février'=>'feb','mars'=>'march','avril'=>'apr','mai'=>'may','juin'=>'jun','juillet'=>'jul','août'=>'aug','septembre'=>'sep','octobre'=>'oct','novembre'=>'nov','décembre'=>'dec'))); 
		return date($this->options['time_format'], $date);
	}

	// select desired values only
	private function select($menu) {

		$r = array();
		$dayId = 0;

		foreach ($menu as $day => $dayMenu) {

			$dayId++;
			$weekday = date('w', strtotime($day));
			if ($dayId >= 6 || !in_array($weekday, $this->options['days']))
				continue;

			$dMenu = array();
			$mealId = -1;

			foreach ($dayMenu as $meal => $mealMenu) {
				$mealId++;
				if ($this->options['meal_format'] == 'numeric')
					$meal = $mealId;
				if (!in_array($mealId - 1, $this->options['meals']))
					continue;
				$dMenu[$meal] = ($this->options['implode'] == TRUE) ?
					implode(bandex::IMPLODE_SUBSTR, $mealMenu) :
					$mealMenu;
			}

			$r[$day] = $dMenu;

		}

		return $r;

	}

}