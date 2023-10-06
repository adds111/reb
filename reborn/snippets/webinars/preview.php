<?php
global $modx;

if (!isset($rowLimit)) {
    $rowLimit = 4;
}

$webinarBody = file_get_contents(MODX_LAYOUT_PATH . "webinars/body.html");
$webinarItem = file_get_contents(MODX_LAYOUT_PATH . "webinars/item.html");
$webinarItemTime = file_get_contents(MODX_LAYOUT_PATH . "webinars/item-time.html");

function setSelect($fields = '') {
    if (!empty($fields)) {
        $fields = ", $fields";
    }

    return " SELECT msc.id, `pagetitle`, `alias` $fields FROM `modx_site_content` AS msc ";
}

function setJoin() {
    return " INNER JOIN `modx_site_tmplvar_contentvalues` AS mstc ON msc.`id` = mstc.`contentid` ";
}

function setWhere($parent = 0, $dateOrder = '>', $tmplvarid = tv_webinarDate) {
    $order = " AND STR_TO_DATE(mstc.`value`, '%d-%m-%Y %H:%i:%s') $dateOrder '".date("Y-m-d H:i:s")."' ";

    return " 
        WHERE msc.`published` = 1 AND 
            msc.`parent` = $parent AND mstc.`tmplvarid` = $tmplvarid $order 
    ";
}

function setOrder($order = 'ASC') {
    return " ORDER BY `value` $order ";
}

function setLimit() {
    global $rowLimit;

    if (is_null($rowLimit)) {
        $rowLimit = 4;
    }

    return " LIMIT $rowLimit ";
}

function getWebinarItems($request, $item) {
    global $modx;

    $html = '';

    while ($resource = $modx->db->getRow($request)) {
        $sample = $item;

        foreach ($resource as $layoutKey => $layoutValue) {
            if ($layoutKey == 'date') {
                $layoutValue = date("j F", strtotime($layoutValue));
            }

            $layoutKey = strtoupper($layoutKey);

            $sample = str_replace('[[+' . $layoutKey . '+]]', $layoutValue, $sample);
        }

        $html .= $sample;
    }

    return $html;
}

/**
 * @var DocumentParser $modx
 */
function getPlannedWebinars($parent, $webinarSampleItem) {
    global $modx;

    $plannedRequest = $modx->db->query(
        setSelect('`value` AS date') . setJoin() . setWhere($parent) . setOrder() . setLimit()
    );

    return getWebinarItems($plannedRequest, $webinarSampleItem);
}

/**
 * @var DocumentParser $modx
 */
function getCompletedWebinars($parent, $webinarSampleItem) {
    global $modx;

    $completedRequest = $modx->db->query(
        setSelect() . setJoin() . setWhere($parent, '<=') . setOrder('DESC') . setLimit()
    );

    return getWebinarItems($completedRequest, $webinarSampleItem);
}

$htmlPlannedWebinars = $htmlCompletedWebinars = "";

$request = $modx->db->query("
    SELECT `id`, `alias` 
    FROM `modx_site_content` 
    WHERE `pagetitle` = 'Вебинары'
");

if ($modx->db->getRecordCount($request) > 0) {
    $webinarResource = $modx->db->getRow($request);

    if (isset($webinarResource['id'])) {
        $htmlPlannedWebinars .= getPlannedWebinars($webinarResource['id'], $webinarItemTime);
        $htmlCompletedWebinars .= getCompletedWebinars($webinarResource['id'], $webinarItem);
    }

    return str_replace(
        array(
            '[[+COMPLETED+]]', '[[+PLANNED+]]', '[[+ROOT_ALIAS+]]'
        ),
        array(
            $htmlCompletedWebinars, $htmlPlannedWebinars, $webinarResource['alias']
        ),
        $webinarBody
    );
}

return;

?>