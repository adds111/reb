<pre><?

$str1 = "ma ha 1 rara";
$str2 = "ra 1 ma ha.";

function parse($s1, $s2){
	$max = (strlen($s1) > strlen($s2) ? $s1 : $s2);
	$min = ($max == $s1 ? $s2 : $s1);
	$str = "";
	$iteration = 0;
	$miss = 0; // Количество непопавших итераций
	$is_continuous = false; // Повторяется ли элемент

	$result = array(
		"in_array" => array(),
		"identical_index" => array(),
		"unique" => array(),
		"difference" => array(),
		"together" => array()
	);

	for($i = 0; $i < strlen($max); $i++){
		
		if(strpos($min, $max[$i]) !== false){

			// Находится в обоих строках
			array_push($result["in_array"], $max[$i]);
			if(strpos($str, $max[$i]) === false){
				// Не повторяется
				array_push($result["unique"], $max[$i]);
				if(strpos($min, $max[$i]) == $i){
					// Индексы совпадают
					array_push($result["identical_index"], $max[$i]);
					$str .= $max[$i];
				}
			} 

			$is_continuous = true;
			$match = ""; // Совпадения
			$match_count = 1;
			while($is_continuous && $iteration + $match_count - 1 <= strlen($min)){
				echo($sub = substr($max, $iteration, $match_count)) . "<br>";
				$is_exists_match = strpos($min, $sub);
// var_dump($is_exists_match);
				if($is_exists_match !== false){
					$match = $sub;
					$match_count++;
				} else {
					$is_continuous = false;
					echo "Прерывается на $iteration итерации";
					// echo $match_count;
					$iteration += $match_count;
					$match_count = 1;
					break;
				}
				echo "($iteration)";
			}

			if(strlen($match) > 1){
				if(!in_array($match, $result["together"]))
				array_push($result["together"], $match);
			}
		} else {
			array_push($result["difference"], $max[$i]);
			$miss++;
			$is_continuous = false;
		}
	}

	return $result;
}

print_r(parse($str1,$str2));
?>