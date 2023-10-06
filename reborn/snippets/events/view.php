<?php
global $modx;

$event_request = $modx->db->query("
    SELECT `id` FROM `modx_site_content` 
    WHERE `pagetitle` = 'Мероприятия' 
");

if ($modx->db->getRecordCount($event_request) > 0) {
    $event_response = $modx->db->getRow($event_request);

    $child_request = $modx->db->query("
        SELECT `id`, `pagetitle`, `alias` 
        FROM `modx_site_content` WHERE `parent` = {$event_response['id']}
    ");

    if ($modx->db->getRecordCount($child_request) > 0) {
        $viewBody = file_get_contents(MODX_LAYOUT_PATH . "events/view/body.html");
        $viewItem = file_get_contents(MODX_LAYOUT_PATH . "events/view/item.html");

        $items = "";

        while ($child = $modx->db->getRow($child_request)) {
            $eventTVids = array(tv_eventDateStart, tv_eventDateEnd);

            $parameters_request = $modx->db->query("
                SELECT `tmplvarid`, `value` 
                FROM `modx_site_tmplvar_contentvalues` 
                WHERE `contentid` = {$child['id']} AND 
                      `tmplvarid` IN (". implode(',', $eventTVids) .")
            ");

            if ($modx->db->getRecordCount($parameters_request) > 0) {
                $item = $viewItem;

                while ($parameter = $modx->db->getRow($parameters_request)) {
                    switch ($parameter['tmplvarid']) {
                        case tv_eventDateStart : {
                            $child += array('DATE_START' => date("j", strtotime($parameter['value'])));
                            break;
                        }
                        case tv_eventDateEnd : {
                            $child += array('DATE_END' => date("j F Y", strtotime($parameter['value'])));
                            break;
                        }
                    }
                }

                foreach ($child as $layoutKey => $layoutValue) {
                    $item = str_replace('[[+' . strtoupper($layoutKey) . '+]]', $layoutValue, $item);
                }

                $items .= $item;
            }
        }

        return str_replace('[[+ITEMS+]]', $items, $viewBody);
    }
}

return;

?>