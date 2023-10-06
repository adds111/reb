<?php
global $modx;

$alias = array_search($modx->documentIdentifier, $modx->documentListing);

preg_match('#(\w+)\/(.+)#', $alias, $match);

if (isset($match[1]) and isset($match[2])) {
    $parentRequest = $modx->db->query($q = "
        SELECT `pagetitle`, `content` FROM `modx_site_content`
        WHERE `parent` IN (
            SELECT DISTINCT `parent` FROM `modx_site_content`
            WHERE `content` = '{$match[1]}' AND `type` = 'reference'
        )
    ");

    if ($modx->db->getRecordCount($parentRequest) > 0) {
        $view = file_get_contents(MODX_LAYOUT_PATH . "inventory/table/view.html");

        $tables = "";

        while ($table = $modx->db->getRow($parentRequest)) {
            $tables .= str_replace(
                array('[[+CONTENT+]]', '[[+PAGETITLE+]]'),
                array($table['content'], $table['pagetitle']),
                $view
            );
        }

        return $tables . $modx->getChunk('nd_productCart') . $modx->getChunk('nd_modelView');
    }
}

return;

?>
