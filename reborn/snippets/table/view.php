<?php
global $modx;

$currentPage = $modx->documentIdentifier;

$tablesRequest = $modx->db->query("
    SELECT `pagetitle`, `content` FROM `modx_site_content`
    WHERE `template` = '0' AND `type` = 'reference' AND `parent` = $currentPage
");

$tableInventoryView = file_get_contents(MODX_LAYOUT_PATH . "inventory/table/view.html");

$tables = "";

while ($table = $modx->db->getRow($tablesRequest)) {
    $tableItem = $tableInventoryView;

    foreach ($table as $layoutKey => $layoutValue) {
        $tableItem = str_replace('[[+'. strtoupper($layoutKey) .'+]]', $layoutValue, $tableItem);
    }

    $tables .= $tableItem;
}

return $tables . $modx->getChunk('nd_productCart') . $modx->getChunk('nd_modelView');

?>