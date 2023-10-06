<!--
    0	::::	NV4-F-8N | 1	::::	Количество | 2	::::	Давление1 | 3	::::	Давление2
    4	::::	Температура | 5	::::	Cv | 6	::::	Среда | 7	::::	Цена
-->

<?php
    echo "<div class='_cv-and-expense-calculator-container'><form class='_cv-and-expense-calculator' id='main'></form></div>";

	if (file_exists('ips')) $ips = json_decode(file_get_contents('ips'), true); else $ips = array();
	if (isset($ips[$_SERVER['REMOTE_ADDR']])) $ips[$_SERVER['REMOTE_ADDR']]++; else $ips[$_SERVER['REMOTE_ADDR']] = 1;
	file_put_contents('ips', json_encode($ips));

    return null;
?>
