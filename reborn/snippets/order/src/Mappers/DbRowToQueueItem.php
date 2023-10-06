<?php

namespace Mappers;

use DTO\QueueItem;

class DbRowToQueueItem{

    public function map($dbRow)
    {
        $queueItem = new QueueItem();

        $queueItem
            ->setId($dbRow['id'])
            ->setUser($dbRow['user'])
            ->setDate($dbRow['date'])
            ->setQuantity($dbRow['quantity'])
            ->setTotalPriceRub($dbRow['total_price_rub'])
            ->setOrder(unserialize($dbRow['order']))
            ->setUserInfo(unserialize($dbRow['userInfo']))
            ->setFilename($dbRow['filename'])
            ->setStatus($dbRow['status'])
            ->setException($dbRow['exception']);

        return $queueItem;
    }

}