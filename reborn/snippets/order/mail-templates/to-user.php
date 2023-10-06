<style type='text/css'>
    *, html { font-size: 18px; }
    .center { text-align: center; }
    .order_list {
        border-collapse: collapse;
        margin-top: 20px;
    }
    .order_list th {
        background-color: black;
        color: white;
    }
    .order_list th, .order_list td {
        padding: 5px;
        border: 1px solid gray;
    }
    th { font-weight: bold; }
    h1 { font-size: 25px; }

    .order_list_1c td { padding: 3px 10px; }
    .order_list_1c { margin-top: 30px; }
</style>

Ваш заказ №<?= $orderId ?> получен и ожидает обработки нашими менеджерами. 
После обработки с Вами свяжется менеджер для подтверждения заказа.

<br><br>

<h2>Ваши данные:</h2>
<table class='info' border='1' cellpadding='5'>
    <tr>
        <td><b>ФИО</b></td>
        <td><?= stripslashes($userData->getName()) ?></td>
    <tr>
    <tr>
        <td><b>Компания</b></td>
        <td><?= stripslashes($userData->getCompany()) ?></td>
    <tr>
    <tr>
        <td><b>Телефон</b></td>
        <td><?= stripslashes($userData->getPhone()) ?></td>
    <tr>
    <tr>
        <td><b>Email</b></td>
        <td><?= stripslashes($userData->getEmail()) ?></td>
    <tr>
    <tr>
        <td><b>Адрес</b></td>
        <td><?= stripslashes($userData->getAddress()) ?></td>
    <tr>
    <tr>
        <td><b>Доставка</b></td>
        <td><?= stripslashes($userData->getDelivery()) ?></td>
    <tr>
</table>

<h2>Реквизиты:</h2>

<table class='info' border='1' cellpadding='5'>
    <tr>
        <td><b>Точное наименование компании</b></td>
        <td><?= stripslashes($userData->getRekvCompany()) ?></td>
    </tr>
    <tr>
        <td><b>Юридический адрес</b></td>
        <td><?= stripslashes($userData->getRekvAddress()) ?></td>
    </tr>
    <tr>
        <td><b>ИНН</b></td>
        <td><?= stripslashes($userData->getRekvInn()) ?></td>
    </tr>
    <tr>
        <td><b>КПП</b></td>
        <td><?= stripslashes($userData->getRekvKpp()) ?></td>
    </tr>
    <tr>
        <td><b>Банк</b></td>
        <td><?= stripslashes($userData->getRekvBank()) ?></td>
    </tr>
    <tr>
        <td><b>БИК Банка</b></td>
        <td><?= stripslashes($userData->getRekvBik()) ?></td>
    </tr>
        <td><b>Расчетный счет</b></td>
        <td><?= stripslashes($userData->getRekvSchet()) ?></td>
    </tr>
</table>

<h2>Заказ:</h2>

<p><?= $userData->getMessage() ?></p>

<table class='order_list' border='1' cellpadding='5'>
    <tr>
        <th>№</th>
        <th></th>
        <th>категория</th>
        <th>кодировка</th>
        <th>кол-во</th>
        <th>цена $(р)</th>
        <th>ссылка</th>
    </tr>
    <?= $orderItemsRows ?>
</table>

<br>

Обратите внимание, что цены могут отличаться от указанных на сайте из-за курса валют, 
налога и других причин. Точную цену вам сообщит менеджер.