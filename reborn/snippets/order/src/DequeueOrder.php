<?php

use Mappers\DbRowToQueueItem;

class DequeueOrder
{

    /** @var DocumentParser */
    private $modx;
    private $mailHandler;

    public function __construct($modx)
    {
        $this->modx = $modx;

        $site_url = rtrim($modx->config['site_url'], '\\/');
        $base_path = rtrim($modx->config['base_path'], '\\/');

        $this->mailHandler = new MailHandler($site_url, $base_path);
    }

    /**
     * Выбираем следующий необработанный заказ из очереди
     */
    public function getNextQueueItem()
    {
        $oldDate = time() - 3600;
        $where = "`status` != " . ORDER_QUEUE_STATUS_PROCESSING . 
            " OR (`status` = " . ORDER_QUEUE_STATUS_PROCESSING . " AND `date` < $oldDate)
        ";

        $itemResource = $this->modx->db->select('*', ORDER_QUEUE_DB_TABLE, $where, 'id ASC', 1);
        $item = $this->modx->db->getRow($itemResource, 'assoc');
        
        if($item === false){
            return false;
        }

        $mapper = new DbRowToQueueItem();
        $queueItem = $mapper->map($item);

        return $queueItem;
    }

    public function updateQueueItemStatus($queueItem, $status)
    {
        $fields = array('status' => $status);
        $where = '`id` = ' . $queueItem->getId();

        $this->modx->db->update($fields, ORDER_QUEUE_DB_TABLE, $where);
    }

    public function handleItem($queueItem)
    {
        $orderId = $this->saveOrderToDb($queueItem);

//        $this->mailHandler->sendToManager($queueItem, $orderId);
        $this->mailHandler->sendToCustomer($queueItem, $orderId);

//        $this->leadToRoistat($queueItem);

        // Удаляем отправленный пользователем файл
        $uploadedFilename = $queueItem->getFilename();
        if(!empty($uploadedFilename)){
            $fileHandler = new FileHandler();
            $fileHandler->unlinkSavedFile($queueItem->getFilename());
        }
    }

    private function saveOrderToDb($queueItem)
    {
        $userInfo = $queueItem->getUserInfo();

        $insertValues = array(
            'user' => $queueItem->getUser(),
            'date' => $queueItem->getDate(),
            'quantity' => $queueItem->getQuantity(),
            'cost' => $queueItem->getTotalPriceRub(),
            'order' => serialize($queueItem->getOrder()),
            'userInfo' => serialize(array(
                'name' => $userInfo->getName(),
                'company' => $userInfo->getCompany(),
                'email' => $userInfo->getEmail(),
                'phone' => $userInfo->getPhone(),
                'message' => $userInfo->getMessage(),
                'delivery' => $userInfo->getDelivery(),
                'address' => $userInfo->getAddress(),
                'comments' => $userInfo->getComments(),
                'rekvBank' => $userInfo->getRekvBank(),
                'rekvCompany' => $userInfo->getRekvCompany(),
                'rekvAddress' => $userInfo->getRekvAddress(),
                'rekvInn' => $userInfo->getRekvInn(),
                'rekvBik' => $userInfo->getRekvBik(),
                'rekvKpp' => $userInfo->getRekvKpp(),
                'rekvSchet' => $userInfo->getRekvSchet(),
                'manager' => $userInfo->getManager(),
            )),
            'roistat_id' => $userInfo->getRoistatParamCompany(),
            'vid' => $userInfo->getVisitorsInfo(),
            'uid' => $userInfo->getYmUid(),
            'uid_ip' => $userInfo->getIp(),
        );

        $insertId = $this->modx->db->insert($insertValues, 'orders');
        if (!$insertId) {
            throw new Exception('Ошибка сохранения заказа');
        }
        
        return $insertId;
    }

    private function leadToRoistat($queueItem)
    {
        $url = 'https://cloud.roistat.com/api/proxy/1.0/leads/add?';

        $userInfo = $queueItem->getUserInfo();

        $comment = ' Компания ' . $userInfo->getCompany();
        $comment .= ' сообщение '. $userInfo->getMessage();
        $comment .= ' способ доставки ' . $userInfo->getDelivery();
        $comment .= ' адрес ' . $userInfo->getAddress(); 
        $comment .= ' банк ' . $userInfo->getRekvBank();
        $comment .= ' ИНН '. $userInfo->getRekvInn();
        $comment .= ' БИК ' . $userInfo->getRekvBik();
        $comment .= ' КПП ' .$userInfo->getRekvKpp();
        $comment .= ' Номер счета ' .$userInfo->getRekvSchet();

        $roistatData = array(
            'roistat' => $userInfo->getRoistatVisitNum() !== '' 
                ? $userInfo->getRoistatVisitNum()
                : 'nocookie',
            'key'     => ROISTAT_INTEGRATION_KEY,
            'title'   => 'Заказ из корзины',
            'comment' => $comment,
            'name'    => $userInfo->getName(),
            'email'   => $userInfo->getEmail(),
            'phone'   => $userInfo->getPhone(),
            'fields'  => array(
                'order' => json_encode($queueItem->getOrder()),
                'form' => 'Корзина'
            ),
        );

        $url = $url . http_build_query($roistatData);

        sendCurlGetRequest($url);
    }

    public function dropFromQueue($queueItem){
        $where = 'id = ' . $queueItem->getId();

        $res = $this->modx->db->delete(ORDER_QUEUE_DB_TABLE, $where);
        if(!$res){
            throw new Exception('Ошибка удаления заказа из очереди');
        }
    }
    
}
