<?php

$error = "";
header('Content-Type: application/json');
include_once $_SERVER['DOCUMENT_ROOT'].'/assets/reborn/snippets/information/CVExpenseCalculator/calc/convertor.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/assets/reborn/snippets/information/CVExpenseCalculator/calc/rashod.php';




$pmax = '';
$tmax = '';
$tmin = '';
$pmax_out = '';


$temp_error_log = '';
$p1_error_log = '';
$p2_error_log = '';


//if (isset($tempsVars[109])){ $max_dav  = $tempsVars[109]; }  //  максимальное давление
//if (isset($tempsVars[110])){ $max_temp = $tempsVars[110]; }  //  максимальная температура
//if (isset($tempsVars[111])){ $min_temp = $tempsVars[111]; }  //  минимальная температура


$all = true;
$units = array(
    "m3h" => array("lm",  "lh", "ftm"),
    "br"  => array("psi", "pa", "kpa", "mpa"),
    "f"   => array("c",   "k")
);


$units2 = array(
    'pressure' => array('br', "psi", "pa", "kpa", "mpa"),
    'temp' => array("f", "c", "k"),
    'speed' => array("m3h", "lm", "lh", "ftm"),
    'bandwidth' => array('cv', 'kv'),
    'weith' => array('kgH', 'kgM', 'grH', 'grM')
);


function each_unit2($from_count, $measure, $from_unit, $to_unit, $sufix = ''){
    global $units2;
    global $all;
    $result = array();

    if ($all){
        foreach ($units2[$measure] as $ar_unit) {
            $result[$sufix.$ar_unit] = unit_translate($from_count, $from_unit, $ar_unit, true);
        }
    } else {
        $result[$sufix.$to_unit] = unit_translate($from_count, $from_unit, $to_unit, true);
    }

    return $result;
}


// state 1 - жидкость
// state 2 - газ


$p1 =	  		isset($_POST['p1_value']) ? unit_translate($_POST['p1_value'], $_POST['p1_unit'], 'br') : "_";
$p2 =	  		isset($_POST['p2_value']) ? unit_translate($_POST['p2_value'], $_POST['p2_unit'], 'br') : "_";
$p1_empty 	= 	isset($_POST['p1_empty']) ? $_POST['p1_empty'] : 0;
$p2_empty 	= 	isset($_POST['p2_empty']) ? $_POST['p2_empty'] : 0;
if ($p1!='') $p1_empty = 0;
if ($p2!='') $p2_empty = 0;

$ro =			isset($_POST['ro_value']) ? $_POST['ro_value'] : '';
$temp =	  		isset($_POST['temp_value']) ? unit_translate($_POST['temp_value'], $_POST['temp_unit'], 'k')  :  '';
$tempС = 		isset($_POST['temp_value']) ? unit_translate($_POST['temp_value'], $_POST['temp_unit'], 'c')  :  0;

$code =	  		isset($_POST['code_value']) ? $_POST['code_value'] : '';
$ro = 			isset($_POST['weight']) ? $_POST['weight'] : '';

$state =  		isset($_POST['state']) ? $_POST['state'] : '';
$dn =	  		(isset($_POST['dn_value']) && $_POST['dn_value']!='') ? $_POST['dn_value'] : '';
$bandwidth = 	isset($_POST['bandwidth_value']) ? $_POST['bandwidth_value'] : '';
$sreda_id = 	isset($_POST['sreda_id']) ? $_POST['sreda_id'] : '';

$rate_unit = 	isset($_POST['rate_unit']) ? $_POST['rate_unit'] : 'm3h';
$rate = 		isset($_POST['rate_value']) ? unit_translate($_POST['rate_value'], $rate_unit, 'm3h') : 1;
$calc_mode = 	isset($_POST['calc_mode']) ? $_POST['calc_mode'] : '';

$bandwidth_unit = isset($_POST['bandwidth_unit'])?$_POST['bandwidth_unit']:'cv';
$bandwidth_empty = isset($_POST['bandwidth_empty'])?$_POST['bandwidth_empty']:0;
if ($bandwidth!='') $bandwidth_empty = 0;

$mode = isset($_POST['mode']) ? $_POST['mode'] : 'consumption'; //'consumption';
$consumption2_unit = isset($_POST['consumption2_unit']) ? $_POST['consumption2_unit'] : "m3h";

$dop = isset($_POST['dop_value'])?$_POST['dop_value']:'';


$GW = true;

if ($dop != ''){
    $dop = explode(";", $dop);
    $GW = strpos('GW', $dop[0])!==false;
}

$GW = ($GW | (strpos($code, 'GW')!==false));



//$error .= " - ".strpos($code,'GW');


//if ($code == 'SO2B-F-8N')
file_put_contents('log.html', "<meta charset='utf-8'>");
console("<pre>".rand(1,100).' test !!!<hr>');
function console($text){
	if(isset($GLOBALS['console'])){
		$GLOBALS['console'] .= $text . "\n";
	} else {
		$GLOBALS['console'] = $text . "\n";
	}
}


$p1_error = 0;
$p2_error = 0;
$temp_error = 0;




console(print_r(
    array(
        'p1' => $p1,
        'p2' => $p2,
        'p1_empty' => $p1_empty,
        'p2_empty' => $p2_empty,
        'ro' => $ro,
        'temp' => $temp,
        'tempС' => $tempС,
        'code' => $code,
        'ro' => $ro,
        'state' => $state,
        'dn' => $dn,
        'bandwidth' => $bandwidth,
        'sreda_id' => $sreda_id,
        'rate_unit' => $rate_unit,
        'rate' => $rate,
        'calc_mode' => $calc_mode,
        'bandwidth_unit' => $bandwidth_unit,
        'bandwidth_empty' => $bandwidth_empty,
        'mode' => $mode,
        'consumption2_unit' => $consumption2_unit
        ),
    true)
);



$dp 				= 0;
$cv 				= 0;
$kv 				= 0;
$flow_consumption 	= 0;
$flow_speed 		= 0;
$consumption 		= 0;


$komplext = "";
$pmax = "";
$tmax = "";
$tmin = "";

$sov_ = array();

$elements = "";

$mat = array("matr" => array(),
             "all" => 5,
             "allq" => 0,
             "contact" => 5,
             "contactq" => 0);




console("code: $code");

$bdVars = array();

if ($code!=''){
    include($_SERVER['DOCUMENT_ROOT'].'/assets/snippets/product/connect_to_server.php');


$templVars = getTempVars($code);

$bdVars['templVars'] = $templVars;
if (isset($templVars[18]))  $bdVars['cv'] 		= $templVars[18];
if (isset($templVars[131])) $bdVars['max_dav']  = $templVars[131];
if (isset($templVars[130]))  $bdVars['max_temp'] = $templVars[130];
if (isset($templVars[129]))  $bdVars['min_temp'] = $templVars[129];
if (isset($templVars[25]))  $bdVars['dn'] 		= $templVars[25];



$detals = isset($templVars['108'])?$templVars['108']:'';
$pmax = isset($templVars[131])? str_replace(',', '.', $templVars[131]) :'';
$tmax = isset($templVars[130])?intval($templVars[130]):'';
$tmin = isset($templVars[129])?intval($templVars[129]):'';

$pmax_out = isset($templVars[133])?intval($templVars[133]):'';

if ($dn==''){
    $dn = isset($templVars[25])?$templVars[25]:'';
}

        console($detals);

        if ($detals!=''){
        $dorty_mat = explode('//', $detals);

        console(print_r($dorty_mat, true));
        //console('-----------');
        console($_SERVER['DOCUMENT_ROOT'].'/assets/reborn/snippets/information/CVExpenseCalculator/json_base/'.$sreda_id.'.json');
        $values_json = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/reborn/snippets/information/CVExpenseCalculator/json_base/'.$sreda_id.'.json'), true);


        if ($calc_mode == 'korr' || isset($_GET['test'])){  //  Временно добавил исключение, разрешено только в режиме korr  //  &&

        foreach ($dorty_mat as $key => $value) {
            console("+ $key - $value");

            $mat1 = explode('::', $value);
            $mat['matr'][$key]['mat'] = $mat1[0];
            $mat['matr'][$key]['ops'] = $mat1[1];
            //console('+ '.print_r($mat1, true));
            //console('+ -----------');
            $ops = explode('|', $mat1[1]);

            $mattt = explode(' ', $mat['matr'][$key]['mat']);
            $mattt = $mattt[count($mattt)-1];

            $qq = 'SELECT * from prosto_materials4 where material like "%'.$mattt.'%" or russian like "%'.$mattt.'%" limit 1';

            //echo "\n\n$qq\n\n";
            $xplc = mysql_fetch_assoc(mysql_query($qq));

            //print_r($xplc);
            console($qq);
            //console(print_r($xplc, true));

            if (isset($xplc['id']) && isset($xplc['another']) && isset($xplc['russian'])){

            $mat['matr'][$key]['id'] = $xplc['id'];
            $mat['matr'][$key]['another'] = $xplc['another'];
            $mat['matr'][$key]['russian'] = $xplc['russian'];


            $mat['matr'][$key]['sov'] = 5;
            $mat['matr'][$key]['sov_q'] = 0;

            //console("=>".$xplc['id']);
            ////console(print_r($values_json[$xplc['id']], true));
            //console(".............................");
            // Пробегаем по совместимости ищем самую наименьшую

            if (isset($values_json[$xplc['id']])){
                console("такая среда есть");


                foreach ($values_json[$xplc['id']] as $keyx => $valuegf) {
                    if (isset($valuegf['value']) && $mat['matr'][$key]['sov']>$valuegf['value']) {
                        if ($mat['matr'][$key]['sov']!=5) $mat['matr'][$key]['sov_q'] = 1;
                        $mat['matr'][$key]['sov'] = $valuegf['value'];

                        if ($mat['all']!=5 & $mat['all'] != $valuegf['value'])  $mat['allq'] = 1;
                        $mat['all'] = $valuegf['value'];

                        if ($ops[1]==0){
                            if ($mat['contact']!=5 & $mat['contact'] != $valuegf['value'])  $mat['contactq'] = 1;
                            $mat['contact'] = $valuegf['value'];
                        }
                    }
                }
            } else {
                //console("такой среды нет");
                $mat['matr'][$key]['sov'] = "00";
            }


            //if (isset($sov_[$min])) $sov_[$min]++;
            //else $sov_[$min] = 1;
            //console(print_r($values_json[$xplc['id']], true));
            ////console("минимальный: ".$min);
            ////console(" и я выбрал [$min]".$xplc['material']);


            $fx_temp = "";

            if ($xplc['tempoFrom']!="NULL"){
                $fx_temp .= "от ".unit_translate($xplc['tempoFrom'], 'c', $_POST['temp_unit']).' ';
            }
            if ($xplc['tempoTo']!="NULL"){
                $fx_temp .= "до ".unit_translate($xplc['tempoTo'], 'c', $_POST['temp_unit']) ;
            }
            if ($fx_temp!=''){
                $fx_temp .= ' '.$val_to_ru[$_POST['temp_unit']];
            }




            $mat['matr'][$key]['temp'] = $fx_temp;
            $mat['matr'][$key]['tempOut'] = 0;

				if (!empty($values_json)){
					$mat['matr'][$key]['values'] = $values_json[$mat['matr'][$key]['id']];
				}
			}

		}


			} // убрать после доработки


		}

		mysql_close();
	}

	if ($bandwidth_unit=='cv'){
		$cv = $bandwidth;
		$kv = unit_translate($cv, 'cv', 'kv');
	} else {
		$kv = $bandwidth;
		$cv = unit_translate($kv, 'kv', 'cv');
	}

	$state = $ro>50?1:2;

	if ($GW & ($sreda_id==1236 | $sreda_id==1268)){
		$error .= "Использование манометров, заполненных глицерином, на кислород небезопасно из-за его горючести<br>";
	}

	if ($p1<0){
		$error .= "Входное давление должно быть не меньше 0<br>";
	}
	if ($p2<0){
		$error .= "Выходное давление должно быть не меньше 0<br>";
	}
	if ($kv<=0 && $bandwidth!='' && $mode=="consumption"){
		$error .= "Пропускная способность должна быть больше 0<br>";
	}


	if ($pmax!=''  &&  $p1>$pmax ){
		$error .= "Входное давление превышает максимально допустимое (".unit_translate($pmax, 'br', $_POST['p1_unit']). ' '.$val_to_ru[$_POST['p1_unit']].")<br>";
		$p1_error = 1;
	}

	if (($pmax!='' && $p2>$pmax) | ($pmax_out!='' && $p2>$pmax_out)){
		$error .= "Выходное давление превышает максимально допустимое";

		if ($pmax!='' && $p2>$pmax){
			$error .= " (".unit_translate($pmax, 'br', $_POST['p2_unit']). ' '.$val_to_ru[$_POST['p2_unit']].")<br>";
		} else if ($pmax_out!='' && $p2>$pmax_out){
			$error .= " (".unit_translate($pmax_out, 'br', $_POST['p2_unit']). ' '.$val_to_ru[$_POST['p2_unit']].")<br>";
		}

		$p2_error = 1;
	}

	if (($tmax!='' && $tempС>$tmax) | ($tmin!='' && $tempС<$tmin)){
		if ($tempС>$tmax){
			$temp_error_log = "Температура выше допустимой (".unit_translate($tmax, 'c', $_POST['temp_unit']). ' '.$val_to_ru[$_POST['temp_unit']].")";
			$error .= $temp_error_log."<br>";
			$temp_error = 1;
		} else {
			$temp_error_log = "Температура ниже допустимой (".unit_translate($tmin, 'c', $_POST['temp_unit']). ' '.$val_to_ru[$_POST['temp_unit']].")";
			$error .= $temp_error_log."<br>";
			$temp_error = 1;
		}
	}

	$dp = $p1 - $p2;

	if ($dp > 0){
		if ($mode=="consumption"){
			if ($p1!='' & $p2!='' & $ro!='' & $temp!='' &  $state!='' & $bandwidth!=''){
				console('заходим в просчет');

				$consumption =  0;
					if ($ro>50){
						// Жидкость
						$consumption = $kv * sqrt(1000 * ($dp) / $ro);
						console("RATE1 = $kv * sqrt(1000 * ($dp) / $ro);");
					} else {
						// Газ
						if ($p2>$p1/2){
							$consumption = 514 * $kv * sqrt(($dp) * $p2/($ro * $temp));
							console("1 --  $consumption = 514 * $kv * sqrt(($dp) * $p2/($ro * $temp)) ");
						} else {
							$consumption = 257 * $kv * $p1 / sqrt($ro * $temp);
							console("2 --  $consumption = 257 * $kv * $p1 / sqrt($ro * $temp) ;");
						}
					}

				$rate = $consumption;
				if ($dn!='')
					$flow_speed = $consumption / 3600 / (3.14 * 0.25 * $dn * $dn * 0.001 * 0.001);
				else
					$flow_speed = '_';
			}
		} else if ($mode=="cv-kv"){
			if ($p1!='' & $p2!='' & $ro!='' & $temp!='' &  $state!=''  & $rate!=''){
				$kv = 1;

				if ($dn!='')
					$flow_speed = $rate / 3600 / (3.14 * 0.25 * $dn * $dn * 0.001 * 0.001);
				else
					$flow_speed = '_';


				if ($state==1){

					$kv = (($rate) * sqrt($ro / ($dp * 1000 )));
					console("KV1 = $rate * sqrt($ro / ($dp * 1000 ))");
					console("KV1 = $kv");

				} else if ($state==2){
					if ($p2>$p1/2) {
						$kv = ($rate / 514 * sqrt($ro * $temp / $dp / $p2)) + -0.0088769558896;
						console("KV2 = ($rate / 514 * sqrt($ro * $temp / $dp / $p2)) + -0.0088769558896");
					} else {
						$kv = $rate / 257 / $p1 * sqrt($ro * $temp);
						console("KV3 = $rate / 257 / $p1 * sqrt($ro * $temp)");
					}
				}
			}
		}

	} else {
		if ($p1!='_'){
			$error .= "Входное давление должо превышать выходное! (".unit_translate($p1, 'br', $_POST['p1_unit'])." ".
			$val_to_ru[$_POST['p1_unit']]." < ".unit_translate($p2, 'br', $_POST['p2_unit'])." ".$val_to_ru[$_POST['p2_unit']].")<br>";
		}
		$p1_error = 1;
	}

	$result = array();
	$result['calc_mode'] = $calc_mode;

	if (count($mat['matr'])>0)
	$result['section'] = $mat;

	$result['inputs'] = array(
			"mode" => array(
				"value" => $mode,
				"var" => array(
					"consumption" => "Расчитать расход",
					"cv-kv" => "Расчитать Cv/Kv"
				),
				"type" => 'radio'
			),
			"p1" => array(
				"title" => "Входное давление (абс.)",
				"value" => each_unit2($p1, "pressure", 'br', $_POST['p1_unit']),
				"measure" => "pressure",
				"type" => 'number',
				"unit" => $_POST['p1_unit'],
				"error" => $p1_error,
				"ident" => 'P1',
				"info" => "Давление среды на входе в клапан",
				"empty" => $p1_empty
			),
			"p2" => array(
				"title" => "Выходное давление (абс.)",
				"value" => each_unit2($p2, "pressure", 'br', $_POST['p2_unit']),
				"measure" => "pressure",
				"type" => 'number',
				"unit" => $_POST['p2_unit'],
				"ident" => 'P2',
				"info" => "Давление среды на выходе из клапана (противодавление)",
				"empty" => $p2_empty,
				"error" => $p2_error,
			),
			"temp" => array(
				"title" => "Температура",
				"value" => each_unit2($temp, "temp", 'k', $_POST['temp_unit']),
				"measure" => "temp",
				"type" => 'number',
				"unit" => $_POST['temp_unit'],
				"error" => $temp_error,
				"ident" => 'T',
				"info" => "Температура рабочей среды"
			),
			"dn" => array(
				"title" => "Минимальный условный проход, мм",
				"value" => $dn,
				"type" => $mode=="consumption"?'number':'hidden'
			),
			"environment" => array(
				"value" => $sreda_id,
				"type" => "hidden"
			),
			"rate" => array(
				"title" => "rate",
				"value" => $rate,
				"measure" => "speed",
				"unit" => "m3h",
				'type' => 'hidden'
			),
			"bandwidth" => array(
				"title" => "cv-kv",
				"value" => unit_translate($kv, 'kv', 'cv'),
				"measure" => "bandwidth",
				"unit"=> 'cv',
				'type' => 'hidden'
			)
		);


//		inputs
/////////////////////////////////////////////////////////////////////

	console("cv == $cv");

	if ($mode=="consumption"){
		$result['inputs']["bandwidth"] = array(
				"title" => "Пропускная способность",
				"value" => array(
								'cv' => $cv!=''?CELL($cv):'_',
								'kv' => CELL($kv)
							),
				"type" => "number",
				"measure" => "bandwidth",
				"unit"=> $bandwidth_unit,
				"ident" => 'key',
				"info" => "Коэффициент Cv или Kv характеризующий пропускную способность конкретного клапана",
				"empty" => $bandwidth_empty
			);
	} else if ($mode=="cv-kv"){
		$result['inputs']['rate'] = array(
			"title" => "Расход",
			"value" => each_unit2($rate, "speed", 'm3h', $rate_unit, ($state==2?'n':'')),
			"type" => "number",
			"measure" => ($state==2?'_':'')."speed",
			"unit"=> ($state==2?'n':'').$rate_unit,
			"ident" => 'q',
			"info" => "Расход пропускной способности"
		);
	}

	if ($calc_mode=='korr') {
		$result['inputs']['consumption2'] = array(
			"title" => "<a href='http://fluid-line.ru/flowcalc?mode=$mode&code=$code&environment=$sreda_id&p1=$p1&p1_unit=".$_POST['p1_unit']."&p2=$p2&p2_unit=".$_POST['p2_unit'].
			"&temp=". unit_translate($temp, 'k', $_POST['temp_unit']) ."&temp_unit=".$_POST['temp_unit']."&dn=".
				$_POST['dn_value']."&bandwidth=".unit_translate($kv, 'kv', 'cv')."&bandwidth_unit=$bandwidth_unit' target='_blank'>Расход ".($state==1?"жидкости":"газа")."</a>",
			"value" => ((($p1_error + $p2_error)==0)?each_unit2($rate, "speed", 'm3h', $rate_unit,($state==2?'n':'')):'-'),
			"type" => "number",
			"measure" => ($state==2?'_':'')."speed",
			"unit"=> ($state==2?'n':'').$consumption2_unit,
			"readonly" => "true",
			"ident" => 'q',
			"error" => 0,//$p1_error + $p2_error,
			"info" => ""//(($p1_error + $p2_error)==0)?"":"Ошибка"
		);
	}

	$corr = array(
		1 => "Не рекомендуется!",
		2 => "Скорее не подойдет!",
		3 => "В основном удовлетворительно",
		4 => "Рекомендуется",
		5 => "Неизвестно"
	);

	$corrA_C = array(
		1 => "D",
		2 => "C",
		3 => "B",
		4 => "A",
		5 => "E"
	);

	if ($dop!='') {
		$dop[1] = 0;
		$dop[2] = ($p1=="_" || ($pmax!='' & $p1>$pmax))?1:0;
		$dop[3] = $p2=='_' || $p2>$p1 || ($pmax!='' & $p2>$pmax)?1:0;
		$dop[4] = $temp_error;

		$dop[5] = $corrA_C[$mat['all']];
		$dop[6] = $GW&($sreda_id==1236 | $sreda_id==1268)?1:0;

		$dop[8] = $dop[1]==''?"Проверь цену":"";
		$dop[9] = $p1_error==1?"Входное давление выше рабочего":"";
		$dop[10] = $p1_error==1?"Выходное давление больше входного":"";

		$dop[11] = $temp_error_log;
		$dop[12] = $corr[ $mat['all'] ];
		$dop[13] = $GW&($sreda_id==1236 | $sreda_id==1268)?"Использование манометров, заполненных глицерином, на кислород небезопасно из-за его горючести":'';

		$result['inputs']['dop'] = array(
			"title" => "dop",
			"value" => implode(';', $dop),
			"type" => "hidden"
		);
	}

/////////////////////////////////////////////////////////////////////////////////////////////
//
//
//
//
//
//
//
//
//
//
//   returned
//
////////////////////////////////////////////////////////////////////////////////////
	if ($mode=="consumption"){
		if ($calc_mode=='korr'){

			$result['returned'] = array(
				"errors" => array(
					"title" => "Имеются ошибки",
					"value" => $error
				)
			);

		} else {
			$result['returned'] = array(
				"errors" => array(
					"title" => "Имеются ошибки",
					"value" => $error
				),
				"consumption" => array(
					"title" => "Расход ".($state==1?"жидкости":"газа"),
					"value" => each_unit2($consumption, "speed", "m3h", "m3h"),
					"measure" => "speed",

				),
				"weith_flow" => array(
					"title" => "Весовой расход",
					"value" => each_unit2($consumption * $ro, "weith", "kgH", "kgH"),
					"measure" => "weith",
				),
				"flow_speed" => array(
					"title" => "Скорость потока",
					"value" => $flow_speed." м/c"
				),
				"dp" => array(
					"title" => "dp (Перепад давления)",
					"value" => $dp.' бар'
				)
			);
		}

	} else if ($mode=="cv-kv"){

		$result['returned'] = array(
			"errors" => array(
				"title" => "Имеются ошибки",
				"value" => $error
			),

			"cv" => array(
				"title" => "Пропускная способность",
				"value" => each_unit2($kv, "bandwidth", "kv", "kv"),
				"measure" => "bandwidth",
			)
		);

	}


////////////////////////////////////////////////////////////////////

	if ($code!=''){
		$result['inputs']['code'] = array(
			"title" => "Code",
			"value" => $code,
			"type" => 'hidden'
		);

		// ДЛЯ Корзины
		if ($calc_mode=='korr'){
			if (!isset($bdVars['cv'])){
				$result['inputs']['p2']['type'] = 'hidden';
				$result['inputs']['p2']['value'] = '1';
				$result['inputs']['dn']['type'] = 'hidden';
				$result['inputs']['bandwidth']['type'] = 'hidden';
				$result['inputs']['bandwidth']['value'] = '1';
				$result['inputs']['bandwidth']['empty'] = '1';

				$result['inputs']['consumption2']['type'] = 'hidden';
			}



			if (!isset($bdVars['dn'])){
				$result['inputs']['dn']['type'] = 'hidden';
			}


			if (!isset($bdVars['max_temp']) & !isset($bdVars['min_temp'])) {
				$result['inputs']['temp']['type'] = 'hidden';
			}

			if (!isset($bdVars['max_dav'])) {
				$result['inputs']['p1']['type'] = 'hidden';
			}
		}
///////////////////////////////// EXCEPTIONS
	}

	$result['params'] = array(
		'pmax' => $pmax,
		'tmax' => $tmax,
		'tmin' => $tmin,
		'kc' => $bdVars
	);

	echo json_encode($result);
	
	file_put_contents('log.html', $GLOBALS['console'], FILE_APPEND);
?>
