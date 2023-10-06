<?php

namespace vebinarClass\messageQueue;

use \Exception;
use \database;

class QueueItem{

    private $database;

    private $id;
    private $vebinar_id;
    private $message;
    private $user_contact;
    private $message_type;
    private $send_status;
    private $add_date;
    private $error_message;
    private $send_date;

    public function __construct(
        $id,
        $vebinar_id,
        $message,
        $user_contact,
        $message_type,
        $send_status,
        $add_date,
        $error_message = null,
        $send_date = null
    )
    {
        $this->database = database::getInstance();

        $this->id = $id;
        $this->vebinar_id = $vabinar_id;
        $this->message = $message;
        $this->user_contact = $user_contact;
        $this->message_type = $message_type;
        $this->send_status = $send_status;
        $this->add_date = $add_date;
        $this->error_message = $error_message;
        $this->send_date = $send_date;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getUserContact()
    {
        return $this->user_contact;
    }

    public function getMessageType()
    {
        return $this->message_type;
    }

    public function setAndPersistSendStatus($status_code)
    {
        $id = $this->id;

        $this->database->Query("
            UPDATE `vebinar_message_queue`
            SET `send_status` = $status_code
            WHERE id = $id
        ");

        $this->send_status = $status_code;
    }

    public function setAndPersistSendDate()
    {
        $id = $this->id;
        $date = date('Y-m-d H:i:s');

        // Set `send_date`
        $this->database->Query("
            UPDATE `vebinar_message_queue`
            SET `send_date` = '$date'
            WHERE id = $id
        ");

        $this->send_date = $date;
    }

    public function setAndPersistErrorMessage($error_message)
    {
        $id = $this->id;

        // Set `send_date`
        $this->database->Query("
            UPDATE `vebinar_message_queue`
            SET `error_message` = '$error_message'
            WHERE id = $id
        ");

        $this->error_message = $error_message;
    }
    
}