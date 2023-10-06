<?php
global $modx;

require_once __DIR__ .'/../classes/Attachments.php';

$attachments = new Attachments($modx->documentIdentifier);

$attachments->setCatalogs(file_get_contents(MODX_LAYOUT_PATH ."product/attachments/catalogs.html"));
$attachments->setCertificates(file_get_contents(MODX_LAYOUT_PATH ."product/attachments/certificates.html"));

return $attachments->getAttachments();

?>