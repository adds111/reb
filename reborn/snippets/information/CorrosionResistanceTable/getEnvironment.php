<?php

if (!defined('MODX_API_MODE')) {
	define('MODX_API_MODE', true);
}

include $_SERVER['DOCUMENT_ROOT'] . "/index.php";

global $modx;
	
$jsonBasePath = $_SERVER['DOCUMENT_ROOT'] . "/assets/reborn/snippets/information/JsonBase/"; 
	
	$xpos = 1;
	if (isset($_POST['xpos'])) $xpos = $_POST['xpos'];

	$max = 100;
	$xpos = ($xpos-1)*$max;

	$selected = isset($_POST['selected'])?$_POST['selected']:12;

	$selected = "SELECT DISTINCT id, environment, weight, state  FROM `sreda5` WHERE id='$selected'";
	$liquid = "SELECT DISTINCT id, environment, weight, state  FROM `sreda5` WHERE `state` = 1 ORDER BY environment ASC LIMIT $max OFFSET $xpos";
	$gas = "SELECT DISTINCT id, environment, weight, state  FROM `sreda5` WHERE `state` = 2 ORDER BY environment ASC LIMIT $max OFFSET $xpos";
	$n = "SELECT DISTINCT id, environment, weight, state  FROM `sreda5` WHERE `state` = 0 ORDER BY environment ASC LIMIT $max OFFSET $xpos";

	$row = array();

	$query_materials = $modx->db->query($selected); //$query_materials = mysqli_query($db, $selected);

	while ($r = $modx->db->getRow($query_materials)){
		$lcg = explode('(', $r['environment']);
		$r['ttl'] = $lcg[0];
		//$r['materials_val'] = h(file_get_contents('../json_base/'.$r['id'].'.json'));
		
		$row['link'][$r['id']] = json_decode(file_get_contents($jsonBasePath.$r['id'].'.json'), true);
	    $row['selected'][] = str_replace('"', '', $r);
	}

	$query_materials = $modx->db->query($liquid);

	while ($r = $modx->db->getRow($query_materials)){
		$lcg = explode('(', $r['environment']);
		$r['ttl'] = $lcg[0];
		$row['link'][$r['id']] = json_decode(file_get_contents($jsonBasePath.$r['id'].'.json'), true);
	    $row ['liquid'][] = str_replace('"', '', $r);
	}

	$query_materials = $modx->db->query($gas);

	while ($r = $modx->db->getRow($query_materials)){
		$lcg = explode('(', $r['environment']);
		$r['ttl'] = $lcg[0];
		$row['link'][$r['id']] = json_decode(file_get_contents($jsonBasePath.$r['id'].'.json'), true);
	    $row ['gas'][] = str_replace('"', '', $r);
	}


	$query_materials = $modx->db->query($n);

	while ($r = $modx->db->getRow($query_materials)){
		$lcg = explode('(', $r['environment']);
		$r['ttl'] = $lcg[0];
		$row['link'][$r['id']] = json_decode(file_get_contents($jsonBasePath.$r['id'].'.json'), true);
	    $row['n'][] = str_replace('"', '', $r);
	}


	if (!isset($_GET['t'])){
		header("Content-Type: application/json", true);
		echo json_encode($row);
	} else {
		echo '<pre>';
		print_r($row);
	}
?>
