<?
	include "rashod.php";
	include "convertor.php";
	


	if (isset($_POST['task'])) {
		if (!headers_sent()) {
		    header('Access-Control-Allow-Origin: *');
		} else {
		    // обработка ошибки или уведомление разработчикам
		}
		switch ($_POST['task']) {
			case 'site':
				
				/*

					task: "site",
					cv: cvArray,
					du: duArray,
					t: form.find('input[name="temperature_0"]').val(),
					p_in: form.find('input[name="p_in"]').val(),
					p_out: form.find('input[name="p_out"]').val(),
					environment: form.find('input[name="environment"]').val(),
					environmentWeight: form.find('input[name="environment-weight"]').val()
					
				*/

				$result = array();

				$cv = $_POST["cv"];
				$du = $_POST["du"];
				$t = $_POST["t"];
				$p_in = $_POST["p_in"];
				$p_out = $_POST["p_out"];
				$environment = $_POST["environment"];
				$environmentWeight = $_POST["environmentWeight"];
				$unit = $_POST["unit"];


				foreach ($cv as $key => $value) {
					array_push($result,  calc($p_in, $p_out, $t, $du[$key],  unit_translate($value, 'cv', 'kv'), $environmentWeight, 'consumption', $unit));
				}

				echo json_encode($result);


			break;
		}
	}

	
?>