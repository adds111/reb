<style type="text/css">
    *,
    html {
        font-size: 18px;
    }

    .center {
        text-align: center;
    }

    .order_list {
        border-collapse: collapse;
        margin-top: 20px;
    }

    .order_list th {
        background-color: black;
        color: white;
    }

    .order_list th,
    .order_list td {
        padding: 5px;
        border: 1px solid gray;
    }

    th {
        font-weight: bold;
    }

    h1 {
        font-size: 25px;
    }

    .order_list_1c td {
        padding: 3px 10px;
    }

    .order_list_1c {
        margin-top: 30px;
    }
</style>

<h3>ИНВОЙС: <?= $orderId ?></h3>

<p>
    <a title="Перейти на сайт" href="<?= $orderLink ?>">
        Заказ № <?= $orderId ?>
    </a>
</p>

</br>

<h2>Заказчик:</h2>
<table class="info" border="1" cellpadding="5">
    <tr>
        <td><b>ФИО</b></td>
        <td><?= $userData->getName() ?></td>
    <tr>
    <tr>
        <td><b>Компания</b></td>
        <td><?= $userData->getCompany() ?></td>
    <tr>
    <tr>
        <td><b>Телефон</b></td>
        <td><?= $userData->getPhone() ?></td>
    <tr>
    <tr>
        <td><b>Email</b></td>
        <td>
            <a href="mailto:<?= $userData->getEmail() ?>?subject=Заказ продукции Fluid-Line">
                <?= $userData->getEmail() ?>
            </a>
        </td>
    <tr>
    <tr>
        <td><b>Адрес</b></td>
        <td><?= $userData->getAddress() ?></td>
    <tr>
    <tr>
        <td><b>Доставка</b></td>
        <td><?= $userData->getDelivery() ?></td>
    <tr>
    <tr>
        <td><b>Номер визита ROISTAT</b></td>
        <td><?= stripslashes($userData->getRoistatVisitNum()) ?></td>
    <tr>
</table>

<h2>Реквизиты:</h2>

<table class="info" border="1" cellpadding="5">
    <tr>
        <td><b>Точное наименование компании</b></td>
        <td><?= stripslashes($userData->getRekvCompany()) ?></td>
    </tr>
    <tr class="hidden">
        <td><b>Юридический адрес</b></td>
        <td><?= stripslashes($userData->getRekvAddress()) ?></td>
    </tr>
    <tr>
        <td><b>ИНН</b></td>
        <td><?= stripslashes($userData->getRekvInn()) ?></td>
    </tr>
    <tr class="hidden">
        <td><b>КПП</b></td>
        <td><?= stripslashes($userData->getRekvKpp()) ?></td>
    </tr>
    <tr class="hidden">
        <td><b>Банк</b></td>
        <td><?= stripslashes($userData->getRekvBank()) ?></td>
    </tr>
    <tr class="hidden">
        <td><b>БИК Банка</b></td>
        <td><?= stripslashes($userData->getRekvBik()) ?></td>
    </tr class="hidden">
        <td><b>Расчетный счет</b></td>
        <td><?= stripslashes($userData->getRekvSchet()) ?></td>
    </tr>
</table>

<h2>Заказ:</h2>

<p><?= $userData->getMessage() ?></p>

<table class="order_list" border="1" cellpadding="5">
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

<table class="order_list_1c" style="border: 1px solid gray;">
    <tr>
        <th colspan=4>Для 1С (неактуально! пользуйтесь файлом!)</th>
    </tr>
    <?= $exportTo1cRows ?>
    <tr>
        <th colspan="4">Параметры заказа:</th>
    </tr>
    <tr>
        <td colspan='2'>order_num</td>
        <td colspan='2'><?= $orderId ?></td>
    </tr>
    <tr>
        <td colspan='2'>cookie_order_num</td>
        <td colspan='2'></td>
    </tr>
</table>
