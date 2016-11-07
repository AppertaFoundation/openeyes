<div class="row field-row">
    <div class="large-2 column">
        <label>Previous Studies:</label>
    </div>
    <div class="large-5 column end">
        <ul class="previous_studies_list">
            <?php if($model->previous_studies):?>
                <?php foreach ($model->previous_studies as $study):?>
                    <li>
                        <!--<span class="genetics_relationship_remove">
                            <i class="fa fa-minus-circle" title="Remove Study"></i>
                        </span>-->
                        <input type="hidden" name="GeneticsPatient[previous_studies][]" value="<?=$study->id?>">
                        <?= $study->name ?> - Ended: <?= Helper::convertMySQL2NHS($study->end_date) ?>
                    </li>
                <?php endforeach?>
            <?php else: ?>
                <li>None</li>
            <?php endif;?>
        </ul>
    </div>
</div>