<?php

class Bandejao {

	var $days = array(
		array('segunda', 'segunda', 'segunda-feira'),
		array('terca', 'ter', 'terça-feira'),
		array('quarta', 'quarta', 'quarta-feira'),
		array('quinta', 'quinta', 'quinta-feira'),
		array('sexta', 'sexta', 'sexta'),
		array('sabado', 'bado', 'sábado'),
		array('domingo', 'domingo', 'domingo')
	);

	var $meals = array(
		array('almoco', 'almo', 'almoço'),
		array('jantar', 'j', 'jantar')
	);

	var $restaurants = array(
		array('central', 'central', 'cardapio.html'),
		array('fisica', 'física', 'cardapiofisica.html'),
		array('prefeitura', 'prefeitura', 'cardcocesp.html'),
		array('quimica', 'química', 'cardapioquimica.html')
	);

	const MENU_BASE_URL = 'http://www.usp.br/coseas/';
	const BALANCE_AUTH_URL = 'http://uspdigital.usp.br/rucard/autenticar';
	const BALANCE_EXTRACT_URL = 'http://uspdigital.usp.br/rucard/extratoListar?codmnu=12';

	public function get($ids, $options = array()) {

		$menu = array();

		if (is_numeric($ids))
			$ids = array($ids);

		foreach ($ids as $id)
			$menu[$this->restaurants[$id][0]] = $this->prettify(
				$this->parse($id),
				$options
			);
		
		return $menu;

	}

	private function parse($id) {

		$text = $this->curl(Bandejao::MENU_BASE_URL . $this->restaurants[$id][2]);

		preg_match_all(
			'/<td[^>]*>(.*?)<\/td>/mis', 
			$text,
			$td
		);

		$menu = array();

		foreach ($td[1] as $t) {

			preg_match_all(
				'/<font[^>]*>(.*?)<\/font>/mis', 
				strip_tags(
					str_replace(array('span', 'div'), 'font', $t),
					'<font><br><div>'
				),
				$m
			);

			foreach ($this->days as $i => $d)
				if (isset($m[1][0]) && stripos($m[1][0], $d[1]) !== FALSE)
					$menu[$i][] = $m[1];

		}

		return $menu;

	}

	private function prettify($menu, $options) {

		$pretty = array();

		foreach ($menu as $dayId => $day) {

			foreach ($day as $timeId => $time) {

				$elems = array();

				foreach ($time as $elId => $elem)
					if ($elId > 0)
						$elems = array_merge(
							$elems, 
							explode('<br>', nl2br($elem, FALSE))
						);

				foreach ($elems as $elId => $elem) {

					if (stripos($elem, '<font') !== FALSE)
						$elem = preg_replace("/<font.*?>/i", "$1", $elem);

					if (strlen($elem) > 3)
						$elems[$elId] = trim($elem);

				}

				$dId = (isset($options['day']) && $options['day'] == 'name') ?
					$this->days[$dayId][0] :
					$dayId;

				$tId = (isset($options['time']) && $options['time'] == 'name') ?
					$this->meals[$timeId][0] :
					$timeId;

				$pretty[$dId][$tId] = array_filter($elems);

			}

		}

		foreach ($pretty as $day)
			foreach ($day as $time)
				array_filter($time);

		return $pretty;

	}

	public function balance($nusp, $pass) {

		$filename = sha1(date('u') . $nusp . $pass) . '.txt';

		$text = $this->curl(Bandejao::BALANCE_AUTH_URL . '?' .
			http_build_query(array('codpes' => $nusp, 'senusu' => $pass)),
			$filename
		);

		if (stripos($text, 'extrato') === FALSE)
			return FALSE;

		$text = $this->curl(Bandejao::BALANCE_EXTRACT_URL, $filename);

		preg_match(
			'/atual[^<]*<[^>]*>[\s]*<[^>]*>([\d]*)/mis',
			$text,
			$balance
		);

		return $balance[1];

	}

	private function curl($url, $cookie = '', $fields = array()) {

		$curl = curl_init(); 

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FAILONERROR, TRUE); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);   

		if (!empty($cookie)) {
			curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); 
			curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
		} 

		if (!empty($fields)) {
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		}

		$result = curl_exec($curl);
		curl_close($curl);

		return mb_convert_encoding($result, 'ISO-8859-1', 'UTF-8');
		
	}   	

}