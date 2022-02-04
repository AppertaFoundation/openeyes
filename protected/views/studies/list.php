<?php
$studies = $model->{$list};
$name = get_class($model);
?>
<div class="data-group flex-layout cols-full">
    <div class="cols-5 column">
        <label><?= $label ?>:</label>
    </div>
    <div class="cols-5 column end">
        <ul class="<?= $list ?> studies_list">
            <?php if ($studies) :?>
                <?php foreach ($studies as $study) :?>
                    <?php $participation = $study->participationForSubject($model) ?>
                    <li>
                        <input type="hidden" name="<?=$name?>[<?=$list?>][]" value="<?=$study->id?>">

                        <?php if (date_create($study->end_date) < date_create('now')) : ?>
                            <?= $study->name; ?>
                            - <i>End date: <?= $study->end_date ?></i><br>
                        <?php else : ?>
                            <?php if (isset($edit_status_url, $participation) && $edit_status_url && $participation) :?>
                            <!-- this link is hidden for now -->
                            <a href="<?=$edit_status_url . $participation->id?>?return=<?=Yii::app()->request->requestUri?>" title="Edit Participation" class="hidden edit-study-participation">
                              <i class="oe-i pencil small" aria-hidden="true"></i>
                            </a>

                            <?php endif;?>
                            <?= $study->name ?>
                            <?php if ($participation && $participation->is_consent_given) : ?>
                                - <i>Consent Given: <?= Helper::convertMySQL2NHS($participation->consent_given_on) ?></i><br>
                            <?php endif;?>
                        <?php endif;?>
                    </li>
                <?php endforeach?>
            <?php else : ?>
                <li>None</li>
            <?php endif;?>
        </ul>
    </div>
</div>