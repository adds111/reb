<?php

	$mngr = isManager();

	function getValue_text($v){
		$rs = 'Неизвестно';
		switch ($v) {
			case 1: $rs = 'Не рекомендуется'; break;
			case 2: $rs = 'Скорее не подойдет'; break;
			case 3: $rs = 'В основном удовлетворительно'; break;
			case 4: $rs = 'Рекомендуется'; break;
		}
		return $rs;
	}

	function getValue_class($v){
		$rs = 'txtbla';
		switch ($v) {
			case 1: $rs = 'txtred'; break;
			case 2: $rs = 'txtora'; break;
			case 3: $rs = 'txtblu'; break;
			case 4: $rs = 'txtgre'; break;
		}
		return $rs;
	}

	function getcolor_box($v){
	$rs = 'bla';
		switch ($v) {
			case 1: $rs = 'red'; break;
			case 2: $rs = 'ora'; break;
			case 3: $rs = 'blu'; break;
			case 4: $rs = 'gre'; break;
		}
		return $rs;
	}


	function getVal($a, $inline, $mngr){
		$opt = '';

		if (count($a)>1){
			$opt.='<span class="fw600 cb p3 ';
			if ($inline) $opt .= 'in-line'; else $opt.='block'; 
			$opt.= '">' . (($inline)?'(':'') . 'Данные разнятся' . ($inline?'): ':'') . '</span>';
		} else {
			$opt.= '<span class="fw600">: </span>';
		}

		foreach ($a as $key => $value) {
			$color = '';
			$source = '';
			$srd_title = '';
			switch ($value['value']) {
				case "0": $color = 'bla'; $srd_title = "Неизвестно"; 			break;
				case "1": $color = 'red'; $srd_title = "Не рекоменд."; 			break;
				case "2": $color = 'ora'; $srd_title = "Скорее не подойдет"; 	break;
				case "3": $color = 'blu'; $srd_title = "В основн. удовл."; 		break;
				case "4": $color = 'gre'; $srd_title = "Рекомендуется"; 		break;
			}

			if (($value['source_book'])=='неизвестно'){
				//$source = 'Неизвестно';
			} else {
				if ($mngr){
					$source = $value['source_book'].': ';
				}
			}
			
			$opt.= '<span class="fline cb p3 br3 ';
				if ($inline) $opt.= 'in-line'; else $opt.= 'block'; 
			$opt.= '">'.$source.'<span class="txt'. $color .'">'. $srd_title .'</span></span>';
		}

		return $opt;
	}


	$GLOBALS['sreda'] = (isset($_GET['sel']) && $_GET['sel']>0)?$_GET['sel']:'12';

	$GLOBALS['sreda_RU'] = 'v';
	$GLOBALS['material'] = isset($_GET['sel1'])?$_GET['sel1']:'0';
	$GLOBALS['material_RU'] = "ВЫБЕРИТЕ МАТЕРИАЛ ИЗ СПИСКА";



	$database_type = 'mysql';
	$database_server = 'mysql68.1gb.ru:3306';
	$database_user = 'gb_fluid_line';
	$database_password = 'fz4ba77bzui';
	$database_connection_charset = 'utf8';
	$database_connection_method = 'SET CHARACTER SET';
	$dbase = '`gb_fluid_line`';
	$table_prefix = 'modx_';

	$dbase = str_replace('`', '', $dbase);
	$arr_server = explode(':', $database_server);
	$server = $arr_server[0];

	if(empty($arr_server[1])) $port = '3306'; else $port = $arr_server[1];
	$db = mysql_connect($database_server, $database_user, $database_password);

	mysql_select_db($dbase, $db);
	mysql_query("SET NAMES utf8", $db);

	$material_array = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/reborn/snippets/information/JsonBase/'.$GLOBALS['sreda'].'.json'), true);

	$q = "SELECT id,environment,wiki_description,wiki_url from sreda5 where id='".$GLOBALS['sreda']."'";
	$GLOBALS['sreda'] = mysql_fetch_assoc(mysql_query($q, $db));
	$GLOBALS['sreda_RU'] = $GLOBALS['sreda']['environment'];

	$q = "SELECT * FROM prosto_materials4";
	$query_materials = mysql_query($q, $db);
	$GLOBALS['export'] = array();
	$GLOBALS['options'] = '<option data-id="0" value="0" data-ttl="0" data-description="mx" data-u1="0" data-w1="0">не выбрано</option>';
	$GLOBALS['ppp'] = '';
	$GLOBALS['p18'] = '';

	while ($r = mysql_fetch_assoc($query_materials)){
		$val = 0;
		$_id =  $r['id'];
		if (isset($material_array[$_id])){
			$val = $material_array[$_id][0]['value'];
		} else {
			$material_array[$_id][0]['value'] = 0;
			$material_array[$_id][0]['source_book'] = 'неизвестно';
		}

		$r['ttl'] = $r['russian'];
		$img = str_replace(array(' ', '#', '/', ',', '(', ')'), '_', $r['material']);

		$spor = false; 
		if (count($material_array[$_id]) > 1){
			$spor = true; 
		}

				$vops =	'<span class="ilb63 c slide" data-slide="0">
							<span class="slider">
								
								<img class="matImg ishow" alt="'.$r['superTotal'].' фотография" src="/assets/snippets/product/img/2/'.$img.'.jpg">
							</span>

							
						</span>
					<span class="ilb63"><b>Совместимость</b> '.getVal($material_array[$_id], true, $mngr).'<br>   
					<b>Основное название: </b>'.$r['material'].' - '.$r['russian'].'<br>
					<b>Другие названия: </b>'.$r['superTotal'].'<br>  <b>Рекомендуемая температура: </b> ';

				$kc = 0;

				if ($r['tempoFrom']!="NULL")	{ $kc+=1; }
				if ($r['tempoTo']!="NULL")		{ $kc+=2; }

				switch ($kc) {
					case 0: $vops .= 'неизвестно<br>'; break;
					case 1: $vops .= 'от '.$r['tempoFrom'].' °C<br>'; break;
					case 2: $vops .= 'до '.$r['tempoTo'].' °C<br>'; break;
					case 3: $vops .= 'от '.$r['tempoFrom'].' до '.$r['tempoTo'].' °C<br>'; break;
				}



				if ($r['wikistore']!='NULL'){
					$vops .= '<b>Wikipedia: </b><a rel="nofollow" class="cutA" target="_blank" href="'.$r['wikistore'].'">'.$r['wikistore'].'</a><br>';
					
				}
				
				if ($mngr){
					foreach ($material_array[$_id] as $key => $itm) {
						$vops .= '<b>Источник: </b> '.$itm['source_book'].'<br>';
					}
				}

				$vops .= '</span></p>';
				$GLOBALS['ppp'] .= "<p id='zz$_id' data-stickid='$_id'>".$vops;   // Добавляем в топ описание
				if ($GLOBALS['material'] == $_id){ $GLOBALS['p18'] = "<p>".$vops; }





		$GLOBALS['options'] .= '<option class="clr26" data-id="'.$r['id'].'" value="'.$r['id'].'" ';
    			if ($GLOBALS['material']==$r['id']){
    				$GLOBALS['options'] .= 'selected="selected"';
    			}
    			$GLOBALS['options'] .= 'data-ttl="'.$r['ttl'].'" data-u="0" data-description="'.getcolor_box($val);
    				if ($spor){
    					$GLOBALS['options'] .= ' sporc';
    				}
    			$GLOBALS['options'] .= '" >'.$r['superTotal'].'</option>';




		$GLOBALS['export'][$val] .= '<a href="/koroziya?sel='.$GLOBALS['sreda']['id'].'&sel1='.$_id.'" data-id="'.$_id.'" data-total="'.$r['superTotal'].'" data-stackid="'.$_id.'" class="mtrlst ';

		if ($_id==$GLOBALS['material']){
			$GLOBALS['export'][$val] .= ' selectmee';
			$GLOBALS['material_RU'] = $r['russian'];
		}

		$GLOBALS['export'][$val] .= '" title="'.$r['russian'].'">'.$r['material'];

			if (isset($material_array[$_id]) && $spor){
				$GLOBALS['export'][$val] .= '<span class="qwes">?</span> 
				<div class="dopqinf"><span class="fw600 cb p3 block">Данные разнятся</span>';
						
						foreach ($material_array[$_id] as $key => $itm) {
							$GLOBALS['export'][$val] .= '<span class="fline cb p3 br3 block">';
							if ($mngr){
								$GLOBALS['export'][$val] .= $itm['source_book'].': ';
							}
							$GLOBALS['export'][$val] .= '<span class="'.getValue_class($itm['value']).'">'.
							getValue_text($itm['value']).'</span></span>';
						}

				$GLOBALS['export'][$val].= '</div>';
			}

		$GLOBALS['export'][$val] .= '</a>';
	}

	$Atitle = "Коррозионная совместимость среды: ".$GLOBALS['sreda_RU']." и материала: ".$GLOBALS['material_RU'];

    return;
?>