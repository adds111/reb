<?php
global $modx;

/**
 * @var DocumentParser $modx
 */
function getIdByPagetitle($modx, $parent, $names)
{
    $names = " ('" . implode("', '", $names) . "') ";

    return $modx->db->getRow(
        $modx->db->query(
            "SELECT `id` FROM `modx_site_content` WHERE `parent` = $parent AND `pagetitle` IN $names ORDER BY `id` ASC LIMIT 1"
        )
    );
}

$aboutPagetitle = "О компании";
$about = getIdByPagetitle($modx, 0, array($aboutPagetitle));

if (isset($about['id'])) {
    $certificatesPagetitle = "Сертификаты";
    $certificates = getIdByPagetitle($modx, $about['id'], array($certificatesPagetitle));

    if (isset($certificates['id'])) {
        $parentQuery = "(SELECT `id` FROM `modx_site_content` WHERE `pagetitle` = 'Сертификаты Флюид-Лайн (производство РФ)' AND `parent` = " . $certificates['id'] . " LIMIT 1)";

        $flCertsRequest = $modx->db->query("
            SELECT `id`, `pagetitle`, `content` 
            FROM `modx_site_content` 
            WHERE `published` = 1 AND 
                  `parent` = $parentQuery 
            LIMIT 5;
        ");

        $htmlCerts = "";
        $certificateSampleItem = file_get_contents(MODX_LAYOUT_PATH . "certificates/certificates-grid-item.html");

        while ($flCert = $modx->db->getRow($flCertsRequest)) {
            $cert = $certificateSampleItem;

            foreach ($flCert as $layoutKey => $layoutValue) {
                $layoutKey = strtoupper($layoutKey);

                $cert = str_replace('[[+' . $layoutKey . "+]]", $layoutValue, $cert);
            }

            $htmlCerts .= $cert;
        }

        return str_replace('[[+ITEMS+]]', $htmlCerts, file_get_contents(MODX_LAYOUT_PATH . "certificates/certificates-grid-view.html"));
    }
}

return null;

?>