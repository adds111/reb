<?php
global $modx;

if (isset($id)) {
    $bannerAttributes = "SELECT `id`, `pagetitle` AS PAGETITLE, `content` AS CONTENT FROM `modx_site_content` WHERE 1 = 1";

    $banner = $modx->db->getRow(
        $modx->db->query($bannerAttributes . " AND `id` = $id")
    );

    if (isset($banner['id'])) {
        $bannerTVids = array(tv_bannerBackground);

        $bannerParamRequest = $modx->db->query(
            "SELECT `tmplvarid`, `value` FROM `modx_site_tmplvar_contentvalues` WHERE `contentid` = {$banner['id']} AND `tmplvarid` IN (" . implode(',', $bannerTVids) . ")"
        );

        while ($param = $modx->db->getRow($bannerParamRequest)) {
            switch ($param['tmplvarid']) {
                case tv_bannerBackground : {
                    $banner += array('BACKGROUND_IMAGE' => $param['value']);

                    $imageInfo = getimagesize(MODX_BASE_PATH . $param['value']);
                    $sizes = preg_match('#height=\"(\d+)\"#', $imageInfo[3], $size);

                    $banner += array('BACKGROUND_IMAGE_HEIGHT' => $size[1]);
                    break;
                }
            }
        }

        $bannerBody = file_get_contents(MODX_LAYOUT_PATH . "banner/banner-body.html");

        foreach ($banner as $layoutKey => $layoutValue) {
            $bannerBody = str_replace('[[+' . $layoutKey . '+]]', $layoutValue, $bannerBody);
        }

        return $bannerBody;
    }
}

return null;

?>
