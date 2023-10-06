<?php

namespace Mappers;

use DTO\UserData;

class OrderRequestToUserData{

    public function map($request){
        $userData = new UserData();
        
        $userData->setName($request['name'])
            ->setEmail($request['email'])
            ->setPhone($request['phone'])
            ->setCompany($request['company'])
            ->setMessage($request['message'])
            ->setDelivery($request['delivery'])
            ->setAddress($request['address'])
            ->setComments($request['comments'])
            ->setRekvBank($request['rekvBank'])
            ->setRekvCompany($request['rekvCompany'])
            ->setRekvAddress($request['rekvAddress'])
            ->setRekvInn($request['rekvInn'])
            ->setRekvBik($request['rekvBik'])
            ->setRekvKpp($request['rekvKpp'])
            ->setRekvSchet($request['rekvSchet'])
            ->setManager($request['manager']);

        return $userData;
    }

}