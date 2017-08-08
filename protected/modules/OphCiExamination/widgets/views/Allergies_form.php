
<div class="field-row row<?= count($element->entries) ? ' hidden' : ''?>" id="<?=$model_name?>_no_allergies_wrapper">
    <div class="large-3 column">
        <label for="<?=$model_name?>_no_allergies">Confirm patient has no allergies:</label>
    </div>
    <div class="large-2 column end">
        <?php echo CHtml::checkBox($model_name .'[no_allergies]', $element->no_allergies_date ? true : false); ?>
    </div>
</div>

<div class="<?= $element->no_allergies_date ? 'hidden' :''?>" id="<?=$model_name?>_form_wrapper">
    <div class="field-row row">
        <div class="large-2 column">
            <label for="<?=$model_name?>_relative_id">Allergy:</label>
        </div>
        <div class="large-3 column end">
            <?php
            $allergies = $element->getAllergyOptions();
            $allergies_opts = array(
                'options' => array(),
                'empty' => '- select -',
            );
            foreach ($allergies as $allergy) {
                $allergies_opts['options'][$allergy->id] = array('data-other' => $allergy->isOther() ? '1' : '0');
            }
            echo CHtml::dropDownList($model_name . '_allergy_id', '', CHtml::listData($allergies, 'id', 'name'), $allergies_opts)
            ?>
        </div>
    </div>

    <div class="field-row row hidden" id="<?= $model_name ?>_other_allergy_wrapper">
        <div class="large-2 column">
            <label for="<?=$model_name?>_other_allergy">Other Allergy:</label>
        </div>
        <div class="large-3 column end">
            <?php echo CHtml::textField($model_name . '_other_allergy', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        </div>
    </div>

    <div class="field-row row">
        <div class="large-2 column">
            <label for="<?= $model_name ?>_comments">Comments:</label>
        </div>
        <div class="large-3 column">
            <?php echo CHtml::textField($model_name . '_comments', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        </div>
        <div class="large-4 column end">
            <button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button>
        </div>
    </div>
</div>