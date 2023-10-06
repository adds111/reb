<?php
header('Content-Type: application/json');

if (isset($_POST) and count($_POST) > 0) {
    define('MODX_API_MODE', true);

    require_once $_SERVER['DOCUMENT_ROOT'] . "/index.php";

    global $modx;

    $cartForm = $_POST;
    $itemCode = $modx->db->escape($cartForm['_products-pagetitle']);

    if (!isset($_POST['_products-pagetitle'])) {
        echo "Product title is empty.";
    }

    $response = sendHttpRequest_Reborn("http://inventory.fluid-line.ru/get/product/$itemCode");

    if ($response['code'] == "200") {
        $response = json_decode($response['response'], true);

        $parent = $modx->db->getRow(
            $modx->db->query("
                SELECT `pagetitle` FROM `modx_site_content` WHERE `content` = '{$response['serial']}'
            ")
        );

        $response['parent'] = $parent['pagetitle']?: "";
        $response['new'] = $cartForm['_products-new']?: false;
        $response['count'] = $cartForm['_products-count'];
        $response['comment'] = $cartForm['_products-comment'];
        $response['price'] = $response['price']['value'];
        $response['currency'] = $response['price']['currency'];

        echo json_encode($response);
    }

} else {
    header('Location: /');
}

?>
