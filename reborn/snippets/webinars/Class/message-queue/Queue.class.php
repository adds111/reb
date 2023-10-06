<?php

namespace vebinarClass\messageQueue;

require_once __DIR__ . '/QueueItem.class.php';

use \Exception;
use \database;
use \vebinarClass\messageQueue\QueueItem;

class Queue{

    private $typeMessageHandlers = array(
        'sms'      => 'vebinarClass\\messageQueue\\Handlers\\Smsc',
        'whatsapp'  => 'vebinarClass\\messageQueue\\Handlers\\Whatsapp',
    );

    private $sendStatuses = array(
        'not_sended' => 0,
        'sending' => 1,
        'sended' => 2,
    );

    private $database;
    private $handlersDir;

    public function __construct()
    {
        $this->database = database::getInstance();
        $this->handlersDir = __DIR__ . '/Handlers';

        $this->loadHandlers();
    }

    public function getLastSendingsStatuses($count)
    {
        $status_sended = $this->sendStatuses['sended'];

        return $this->database->Select("
            SELECT
                `message`, 
                `add_date`, 
                `message_type`, 
                COUNT(*) as count_summary,
                COUNT(IF(`send_status` = $status_sended, 1, NULL)) as count_sended,
                COUNT(IF(`error_message` IS NOT NULL, 1, NULL)) as count_errors
            FROM vebinar_message_queue 
            GROUP BY `message`, `add_date`, `message_type`
            ORDER BY `add_date` DESC
            LIMIT $count
        ");
    }

    /**
     * Поставить сообщение в очередь на отправку
     * 
     * @param $vebinar_id - id вебинара
     * @param $user_contacts[] - массив из контактов пользователей
     * @param $message - текст сообщения для рассылки
     * @param $messageType - тип сообщения (см. $this->typeMessageHandlers)
     */
    public function enqueueMessages($vebinar_id, $user_contacts, $message, $messageType)
    {
        if(!array_key_exists($messageType, $this->typeMessageHandlers)){
            throw new Exception("Не существует способа отправки через '$messageType'");
        }

        if(!$this->database->beginTransaction()){
            throw new Exception('Не удалось инициализировать транзакцию в базе данных, все сообщения отменены');
        }

        foreach($user_contacts as $user_contact) {

            $status = $this->sendStatuses['not_sended'];
            $add_date = date('Y-m-d H:i:s');

            $sql = "
                INSERT INTO `vebinar_message_queue` (
                    `vebinar_id`, `message`, `user_contact`, `message_type`, `send_status`, `add_date`
                ) VALUES (
                    $vebinar_id, '$message', '$user_contact', '$messageType', $status, '$add_date'
                )
            ";
            
            try{
                $this->database->QueryInsert($sql);
            } catch (Exception $e){
                $this->database->rollBackTransaction();
                throw $e;
            }
        }
        
        if(!$this->database->commitTransaction()){
            throw new Exception('Не удалось закрыть транзакцию в базе данных, все сообщения отменены');
        }
    }

    /**
     * Отправка сообщений из очереди. Вызывается через cron
     */
    public function runQueue()
    {
        $lock_file = __DIR__ . '/lock';

        if(\file_exists($lock_file)){
            //Cron already running
            return;
        }

        //cron lock
        if(\file_put_contents($lock_file, true) === false) {
            throw new Exception('Failed create lock file');
        };

        //cron unlock
        register_shutdown_function(function($lock_file) {
            if(!unlink($lock_file)) {
                echo 'error: Failed remove cron lock file';
            }
        }, $lock_file);
       
        try{
            while(($queueItem = $this->getMessageFromQueue()) !== false) {
               
                try{
                    $this->handleMessage(
                        $queueItem->getMessage(), 
                        $queueItem->getUserContact(), 
                        $queueItem->getMessageType()
                    );

                    $queueItem->setAndPersistSendStatus($this->sendStatuses['sended']);
                    // Set `send_date`
                    $queueItem->setAndPersistSendDate();

                } catch(Exception $e) {
                    // Set error message
                    $queueItem->setAndPersistErrorMessage($e->getMessage());
                    $queueItem->setAndPersistSendStatus($this->sendStatuses['sended']);
                }
            };
        } catch(Exception $e) {
            echo 'error: ' . $e->getMessage();
        }
    }

    /**
     * Отправка сообщения обработчиком его типа
     * 
     * @param string $text - текст сообщения
     * @param string $contact - контакт пользователя, на который отправить
     * @param string $type - тип сообщения
     */
    private function handleMessage($text, $contact, $type)
    {
        if(!array_key_exists($type, $this->typeMessageHandlers)){
            throw new Exception("Unknown message type '$type'");
        }

        $handlerClass = $this->typeMessageHandlers[$type];
        $handler = new $handlerClass;

        $handler->sendMessage($contact, $text);
    }

    private function getMessageFromQueue()
    {
        if(!$this->database->beginTransaction()){
            throw new Exception('Не удалось инициализировать транзакцию в базе данных');
        }

        try{
            $status = $this->sendStatuses['not_sended'];

            $response = $this->database->Select("
                SELECT * FROM `vebinar_message_queue`
                WHERE `send_status` = $status LIMIT 1 FOR UPDATE
            ");

            $result = isset($response[0]) ? $response[0] : false;
            $queueItem = false;

            if($result !== false){
                $queueItem = new QueueItem(
                    $result['id'],
                    $result['vebinar_id'],
                    $result['message'],
                    $result['user_contact'],
                    $result['message_type'],
                    $result['send_status'],
                    $result['add_date'],
                    $result['error_message'],
                    $result['send_date']
                );

                $queueItem->setAndPersistSendStatus($this->sendStatuses['sending']);
            }
        } catch (Exception $e) {
            $this->database->rollBackTransaction();
            throw $e;
        }

        if(!$this->database->commitTransaction()){
            throw new Exception('Не удалось закрыть транзакцию в базе данных');
        }

        return $queueItem;
    }

    private function loadHandlers()
    {
        if(!is_dir($this->handlersDir)){
            throw new Exception("Не удалось подключить обработчики сообщений по пути '" . $this->handlersDir . "'");
        }

        $handlersPaths = array_diff(scandir($this->handlersDir), array('..', '.'));
        foreach($handlersPaths as $path){

            $dir = rtrim($this->handlersDir, '/');
            require_once $dir . '/' . $path;
        }
    }
    
}