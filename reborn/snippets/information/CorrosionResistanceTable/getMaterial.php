<?php

if (!defined('MODX_API_MODE')) {
	define('MODX_API_MODE', true);
}

include $_SERVER['DOCUMENT_ROOT'] . "/index.php";

global $modx;

$jsonBasePath = $_SERVER['DOCUMENT_ROOT'] . "/assets/reborn/snippets/information/JsonBase/";

$sreda = isset($_POST['sreda'])?$_POST['sreda']:$_GET['sreda'];

	if ($sreda=='') die('use sreda');
	$material_array = json_decode(file_get_contents($jsonBasePath.$sreda.'.json'), true);





	$row = array();

	$q = "SELECT * FROM prosto_materials4";
	$query_materials = $modx->db->query($q);


	while ($r = $modx->db->getRow($query_materials)){
		$_id =  $r['id'];
		$val = 0;
		if (isset($material_array[$_id])) {
			$r['value'] = $material_array[$_id];

		} else {
			$r['value'][0]['value'] = 0;
			$r['value'][0]['source_book'] = 'неизвестно';
		}


		$r['ttl'] = $r['russian'];
		$img = str_replace(array(' ', '#', '/', ',', '(', ')'), '_', $r['material']);
		
		$r['img'] = $img;

		$row[] = $r;
	}

	if (!isset($_GET['t'])){
		header("Content-Type: application/json", true);
		echo json_encode($row);
	} else {

		echo '<pre>';
		print_r($row);

		echo "</pre>";
	}

?>
