<?php
global $modx;

if (!is_null($modx)) {
    $cardsRequest = $modx->db->query(
        "SELECT `id`, `pagetitle`, `alias`, `template`, `introtext` FROM `modx_site_content` WHERE `published` = 1 AND `parent` = {$modx->documentIdentifier}"
    );

    $cardBody = file_get_contents(MODX_LAYOUT_PATH . "cards/card-body.html");
    $cardItem = file_get_contents(MODX_LAYOUT_PATH . "cards/card-item.html");

    $cardCategoryBody = file_get_contents(MODX_LAYOUT_PATH . "cards/card-category-body.html");
    $cardCategoryItem = file_get_contents(MODX_LAYOUT_PATH . "cards/card-category-item.html");

    $cardResourceItem = file_get_contents(MODX_LAYOUT_PATH . "cards/card-resource-item.html");

    $items = "";

    function getItems($cardItem, $card) {
        global $modx;

        $itemsTVids = implode(', ', array(tv_resourceImage));

        $TVRequest = $modx->db->query("
            SELECT `tmplvarid`, `value` 
            FROM `modx_site_tmplvar_contentvalues` 
            WHERE `contentid` = {$card['id']} AND
                  `tmplvarid` IN ($itemsTVids)
        ");

        $count = $modx->db->getRecordCount($TVRequest);

        if ($count < 1) {
            $aliasUpper = strtoupper($card['alias']);

            if (file_exists(MODX_IMAGE_SVG_PATH . "$aliasUpper.svg")) {
                $card += array('IMAGE' =>  MODX_IMAGE_SVG_URL . "$aliasUpper.svg");
            }
        } else {
            while ($param = $modx->db->getRow($TVRequest)) {
                switch ($param['tmplvarid']) {
                    case tv_resourceImage : {
                        if (file_exists(MODX_BASE_PATH . $param['value'])) {
                            $card += array('IMAGE' => $param['value']);
                        }

                        break;
                    }
                }
            }
        }

        if (!isset($card['PRICE'])) {
            $card += array('PRICE' => 'От 0 000,0 руб.');
        }

        foreach ($card as $layoutKey => $layoutValue) {
            $cardItem = str_replace('[[+' . strtoupper($layoutKey) . '+]]', strip_tags($layoutValue, array()), $cardItem);
        }

        return $cardItem;
    }

    $items = $cards = $categories = "";

    while ($card = $modx->db->getRow($cardsRequest)) {
        switch ($card['template']) {
            case template_catalogueCategory : {
                $categoriesRequest = $modx->db->query(
                    "SELECT `id`, `pagetitle`, `alias`, `template`, `introtext` FROM `modx_site_content` WHERE `published` = 1 AND `parent` = {$card['id']}"
                );

                $categoryItems = "";
                $categoryBody = $cardCategoryBody;

                while ($cardChild = $modx->db->getRow($categoriesRequest)) {
                    $categoryItems .= getItems($cardCategoryItem, $cardChild);
                }

                foreach ($card as $layoutKey => $layoutValue) {
                    $categoryBody = str_replace('[[+' . strtoupper($layoutKey) . '+]]', strip_tags($layoutValue, array()), $categoryBody);
                }

                $categories .= str_replace('[[+ITEMS+]]', $categoryItems, $categoryBody);

                break;
            }

            case template_productView : {
                $cards .= getItems($cardItem, $card);
                break;
            }

            default : {
                $cards .= getItems($cardResourceItem, $card);
                break;
            }
        }
    }

    if (!empty($cards)) {
        $items .= str_replace('[[+ITEMS+]]', $cards, $cardBody);
    }

    if (!empty($categories)) {
        $items .= $categories;
    }
//    return str_replace('[[+ITEMS+]]', $items, $cardBody);
    return $items;
}

return;

?>
