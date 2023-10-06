<?php
global $modx;

$stuffModelBody = file_get_contents(MODX_LAYOUT_PATH . "products/stuff/stuff-model-body.html");

$currentResource = $modx->getPageInfo($modx->documentIdentifier, 1, 'id, pagetitle, description, alias');

$models = getResource3DModel_Reborn($currentResource['pagetitle']);

$stuffModelBody = str_replace('[[+DATA_FL_MODEL+]]', json_encode($models), $stuffModelBody);

return $stuffModelBody;

?>