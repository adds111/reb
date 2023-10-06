<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/snippets/lib/lib.php';
/**
 * @var $modx object
 */
$userid = $modx->getLoginUserID();
$userinfo = $userid ? $modx->getWebUserInfo($userid) : array();
?>
<form id="registration-form" style="display: none;" class="validate-form" onsubmit="return false;"
      action="/assets/snippets/vebinar-class/vebinar.class.php" method="post">
    <br><br>

    <div class="inputs">
        <input type="hidden" name="roistat_id" value="<?=  $_COOKIE['roistat_param_company'] ?>">
        <div class="input">
            <input type="email" name="email" placeholder="Email" value="<?= $userinfo['email'] ?>"
                   required/>
            <span class="alert"></span>
        </div>
        <div class="input">
            <input type="text" name="name" placeholder="ФИО" value="<?= $userinfo['fullname'] ?>" required/>
        </div>
        <div class="input">
            <input type="tel" name="phone" placeholder="+7 (999)-999-99-99"
                   value="<?= $userinfo['phone'] ?>" required/>
        </div>
        <div class="input">
            <input type="text" name="position" placeholder="Должность" value="<?= $userinfo['doljnost'] ?>" required/>
        </div>
        <div class="input company-list-container">
            <input type="text" name="company" placeholder="Компания" autocomplete="off" value="<?= $userinfo['fax'] ?>" required/>
            <ul id="company-list"></ul>
        </div>
        <div class="checko" style="margin-bottom: 5px; display: flex; align-items: center; justify-content: space-between">
            <label>
                <input type="checkbox" name="whatsApp_reminder" value="1" style="display: none;" checked/>
                <i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;
                Оповестить по WhatsApp
            </label>
            <label>
                <input type="checkbox" name="sms_reminder"  value="1" style="display: none;" checked/>
                <i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;
                Оповестить по SMS
            </label>
        </div>
    </div>

    <button class="button" type="submit">Участвовать в выбранных вебинарах</button>
    <hr>
    <center style="color: red">
        <i>
            Eсли у Вас проблемы с регистрацией. Пришлите ваш e-mail, телефон и список вебинаров, которые хотите
            прослушать на почту <a href="mailto:avp@fluid-line.ru">avp@fluid-line.ru</a>, и мы Вас добавим.
        </i>
    </center>

</form>



