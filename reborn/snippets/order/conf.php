<?php

global $modx;

// Если вызов скрипта из CLI
if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', true);
    require_once __DIR__ . '/../../../../index.php';
    $modx->getSettings();
}

mb_internal_encoding("UTF-8");
ini_set('post_max_size', '10M');
ini_set('upload_max_filesize', '10M');

define('ORDER_QUEUE_ROOT_PATH', __DIR__);
define('ORDER_QUEUE_FILE_STORAGE_PATH', ORDER_QUEUE_ROOT_PATH . '/storage');

define('ORDER_QUEUE_DB_TABLE', 'orders_queue');

define('ORDER_QUEUE_STATUS_NEW', 0);
define('ORDER_QUEUE_STATUS_PROCESSING', 1);
define('ORDER_QUEUE_STATUS_ERROR', 2);

// Ключ для интеграции с CRM, указывается в настройках интеграции с CRM.
define('ROISTAT_INTEGRATION_KEY', 'MTI0NDM3OjE0MjQxMTo2M2M0Mjk0MGM2M2IyNDljNTY3Zjc0MDg3NDA3MzJlMw==');

require_once __DIR__ . '/src/Exceptions/ValidationException.php';
require_once __DIR__ . '/src/DTO/UserData.php';
require_once __DIR__ . '/src/DTO/QueueItem.php';
require_once __DIR__ . '/src/Mappers/OrderRequestToUserData.php';
require_once __DIR__ . '/src/Mappers/DbRowToQueueItem.php';
require_once __DIR__ . '/src/FileHandler.php';
require_once __DIR__ . '/src/UpqueueOrder.php';
require_once __DIR__ . '/src/DequeueOrder.php';
require_once __DIR__ . '/src/MailHandler.php';
require_once __DIR__ . '/src/helpers.php';

require_once $modx->config['base_path'] . 'assets/snippets/product/roistat_MailReplacer.php';
require_once $modx->config['base_path'] . 'assets/snippets/product/funcs/access-convert_curr-getCurrencyPrice.php';