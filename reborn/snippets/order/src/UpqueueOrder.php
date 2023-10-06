<?php

use Exceptions\ValidationException;
use Mappers\OrderRequestToUserData;

class UpqueueOrder {

    /** @var DocumentParser */
    private $modx;
    private $request;
    private $orderCart;
    private $isAuthorized;

    public function __construct($modx, $request, $orderCart, $isAuthorized) {
        $this->modx = $modx;
        $this->request = $request;
        $this->orderCart = $orderCart;
        $this->isAuthorized = $isAuthorized;
    }

    public function push() {
        $mapper = new OrderRequestToUserData();
        $userData = $mapper->map($this->request);
        
        $this->setUserTrackParams($userData);

        $fileInputName = 'rekvizity';
        $fileHandler = new FileHandler($fileInputName);
        $filename = null;
        $fileUploaded = $fileHandler->isUpload($fileInputName);

        if ($fileUploaded) {
            $filename = $fileHandler->moveToStorage($fileInputName);
        }

        if (empty($this->orderCart)) {
            throw new ValidationException('
                Ваш заказ пуст. Если Вы хотите зарегистрироваться на сайте, 
                пожалуйста, пройдите <a href="/registration">регистрацию</a>.
            ');
        }
//        $this->orderCart = $this->updatePrices($this->orderCart);
        $this->orderCart = $this->updateItemsPrice($this->orderCart);

         // Кол-во и цена
        $count = $totalPriceRub = 0;

//        foreach ($this->orderCart as $val) {
//            $itemCount = $val[1];
//            $itemPrice = $val[2];
//
//            $count += $itemCount;
//            $totalPriceRub += $itemCount * convertToCurrency($itemPrice, $val['valute'], 'rub');
//        }

        foreach ($this->orderCart as $item) {
            $count += $item['count'];
            $totalPriceRub += $item['count'] * getCurrencyRUB_Reborn($item['price'], $item['currency']);
        }

        $user_id = $this->isAuthorized ? $_SESSION['webInternalKey'] : 0;
	    $insertValues = array(
	        'user' => $user_id,
            'date' => time(),
            'quantity' => $count,
            'total_price_rub' => (int)ceil($totalPriceRub),
            'order' => $this->modx->db->escape(serialize($this->orderCart)),
            'userInfo' => $this->modx->db->escape(serialize($userData)),
            'status' => ORDER_QUEUE_STATUS_NEW,
        );

        if ($fileUploaded) {
            $insertValues['`filename`'] = $filename;
        }

        $insertId = $this->modx->db->insert(
            $insertValues,
            ORDER_QUEUE_DB_TABLE
        );

        if (!$insertId) {
            throw new Exception('Ошибка сохранения заказа');
        }

        $this->clearSession();

        return $insertId;
    }

    private function setUserTrackParams($userData){
        $roistat_email = isset($_COOKIE['roistat_mail']) ? $_COOKIE['roistat_mail'] : "";
        $roistat_visit_num = isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : "";
        $roistat_param_company = isset($_COOKIE['roistat_param_company']) 
            ? $_COOKIE['roistat_param_company'] 
            : "";
        $vid = isset($_COOKIE['visitorsInfo']) ? $_COOKIE['visitorsInfo'] : "";
        $yandex_uid = isset($_COOKIE['_ym_uid']) ? $_COOKIE['_ym_uid'] : "";
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

        $userData
            ->setRoistatEmail($roistat_email)
            ->setRoistatVisitNum($roistat_visit_num)
            ->setRoistatParamCompany($roistat_param_company)
            ->setVisitorsInfo($vid)
            ->setYmUid($yandex_uid)
            ->setIp($ip);
    }

    /**
     * @deprecated
     */
    private function updatePrices($purchases) {
        foreach ($purchases as $key => $value) {

            $priceQuery = $this->modx->db->query('
                SELECT `value` 
                FROM `modx_site_tmplvar_contentvalues` 
                WHERE `tmplvarid` = 15 AND `contentid` = ' . (int)$value[0]
            );
            $priceRow = $this->modx->db->getRow($priceQuery);

            $newPrice = $this->getPriceOfMateral($priceRow['value'], $value['material']);
            if ($newPrice != 0){
                $purchases[$key][2] = $newPrice;
            }
        }

        return $purchases;
    }

    private function updateItemsPrice($cartItems) {
        foreach ($cartItems as $itemIndex => $itemInfo) {
            $priceQuery = $this->modx->db->query('
                SELECT `value` 
                FROM `modx_site_tmplvar_contentvalues` 
                WHERE `tmplvarid` = 15 AND `contentid` = ' . $itemInfo['id']
            );

            $priceRow = $this->modx->db->getRow($priceQuery);

//            $newPrice = $this->getPriceOfMateral($priceRow['value'], $value['material']);
//            if ($newPrice != 0){
//                $purchases[$key][2] = $newPrice;
//            }
            $cartItems[$itemIndex]['price'] = $priceRow['value'];
        }

        return $cartItems;
    }

    private function getPriceOfMateral($snippetValue, $material = 'S316'){
        $array = explode('||', $snippetValue);

        $prices = array();
        foreach ($array as $key) {
            $keyArr = explode(':', $key);
            if (!empty($keyArr[1]) && $keyArr[1] != 0){
                $prices[$keyArr['0']] = $keyArr[1];
            }
        }

        if (isset($prices[$material])) {
            return $prices[$material];
        }

        return 0;
    }

    private function clearSession(){
        unset(
            $_SESSION['invoice'],
            $_SESSION['shopOrderForm_hash'],
            $_SESSION['shk_order_id'],
            $_SESSION['shk_payment_method'],
            $_SESSION['shk_order_price'],
            $_SESSION['shk_currency'],
            $_SESSION['shk_order_user_id'],
            $_SESSION['shk_order_user_email'],
            $_SESSION['mod_loaded'],
            $_SESSION['purchases'],
            $_SESSION['addit_params']
        );
    }

}
