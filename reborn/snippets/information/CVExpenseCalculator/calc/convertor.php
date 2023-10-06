<?php

$val_to_ru = array(
    "lm" => "л/мин",
    "m3h" => "м3/ч",
    "lh" => "л/ч",
    "ftm" => "футы3/мин",
    "gm" => "галлоны/мин",

    "br" => "бар",
    "pa" => "паскаль",
    "psi" => "фунт на квадратный дюйм (PSIA)",
    "kpa" => "кПа",
    "mpa" => "МПа",

    "c" => "°C",
    "f" => "°F",
    "k" => "K",

    "kgH" => "кг/час",
    "kgM" => "кг/мин",
    "grH" => "г/час",
    "grM" => "г/мин"
);

function CurrrencyTime()
{
    return date('d/m/Y', time() + 60 * 60 * 24);
}

function get_currencies()
{
    ini_set('allow_url_fopen', '1');

    if (!function_exists('simplexml_load_file')) {
        die('Нет функции simplexml_load_file');
    }

    $currenciesPath = __DIR__ . '/currencies.xml';

    $f = fopen('http://cbr.ru/scripts/XML_daily.asp?date_req=' . CurrrencyTime(), 'r');
    file_put_contents($currenciesPath, stream_get_contents($f));
    fclose($f);

    $xml = simplexml_load_file($currenciesPath);

    $currencies = array();
    foreach ($xml->xpath('//Valute') as $valute) {
        $currencies[(string)$valute->CharCode] = (float)str_replace(',', '.', $valute->Value);
    }
    return $currencies;
}

function getCurrrencyArray()
{
    $temp = array();
    $need_load = false;
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/reborn/snippets/information/CVExpenseCalculator/calc/curr.json')) {
        $tempx = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/assets/reborn/snippets/information/CVExpenseCalculator/calc/curr.json'), true);
        if ($tempx['data'] != CurrrencyTime()) $need_load = true;
        else $temp = $tempx;
    } else {
        $need_load = true;
    }

    if ($need_load) {
        $temp = array(
            'currency' => get_currencies(),
            'data' => CurrrencyTime()
        );
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/assets/reborn/snippets/information/CVExpenseCalculator/calc/curr.json', json_encode($temp));
    }
    //print_r($temp);

    return $temp['currency'];
}


function unit_translate($from_count, $from_unit, $to_unit, $look_good = false, $format = 3, $show_unit = false, $to_translate = false)
{
    //$result;
    $currency_Array = array();

    $translate = array(
        "л/мин" => "lm",
        "м3/ч" => "m3h",
        "л/ч" => "lh",
        "футы3/мин" => "ftm",
        "галлоны/мин" => "gm",
        "бар" => "br",
        "фунт на квадратный дюйм (PSIA)" => "psi",
        "кПа" => "kpa",
        "МПа" => "mpa",
        "цельсий" => "c",
        "фаренгейт" => "f",
        "кельвин" => "k",
        '$' => 'usd',
        "€" => 'eur',
        "руб." => 'rub'
    );

    $translate_keys = array_keys($translate);

    // Исключения, при которых не будет работать обычное умножение. Используется при переводе температуры.
    $exception = array("c", "k", "f", "rub", "usd", "eur");

    $current_index_from = (in_array($from_unit, $translate_keys) ? $translate[$from_unit] : $from_unit); // Перевод входящей велечины в "тэг".
    $current_index_to = (in_array($to_unit, $translate_keys) ? $translate[$to_unit] : $to_unit); // Перевод выходящей велечины в "тэг".

    $currr = array('usd', 'eur', 'rub');
    $currency_Array = getCurrrencyArray();

    $convert = array(
        // Весовой расход
        "rub" => array(
            "from" => "rub",
            "function" => true,
            "to" => array(
                "rub" => function ($from_count, $currency_Array) {
                    return $from_count;
                },
                "usd" => function ($from_count, $currency_Array) {
                    return ($from_count / $currency_Array['USD']);
                },
                "eur" => function ($from_count, $currency_Array) {
                    return ($from_count / $currency_Array['EUR']);
                },
            )
        ),

        "usd" => array(
            "from" => "rub",
            "function" => true,
            "to" => array(
                "usd" => function ($from_count, $currency_Array) {
                    return $from_count;
                },
                "rub" => function ($from_count, $currency_Array) {
                    return ($from_count * $currency_Array['USD']);
                },
                "eur" => function ($from_count, $currency_Array) {
                    return (($from_count * $currency_Array['USD']) / $currency_Array['EUR']);
                },
            )
        ),

        "eur" => array(
            "from" => "rub",
            "function" => true,
            "to" => array(
                "eur" => function ($from_count, $currency_Array) {
                    return $from_count;
                },
                "rub" => function ($from_count, $currency_Array) {
                    return ($from_count * $currency_Array['EUR']);
                },
                "usd" => function ($from_count, $currency_Array) {
                    return (($from_count * $currency_Array['EUR']) / $currency_Array['USD']);
                }
            )
        ),
        "kgH" => array(
            "from" => "kgH",
            "to" => array(
                "kgH" => 1,
                "kgM" => 0.01666666666666666666666666666667, //  0,01666666666666666666666666666667
                "grH" => 1000,
                "grM" => 16.666666666666666666666666666667
            )
        ),
        "kgM" => array(
            "from" => "kgM",
            "to" => array(
                "kgH" => 60,
                "kgM" => 1,
                "grH" => 60000,
                "grM" => 1000
            )
        ),
        "grH" => array(
            "from" => "grH",
            "to" => array(
                "kgH" => 0.001,
                "kgM" => 0.05999999999999999999999999999999, //  0,05999999999999999999999999999999
                "grH" => 1,
                "grM" => 0.01666666666666666666666666666667
            )
        ),
        "grM" => array(
            "from" => "grM",
            "to" => array(
                "kgH" => 0.06,     //0.06
                "kgM" => 0.001, //0,001
                "grH" => 60, //60
                "grM" => 1
            )
        ),
        // Производительность
        "kv" => array(
            "from" => "kv",
            "to" => array(
                "cv" => 1.1668611435239206534422403733956,
                "kv" => 1
            )
        ),
        "cv" => array(
            "from" => "cv",
            "to" => array(
                "kv" => 0.857,
                "cv" => 1
            )
        ),
        "m3h" => array(
            "from" => "m3h",
            "to" => array(
                "lm" => 16.66666666667,
                "lh" => 1000,
                "ftm" => 0.588578,
                "gm" => 4.403,
                "m3h" => 1
            )
        ),
        "lm" => array(
            "from" => "lm",
            "to" => array(
                "m3h" => 0.06,
                "lh" => 60,
                "ftm" => 0.0353147,
                "gm" => 0.2642,
                "lm" => 1
            )
        ),
        "lh" => array(
            "from" => "lh",
            "to" => array(
                "m3h" => 0.001,
                "lm" => 0.0167,
                "ftm" => 0.0006,
                "gm" => 0.0044,
                "lh" => 1
            )
        ),
        "ftm" => array(
            "from" => "ftm",
            "to" => array(
                "m3h" => 1.699,
                "lm" => 28, 32,
                "lh" => 1699,
                "gm" => 7.48,
                "ftm" => 1
            )
        ),
        "gm" => array(
            "from" => "gm",
            "to" => array(
                "m3h" => 0.227,
                "lm" => 3.785,
                "lh" => 227.1,
                "ftm" => 0.1337,
                "gm" => 1
            )
        ),


        // Давление
        "psi" => array(
            "from" => "psi",
            "to" => array(
                "br" => 0.06894744825494008466746645706642,
                "kpa" => 6.895,
                "mpa" => 0.006894757293168,
                "psi" => 1,
                "pa" => 6894.7448254940084667466457066424 // 6894,7448254940084667466457066424
            )
        ),
        "br" => array(
            "from" => "br",
            "to" => array(
                "psi" => 14.5038,
                "pa" => 100000, // 100000
                "kpa" => 100,
                "mpa" => 0.1,
                "br" => 1
            )
        ),
        "kpa" => array(
            "from" => "kpa",
            "to" => array(
                "br" => 0.01,
                "psi" => 0.145,
                "mpa" => 0.001,
                "pa" => 1000, //1000
                "kpa" => 1
            )
        ),
        "mpa" => array(
            "from" => "mpa",
            "to" => array(
                "br" => 10,
                "kpa" => 1000,
                "psi" => 145.03773773022,
                "pa" => 1000000,
                "mpa" => 1
            )
        ),
        "pa" => array(
            "from" => "pa",
            "to" => array(
                "br" => 0.00001,        // 0,00001
                "psi" => 0.000145038,    // 0,000145038
                "kpa" => 0.001,        // 0,001
                "mpa" => 0.000001,    // 1000
                "pa" => 1
            )
        ),
        // Температура
        "c" => array(
            "from" => "c",
            "to" => array(
                "f" => function ($from_count) {
                    return (9 / 5 * $from_count) + 32;
                },
                "k" => function ($from_count) {
                    return $from_count + 273.15;
                },
                "c" => function ($from_count) {
                    return $from_count;
                }
            )
        ),
        "f" => array(
            "from" => "f",
            "to" => array(
                "c" => function ($from_count) {
                    return ($from_count - 32) * 5 / 9;
                },
                "k" => function ($from_count) {
                    return ($from_count + 459.87) / 1.8;
                },
                "f" => function ($from_count) {
                    return $from_count;
                },
            )
        ),
        "k" => array(
            "from" => "k",
            "to" => array(
                "c" => function ($from_count) {
                    return $from_count - 273.15;
                },
                "f" => function ($from_count) {
                    return $from_count * 1.8 - 459.87;
                },
                "k" => $from_count,
            )
        )
    );
    //print_r($convert);
    $to = $convert[$current_index_from]["to"][$current_index_to];
    $exception = array("rub", "usd", "eur");
    $result = 0;

    // Проверка на температуру. Требуется для подходящей операции (умножение или сложение).
    if ($from_count != '_') {

        if (in_array($current_index_from, $exception) && gettype($to) == "object") { // Проверка для единиц измерений не входящие в exception[]
            $result = $to($from_count, $currency_Array);
        } elseif (!in_array($current_index_from, $exception) && gettype($to) == "object") {
            $result = $to($from_count);
        }
        if (!in_array($current_index_from, $exception) && gettype($to) != "object") {
            $result = $from_count * $to;
        }
        if ($look_good) {
            $result = number_format($result, $format, '.', '');
            if ($show_unit)
                if (!$to_translate)
                    $result .= ' ' . array_search($to_unit, $translate);
                else
                    $result .= ' ' . $to_unit;

            if (strpos($result, '.') !== false) {
                $result = rtrim(rtrim($result, '0'), '.');
            }
        }
    } else {
        $result = '_';
    }

    console("from_count = $from_count, from_unit = $from_unit, to_unit = $to_unit, result=$result\n");
    return $result;
}

function CELL($a)
{
    $result = number_format($a, 3, '.', '');
    if (strpos($result, '.') !== false) {
        $result = rtrim(rtrim($result, '0'), '.');
    }
    return $result;
}

?>