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
        $body = file_get_contents(MODX_LAYOUT_PATH . "inventory/navigation/body.html");
        $item = file_get_contents(MODX_LAYOUT_PATH . "inventory/navigation/item.html");

        $items = "";

        while ($table = $modx->db->getRow($parentRequest)) {
            $items .= str_replace(
                array('[[+ALIAS+]]', '[[+PAGETITLE+]]'),
                array($alias ."#". $table['content'], $table['pagetitle']),
                $item
            );
        }

        return str_replace('[[+ITEMS+]]', $items, $body);
    }
}

return;

?>
