<?php

namespace DTO;

use Exceptions\ValidationException;
use DTO\UserData;

class QueueItem{

    private $id;
    private $user;
    private $date;
    private $quantity;
    private $totalPriceRub;
    private $order;
    private $userInfo;
    private $filename;
    private $status;
    private $exception;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        if(!$id){
            throw new ValidationException('`id` не может быть пустым значением');
        }

        $this->id = intval($id);

        return $this;
    }

    public function getUser(){
        return $this->user;
    }

    public function setUser($user){
        $this->user = intval($user);

        return $this;
    }

    public function getDate(){
        return $this->date;
    }

    public function setDate($date){
        if(!$date){
            throw new ValidationException('`date` не может быть пустым значением');
        }

        $this->date = intval($date);

        return $this;
    }

    public function getQuantity(){
        return $this->quantity;
    }

    public function setQuantity($quantity){
        if(!$quantity){
            throw new ValidationException('`quantity` не может быть пустым значением');
        }

        $this->quantity = intval($quantity);

        return $this;
    }

    public function getTotalPriceRub(){
        return $this->totalPriceRub;
    }

    public function setTotalPriceRub($total_price_rub){
        $this->totalPriceRub = intval($total_price_rub);

        return $this;
    }

    public function getOrder(){
        return $this->order;
    }

    public function setOrder($order){
        if(!is_array($order)){
            throw new ValidationException('`order` должен быть массивом');
        }

        $this->order = $order;

        return $this;
    }

    public function getUserInfo(){
        return $this->userInfo;
    }

    public function setUserInfo($userInfo){
        $this->userInfo = $userInfo;

        return $this;
    }

    public function getFilename(){
        return $this->filename;
    }

    public function setFilename($filename){
        $this->filename = strval($filename);

        return $this;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = intval($status);

        return $this;
    }

    public function getException(){
        return $this->exception;
    }

    public function setException($exception){
        $this->exception = strval($exception);

        return $this;
    }

}
