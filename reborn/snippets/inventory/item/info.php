<?php
global $modx;

$alias = array_search($modx->documentIdentifier, $modx->documentListing);

$filepath = MODX_INVENTORY_PATH . $alias .".json";

if (file_exists(MODX_INVENTORY_PATH . $alias .".json")) {
    $item_encoded = file_get_contents($filepath);
    $item = json_decode($item_encoded, true);

    $item_parameters = "";

    $body = file_get_contents(MODX_LAYOUT_PATH . "/inventory/item/info/body.html");
    $descriptionItem = file_get_contents(MODX_LAYOUT_PATH . "/inventory/item/info/description-item.html");

    foreach ($item['parameters'] as $parameter) {
        $item_parameters .= str_replace(
            array('[[+NAME+]]', '[[+VALUE+]]'), array($parameter['name'], $parameter['value']), $descriptionItem
        );
    }

    $image = $item['attachments']['image'];
    $imageInfo = pathinfo($image);

    $fullpath = $imageInfo['dirname'] .'/'. $imageInfo['filename'] ."-full.". $imageInfo['extension'];
    if (file_exists(MODX_BASE_PATH . $fullpath)) {
        $image = $fullpath;
    }

    return str_replace(
        array('[[+CODE+]]', '[[+PARAMETERS+]]', '[[+BASE64_ITEM+]]', '[[+IMAGE+]]'),
        array($item['code'], $item_parameters, base64_encode($item_encoded), $image),
        $body
    );
}

return;

?>
?>