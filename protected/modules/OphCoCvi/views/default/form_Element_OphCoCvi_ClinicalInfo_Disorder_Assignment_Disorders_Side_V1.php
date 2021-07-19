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
        <tr>
            <td><?= CHtml::encode($disorder->name); ?></td>
            <td>
                <label class="inline highlight ">
                    <?= \CHtml::checkBox($right_field_base_name . "[main_cause]", $element->isCviDisorderMainCauseForSide($disorder, 'right'), array(
                            'class' => 'disorder-main-cause',
                            'disabled' => !$element->isCviDisorderMainCauseForAny($disorder, 'right'),
                            'data-active' => $element->hasCviDisorderForAny($disorder),
                        ),
                    );
                    ?>Main cause
                </label>
            </td>
            <td data-group_id="<?=$disorder->group_id;?>" data-disorder_id="<?=$disorder->id;?>">
                <label class="inline highlight icd10code">
                    <?=CHtml::encode($disorder->code)?>
                </label>

                <?php $this->widget('application.widgets.EyeSelector', [
                    'inputNamePrefix' => $field_base_name,
                    'selectedEyeId' => $element->getCviDisorderSide($disorder),
                    'template' => "{Right}{Left}"
                ]);?>
            </td>
<!--            <td>-->
<!--                <button class="button button-icon small js-unchecked-diagnosis-element disabled" data-id=" php CHtml::encode($disorder->id) " title="Delete Diagnosis">-->
<!--                    <span class="icon-button-small-mini-cross"></span>-->
<!--                    <span class="hide-offscreen">Remove element</span>-->
<!--                </button>-->
<!--            </td>-->
        </tr>
<?php } ?>