<?

	function cors() {
	    if (isset($_SERVER['HTTP_ORIGIN'])) {
	        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	        header('Access-Control-Allow-Credentials: true');
	        header('Access-Control-Max-Age: 86400');
	    }

	    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
	            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

	        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
	            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	    }
	}

	cors();
	header('Content-Type: application/json');

	switch ($_POST['task']) {
		case 'load':
			include('../../connect_to_server.php');

            $paramsOffset = is_numeric($_POST['params']['offset']) ? $_POST['params']['offset'] : '';
            $paramsLimit = is_numeric($_POST['params']['limit']) ? $_POST['params']['limit'] : '';
            $paramsSelect = is_numeric($_POST['params']['select']) ? $_POST['params']['select'] : '';
            $paramsText = mysql_real_escape_string($_POST['params']['text']);
			
			$temp = array();
			$where = "";
			if ($_POST['params']['tempoTonotnull']=="1"){
				$where = " WHERE tempoFrom!=0 ";
			}
			$qtext = "SELECT id,material,russian,tempoFrom,tempoTo FROM prosto_materials4 $where order by id DESC limit ".$paramsOffset.', '.$paramsLimit;
			$q = mysql_query($qtext);
			
			while ($row = mysql_fetch_assoc($q)) {
				if (!isset($temp[$row['id']]))
				$temp[$row['id']] = array(
					'id' => $row['id'],
					'russian' => $row['russian'].' ('.$row['material'].')',
					'tempoFrom' => $row['tempoFrom'],
					'tempoTo' => $row['tempoTo']
				);
			}

			if ($_POST['params']['offset']==0 && $_POST['params']['select']!=''){
				$temp[$_POST['params']['select']] = mysql_fetch_assoc(mysql_query("SELECT id,russian,tempoFrom,tempoTo FROM prosto_materials4 where id=".$paramsSelect));
			}

			echo json_encode($temp);
		break;
		case 'search': 
			include('../../connect_to_server.php');

            $paramsOffset = is_numeric($_POST['params']['offset']) ? $_POST['params']['offset'] : '';
            $paramsLimit = is_numeric($_POST['params']['limit']) ? $_POST['params']['limit'] : '';
            $paramsSelect = is_numeric($_POST['params']['select']) ? $_POST['params']['select'] : '';
            $paramsText = mysql_real_escape_string($_POST['params']['text']);
			
			$temp = array();
			$where = "";
			if ($_POST['params']['tempoTonotnull']=="1"){
				$where = "  tempoFrom!=0 AND";
			}

			$qtext = "SELECT id,material,russian,tempoFrom,tempoTo FROM prosto_materials4 WHERE $where russian like '%".$paramsText."%' OR another like '%".$paramsText."%' OR material like '%".$paramsText."%'  order by id DESC limit ".$paramsOffset.', '.$paramsLimit;
			$q = mysql_query($qtext);
			
			while ($row = mysql_fetch_assoc($q)) {
				//echo $row['id'].' - '.$row['russian'];
				$temp[$row['id']] = array(
					'id' => $row['id'],
					'russian' => $row['russian'].' ('.$row['material'].')',
					'tempoFrom' => $row['tempoFrom'],
					'tempoTo' => $row['tempoTo']
				);
			}


			if ($_POST['params']['offset']==0 && $_POST['params']['select']!=''){
				$temp[$_POST['params']['select']] = mysql_fetch_assoc(mysql_query("SELECT id,russian,tempoFrom,tempoTo FROM prosto_materials4 where id=".$paramsSelect));
			}

			echo json_encode($temp);
		break;
	}
?>