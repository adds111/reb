<?php
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

	if (mysql_query("SET NAMES utf8", $db)){

		$selected = "SELECT id, environment  FROM sreda5 limit 1000";
		
		if (isset($_GET['kstl'])){
			$selected = "SELECT id, environment FROM sreda5 WHERE id>1000";
		}

		$query_materials = mysql_query($selected, $db);

		while ($r = mysql_fetch_assoc($query_materials )){
			$txt = $r['environment'];//substr($r['environment'], 0, strpos($r['environment'], '('));
			echo "<a class='_link' href='/koroziya?sel=".$r['id']."'>$txt</a>";
		}
	}

    return;
?>