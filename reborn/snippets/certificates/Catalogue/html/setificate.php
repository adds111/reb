<li>
    <a href="<?= $link ?>" class="certificates-link" target="_blank" title="<?= $title ?>">
        <?= mb_substr($title, 0, 80, 'UTF-8') . (mb_strlen($title, 'UTF-8') > 80 ? '...' : '')  ?>
    </a>
    <?php if (!$vendor): ?>
        <a href="<?= $link ?>" download>Скачать (<?= $filesize ?>)</a>
    <?php endif; ?>
    <?php if ($extra): ?>
        <span class="extra">
            <a href="<?= $extra['link'] ?>" class="extra" target="_blank"><?= $extra['title'] ?></a>
            <a href="<?= $extra['link'] ?>" download>Скачать (<?= get_filesize($extra['link']) ?>)</a>
        </span>
    <?php endif; ?>
</li>