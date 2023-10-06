<?php
global $modx;

$newsRequest_root = $modx->db->query(
    "SELECT `id`, `alias`, `type` FROM `modx_site_content` WHERE `pagetitle` = 'События'"
);

if ($modx->db->getRecordCount($newsRequest_root) > 0) {
    $newsResponse_root = $modx->db->getRow(
        $newsRequest_root
    );

    $newsRequest_tiding = $modx->db->query("
        SELECT `id`, `pagetitle`, `alias`, `longtitle`, `pub_date`, `introtext`
        FROM `modx_site_content` 
        WHERE `published` = 1 AND `parent` = {$newsResponse_root['id']} 
        ORDER BY `pub_date` DESC
    ");

    $items = "";
    $newsRowsItem = file_get_contents(MODX_LAYOUT_PATH . "/news/rows/item.html");

    while ($news = $modx->db->getRow($newsRequest_tiding)) {
        $paramsRequest = $modx->db->query(
            "SELECT `tmplvarid`, `value` FROM `modx_site_tmplvar_contentvalues` WHERE `contentid` = " . $news['id']
        );

        while ($param = $modx->db->getRow($paramsRequest)) {
            switch ($param['tmplvarid']) {
                case tv_resourceImage : {
                    $news += array('IMAGE' => $param['value']);
                    break;
                }
            }
        }

        $item = $newsRowsItem;

        foreach ($news as $layoutKey => $layoutValue) {
            if ($layoutKey == "pub_date") {
                $layoutValue = date("d.m.Y", $layoutValue);
            }

            $layoutKey = strtoupper($layoutKey);

            $item = str_replace('[[+' . $layoutKey . '+]]', strip_tags($layoutValue, ''), $item);
        }

        $items .= $item;
    }

    return str_replace('[[+ITEMS+]]', $items, file_get_contents(MODX_LAYOUT_PATH . "/news/rows/view.html"));
} 

return;

?>