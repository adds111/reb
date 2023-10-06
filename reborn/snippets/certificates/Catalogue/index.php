<?php

global $modx;

$certificateRequest_root = $modx->db->query("
    SELECT `id` FROM `modx_site_content` WHERE `pagetitle` = 'Сертификаты'
");

$certificatesAloneHTML = $certificatesHTML = "";

function getAdaptiveRows($count, &$rows) {
    $style = "";
    $cols = (int)($count / 10);

    if ($cols > 0) {
        if ($rows['1'] < $rows['2']) {
            $currentRow = $rows['1'];
            $toRow = $currentRow + $cols + 1;
            $rows['1'] = $toRow;

            return "style='grid-row: {$currentRow} / $toRow ;'";

        } else {
            $currentRow = $rows['2'];
            $toRow = $currentRow + $cols + 1;
            $rows['2'] = $toRow;

            return "style='grid-row: {$currentRow} / $toRow ;'";
        }
    }

    return $style;
}

function fillAlone($category) {
    global $modx;

    $aloneRequest = $modx->db->query("
        SELECT `pagetitle`, `content` 
        FROM `modx_site_content` 
        WHERE `type` = 'reference' AND
              `parent` = {$category['id']}
    ");

    if ($modx->db->getRecordCount($aloneRequest) < 1) {
        return "";

    } else {
        $itemAlone = file_get_contents(MODX_LAYOUT_PATH . "certificates/view/item-alone.html");
        $link = file_get_contents(MODX_LAYOUT_PATH . "certificates/view/link.html");

        $aloneItems = '';

        while ($alone = $modx->db->getRow($aloneRequest)) {
            $aloneItems .= str_replace(
                array('[[+PAGETITLE+]]', '[[+LINK+]]'),
                array($alone['pagetitle'], $alone['content']),
                $link
            );
        }

        return str_replace(
            array('[[+PAGETITLE+]]', '[[+ITEMS+]]'),
            array($category['pagetitle'], $aloneItems),
            $itemAlone
        );
    }
}

if ($modx->db->getRecordCount($certificateRequest_root) > 0) {
    $certificateResponse_root = $modx->db->getRow($certificateRequest_root);

    $certificateRequest_categories = $modx->db->query("
        SELECT `id`, `pagetitle`
        FROM `modx_site_content` 
        WHERE `parent` = {$certificateResponse_root['id']}
        ORDER BY `menuindex` ASC
    ");

    if ($modx->db->getRecordCount($certificateRequest_categories) > 0) {
        $certificatesViewBody = file_get_contents(MODX_LAYOUT_PATH . "certificates/view/body.html");
        $certificatesViewItem = file_get_contents(MODX_LAYOUT_PATH . "certificates/view/item.html");
        $certificatesViewLink = file_get_contents(MODX_LAYOUT_PATH . "certificates/view/link.html");

        $certs = "";

        $gridRows = array('1' => 1, '2' => 1);

        while ($categories = $modx->db->getRow($certificateRequest_categories)) {
            if ($categories['pagetitle'] == "Сертификаты Флюид-Лайн (производство РФ)") {
                $certificatesAloneHTML = fillAlone($categories);
                continue;
            }

            $certificateRequest_child = $modx->db->query("
                SELECT `pagetitle`, `content` 
                FROM `modx_site_content`
                WHERE `type` = 'reference' AND `parent` = {$categories['id']}
            ");

            $childs = "";

            $count = $modx->db->getRecordCount($certificateRequest_child);

            if ($count < 1) {
                continue;
            } else {
                while ($child = $modx->db->getRow($certificateRequest_child)) {
                    $childs .= str_replace(
                        array('[[+PAGETITLE+]]', '[[+LINK+]]'),
                        array($child['pagetitle'], $child['content']),
                        $certificatesViewLink
                    );
                }
            }


            $certs .= str_replace(
                array('[[+PAGETITLE+]]', '[[+ITEMS+]]', '[[+GRID_ROWS+]]'),
                array($categories['pagetitle'], $childs, getAdaptiveRows($count, $gridRows)),
                $certificatesViewItem
            );
        }

        $certificatesHTML .= str_replace('[[+ITEMS+]]', $certs, $certificatesViewBody);
    }
}

return $certificatesAloneHTML . $certificatesHTML;

?>