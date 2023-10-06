<?php
global $modx;

if (!function_exists('getYoutubeIdFromUrl')) {
    function getYoutubeIdFromUrl($url) {
        $parts = parse_url($url);

        if (isset($parts['query'])) {
            parse_str($parts['query'], $qs);
            if (isset($qs['v'])) {
                return $qs['v'];
            } else if (isset($qs['vi'])) {
                return $qs['vi'];
            }
        }

        if (isset($parts['path'])) {
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path) - 1];
        }
        return false;
    }
}

if (!function_exists('parse_query')) {
    function parse_query($query) {
        if (!$query)
            return false;
        $result = array();
        $query = explode('&', $query);
        foreach ((array)$query as $item) {
            $exp = explode('=', $item);
            $result[$exp[0]] = $exp[1];
        }
        return $result;
    }
}

if (!function_exists('curl_get_content')) {
    function curl_get_content($url, $cookie = false) {
        $url = urldecode($url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($cookie)
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 0);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

$currentResource = $modx->getPageInfo($modx->documentIdentifier, 1, 'id, parent');

$videoQuerySample = "
    SELECT `value` 
	FROM `modx_site_tmplvar_contentvalues` 
	WHERE `tmplvarid` = ". tv_productVideo ." AND 
	      `contentid` = %s
";

$videoRequest = $modx->db->query(
    sprintf($videoQuerySample, $currentResource['id'])
);

$count = $modx->db->getRecordCount($videoRequest);


if ($count < 1) {
    $parentResource = $modx->getPageInfo($currentResource['parent'], 1, 'id, parent');

    while (!in_array($parentResource['id'], array(0, 48))) {
        $videoRequest = $modx->db->query(
            sprintf($videoQuerySample, $parentResource['id'])
        );

        $count = $modx->db->getRecordCount($videoRequest);

        if ($count > 0) {
            break;
        }

        $parentResource = $modx->getPageInfo($parentResource['parent'], 1, 'id, parent');
    }
}

$video = $modx->db->getRow($videoRequest);

$videos = preg_match('/^\[/', trim($video['value'])) ? json_decode($video['value']) : array($video['value']);
$items = '';

$videoItem = file_get_contents(MODX_LAYOUT_PATH . "products/video/video-item.html");
$videoModal = file_get_contents(MODX_LAYOUT_PATH . "products/video/video-modal.html");

$videos = is_array($videos) ? $videos : array();

foreach ($videos as $video) {
    $video_parse = parse_url($video);
    $video_query = $video_parse['query'];
    $parse_query = parse_query($video_query);

    $video_id = getYoutubeIdFromUrl($video);

    $data = curl_get_content('https://www.youtube.com/oembed?url=' . $video . '&format=json');
    $time = is_array($parse_query) ? $parse_query['t'] : 0;
    $info = json_decode($data, 1);

    $items .= str_replace(
        array('[[+TIME+]]', '[[+VIDEO_ID+]]', '[[+TITLE+]]'),
        array($time, $video_id, $info['title']),
        $videoItem
    );
}

return $items . $videoModal;

?>
