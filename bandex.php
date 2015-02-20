<?php

class Bandex {

	const TIME_FORMAT = 'd-m-Y';
	const IMPLODE_SUBSTR = '<br>';

	protected function sanitize($options) {

		$default = array(
			'time_format' => bandex::TIME_FORMAT,
			'meal_format' => 'name',
			'implode' => FALSE,
			'days' => range(0, 6),
			'meals' => range(0, 1)
		);

		foreach ($default as $key => $value)
			if (!isset($options[$key]))
				$options[$key] = $value;

		return ($options['days'] < 0 || $options['meals'] < 0) ?
			$this->guess_time($options) :
			$options;

	}

	protected function guess_time($options, $offset = 0) {

		$offset = 24*60*60;
		$time = time() + $offset;

		$hour = date('H', $time);
		$weekday = date('N', $time) - 1;

		$meal = ($hour >= 15 && $hour < 20) ? 1 : 0;

		if ($hour >= 20)
			$weekday = ($weekday + 1)%7;

		if ($meal == 1 && $weekday >= 5) {
			$meal = 0;
			$weekday = ($weekday + 1)%7;
		}

		if ($options['days'] < 0)
			$options['days'] = array($weekday);

		if ($options['meals'] < 0)
			$options['meals'] = array($meal);

		return $options;

	}

	protected function treatIDs($ids) {

		if (!is_array($ids)) {
			if (stripos($ids, ',') !== FALSE)
				$ids = explode(',', $ids);
			else
				$ids = array($ids);
		}

		return $ids;

	}

	protected function curl($url, $cookie = '', $fields = array()) {

		$curl = curl_init(); 

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FAILONERROR, TRUE); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);   

		if (!empty($cookie)) {
			echo $cookie;
			curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); 
			curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
		} 

		if (!empty($fields)) {
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		}

		$result = curl_exec($curl);
		curl_close($curl);

		return $this->convert_encoding($result);
		
	}   	

	protected function convert_encoding($content) { 

	    if (!mb_check_encoding($content, 'UTF-8') || !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) { 
	        $content = mb_convert_encoding($content, 'UTF-8'); 
	    } 

	    return $content; 

	} 

}