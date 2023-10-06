<?php
global $modx;

$alias = array_search($modx->documentIdentifier, $modx->documentListing);

preg_match('#(\w+)\/(.+)#', $alias, $match);

$title = $match[2];
$description = "В компании Флюид-лайн широкий ассортимент {$match[2]} по низким ценам, доставка по всей России и СНГ.";

$scripts = file_get_contents(MODX_LAYOUT_PATH . "inventory/meta/scripts.html");

$scripts = str_replace(
    array('[+TITLE+]', '[+DESCRIPTION+]'),
    array($title, $description),
    $scripts
);

return $scripts;

?>