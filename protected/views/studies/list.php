<?php
$studies = $model->{$list};
$name = get_class($model);
?>
<div class="row field-row">
    <div class="large-2 column">
        <label><?= $label ?>:</label>
    </div>
    <div class="large-5 column end">
        <ul class="<?= $list ?> studies_list">
            <?php if($studies):?>
                <?php foreach ($studies as $study):?>
                    <li>
                        <input type="hidden" name="<?=$name?>[<?=$list?>][]" value="<?=$study->id?>">
                        <?= $study->name ?>
                        <?php if($study->end_date < date_create('now')->format('Y-m-d')): ?>
                            - <i>Ended: <?= Helper::convertMySQL2NHS($study->end_date) ?></i>
                        <?php endif;?>
                    </li>
                <?php endforeach?>
            <?php else: ?>
                <li>None</li>
            <?php endif;?>
        </ul>
    </div>
</div>