<?php

$orderUnregistered = file_get_contents(MODX_LAYOUT_PATH . "cart/order-unregistered.html");
$orderUnregisteredForm = file_get_contents(MODX_LAYOUT_PATH . "cart/order-unregistered-form.html");

if ($_SESSION['webValidated'] == 1) {
    header('location: /?id=66475');
}

if (isset($_SESSION['message'])) {
    echo '<div class="message">'. $_SESSION['message'] .'</div>';
    unset($_SESSION['message']);
}

if(!empty($_SESSION['message'])){
    echo
        '<div style="padding: 15px; border: 1px solid #A33838">
	        <h2 style="color: #A33838; margin-top: 0px; margin-bottom: 0px; padding-bottom: 0; padding-top: 0;">'.$_SESSION['message'].'</h2>
        </div>';

    unset($_SESSION['message']);
}

return str_replace('[[+ORDER_FORM+]]', $orderUnregisteredForm, $orderUnregistered);
