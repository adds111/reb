<?php

function imageSize($base_path, $url, $max) 
{
    $filepath = parse_url($url, PHP_URL_PATH);
    if (!file_exists($base_path . '/' . $filepath)) {
        throw new RuntimeException('Картинка не найдена: ' . $base_path . '/' . $filepath);
    }

    $info = @getimagesize($url);
    if (!is_array($info)) {
        throw new RuntimeException('ошибка определения размера картинки для url '.$url);
    }

    $imageWidth = $info[0];
    $imageHeight = $info[1];

    $maxArr['w'] = @current(explode('x', $max));
    $maxArr['h'] = @end(explode('x', $max));

    $percent['w'] = $imageWidth / 100;
    $percent['h'] = $imageHeight / 100;

    //если ширина превышает
    if (($imageWidth > $maxArr['w'])) {
        //на сколько пикселей?
        $oversizePixel = $imageWidth - $maxArr['w'];
        $oversizePercent = round($oversizePixel / $percent['w']);

        $imageWidth = $imageWidth - $oversizePixel; //изменяем ширину
        $imageHeight = round($imageHeight - ($oversizePercent * $percent['h']) ); //подгоняем высоту
    }

    //если высота превышает
    if (($imageHeight > $maxArr['h'])) {
        //на сколько пикселей?
        $oversizePixel = $imageHeight - $maxArr['h'];
        $oversizePercent = round($oversizePixel / $percent['h']);

        $imageHeight = $imageHeight - $oversizePixel; //изменяем высоту
        $imageWidth = round( $imageWidth - ($oversizePercent * $percent['w']) ); //подгоняем ширину
    }

    return array($imageWidth, $imageHeight);
}

function sendCurlGetRequest($url){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}