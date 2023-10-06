<?php

namespace DTO;

use Exceptions\ValidationException;

class UserData{

    private $name;
    private $email;
    private $phone;
    private $company = '';
    private $message = '';
    private $delivery = '';
    private $address = '';
    private $comments = array();
    private $rekvBank = '';
    private $rekvCompany = '';
    private $rekvAddress = '';
    private $rekvInn = '';
    private $rekvBik = '';
    private $rekvKpp = '';
    private $rekvSchet = '';
    private $manager = '';
    private $roistatVisitNum = '';
    private $roistatEmail = '';
    private $roistatParamCompany = '';
    private $visitorsInfo = '';
    private $ymUid = '';
    private $ip = '';

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $name = strval($name);

        if($name === ''){
            throw new ValidationException('Имя должно быть заполнено');
        }

        $this->name = mb_substr(strip_tags($name), 0, 50);

        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $email = strval($email);

        if($email === ''){
            throw new ValidationException('Email должен быть заполнен');
        }

        $this->email = mb_substr(strip_tags($email), 0, 100);

        return $this;
    }

    public function getPhone(){
        return $this->phone;
    }

    public function setPhone($phone){
        $phone = strval($phone);

        if($phone === ''){
            throw new ValidationException('Телефон должен быть заполнен');
        }

        $this->phone = mb_substr(strip_tags($phone), 0, 70);

        return $this;
    }

    public function getCompany(){
        return $this->company;
    }

    public function setCompany($company){
        $this->company = mb_substr(strip_tags($company), 0, 150);

        return $this;
    }

    public function getMessage(){
        return $this->message;
    }

    public function setMessage($message){
        $this->message = strip_tags($message);

        return $this;
    }

    public function getDelivery(){
        return $this->delivery;
    }

    public function setDelivery($delivery){
        $this->delivery = mb_substr(strip_tags($delivery), 0, 20);

        return $this;
    }

    public function getAddress(){
        return $this->address;
    }

    public function setAddress($address){
        $this->address = mb_substr(strip_tags($address), 0, 220);

        return $this;
    }

    public function getComments(){
        return $this->comments;
    }

    public function setComments($comments){

        $comments = explode('|@|', $comments);
        if(!empty($comments)){

            foreach ($comments as $value) {
                if (!empty($value)) {
                    $arr = explode(':&:', $value);
                    $arr[0] = (int)$arr[0];
                    $arr[1] = mb_substr(strip_tags($arr[1]), 0, 300);
    
                    if (!empty($arr[1])) {
                        $this->comments[$arr[0]] = $arr[1];
                    }
                }
            }
        }

        return $this;
    }

    public function getRekvBank(){
        return $this->rekvBank;
    }

    public function setRekvBank($rekvBank){
        $this->rekvBank = mb_substr(strip_tags($rekvBank), 0, 100);

        return $this;
    }

    public function getRekvCompany(){
        return $this->rekvCompany;
    }

    public function setRekvCompany($rekvCompany){
        $this->rekvCompany = mb_substr(strip_tags($rekvCompany), 0, 100);

        return $this;
    }

    public function getRekvAddress(){
        return $this->rekvAddress;
    }

    public function setRekvAddress($rekvAddress){
        $this->rekvAddress = mb_substr(strip_tags($rekvAddress), 0, 200);

        return $this;
    }

    public function getRekvInn(){
        return $this->rekvInn;
    }

    public function setRekvInn($rekvInn){
        $this->rekvInn = mb_substr(strip_tags($rekvInn), 0, 70);

        return $this;
    }

    public function getRekvBik(){
        return $this->rekvBik;
    }

    public function setRekvBik($rekvBik){
        $this->rekvBik = mb_substr(strip_tags($rekvBik), 0, 70);

        return $this;
    }

    public function getRekvKpp(){
        return $this->rekvKpp;
    }

    public function setRekvKpp($rekvKpp){
        $this->rekvKpp = mb_substr(strip_tags($rekvKpp), 0, 100);

        return $this;
    }

    public function getRekvSchet(){
        return $this->rekvSchet;
    }

    public function setRekvSchet($rekvSchet){
        $this->rekvSchet = mb_substr(strip_tags($rekvSchet), 0, 100);

        return $this;
    }

    public function getManager(){
        return $this->manager;
    }

    public function setManager($manager){
        $this->manager = mb_substr(strip_tags($manager), 0, 15);;

        return $this;
    }

    public function getRoistatEmail(){
        return $this->roistatEmail;
    }

    public function setRoistatEmail($roistatEmail){
        $this->roistatEmail = $roistatEmail;

        return $this;
    }

    public function getRoistatVisitNum(){
        return $this->roistatVisitNum;
    }

    public function setRoistatVisitNum($roistatVisitNum){
        $this->roistatVisitNum = $roistatVisitNum;

        return $this;
    }

    public function getRoistatParamCompany(){
        return $this->roistatParamCompany;
    }

    public function setRoistatParamCompany($roistatParamCompany){
        $this->roistatParamCompany = $roistatParamCompany;

        return $this;
    }

    public function getVisitorsInfo(){
        return $this->visitorsInfo;
    }

    public function setVisitorsInfo($visitorsInfo){
        $this->visitorsInfo = $visitorsInfo;

        return $this;
    }

    public function getYmUid(){
        return $this->ymUid;
    }

    public function setYmUid($ymUid){
        $this->ymUid = $ymUid;

        return $this;
    }

    public function getIp(){
        return $this->ip;
    }

    public function setIp($ip){
        $this->ip = $ip;

        return $this;
    }
}