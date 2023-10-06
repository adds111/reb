<?php
if (!empty($_GET['invoiceNum'])) {
	$_SESSION['invoice']['num'] = (int)$_GET['invoiceNum'];
	$_SESSION['invoice']['discount'] = (int)$_GET['discount'];
	$_SESSION['invoice']['delivery'] = (int)$_GET['delivery'];
	$_SESSION['invoice']['comment'] = strip_tags($_GET['comment']);
	$_SESSION['invoice']['manager'] = $_GET['manager'];
}

$invoiceOrderLayout = file_get_contents(MODX_LAYOUT_PATH . "cart/invoice-order.html");

if (isset($_GET['invoice'])) {
    unset($_SESSION['purchases']);
	
	require_once MODX_BASE_PATH .'assets/snippets/fluid/lib.php';

    if (!empty($_GET[comment])) {
        $comment = base64_decode(str_replace(' ', '+', $_GET[comment]));
    }

    echo '<div style="padding: 15px; margin-bottom: 15px; background-color: #FFFBC2;">'.$comment.'</div>';

	function saveToHistory() {
		global $modx;
		$vid = $modx->runSnippet('visitorsInfo', array("tpl" => array()));
		$line =  date("Y.m.d - H:i", time()) ."\t". $vid ."\t". $_SERVER['REMOTE_ADDR'] ."\t". $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		saveToFile('temp/invoiceLinks', $line, 100);
	}
	
    function getId($code) {
        global $modx;

        $query = $modx->db->query('SELECT `id` FROM `modx_site_content` WHERE `pagetitle` = "'.$modx->db->escape($code).'"');
        if($modx->db->getRecordCount($query) != 1)
            return 1;
        $id = $modx->db->getRow($query);
        return $id['id'];
    }

    function getPriceFromId($id) {
        global $modx;
        $id = (int)$id;

        //узнаем внешний это документ или нет
        $query = $modx->db->query('SELECT `foreignTable` FROM `modx_site_content` WHERE `id` = '.$id);
        $row = $modx->db->getRow($query);
        if($row['foreignTable'] == 1)
            $table = 'product_tmplvar_contentvalues';
        else
            $table = 'modx_site_tmplvar_contentvalues';

        //вытаскиваем цену
        $query = $modx->db->query('SELECT `value` FROM `'.$table.'` WHERE `contentid` = '.$id.' AND `tmplvarid` = 15');
        if ($modx->db->getRecordCount($query) == 1) {
            $row = $modx->db->getRow($query);
            $price = $row['value'];
        } else {
            $price = 0;
        }

        return $price;
    }

    function getSectionTitle($id){
        $id = (int)$id;
        global $modx;

        $query = $modx->db->query('SELECT `parent` FROM `modx_site_content` WHERE `id` = '.$id.' AND `parent` <> 0');
        $parentArr = $modx->db->getRow($query);
        if(empty($parentArr)) return;
        $parent = current($parentArr);

        $query = $modx->db->query('SELECT `pagetitle`,`template`,`parent` FROM `modx_site_content` WHERE `id` = '.$parent);
        $parentValues = $modx->db->getRow($query);
        //if($parentValues['template'] != 16)
        return $parentValues['pagetitle'];

        /*
        $query = $modx->db->query('SELECT `pagetitle` FROM `modx_site_content` WHERE `id` = '. $parentValues['parent']);
        return current($modx->db->getRow($query));
        */
    }

    function getCatalogFromId($id){
        $id = (int)$id;
        global $modx;

        if($id == 1)
            return;

        $query = $modx->db->query('SELECT `parent` FROM `modx_site_content` WHERE `id` = '.$id.' AND `parent` <> 0');
        $parentArr = $modx->db->getRow($query);
        if(empty($parentArr)) return;
        $parent = current($parentArr);

        $query = $modx->db->query('SELECT `id`,`template`,`parent` FROM `modx_site_content` WHERE `id` = '.$parent);
        $parentValues = $modx->db->getRow($query);
        if($parentValues['template'] != 16)
            $link = $parentValues['id'];
        else{
            $query = $modx->db->query('SELECT `id`,`template`,`parent` FROM `modx_site_content` WHERE `id` = '.$parentValues['parent']);
            $parentValues = $modx->db->getRow($query);
            if($parentValues['template'] != 16)
                $link =  $parentValues['id'];
        }

        return '/?id='.$link;
        /*
        $query = $modx->db->query('SELECT `pagetitle` FROM `modx_site_content` WHERE `id` = '. $parentValues['parent']);
        return current($modx->db->getRow($query));
        */
    }

    //переделать функцию
    function getValuteFromId($id)
    {
        $id = (int)$id;
        global $modx;

        //узнаем внешний это документ или нет
        $query = $modx->db->query('SELECT `foreignTable` FROM `modx_site_content` WHERE `id` = ' . $id);
        $row = $modx->db->getRow($query);
        if ($row['foreignTable'] == 1)
            $table = 'product_tmplvar_contentvalues';
        else
            $table = 'modx_site_tmplvar_contentvalues';
        $query = $modx->db->query('SELECT `value` FROM `' . $table . '` WHERE `contentid` = ' . $id . ' AND `tmplvarid` = 58');
        if ($modx->db->getRecordCount($query) != 1){
            //todo попытаться найти параметр в контенте родителей
            $valute = 'dol';
        }else{
            $valuteArr = $modx->db->getRow($query);
            $valute = $valuteArr['value'];

            //если таблица лишняя, мы выбрали не значение а ссылку на него. Получ знач.
            if($row['foreignTable'] == 1){
                $query = $modx->db->query('SELECT `value` FROM `product_tmplvar_data` WHERE `id` = '.(int)$valute);
                $row = $modx->db->getRow($query);
                $valute = $row['value'];
            }
        }

        return $valute;
    }
	
	//if(!isManager())
		saveToHistory();

    $_GET['invoice'] = strip_tags( urldecode($_GET['invoice']) );
    if(empty($_GET['invoice']))
        return;

    $_GET['invoice'] = explode('//',$_GET['invoice']);
    foreach($_GET['invoice'] as $key => $val){

        //отделяем кодировку от количества
        $valArr = explode(':',$val);

        $purchases[$key]['index'] = $key; //порядок в списке
        $purchases[$key]['code'] = $valArr[0];
        $purchases[$key][1] = ( empty($valArr[1]) ? 1 : $valArr[1] ); //кол-во
        $purchases[$key][0] = getId($valArr[0]); //id
        $purchases[$key][2] = getPriceFromId($purchases[$key]['0']); //цена
        $purchases[$key]['title'] = getSectionTitle($purchases[$key][0]);
        $purchases[$key]['valute'] = getValuteFromId($purchases[$key][0]);
        $purchases[$key]['image'] = getProductImage($purchases[$key][0]);
        $purchases[$key]['catalog'] = getCatalogFromId($purchases[$key][0]);
		
		if($purchases[$key][0] == 1)
			$absent[] = $purchases[$key]['code'];
    }
	
	if( isManager() and is_array($absent) ){
		echo '<b style="color: red;">Следующие кодировки не были найдены на сайте:</b><br/>';
		foreach($absent as $val)
			echo $val.'<br/>';
	}

    $_SESSION['purchases'] = serialize($purchases);
    //сохранить заказ в сессию
} else {
    return $invoiceOrderLayout;
}

?>
