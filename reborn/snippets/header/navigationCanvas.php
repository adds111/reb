<?php
global $modx;

$request = $modx->db->query("
    SELECT `id`, `pagetitle`, `alias` 
    FROM `modx_site_content` 
    WHERE `parent` = 0 AND `published` = 1 AND `hidemenu` = 0
");

if ($modx->db->getRecordCount($request) > 0) {
    $items = "";
    $canvasBody = file_get_contents(MODX_LAYOUT_PATH . "header/canvas/body.html");
    $canvasItem = file_get_contents(MODX_LAYOUT_PATH . "header/canvas/item.html");

    while ($resource = $modx->db->getRow($request)) {
        if (isset($resource['id'])) {
            if ($resource['id'] == $modx->documentIdentifier) {
                $resource += array('CLASS' => ' active');
            } else {
                $resource += array('CLASS' => '');
            }
        }

        $item = $canvasItem;

        foreach ($resource as $layoutKey => $layoutValue) {
            $item = str_replace('[[+' . strtoupper($layoutKey) . '+]]', $layoutValue, $item);
        }

        $items .= $item;
    }

    return str_replace('[[+ITEMS+]]', $items, $canvasBody);
}

return;

?>