<?php
    foreach ($disorder_section->disorders as $disorder) {
        $right_field_base_name = CHtml::modelName($element) . "[right_disorders][{$disorder->id}]";
        $field_base_name = CHtml::modelName($element) . "[disorders][{$disorder->id}]";
        if (!empty($disorder->disorder)) {
            $disorder_name = $disorder->disorder->term;
        } else {
            $disorder_name = $disorder->term_to_display;
        }
    ?>
    <fieldset class="row field-row">
        <div class="column large-4">
            <label><?php echo CHtml::encode($disorder->name); ?></label>
        </div>
        <div class="column large-2 text-center">
            <?php echo CHtml::checkBox($right_field_base_name . "[main_cause]", $element->isCviDisorderMainCauseForSide($disorder, 'right'), array('class' => 'disorder-main-cause', 'disabled' => !$element->isCviDisorderMainCauseForAny($disorder, 'right'), 'data-active' => $element->hasCviDisorderForAny($disorder)));?>
        </div>
        <div class="column large-2 text-center icd10code">
            <?=CHtml::encode($disorder->code)?>
        </div>
        <div class="column large-1 text-center">
            <?php
            echo CHtml::radioButton($field_base_name . "[affected]", $element->hasCviDisorderForSide($disorder, 'right'), array('value' => 2, 'data-group_id' => $disorder->group_id, 'class' => 'affected-selector', 'data-name' => 'Right '.$disorder_name, 'data-code' => $disorder->code, 'data-id' => $disorder->id, 'data-eye' => Eye::RIGHT))
            ?>
        </div>
        <div class="column large-1 text-center">
            <?php
            echo CHtml::radioButton($field_base_name . "[affected]", $element->hasCviDisorderForSide($disorder, 'left'), array('value' => 1, 'data-group_id' => $disorder->group_id, 'class' => 'affected-selector', 'data-name' => 'Left '.$disorder_name, 'data-code' => $disorder->code, 'data-id' => $disorder->id, 'data-eye' => Eye::LEFT))
            ?>
        </div>
        <div class="column large-1 text-center end">
            <?php
            echo CHtml::radioButton($field_base_name . "[affected]", $element->hasCviDisorderForSide($disorder, 'both'), array('value' => 3, 'data-group_id' => $disorder->group_id, 'class' => 'affected-selector', 'data-name' => 'Bilateral '.$disorder_name, 'data-code' => $disorder->code, 'data-id' => $disorder->id, 'data-eye' => Eye::BOTH))
            ?>
        </div>
        <div class="column large-1 text-center end">
            <button class="button button-icon small js-unchecked-diagnosis-element disabled" data-id="<?=CHtml::encode($disorder->id)?>" title="Delete Diagnosis">
                <span class="icon-button-small-mini-cross"></span>
                <span class="hide-offscreen">Remove element</span>
            </button>
        </div>
    </fieldset>
<?php } ?>