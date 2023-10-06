<?php

class MailHandler{

    private $EOL = "\r\n";

    private $site_url;
    private $base_path;

    public function __construct($site_url, $base_path){
        $this->site_url = $site_url;
        $this->base_path = $base_path;
    }

    /**
     * Отправить письмо с заказом менеджерам
     */
    public function sendToManager($queueItem, $orderId)
    {
        $orderItemsRows = $this->generateOrderItemsTableRows($queueItem);
	    $exportTo1cRows = $this->generate1cExportRows($queueItem);

        $userData = $queueItem->getUserInfo(); 
        $orderLink = $this->site_url . '/orders-history-view/?open=' . $orderId;

        $emailBody = '';
        ob_start();
        include ORDER_QUEUE_ROOT_PATH . '/mail-templates/to-manager.php';
        $emailBody .= ob_get_clean();

        $companyName = $userData->getCompany();
        $customerName = $userData->getName();
        $titlefrom = !empty($companyName) 
            ? stripslashes($companyName) 
            : stripslashes($customerName);
        
        $manager = '';
        if($userData->getManager()){
            $managerName = $userData->getManager();
            $manager = sprintf('[%s]', $managerName);
        }
        $totalPriceInRub = $queueItem->getTotalPriceRub();
	$totalQuantity = $queueItem->getQuantity();

        $subject = sprintf('[FL]%sЗаказ №%d: %s (%d/%s)', 
            $manager, 
            $orderId, 
            $titlefrom, 
            $totalQuantity, 
            getPrice($totalPriceInRub, 'rub', false)
        );
        $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';

        // Присоединяем текстовое сообщение 
	$mimeBody = $emailBody;

	$attachments = array();	
	
	$_1C_export_file = tmpfile();
	$_1C_export_file_path = stream_get_meta_data($_1C_export_file);	
	$_1C_export_file_path = $_1C_export_file_path['uri'];
	fwrite($_1C_export_file, $this->generateOrderFile($queueItem, $orderId));
        
        // Прикрепляем файл для экспорта в 1С
	$attachments[] = array(
		'path' => $_1C_export_file_path,
		'name' => 'order.txt',
	);
        
        if ($queueItem->getFilename() != '') {
            try {
                $filehandler = new FileHandler();

                $attachments[] = array(
                     'path' => $filehandler->getSavedFilePath($queueItem->getFilename()),
                     'name' => $queueItem->getFilename(),
                );
            } catch(Throwable $e) {
                var_dump($e->getMessage());
                    error_log($e->getMessage());
            }
        }
    
        $to = 'mail@fluid-line.ru';
        $customerRoistatEmail = $userData->getRoistatEmail();
        if(!empty($customerRoistatEmail)){
            $to = $customerRoistatEmail;
        }
    
        // Отправляем письмо
	$res = flmail($to, $subject, $mimeBody, false, true, $attachments);

	fclose($_1C_export_file_path);

	return $res;
    }

    /**
     * Отправить письмо с заказом покупателю
     */
    public function sendToCustomer($queueItem, $orderId)
    {
        $orderItemsRows = $this->generateOrderItemsTableRows($queueItem);

        $userData = $queueItem->getUserInfo();

        $emailBody = '';
        ob_start();
        include ORDER_QUEUE_ROOT_PATH . '/mail-templates/to-user.php';
        $emailBody .= ob_get_clean();

        $subject = 'Заказ получен (Fluid-Line.ru)';
        $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';

        // любая строка, которой не будет ниже в потоке данных. 
        $headers = 'X-Priority: 1' . $this->EOL;

        // Присоединяем текстовое сообщение 
        $mimeBody = $emailBody;

        return flmail($userData->getEmail(), $subject, $mimeBody, $headers);
    }

    /**
     * HTML код строк для таблицы товаров в заказе
     */
    private function generateOrderItemsTableRows($queueItem)
    {
        $itemCount = 1;
        $allComments = $queueItem->getUserInfo()->getComments();

        $out = '';

        foreach ($queueItem->getOrder() as $orderItem) {
            $itemId = $orderItem['id'];
            $itemComment = $orderItem['comment'];

            $productImageUrl = trim($orderItem['image'], '\\/');
            $productImageUrl = str_replace('//www.', '//', $productImageUrl);
            $productImageUrl = str_replace($this->site_url, '', $productImageUrl);

            $productImageUrl = $this->site_url . '/' . $productImageUrl;

            $itemTitle = $orderItem['parent'];
            $itemCode = $orderItem['title'];
            $itemOrderedCount = $orderItem['count'];

            $priceRub = getCurrencyRUB_Reborn($orderItem['price'], $orderItem['currency']);
            
            $displayedNativePrice = getCurrencyChar_Reborn($orderItem['currency']) . $orderItem['price'];
            $displayedRubPrice = getCurrencyChar_Reborn('CUR_RUB') . number_format(
                    $priceRub, 2, '', ''
                );

            $orderItem['catalog'] = ''; // TODO delete

            $itemLink = $this->site_url;
            $itemLink .= $orderItem['catalog'] 
                ? '/' . ltrim($orderItem['catalog'], '\\/') 
                : '?id=' . $itemId;

            $imageExists = true;
            try{
                list($imageWidth, $imageHeight) = imageSize($this->base_path, $productImageUrl, '80x80');
            } catch(Exception $e){
                error_log($e->getMessage());
                $imageExists = false;
            }

            ob_start();

            include ORDER_QUEUE_ROOT_PATH . '/mail-templates/partials/orderItemsExportRows.php';

            $out .= ob_get_clean();

            $itemCount++;
        }

        return $out;
    }

    /**
     * HTML код строк для таблицы экспорта заказа в 1С
     */
    private function generate1cExportRows($queueItem)
    {
        $allComments = $queueItem->getUserInfo()->getComments();

        $out = '';

        foreach ($queueItem->getOrder() as $orderItem) {
            $itemId = $orderItem['id'];
            $itemComment = $orderItem['comment'];
            $itemCode = $orderItem['title'];
            $itemOrderedCount = $orderItem['count'];
            $priceRub = getCurrencyRUB_Reborn($orderItem['price'], $orderItem['currency']);

            ob_start();

            include ORDER_QUEUE_ROOT_PATH . '/mail-templates/partials/1cExportRows.php';

            $out .= ob_get_clean();
        }

        return $out;
    }

    /**
     * Контент файла для экспорта в 1С
     */
    private function generateOrderFile($queueItem, $orderId)
    {
        $out = '';

        foreach ($queueItem->getOrder() as $orderItem) {
            $itemCode = $orderItem['title'];
            $itemOrderedCount = $orderItem['count'];
            $priceRub = getCurrencyRUB_Reborn($orderItem['price'], $orderItem['currency']);

            $out .= $itemCode ."\t". $itemOrderedCount ."\t". (int)$priceRub ."\r\n";
        }

        $out .= "order_num\t" . $orderId . "\r\n";

        return $out;
    }

}
