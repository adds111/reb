<?php
global $modx;

$eventRequest_root = $modx->db->query(
    "SELECT `id` FROM `modx_site_content` WHERE `pagetitle` = 'Мероприятия'"
);

if ($modx->db->getRecordCount($eventRequest_root) > 0) {
    $event = $modx->db->getRow($eventRequest_root);

    $eventItem = file_get_contents(MODX_LAYOUT_PATH . "events/event-item.html");

    $eventsRequest =  $modx->db->query("
        SELECT `id`, `pagetitle`, `alias`
        FROM `modx_site_content` 
        WHERE `published` = 1 AND 
              `parent` = {$event['id']} 
        LIMIT 6
    ");

    $items = "";

    while ($event = $modx->db->getRow($eventsRequest)) {
        $eventTVids = array(tv_eventDateStart, tv_eventDateEnd);

        $paramsRequest = $modx->db->query("
            SELECT `tmplvarid`, `value` 
            FROM `modx_site_tmplvar_contentvalues` 
            WHERE `contentid` = {$event['id']} AND 
                  `tmplvarid` IN (". implode(',', $eventTVids) .")
        ");

        $item = $eventItem;

        while ($param = $modx->db->getRow($paramsRequest)) {
            switch ($param['tmplvarid']) {
                case tv_eventDateStart : {
                    $event += array('DATE_START' => date("j", strtotime($param['value'])));
                    break;
                }
                case tv_eventDateEnd : {
                    $event += array('DATE_END' => date("j F Y", strtotime($param['value'])));
                    break;
                }
            }
        }

        foreach ($event as $layoutKey => $layoutValue) {
            $item = str_replace('[[+' . strtoupper($layoutKey) . '+]]', $layoutValue, $item);
        }

        $items .= $item;
    }

    return str_replace('[[+ITEMS+]]', $items, file_get_contents(MODX_LAYOUT_PATH . "events/event-body.html"));
}

return;

?>