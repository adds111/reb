<?php
header('Content-Type: application/json');

define('MODX_API_MODE', true);

require_once $_SERVER['DOCUMENT_ROOT'] . "/index.php";

global $modx;

if (isset($_POST['item'])) {
    $itemCode = $modx->db->escape($_POST['item']);

    $resourceModelAttachments = array(
        'pagetitle' => $itemCode,
        'attachments' => array(),
    );

    $modelRequest = $modx->db->query(
        "SELECT `id`, `title`, `link` FROM `3d_models` WHERE `pagetitle` = '$itemCode'"
    );

    $count = $modx->db->getRecordCount($modelRequest);

    if ($count < 1) {
        die;
    }

    while ($model = $modx->db->getRow($modelRequest)) {
        $resourceModelAttachments['attachments'][] = array(
            'id' => base64_encode($model['id']), 'title' => $model['title'], 'link' => $model['link']
        );
    }

    echo base64_encode(json_encode($resourceModelAttachments));
}
