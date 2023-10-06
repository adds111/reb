<?php

require_once __DIR__ . '/conf.php';

error_reporting(E_ALL);

use Exceptions\ValidationException;

$request = $_POST;

$orderCart = array();

//$orderCart = !empty($_SESSION['purchases']) // заполняется в сниппете `shopkeeper`
//    ? unserialize($_SESSION['purchases'])
//    : array();

if (isset($_POST['cart'])) {
    $orderCart = json_decode(base64_decode($_POST['cart']),true);
    unset($_POST['cart']);
}

$isAuthorized = !empty($_SESSION['webInternalKey']);

try{
    $queueOrder = new UpqueueOrder($modx, $request, $orderCart, $isAuthorized);
    $orderId = $queueOrder->push();

    echo "
        Ваш заказ успешно отправлен. 
        После проверки заказа с Вами свяжется менеджер для подтверждения заказа. 
        У менеджера можно будет узнать точную сумму заказа с учетом налога и текущего курса доллара. 
        Также Вы можете позвонить нам и сообщить номер заказа для ускорения процесса его обработки.
    ";
    return;
    
} catch (ValidationException $e){

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    $_SESSION['message'] = '<p class="error">' . $e->getMessage() . '</p>';

    return;
}
