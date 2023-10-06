<tr>
    <td class='center'>
        <?= $itemCount ?>
    </td>
    <td class='center'>
        <?php if($imageExists): ?>
            <img 
                src="<?= $productImageUrl ?>" width="<?= $imageWidth ?>" height="<?= $imageHeight ?>" 
            />
        <?php endif ?>
    </td>
    <td><?= $itemTitle ?></td>
    <td><?= $itemCode ?></td>
    <td class='center'><?= $itemOrderedCount ?></td>
    <td class='center'>
        <?= $displayedNativePrice ?> (<?= $displayedRubPrice ?>)
    </td>
    <td>
        <a href="<?= $itemLink ?>">
            <?= $itemId ?>
        </a>
    </td>
</tr>
<tr style='color:red;'>
    <td colspan='7'>
        <?= $itemComment ?>
    </td>
</tr>