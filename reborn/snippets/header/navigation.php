<?php
global $modx;

if (!function_exists('getChildNodes')) {
    /** @var DocumentParser $modx */
    function getChildNodes($modx, $navSubItemSample, $navSubLevel, $parent, $level) {
        $queryParameters = "`id`, `pagetitle`, `alias`, `type` ";

        $subItems = "";

        if ($level > navbarDefaultLevels) {
            return $subItems;
        }

        $childRequest = $modx->db->query(
            "SELECT $queryParameters FROM `modx_site_content` WHERE `published` = 1 AND `hidemenu` = 0 AND `parent` = $parent ORDER BY `menuindex` ASC"
        );

        if ($modx->db->getRecordCount($childRequest) == 0) {
            return $subItems;
        }

        while ($childResource = $modx->db->getRow($childRequest)) {
            if ($childResource['type'] == "reference") {
                $childResource = $modx->getPageInfo($childResource['id'], 1, 'id, pagetitle, content AS `alias`');
            }

            $subItem = $navSubItemSample;
            $subLevel = $navSubLevel;

            $items = 'ITEMS-LEVEL-' . $level;
            $subLevel = str_replace('ITEMS', $items, $subLevel);

            foreach ($childResource as $layoutKey => $layoutValue) {
                $subItem = str_replace('[[+' . strtoupper($layoutKey) . '+]]', $layoutValue, $subItem);
            }

            $childs = getChildNodes($modx, $navSubItemSample, $navSubLevel, $childResource['id'], $level + 1);

            if ($childs !== "") {
                $subItem = str_replace('[[+HAS_CHILDS+]]', '<img src="/assets/reborn/images/objects/header/arrow.svg">', $subItem);
                $subLevel = str_replace('[[+' . $items . '+]]', $childs, $subLevel);
                $subItem = str_replace('[[+LEVELS+]]', $subLevel, $subItem);

            } else {
                $subItem = str_replace(array('[[+HAS_CHILDS+]]', '[[+LEVELS+]]'), $childs, $subItem);
            }

            $subItems .= $subItem;
        }

        return $subItems;
    }
}

$navbar = "";

// Кнопки разделов
$navbarBody = file_get_contents(MODX_LAYOUT_PATH . "header/nav-item.html");

// Первый уровень
$navbarMiniBody = file_get_contents(MODX_LAYOUT_PATH . "header/nav-mini-body.html");

// Шаблон уровня подраздела
$navbarMiniLevel = file_get_contents(MODX_LAYOUT_PATH . "header/nav-mini-level.html");

// Шаблон элемента подраздела
$navbarMiniLevelItem = file_get_contents(MODX_LAYOUT_PATH . "header/nav-mini-level-item.html");

$request = $modx->db->query("
    SELECT `id`, `pagetitle`, `alias`, `type`
    FROM `modx_site_content` 
    WHERE `parent` = 0 AND `published` = 1 AND `hidemenu` = 0"
);

if ($modx->db->getRecordCount($request) > 0) {
    while ($resource = $modx->db->getRow($request)) {
        if ($resource['type'] == "reference") {
            $resource = $modx->getPageInfo($resource['id'], 1, 'id, pagetitle, content as alias');
        }

        if (isset($resource['id'])) {
            if ($resource['id'] == $modx->documentIdentifier) {
                $resource += array('CLASS' => ' active');
            } else {
                $resource += array('CLASS' => '');
            }
        }

        $subItems = getChildNodes($modx, $navbarMiniLevelItem, $navbarMiniLevel, $resource['id'], 1);

        $resource += array('SUB_MENU' => str_replace('[[+ITEMS+]]', $subItems, $navbarMiniBody));

        $items = $navbarBody;

        foreach ($resource as $layoutKey => $layoutValue) {
            $items = str_replace('[[+' . strtoupper($layoutKey) . '+]]', $layoutValue, $items);
        }

        $navbar .= $items;
    }

    return $navbar;
}

return;

?>