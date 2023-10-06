<?php

global $modx;

include_once WEBINAR_WATCHERS_ROOT . '/Class/vebinar.class.php';

$webinar = new Webinar();

/**
 * @var $modx object
 */
$id = $modx->documentIdentifier;

$webinarRequest = $webinar->get_all($id);

$vebinars = array();

while ($fetch = $modx->db->getRow($webinarRequest)) {
    // if (strtotime($fetch['date']) > time())
        $vebinars['upcoming'][] = $fetch;
    // else{
    //     if($_GET['tag'] && $_GET['tag'] != $fetch['tag'])
    //         continue;
        $vebinars['passed'][] = $fetch;
    // }
}

if (!empty($vebinars['upcoming'])):?>
    <center>
        <h4>Планируемые вебинары:</h4>
        <h5>выберите вебинары, которые хотели бы посетить</h5>
    </center><br>
    <div class="vebinars-grids">
        <?php foreach (array_reverse($vebinars['upcoming']) as $row): ?>
            <div class="vebinar-grid-item upcoming"
                 data-tag="<?= $row['tag'] ?>"
                 data-id="<?= $row['id'] ?>">
                <p class="vebinar-date" style="font-size: larger;">
                    <i class="fa fa-square-o vebinar-is-selected" aria-hidden="true"></i>
                    <?= date_rus_format($row['date']) ?>
                </p>
                <img src="<?= $row['image'] ?>" alt="" class="vebinar-image"/>
                <a class="webinar-card-link" href="/<?= $row['alias'] ? $row['alias'] : $row['id'] ?>" target="_blank" title="Открыть карточку вебинара">
                    <h3 class="vebinar-title pointer"><?= strip_tags($row['title']) ?></h3>
                </a>
                <button type="button" class="select-vebinar veninar-details" <?=($row['id']==7709282) ? 'style="background: red; "': ''?> data-id="<?= $row['id'] ?>"><?=($row['id']==7709282) ? 'Посетить': 'Подробнее'?>
                </button>
                <span class="vebinar-watchers" title="Количество человек, записавшихся на вебинар">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    <?= $row['watchers'] ?>
                </span>
                <input type="checkbox" name="vebinar_id" value="<?= $row['id'] ?>" style="display: none;"/>
            </div>
        <?php endforeach; ?>
    </div>
    <hr>
    <label style="font-size: 16px;">
        <input type="checkbox" name="check-all" style="display: none;"/>
        <i class="fa fa-square-o" aria-hidden="true"></i>&nbsp;
        Выбрать все
    </label>
    <div id="vebinar-details-description"></div>
    <?php include_once __DIR__ . '/form.php'; ?>
<?php endif; ?>
<?php if (!empty($vebinars['passed'])): ?>
    <br>

    <hr>
    <center><h4>Прошедшие вебинары:</h4></center><br>
    <div class="bd-example" data-modx-identifier="<?= $id ?>">
        <span class="tag tag-<?= !$_GET['tag'] || $_GET['tag'] == 'default' ? 'primary' : 'default' ?> vebinar-filter" data-filter-value="default">Все</span>
        <span class="tag tag-<?= $_GET['tag'] == 'compas' ? 'primary' : 'default' ?> vebinar-filter" data-filter-value="compas">Курс Компас-3D</span>
        <span class="tag tag-<?= $_GET['tag'] == 'solidworks' ? 'primary' : 'default' ?> vebinar-filter" data-filter-value="solidworks">Курс SolidWorks</span>
        <span class="tag tag-<?= $_GET['tag'] == 'vebinar' ? 'primary' : 'default' ?> vebinar-filter" data-filter-value="vebinar">Вебинары</span>
    </div>
    <div class="vebinars-grids passed-vebinars">
        <?php foreach ($vebinars['passed'] as $i => $row): ?>
            <div class="vebinar-grid-item passed <?= $i < 6 ? '' : 'hidden' ?>" 
                 data-tag="<?= $row['tag'] ?>"
                 data-id="<?= $row['id'] ?>" onclick="window.location.href='/<?= $row['uri'] ?>'">
                <p class="vebinar-date" style="font-size: larger;">
                    <?= date_rus_format($row['date'], 1) ?>
                </p>
                <img src="<?= $row['image'] ?>" alt="" class="vebinar-image"/>
                <h3 class="vebinar-title"><?= strip_tags($row['title']) ?></h3>
                <a href="/<?= $row['uri'] ?>">
                    <button class="select-vebinar" data-id="<?= $row['id'] ?>">Смотреть</button>
                </a>
                <span class="vebinar-watchers" title="Количество человек, записавшихся на вебинар">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    <?= $row['watchers'] ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
    <center class="<?= count($vebinars['passed']) > 6 ? '' : 'hidden' ?>">
        <br>
        <br>
        <button class="btn btn-primary" id="show-all-vebinars">Отобразить все</button>
    </center>
<?php endif; ?>

<div class="banner hidden"><span>Заполните регистрационную форму ниже</span></div>
