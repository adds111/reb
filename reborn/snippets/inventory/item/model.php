<?php
global $modx;

$alias = array_search($modx->documentIdentifier, $modx->documentListing);

preg_match('#(\w+)\/(.+)#', $alias, $match);

$body = file_get_contents(MODX_LAYOUT_PATH . "inventory/model/body.html");

$model = getResource3DModel_Reborn($match[2]);

return str_replace('[[+BASE64_DATA+]]', base64_encode(json_encode($model)), $body);

?>
