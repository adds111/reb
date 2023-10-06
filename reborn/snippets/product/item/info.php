<?php
global $modx;

$currentResource = $modx->getPageInfo($modx->documentIdentifier, 1, 'id, pagetitle, alias');

$collectionsPath = MODX_COLLECTIONS_PATH . $currentResource['alias'];

$body = file_get_contents(MODX_LAYOUT_PATH . "product/info/body.html");
$item = file_get_contents(MODX_LAYOUT_PATH . "product/info/item.html");
$images = "";

if (is_dir($collectionsPath)) {
    $currentCollections = array_diff(scandir($collectionsPath), array('.', '..'));
}

if (count($currentCollections) > 0) {
    foreach (array_values($currentCollections) as $imageIndex => $image) {
        $currentItem = $item;

        if ($imageIndex > 2) {
            break;
        }

        if ($imageIndex === 0) {
            $currentItem = str_replace('[[+ACTIVE+]]', '_active', $currentItem);
        }

        $images .= str_replace('[[+IMAGE+]]',
            MODX_COLLECTIONS_URL . $currentResource['alias'] ."/". $image,
            $currentItem
        );
    }
}

return str_replace('[[+IMAGES+]]', $images, $body);

?>