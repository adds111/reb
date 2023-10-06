<?php
global $modx;

$sliderPagetitle = "Слайдер";

$request = $modx->db->query(
    "SELECT `id` FROM `modx_site_content` WHERE `parent` = 0 AND `pagetitle` = '$sliderPagetitle'"
);

$resource = $modx->db->getRow($request);

if (isset($resource['id'])) {
    $items = "";
    $sliderBody = file_get_contents(MODX_LAYOUT_PATH . "slider/slider-body.html");
    $sliderItem = file_get_contents(MODX_LAYOUT_PATH . "slider/slider-item.html");

    $slidesRequest = $modx->db->query(
        "SELECT `pagetitle` AS ALT, `content` AS SRC FROM `modx_site_content` WHERE `published` = 1 AND `hidemenu` = 0 AND `parent` = {$resource['id']}"
    );

    $countOfSlides = 0;
    $sliderClass = " active";

    while ($slide = $modx->db->getRow($slidesRequest)) {
        $slide += array('CLASS' => $sliderClass);

        if ($countOfSlides === 0) {
            $sliderClass = "";
        }

        $item = $sliderItem;

        foreach ($slide as $layoutKey => $layoutValue) {
            $item = str_replace('[[+' . $layoutKey . '+]]', $layoutValue, $item);
        }

        $items .= $item;
        $countOfSlides++;
    }

    if ($countOfSlides === 0) {
        $items .= file_get_contents(MODX_LAYOUT_PATH . "slider/slider-item-empty.html");
    }

    return str_replace('[[+ITEMS+]]', $items, $sliderBody);
}

return null;

?>
