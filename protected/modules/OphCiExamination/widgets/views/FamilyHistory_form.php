<div class="element-fields">
    <div class="field-row row<?= count($element->entries) ? ' hidden' : ''?>" id="<?=$model_name?>_no_family_history_wrapper">
        <div class="large-3 column">
            <label for="<?=$model_name?>_no_family_history">Confirm patient has no family history:</label>
        </div>
        <div class="large-2 column end">
            <?php echo CHtml::checkBox($model_name .'[no_family_history]', $element->no_family_history_date ? true : false); ?>
        </div>
    </div>

    <div class="<?= $element->no_family_history_date ? 'hidden' :''?>" id="<?=$model_name?>_form_wrapper">
        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?=$model_name?>_relative_id">Relative:</label>
            </div>
            <div class="large-3 column end">
                <?php
                $relatives = $element->getRelativeOptions();
                $relatives_opts = array(
                    'options' => array(),
                    'empty' => '- select -',
                );
                foreach ($relatives as $rel) {
                    $relatives_opts['options'][$rel->id] = array('data-other' => $rel->is_other ? '1' : '0');
                }
                echo CHtml::dropDownList($model_name . '_relative_id', '', CHtml::listData($relatives, 'id', 'name'), $relatives_opts)
                ?>
            </div>
        </div>

        <div class="field-row row hidden" id="<?= $model_name ?>_other_relative_wrapper">
            <div class="large-2 column">
                <label for="<?=$model_name?>_other_relative">Other Relative:</label>
            </div>
            <div class="large-3 column end">
                <?php echo CHtml::textField($model_name . '_other_relative', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
            </div>
        </div>

        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?=$model_name?>side_id">Side:</label>
            </div>
            <div class="large-3 column end">
                <?php echo CHtml::dropDownList($model_name . '_side_id', '', CHtml::listData($element->getSideOptions(), 'id', 'name'))?>
            </div>
        </div>

        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?= $model_name ?>_condition_id">Condition:</label>
            </div>
            <div class="large-3 column end">
                <?php
                $conditions = $element->getConditionOptions();
                $conditions_opts = array(
                    'options' => array(),
                    'empty' => '- select -',
                );
                foreach ($conditions as $con) {
                    $conditions_opts['options'][$con->id] = array('data-other' => $con->is_other ? '1' : '0');
                }
                echo CHtml::dropDownList($model_name . '_condition_id', '', CHtml::listData($conditions, 'id', 'name'), $conditions_opts);
                ?>
            </div>
        </div>

        <div class="field-row row hidden" id="<?= $model_name ?>_other_condition_wrapper">
            <div class="large-2 column">
                <label for="<?= $model_name ?>_other_condition">Other Condition:</label>
            </div>
            <div class="large-3 column end">
                <?php echo CHtml::textField($model_name . '_other_condition', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
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