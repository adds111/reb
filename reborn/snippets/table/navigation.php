<?php
global $modx;

$currentResource = $modx->getPageInfo($modx->documentIdentifier, 1, 'id, alias');

$tableRequest = $modx->db->query("
    SELECT `pagetitle`, `content` 
    FROM `modx_site_content` 
    WHERE `parent` = {$currentResource['id']}
");

$items = "";

$body = file_get_contents(MODX_LAYOUT_PATH . "inventory/navigation/body.html");
$item = file_get_contents(MODX_LAYOUT_PATH . "inventory/navigation/item.html");

while ($table = $modx->db->getRow($tableRequest)) {
    $items .= str_replace(
        array('[[+ALIAS+]]', '[[+PAGETITLE+]]'),
        array($currentResource['alias'] ."#". $table['content'], $table['pagetitle']),
        $item
    );
}

return str_replace('[[+ITEMS+]]', $items, $body);

?>