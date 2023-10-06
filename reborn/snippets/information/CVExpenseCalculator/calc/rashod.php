<?

	$log = "";

	function console2($f){
		global $log;
		$log .= $f."<br>";
	}

//								   du 	     вес среды,	режим	rate_unit
	function calc($p1, $p2, $temp, $dn, $kv, $ro, 		$mode,  $unit){
		global $log;
		file_put_contents("log555.html", '<meta charset="utf-8">'.rand(1,100).'<br>');

		$log = "p1 = $p1<br>
				p2 = $p2<br>
				temp = $temp<br>
				dn = $dn<br>
				kv = $kv<br>
				ro = $ro<br>
				mode = $mode<br>
				unit = $unit<br>";


		$state = $ro>50?1:2;

		$flow_speed = 0;
		$rate = 0;
		$error = "";

		$dp = $p1 - $p2;
			if ($dp > 0){
				if ($mode == "consumption"){
							if ($ro>50){
								// Жидкость
								$rate = $kv * sqrt(1000 * ($dp) / $ro);
								console2("RATE1 = $kv * sqrt(1000 * ($dp) / $ro);");
							} else {
								// Газ
								if ($p2>$p1/2){
									$rate = 514 * $kv * sqrt(($dp) * $p2/($ro * $temp));
									console2("1 --  $consumption = 514 * $kv * sqrt(($dp) * $p2/($ro * $temp)) ");
								} else {
									$rate = 257 * $kv * $p1 / sqrt($ro * $temp);
									console2("2 --  $consumption = 257 * $kv * $p1 / sqrt($ro * $temp) ;");
								}
							}

						if ($dn!='')
							$flow_speed = $rate / 3600 / (3.14 * 0.25 * $dn * $dn * 0.001 * 0.001);
						else
							$flow_speed = '_';
					
				} else if ($mode=="cv-kv"){
					if ($p1!='' & $p2!='' & $ro!='' & $temp!='' &  $state!=''  & $rate!=''){
						$kv = 1;

						if ($dn!='')
							$flow_speed = $rate / 3600 / (3.14 * 0.25 * $dn * $dn * 0.001 * 0.001);
						else 
							$flow_speed = '_';
						

						if ($state==1){

							$kv = (($rate) * sqrt($ro / ($dp * 1000 )));
							console2("KV1 = $rate * sqrt($ro / ($dp * 1000 ))");
							console2("KV1 = $kv");

						} else if ($state==2){
							if ($p2>$p1/2) {
								$kv = ($rate / 514 * sqrt($ro * $temp / $dp / $p2)) + -0.0088769558896;
								console2("KV2 = ($rate / 514 * sqrt($ro * $temp / $dp / $p2)) + -0.0088769558896");
							} else {
								$kv = $rate / 257 / $p1 * sqrt($ro * $temp);
								console2("KV3 = $rate / 257 / $p1 * sqrt($ro * $temp)");
							}
						}
					}
				}

			} else {
				$error = "Входное давление должо превышать выходное!";

				/*." (".unit_translate($p1, 'br', $_POST['p1_unit'])." ".$val_to_ru[$_POST['p1_unit']]." < ".
				unit_translate($p2, 'br', $_POST['p2_unit'])." ".$val_to_ru[$_POST['p2_unit']].")<br>";
				
				$p1_error = 1;*/
			}

			$result = array('rate'=> unit_translate($rate, "m3h" ,$unit), 'flow_speed' => $flow_speed, 'error' => $error);
			console2(print_r($result, true));
			file_put_contents("log555.html", $log, FILE_APPEND);
		return $result;
	}



	function getArrayFromMysqlTMPLVAR($q, &$tempsVars22, $i){
		//showMysqlLink($q);
		$res = mysql_query($q);

		while ($row = mysql_fetch_assoc($res)){
			if (!isset($tempsVars22[$row['tmplvarid']])){
				if ($row['value']!=NULL){
					$tempsVars22[$row['tmplvarid']] = $row['value'];
				} else {
					$tempsVars22[$row['tmplvarid']] = $row['pval'];
				}


				if ($row['tmplvarid']==6){
					$tempsVars22[$row['tmplvarid']] = str_replace('http://www.fluid-line.ru', '', $tempsVars22[$row['tmplvarid']]);
					$tempsVars22[$row['tmplvarid']] = str_replace('https://www.fluid-line.ru', '', $tempsVars22[$row['tmplvarid']]);
				}
			}
		}
	}




	function getTempVars($code){
	
		$koleno = 0;
		$tempsVars22 = array();
		$the_par_id = $itm[0];
		$parents = array();
		$parents[$koleno] =	mysql_fetch_assoc(mysql_query("SELECT id,parent FROM `modx_site_content` WHERE pagetitle = '$code'"));
		if ($parents[$koleno]['id']!=''){
		

			getArrayFromMysqlTMPLVAR("SELECT product.tmplvarid as tmplvarid, product.value as pval, data.value as value  FROM  `product_tmplvar_contentvalues` as product
												LEFT JOIN `product_tmplvar_data` as data on product.value = data.id
												WHERE  product.contentid = ".$parents[$koleno]['id'], $tempsVars22, $koleno);
			getArrayFromMysqlTMPLVAR("SELECT tmplvarid,value FROM modx_site_tmplvar_contentvalues WHERE contentid = '".$parents[$koleno]['id']."'", $tempsVars22, $koleno);

		}

	
		do { // поиск предков
			$the_par_id = $parents[$koleno]['parent'];
			$koleno++;
			$q = "SELECT id,parent,pagetitle FROM `modx_site_content` WHERE id = '$the_par_id'";
			if ($reqc = mysql_query($q)){
				$parents[$koleno] = mysql_fetch_assoc($reqc);
				if ($the_par_id==0 | $the_par_id=='') break;
				
				getArrayFromMysqlTMPLVAR("SELECT product.tmplvarid as tmplvarid, product.value as pval, data.value as value  FROM  `product_tmplvar_contentvalues` as product
										  LEFT JOIN `product_tmplvar_data` as data on product.value = data.id
									      WHERE  product.contentid = $the_par_id", $tempsVars22, $koleno);

				getArrayFromMysqlTMPLVAR("SELECT tmplvarid,value FROM modx_site_tmplvar_contentvalues WHERE contentid = $the_par_id", $tempsVars22, $koleno);
			}
		} while ($koleno<15 & isset($parents[$koleno]['parent']));

		$tempsVars22['id'] = $parents[0]['id'];
		$tempsVars22['pagetitle'] = $parents[1]['pagetitle'];

		return $tempsVars22;
	}












	function getArrayFromMysqlTMPLVAR_like($q, &$tempsVars22, $i, $pagetitle){
		
		$res = mysql_query($q);

		while ($row = mysql_fetch_assoc($res)){
			if (!isset($tempsVars22[$pagetitle][$row['tmplvarid']])){
				if ($row['value']!=NULL){
					$tempsVars22[$pagetitle][$row['tmplvarid']] = htmlspecialchars($row['value']);
				} else {
					$tempsVars22[$pagetitle][$row['tmplvarid']] = htmlspecialchars($row['pval']);
				}
			}
		}
	}





	function getTempVars_like($code){
		$koleno = 0;
		$tempsVars22 = array();
		$the_par_id = $itm[0];
		$parents = array();
		$parents[$koleno] =	mysql_fetch_assoc(mysql_query("SELECT id,parent,pagetitle FROM `modx_site_content` WHERE pagetitle like '$code'"));



		$sq = mysql_query("SELECT id,parent,pagetitle FROM `modx_site_content` WHERE pagetitle like '$code'");
		$lc = 1;

		while ($row = mysql_fetch_assoc($sq)) {
			$pagetitle = $row['pagetitle'];
			if ($row['id']!=''){
				$parents[$pagetitle][$koleno] = $row;

				getArrayFromMysqlTMPLVAR_like("SELECT product.tmplvarid as tmplvarid, product.value as pval, data.value as value  FROM  `product_tmplvar_contentvalues` as product
											   LEFT JOIN `product_tmplvar_data` as data on product.value = data.id
											   WHERE  product.contentid = ".$parents[$pagetitle][$koleno]['id'], $tempsVars22, $koleno, $pagetitle);
				getArrayFromMysqlTMPLVAR_like("SELECT tmplvarid,value FROM modx_site_tmplvar_contentvalues WHERE contentid = '".$parents[$pagetitle][$koleno]['id']."'", $tempsVars22, $koleno, $pagetitle);


				do { // поиск предков
					$the_par_id = $parents[$pagetitle][$koleno]['parent'];
					$koleno++;
					if ($the_par_id=='') break;
					$q = "SELECT id,parent,pagetitle FROM `modx_site_content` WHERE id = '$the_par_id'";
					if ($reqc = mysql_query($q)){
						$parents[$pagetitle][$koleno] = mysql_fetch_assoc($reqc);
						if ($the_par_id==0 | $the_par_id=='') break;
						
						getArrayFromMysqlTMPLVAR_like("SELECT product.tmplvarid as tmplvarid, product.value as pval, data.value as value  FROM  
													  `product_tmplvar_contentvalues` as product
													   LEFT JOIN `product_tmplvar_data` as data on product.value = data.id
													   WHERE  product.contentid = $the_par_id", $tempsVars22, $koleno, $pagetitle);

						getArrayFromMysqlTMPLVAR_like("SELECT tmplvarid,value FROM modx_site_tmplvar_contentvalues WHERE contentid = $the_par_id", $tempsVars22, $koleno, $pagetitle);

						$tempsVars22[$pagetitle]['code'] = $pagetitle;

						$lc++;
					}
				} while ($koleno<15 & isset($parents[$pagetitle][$koleno]['parent']));
			}
		}

		return $tempsVars22;
	}

?>