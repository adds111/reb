<?php

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', true);
}

include $_SERVER['DOCUMENT_ROOT'] . "/index.php";

global $modx;

if (isset($_POST['sreda'])){
    header('Content-Type: application/json');
    $q = "SELECT wiki_description,wiki_url FROM sreda5 WHERE id = $_POST[sreda]";

    $res = $modx->db->getRow($modx->db->query($q));
    echo json_encode($res);
}

?>
