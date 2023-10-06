<?

	function unit_translate($from_count, $from_unit, $to_unit, $format = 2, $show_unit = false, $to_translate = false){
		$result;
		$translate = array(
			"л/мин" => "lm",
			"м3/ч" => "m3h",
			"л/ч" => "lh",
			"футы3/мин" => "ftm",
			"галлоны/мин" => "gm",
			"бар" => "br",
			"фунт на квадратный дюйм (PSIA)" => "psi",
			"кПа" => "kpa",
			"мПа" => "mpa",
			"цельсий" => "c",
			"фаренгейт" => "f",
			"кельвин" => "k"
		);
		$exception = array("c", "k", "f"); // Исключения, при которых не будет работать обычное умножение. Используется при переводе температуры.
		$current_index_from = ($to_translate ? $translate[$from_unit] : $from_unit); // Перевод входящей велечины в "тэг".
		$current_index_to = ($to_translate ? $translate[$to_unit] : $to_unit); // Перевод выходящей велечины в "тэг".
		$convert = array(
			// Производительность
			[
				"from" => "m3h",
				"to" => [
					"lm" => 16.66666666667, 
					"lh" => 1000,
					"ftm" => 0.588578,
					"gm" => 4.403,
					"m3h" => 1
				]
			],
			[
				"from" => "lm",
				"to" => [
					"m3h" => 0.06,
					"lh" => 60,
					"ftm" => 0.0353147,
					"gm" => 0.2642,
					"lm" => 1
				]
			],
			[
				"from" => "lh",
				"to" => [
					"m3h" => 0.001,
					"lm" => 0.0167,
					"ftm" => 0.0006,
					"gm" => 0.0044,
					"lh" => 1
				]
			],
			[
				"from" => "ftm",
				"to" => [
					"m3h" => 1.699,
					"lm" => 28,32,
					"lh" => 1699,
					"gm" => 7.48,
					"ftm" => 1
				]
			],
			[
				"from" => "gm",
				"to" => [
					"m3h" => 0.227,
					"lm" => 3.785,
					"lh" => 227.1,
					"ftm" => 0.1337,
					"gm" => 1
				]
			],
			// Давление
			[
				"from" => "psi",
				"to" => [
					"br" => 0.06895,
					"kpa" => 6.895,
					"mpa" => 0.006895,
					"psi" => 1
				]
			],
			[
				"from" => "br",
				"to" => [
					"psi" => 14.5,
					"kpa" => 100,
					"mpa" => 0.1,
					"br" => 1
				]
			],
			[
				"from" => "kpa",
				"to" => [
					"br" => 0.01,
					"psi" => 0.145,
					"mpa" => 0.001,
					"kpa" => 1
				]
			],
			[
				"from" => "mpa",
				"to" => [
					"br" => 10,
					"kpa" => 1000,
					"psi" => 145,
					"mpa" => 1
				]
			],
			// Температура
			[
				"from" => "c",
				"to" => [
					"f" => function($c){
						return (9/5 * $c) + 32;	
					},
					"k" => 273.15,
					"c" => 1
				]
			],
			[
				"from" => "f",
				"to" => [
					"c" => function($f){
						return ($f - 32) * 5/9;
					},
					"k" => function($f){
						return ($f + 459.87) / 1.8;
					},
					"f" => 1
				]
			],
			[
				"from" => "k",
				"to" => [
					"c" => -273.15,
					"f" => function($k){
						return $k * 1.8 - 459.87;
					},
					"k" => 1
				]
			]
		);


		for($i = 0; $i < count($convert); $i++){
			// При совпадении входящей величины и итерации массива.
			if($current_index_from == $convert[$i]["from"]){
				$to = $convert[$i]["to"][$current_index_to];

				// Проверка на температуру. Требуется для подходящей операции (умножение или сложение).
				if(!in_array($current_index_from, $exception)){
					$result = $from_count * $to;
				} else {			
					// Проверка на тип элемента. Если функция, то её выполнение, иначе обычное сложение.
					if(gettype($to) != "integer"){
						$result = $to($from_count);
					} else {
						$result = $from_count + $to;
					}
				}
			}
		} 


		$result = number_format($result, $format, '.', ' ');
		if ($show_unit)
			if (!$to_translate)
					$result .= ' '.array_search($to_unit, $translate);
				else 
					$result .= ' '.$to_unit;
				

		return $result;
	}
?>
