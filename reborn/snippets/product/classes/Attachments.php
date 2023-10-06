<?php

class Attachments
{
    private $page;

    private $body;

    private $item;

    private $catalogs;
    private $catalogsItems;

    private $certificates;
    private $certificatesItems;

    public function __construct($page)
    {
        $this->setPage($page);
        $this->setBody(file_get_contents(MODX_LAYOUT_PATH . "product/attachments/body.html"));
        $this->setItem(file_get_contents(MODX_LAYOUT_PATH . "product/attachments/item.html"));

        $this->catalogsItems = $this->certificatesItems = "";
    }

    private function setPage($value)
    {
        $this->page = $value;
    }

    private function getPage()
    {
        return $this->page;
    }

    private function setBody($value)
    {
        $this->body = $value;
    }

    private function getBody()
    {
        return $this->body;
    }

    private function setItem($value)
    {
        $this->item = $value;
    }

    private function getItem()
    {
        return $this->item;
    }

    public function setCatalogs($value)
    {
        global $modx;
        $catalogsItems = &$this->catalogsItems;

        $this->catalogs = $value;

        $catalogsRequest = $this->getRequest(tv_catalogs);

        if ($catalogsRequest) {
            $catalogsResponse = $modx->db->getRow($catalogsRequest);
            $catalogsIDs = explode(';', $catalogsResponse['value']);

            foreach ($catalogsIDs as $id) {
                $catalogsItems .= $this->getAttachmentItem(
                    $modx->getPageInfo($id, 1, 'pagetitle, content'), $this->getItem()
                );
            }
        }
    }

    private function getCatalogs()
    {
        return $this->catalogs;
    }

    private function getCatalogsItems()
    {
        return $this->catalogsItems;
    }

    public function setCertificates($value)
    {
        global $modx;
        $certificatesItems = &$this->certificatesItems;

        $this->certificates = $value;

        $certificatesRequest = $this->getRequest(tv_certificate);

        if ($certificatesRequest) {
            $certificatesResponse = $modx->db->getRow($certificatesRequest);
            $certificatesIDs = explode(';', $certificatesResponse['value']);

            foreach ($certificatesIDs as $id) {
                $certificatesItems .= $this->getAttachmentItem(
                    $modx->getPageInfo($id, 1, 'pagetitle, content'), $this->getItem()
                );
            }
        }
    }

    private function getCertificates()
    {
        return $this->certificates;
    }

    private function getCertificatesItems()
    {
        return $this->certificatesItems;
    }

    public function getAttachments()
    {
        $body = $this->getBody();

        $catalogsItems = $this->getCatalogsItems();
        if (!empty($catalogsItems)) {
            $catalogsBody = str_replace('[[+ITEMS+]]', $catalogsItems, $this->getCatalogs());
            $body = str_replace('[[+CATALOGS+]]', $catalogsBody, $body);
        }

        $certificatesItems = $this->getCertificatesItems();
        if (!empty($certificatesItems)) {
            $certificatesBody = str_replace('[[+ITEMS+]]', $certificatesItems, $this->getCertificates());
            $body = str_replace('[[+CERTIFICATES+]]', $certificatesBody, $body);
        }

        return $body;
    }

    private function getAttachmentItem($item, $sample) {
        return str_replace(array('[[+LINK+]]', '[[+TITLE+]]'), array($item['content'], $item['pagetitle']), $sample);
    }

    private function getRequest($tvID) {
        global $modx;

        $id = $this->getPage();

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