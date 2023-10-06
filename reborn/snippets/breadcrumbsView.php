<?php
global $modx;

if (!is_null($modx)) {
    $crumbsBody = file_get_contents(MODX_LAYOUT_PATH . 'breadcrumbs/crumbs-body.html');
    $crumbsItem = file_get_contents(MODX_LAYOUT_PATH . 'breadcrumbs/crumbs-item.html');
    $crumbsDelimiter = file_get_contents(MODX_LAYOUT_PATH . 'breadcrumbs/crumbs-delimiter.html');

    $items = "";

    $resources = array();
    $parameters = "id, pagetitle, description, alias, parent, template";
    $currentResource = $modx->getPageInfo($modx->documentIdentifier, 1, $parameters);

    while ($currentResource !== false) {
        if (!in_array($currentResource['template'], array(template_table, template_catalogueCategory))) {
            $resources[] = $currentResource;
        }

        $currentResource = $modx->getPageInfo($currentResource['parent'], 1, $parameters);
    }

    if (count($resources) > 1) {
        foreach (array_reverse($resources) as $resourceIndex => $resource) {
            $item = $crumbsItem;

            if ($resourceIndex > 0) {
                $items .= $crumbsDelimiter;
            }

            foreach ($resource as $layoutKey => $layoutValue) {
                $item = str_replace('[[+' . strtoupper($layoutKey) . '+]]', $layoutValue, $item);
            }

            $items .= $item;
        }

        return str_replace('[[+ITEMS+]]', $items, $crumbsBody);
    }
}

return;

?>
