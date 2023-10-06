<?php

global $modx;

$lock_file = __DIR__ . '/cron.lock';

if(file_exists($lock_file)){
    $lockLastAction = filemtime($lock_file);

    /**
     * Cron мог завершиться после сигнала от системы,
     * который не обрабатывается register_shutdown_function(),
     * поэтому, если файл был создан раньше 1 часа назад,
     * то скорее всего он уже не активен и мы начинаем выполнение
     * скрипта
     */
    if(time() - $lockLastAction < 3600){
        return;
    }
}

file_put_contents($lock_file, 1);
register_shutdown_function(function() use ($lock_file){
    unlink($lock_file);
});

require_once __DIR__ . '/conf.php';

error_reporting(E_ALL);

$dequeueOrder = new DequeueOrder($modx);

while (($item = $dequeueOrder->getNextQueueItem()) !== false) {
    try {
        $dequeueOrder->updateQueueItemStatus($item, ORDER_QUEUE_STATUS_PROCESSING);

        $dequeueOrder->handleItem($item);

        $dequeueOrder->dropFromQueue($item);
        
    } catch(Throwable $e){
	var_dump($e);
	error_log($e);
        $dequeueOrder->updateQueueItemStatus($item, ORDER_QUEUE_STATUS_ERROR);
    }
}
