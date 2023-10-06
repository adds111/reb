<?php
/**
 * @var $modx object
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/lib/lib.php';
include_once __DIR__ . '/../php/data.php';

if (!$cert) {
    $parent = $modx->getParent($modx->documentIdentifier);

    while ($parent['id'] && !$tv['value']) {
        $tv = $modx->getTemplateVar('cetificate', '*', $parent['id']);
        $parent = $modx->getParent($parent['id']);
    }

    if (!$tv['value'])
        return '';
    else
        $cert = $tv['value'];
}


function get_certificate_name($certificates, $link)
{
    $server = 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
    $link = str_replace($server, '', $link);
    foreach ((array)$certificates as $array) {
        foreach ((array)$array['certificates'] as $item) {
            if ($link == $item['link'])
                return $item['title'];
            elseif ($link == $item['extra']['link'])
                return $item['extra']['title'];
        }
    }
    return $link;
}

$cert = explode(',', (string)$cert);
$certificates_lis = '';
foreach ($cert as $link){

    $filename = get_certificate_name($certificates, trim($link));
    $filename_short = mb_substr($filename, 0, 80, 'UTF-8') . (mb_strlen($filename, 'UTF-8') > 80 ? '...' : '');

    $certificates_lis .= '<li>
                          <a href="' . trim($link) . '" target="_blank" title="' . $filename . '">' . $filename_short . '</a>';
    $filesize = get_filesize(trim($link));
    if((int)$filesize)
        $certificates_lis .= ' | <a href="' . trim($link) . '" target="_blank" class="fileLink" title="Скачать сертификат с сервера" download="">Скачать</a>
                          <span style="color: #555555;">(' . $filesize . ')</span>';

    $certificates_lis .= '</li>';
}


echo '<br><br><span class="certificates-title">Сертификаты</span><ul class="pdf certificates-list">' . $certificates_lis . '</ul>';
return;