<?php

if (!defined('MODX_API_MODE')) {
	define('MODX_API_MODE', true);	
}

include $_SERVER['DOCUMENT_ROOT'] . "/index.php";

global $modx;

$jsonBasePath = $_SERVER['DOCUMENT_ROOT'] . "/assets/reborn/snippets/information/JsonBase/";

if (!isset($_POST['text'])) die('nt');
	$text =  str_replace(array("'", '"'), '`', $_POST['text']);
	$q = "SELECT id,environment FROM sreda5 WHERE environment like '%$text%' limit 30";

	$result = array('info' => '1', 'empty' => 1);
	$query_sreda = $modx->db->query($q);

	$mat_id = isset($_POST['mat_id'])?$_POST['mat_id']:-1;
	
	

	if ($query_sreda){
		while ($r = $modx->db->getRow($query_sreda)){
			$lcg = explode('(', $r['environment']);
			$r['ttl'] = $lcg[0];

			if ($mat_id!=-1){
				$materials = json_decode(
					file_get_contents($jsonBasePath.$r['id'].'.json'), true
				);
				$r['value'] = isset($materials[$mat_id][0]['value']) ? $materials[$mat_id][0]['value'] : null;
				if (isset($materials[$mat_id]) && count($materials[$mat_id])>1){
					$r['except'] = '+';
				} else {
					$r['except'] = '-';
				}
			}
			
			$result['search'][] = $r;
			$result['empty'] = 0;
		}
	}



	if (!isset($_GET['t'])){
		header("Content-Type: application/json", true);
		echo json_encode($result);
	} else {

		echo '<pre>';
		print_r($result);

		echo "</pre>";
	}

?>
