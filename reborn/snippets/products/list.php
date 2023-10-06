<?php
global $modx;

$productsPagetitle = "Продукция";

$request = $modx->db->query(
    "SELECT `id` FROM `modx_site_content` WHERE `parent` = 0 AND `pagetitle` = '$productsPagetitle'"
);

$resource = $modx->db->getRow($request);

if (isset($resource['id'])) {
    $catalogsRequest = $modx->db->query(
        "SELECT `id`, `pagetitle` AS PAGETITLE, `alias` AS ALIAS FROM `modx_site_content` WHERE `published` = 1 AND `hidemenu` = 0 AND `parent` = {$resource['id']}"
    );

    $items = "";
    $productsBody = file_get_contents(MODX_LAYOUT_PATH . "products/list/list-body.html");
    $productsItem = file_get_contents(MODX_LAYOUT_PATH . "products/list/list-item.html");

    while ($catalog = $modx->db->getRow($catalogsRequest)) {

        $catalogTVids = array(tv_resourcePreview, tv_resourceMiniDescription);

        $catalogParamsRequest = $modx->db->query(
            "SELECT `tmplvarid`, `value` FROM `modx_site_tmplvar_contentvalues` WHERE `contentid` = {$catalog['id']} AND `tmplvarid` IN (" . implode(", ", $catalogTVids) . ")"
        );

        while ($param = $modx->db->getRow($catalogParamsRequest)) {
            switch ($param['tmplvarid']) {
                case tv_resourcePreview : {
                    $catalog += array('RESOURCE_PREVIEW' => $param['value']);
                    break;
                }
                case tv_resourceMiniDescription : {
                    $catalog += array('RESOURCE_MINI_DESCRIPTION' => $param['value']);
                    break;
                }
            }
        }

        if (!isset($catalog['RESOURCE_PREVIEW'], $catalog['RESOURCE_MINI_DESCRIPTION'])) {
            continue;
        }

        $item = $productsItem;

        foreach ($catalog as $layoutKey => $layoutValue) {
            $item = str_replace('[[+' . $layoutKey . '+]]', $layoutValue, $item);
        }

        $items .= $item;
    }

    return str_replace('[[+ITEMS+]]', $items, $productsBody);
}

return null;

?>
