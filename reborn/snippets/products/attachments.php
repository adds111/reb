<?php
global $modx;

$currentPage = $modx->documentIdentifier;

$attachmentsBody = file_get_contents(MODX_LAYOUT_PATH . "products/attachments/attachments-body.html");
$attachmentsItem = file_get_contents(MODX_LAYOUT_PATH . "products/attachments/attachments-item.html");

$attachCatalogsBody = file_get_contents(MODX_LAYOUT_PATH . "products/attachments/attachments-catalogs-body.html");
$attachCertificatesBody = file_get_contents(MODX_LAYOUT_PATH . "products/attachments/attachments-certificates-body.html");;

$catalogsItems = $certificatesItems = "";

if (!function_exists('getAttachmentItem2')) {
    function getAttachmentItem($item, $sample) {
        return str_replace(array('[[+LINK+]]', '[[+TITLE+]]'), array($item['content'], $item['pagetitle']), $sample);
    }
}

if (!function_exists('getRequest')) {
    function getRequest($id, $tvID) {
        global $modx;

        $resource = $modx->getPageInfo($id, 1, 'id, parent');

        $request = $modx->db->query("
            SELECT `value` 
            FROM `modx_site_tmplvar_contentvalues` 
            WHERE `tmplvarid` = ". $tvID ." AND `contentid` = $id 
        ");

        $count = $modx->db->getRecordCount($request);

        if ($count < 1) {
            while ($resource['parent'] != "0") {
                $request = $modx->db->query("
                SELECT `value` 
                FROM `modx_site_tmplvar_contentvalues` 
                WHERE `tmplvarid` = ". $tvID ." AND `contentid` = {$resource['parent']}
            ");

                $count = $modx->db->getRecordCount($request);

                if ($count > 0) {
                    break;
                }

                $resource = $modx->getPageInfo($resource['parent'], 1, 'id, parent');
            }
        }

        if (!$request) {
            return false;
        }

        return $request;
    }
}

$catalogsRequest = getRequest($currentPage, tv_catalogs);

if ($catalogsRequest) {
    $catalogsResponse = $modx->db->getRow($catalogsRequest);
    $catalogsIDs = explode(';', $catalogsResponse['value']);

    foreach ($catalogsIDs as $id) {
        $catalogsItems .= getAttachmentItem($modx->getPageInfo($id, 1, 'pagetitle, content'), $attachmentsItem);
    }
}

$certificatesRequest = getRequest($currentPage, tv_certificate);

if ($certificatesRequest) {
    $certificatesResponse = $modx->db->getRow($certificatesRequest);
    $certificatesIDs = explode(';', $certificatesResponse['value']);

    foreach ($certificatesIDs as $id) {
        $certificatesItems .= getAttachmentItem($modx->getPageInfo($id, 1, 'pagetitle, content'), $attachmentsItem);
    }
}

if (!empty($catalogsItems)) {
    $attachCatalogsBody = str_replace('[[+ITEMS+]]', $catalogsItems, $attachCatalogsBody);
}

if (!empty($certificatesItems)) {
    $attachCertificatesBody = str_replace('[[+ITEMS+]]', $certificatesItems, $attachCertificatesBody);
}

return str_replace(array('[[+CATALOGS+]]', '[[+CERTIFICATES+]]'), array($attachCatalogsBody, $attachCertificatesBody), $attachmentsBody);

?>