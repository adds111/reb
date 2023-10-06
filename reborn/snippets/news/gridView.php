<?php
global $modx;

$newsRequest_root = $modx->db->query(
    "SELECT `id`, `alias`, `type` FROM `modx_site_content` WHERE `pagetitle` = 'События'"
);

if ($modx->db->getRecordCount($newsRequest_root) > 0) {
    $newsResponse_root = $modx->db->getRow(
        $newsRequest_root
    );

    $newsRequest = $modx->db->query("
        SELECT `id`, `pagetitle`, `alias`, `longtitle`, `pub_date` 
        FROM `modx_site_content` 
        WHERE `published` = 1 AND 
              `pub_date` <> 0 AND `parent` = '{$newsResponse_root['id']}' 
        ORDER BY `PUB_DATE` DESC LIMIT 4"
    );

    $items = "";
    $newsGridItem = file_get_contents(MODX_LAYOUT_PATH . "news/grid/item.html");

    while ($tidings = $modx->db->getRow($newsRequest)) {
        $newsTVids = array(tv_resourceImage);

        $newsParamsRequest = $modx->db->query(
            "SELECT `tmplvarid`, `value` FROM `modx_site_tmplvar_contentvalues` WHERE `contentid` = {$tidings['id']} AND `tmplvarid` IN (". implode(',', $newsTVids) .")"
        );

        while ($param = $modx->db->getRow($newsParamsRequest)) {
            switch ($param['tmplvarid']) {
                case tv_resourceImage : {
                    $tidings += array('IMAGE' => $param['value']);
                    break;
                }
            }
        }

        $item = $newsGridItem;

        foreach ($tidings as $layoutKey => $layoutValue) {
            if ($layoutKey == 'pub_date') {
                $layoutValue = date("j F Y", $layoutValue);
            }

            $layoutKey = strtoupper($layoutKey);

            $item = str_replace('[[+' . $layoutKey . '+]]', $layoutValue, $item);
        }

        $items .= $item;
    }

    if ($newsResponse_root['type'] == "reference") {
        $newsResponse_root = $modx->getPageInfo($newsResponse_root['id'], 1, 'id, content as alias');
    }

    return str_replace(
        array('[[+ITEMS+]]', '[[+ROOT_ALIAS+]]'),
        array($items, $newsResponse_root['alias']),
        file_get_contents(MODX_LAYOUT_PATH . "/news/grid/view.html")
    );
}

return;

?>