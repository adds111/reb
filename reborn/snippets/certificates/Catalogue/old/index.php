<?php
if (isset($_GET['debug'])) {
    include __DIR__."/index-new.php";
    return;
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/lib/lib.php';
require_once __DIR__ . '/php/data.php';

$html = '';

foreach ($certificates as $group){
    $certificates_list = '';
    foreach ($group['certificates'] as $row){
        $row['filesize'] = get_filesize($row['link']);
        $certificates_list .= get_ob_content(__DIR__ . '/html/setificate.php', $row);
    }
    $html .= get_ob_content(__DIR__ . '/html/group.php', array(
            'group' => $group['group'],
            'certificates_list' => $certificates_list
    ));
}

?>
<link rel="stylesheet" href="/assets/snippets/certificates/css/style.css">

<!--

</div>-->

<div class="container-certificates">
    <?= $html ?>
</div>
