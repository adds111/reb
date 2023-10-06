<?php
global $modx;

if (!isset($cols)) {
    $cols = 3;
}
if (!isset($documents)) {
    return null;
}

$items = "";

if (isset($titles)) {
    $documentItemTitle = file_get_contents(MODX_LAYOUT_PATH . "open_document/document-item-title.html");
    $titles = explode(';', $titles);

    foreach ($titles as $titleCol => $titleValue) {
        if ($titleCol > $cols) {
            break;
        }

        $items .= str_replace('[[+TITLE+]]', $titleValue, $documentItemTitle);
    }
}

$documentItem = file_get_contents(MODX_LAYOUT_PATH . "open_document/document-item.html");
$documents = explode(';', $documents);

foreach ($documents as $document) {
    $items .= str_replace(array('[[+CONTENT+]]', '[[+ID+]]', '[[+PAGETITLE+]]'), array($document, uniqid(), ''), $documentItem);
}

return str_replace(array('[[+ITEMS+]]', '[[+COLS+]]'), array($items, $cols), file_get_contents(MODX_LAYOUT_PATH . "/open_document/document-body.html"));

?>
