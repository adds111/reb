<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/lib/lib.php';
//$tv = $modx->getTemplateVar('cetificate', '*', 66472);

/*$parent = $modx->getParent($modx->documentIdentifier);
while($parent && !$tv['value']){
    $tv = $modx->getTemplateVar(array('cetificate'), '*', $parent);
    $parent = $modx->getParent($parent);
}

pre($tv['value']);*/

//echo 'fewwefgweffweefw';

/*$id = $modx->documentIdentifier;
pre($id);
$doc = $modx->getDocument($id);
pre($doc);*/

$a = array('Фторопластовая PTFE трубка 1/16”; 1,59х0,4мм', 'Каленюк Вадим Александрович');

$a_json = normJsonStr(json_encode($a));

pre($a_json);