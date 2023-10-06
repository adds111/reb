<pre><?
// code here
// http://fluid-line.ru/assets/snippets/product/calcflow3/calc/currency_test.php

	include $_SERVER['DOCUMENT_ROOT'].'/assets/snippets/product/calcflow3/calc/convertor.php';
	$usd = getCurrrencyArray();
	echo $usd['USD'].'<br>';
	echo $usd['EUR'].'<br>';



	echo '100 евро = '.unit_translate(100, 'eur', 'rub').' руб.<br>';
	echo '100 долл = '.unit_translate(100, 'usd', 'rub').' руб.<br>';

	echo '10000 руб. = '.unit_translate(10000, 'rub', 'usd').' долл <br>';
	echo '10000 руб. = '.unit_translate(10000, 'rub', 'eur').' евро <br>';

	

?>
</pre>
