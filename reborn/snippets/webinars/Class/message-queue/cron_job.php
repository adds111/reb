<?php

file_put_contents(__DIR__ . "/cron.log", date("d m Y H:i:s")."\n", FILE_APPEND);

require_once __DIR__ . '/../../../../manager/includes/config.inc.php';
require_once __DIR__ . '/../../lib/database.class.php';
require_once __DIR__ . '/Queue.class.php';

database::getInstance()->Connect(
    $GLOBALS['database_user'], 
    $GLOBALS['database_password'], 
    $GLOBALS['dbase'], 
    $GLOBALS['database_server'], 
    $GLOBALS['database_port']
);

$queue = new vebinarClass\messageQueue\Queue();
$queue->runQueue();
