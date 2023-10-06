<script>var sredaName = '<?=$GLOBALS['sreda_RU'];?>';</script>

<div class="c">
	<h2 class="ut_">Среда: <span class="cutA" id="h1srd"><div class="p3"><?=$GLOBALS['sreda_RU'];?></div></span> материал: <span class="cutA" id="h1material"><div class="p3"><?=$GLOBALS['material_RU'];?></div></span></h2>
</div>

<div id="cl18"><?=$GLOBALS['p18'];?></div>

<div id="SredaWiki" <?=($GLOBALS['sreda']['wiki_description']==''?"style='display: none;'":'')?>>
	<p><b>Описание среды: </b><span id="opis_sred"><?


		$wiki_description = explode(".", $GLOBALS['sreda']['wiki_description']);
		
		foreach ($wiki_description as $key => $value) {
			echo "$value. ";
			if ($key==2) echo "<span id='hiden_text'>";

		}
		echo "<a target='_blank' rel='nofollow' href='".$GLOBALS['sreda']['wiki_url']."'>Подробнее...</a> ";

		echo "</span>";

	?></span><span id="end_link" class="link" onclick="showHiden_text_or_hide(this);"> [далее...]</span></p>
</div>
<div id="cl20">
	<p><b>Примечание:</b> Условия использования сервиса проверки коррозионной совместимости.<br> Компания ООО "Флюид-Лайн" не гарантирует стойкость материалов представленных на данной странице, и не несет ответственность перед клиентами и третьими лицами за представленную информацию <span class="link" onclick="get_big_text(false);">[далее...]</span>
</p>
</div>

<table border="1" class="products noprint">
	<tr>
		<td colspan="2" style="text-align:center; font-size:12px;">
			<table width="100%" border="0">
				<tr>
					<td class="bla ffff">неизвестно</td>
					<td class="red ffff">не рекомендуется</td>
					<td class="ora ffff">скорее не подойдет</td>
					<td class="blu ffff">в основном удовлетворительно</td>
					<td class="gre ffff">рекомендуется</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<th><span>
				<label data-val="[+index+]" for="sel">
					<div  tabindex="1" id="sel5" class="fcx">
						<div class="title">Не выбрано</div>
					
						<div class="list">
							<input placeholder="Искать..." class="fcx" id="sel_search" type="text" tabindex="-1">
							<div class="scroll">
								<ol id="mylist">
									
								</ol>
								<div id="hduwh861h2" class="btn" onclick="gotoy(+1)">еще</div>
							</div>
						</div>
					</div>
					<!--
					<select id="sel" data-value="" data-u="0" style="width:200px" name="CalculationType" data-t="[+index+]"  class="CalculationType media" onchange="updsl(this);"></select>
					-->
			</label></span></th>
		<th ><span>

				<label data-val="[+index+]" for="sel1">
					<select id="sel1" data-value="" data-u="0" style="width:200px" name="CalculationType1" data-t="[+index+]" class="CalculationType1 media1 hide" onchange="gotm();"><?=$GLOBALS['options']?></select>
				</label>

			</span></th>
	</tr>
	<tr><td colspan="2" style="text-align:center;"><h2>Список материалов</h2></td></tr>
	<tr>
		<td colspan="2" align="left">
			<div class="form" id="text">
				<div id="v1" class="h">
					<div class="dopis">Не рекомендуется!</div>
					<div class="v"><?=$GLOBALS['export'][1];?></div>
				</div>
				<div id="v2" class="h">
					<div class="dopis">Скорее не подойдет!</div>
					<div class="v"><?=$GLOBALS['export'][2];?></div>
				</div>
				<div id="v3" class="h">
					<div class="dopis">В основном удовлетворительно</div>
					<div class="v"><?=$GLOBALS['export'][3];?></div>
				</div>
				<div id="v4" class="h">
					<div class="dopis">Рекомендуется</div>
					<div class="v"><?=$GLOBALS['export'][4];?></div>
				</div>
				<div id="v0" class="h">
					<div class="dopis">Неизвестно</div>
					<div class="v"><?=$GLOBALS['export'][0];?></div>
				</div>
			</div>
		</td>
	</tr>
</table>

<br><br><br>

<p id="p1">Список материалов с описанием</p><br><br>
<?=$GLOBALS['ppp'];?>


<script>
	mat_ID = <?=$GLOBALS['material'];?>;
</script>

<?php
    return null;
?>