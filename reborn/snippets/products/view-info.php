<?php
global $modx;

$currentPage = $modx->documentIdentifier;

$infoViewBody = file_get_contents(MODX_LAYOUT_PATH . "products/info/info-view-body.html");

$sliderRequest = $modx->db->query("
    SELECT `value` 
    FROM `modx_site_tmplvar_contentvalues` 
    WHERE `tmplvarid` = ". tv_imageSlider ." AND
        `contentid` = $currentPage  
");

$count = $modx->db->getRecordCount($sliderRequest);

if ($count > 0) {
    $images = $modx->db->getRow($sliderRequest);
    $imageArray = explode(';', $images['value']);

    $viewInfoItem = file_get_contents(MODX_LAYOUT_PATH . "products/info/info-view-item.html");

    $items = "";
    foreach ($imageArray as $index => $image) {
        $active = "";

        if ($index == 0) {
            $active = "_active";
        }

        $items .= str_replace(array('[[+ACTIVE+]]', '[[+IMAGE+]]'), array($active, $image), $viewInfoItem);
    }

    $infoViewBody = str_replace('[[+ITEMS+]]', $items, $infoViewBody);
}

return $infoViewBody;

?>