


<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="/assets/snippets/product/calcflow3/fluid_input/input3.js"></script>
<link rel="stylesheet" href="/assets/snippets/product/calcflow3/fluid_input/input3.css">
<script src="/assets/snippets/product/calcflow3/select_for_environment/select3.js"></script>
<link rel="stylesheet" href="/assets/snippets/product/calcflow3/select_for_environment/select3.css">

<link rel="stylesheet" href="/assets/snippets/product/calcflow3/calc_form/style.css">
<style>
	.ret {
		display: inline-block;
		width: Calc(100% - 207px);
		border: 1px solid #ccc;
		vertical-align: top;
		border-radius: 3px;
		min-height: 598px;
	}

	.fluid-input {
		display: block;
	}

	h4 {
		margin-top: 20px;
	}
</style>


<!--
		

			КОРЗИНА
			КОРЗИНА
			КОРЗИНА
			КОРЗИНА


-->
	<form id="main"></form>

	<script src="/assets/snippets/product/calcflow3/calc_form/form.js"></script>
	<script>
		function cc(){ console.log(arguments); }
		new fluid_form('main', {
			style: {input: "color: #222;",
					fluid_form_mode: 'korr',
					sreda_id: <?=(isset($_GET['sreda']))?$_GET['sreda']:"14";?>
			}, 
			inputs: {
				<?
					if (isset($_GET['code'])){
						echo "code: {title: 'Code',  value: '".$_GET['code']."', type: 'hidden'},";
					}
				?>
				p1 :  {measure: "pressure", title: "Входное давление",   unit: "br", value: <?=isset($_GET['p1'])?$_GET['p1']:'""'?><?=isset($_GET['p1'])?'':', empty: 1'?>},	
				p2 :  {measure: "pressure", title: "Выходное давление",  unit: "br", value: <?=isset($_GET['p2'])?$_GET['p2']:1?>},	
				temp :{measure: "temp", title: "Температура", unit: "c", value: <?=isset($_GET['t'])?$_GET['t']:0?>},
				dn :  {title: 	"DN мм", value: <?=isset($_GET['dn'])?$_GET['dn']:'""'?>},
				bandwidth : {measure: "bandwidth", title: "Пропускная способность", unit: "cv", value: <?=isset($_GET['cv'])?$_GET['cv']:1?> <?=(isset($_GET['code'])?', empty: 0':', empty: 1') ?>}

			}, function(data){
			console.log(data);
		}});
	</script>





























































<?
	
	if (file_exists('ips')) $ips = json_decode(file_get_contents('ips'), true); else $ips = array();
	if (isset($ips[$_SERVER['REMOTE_ADDR']])) $ips[$_SERVER['REMOTE_ADDR']]++; else $ips[$_SERVER['REMOTE_ADDR']] = 1;
	file_put_contents('ips', json_encode($ips));
	
?>