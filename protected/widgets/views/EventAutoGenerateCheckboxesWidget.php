<tr class="suffix-container" data-suffix="<?= $suffix; ?>">
    <td>Generate the following:</td>
    <td>
        <div class="flex-l">
            <?php $correspondence_api = \Yii::app()->moduleAPI->get('OphCoCorrespondence');
            $firm = \Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            $macro = $correspondence_api->getDefaultMacro($firm, Yii::app()->session['selected_site_id'], $macro_name);
            if ($macro) { ?>
                <label class="highlight inline">
                    <?= \CHtml::hiddenField('auto_generate_gp_letter_after_' . $suffix, 0); ?>
                    <?= \CHtml::checkBox('auto_generate_gp_letter_after_' . $suffix, $gp_letter_setting,
                        ['class' => 'js-auto-generate-event-checkbox', 'data-test' => 'generate-standard-gp-letter',
                            'data-event-type' => EventAutoGenerateEsign::CORRESPONDENCE]); ?>GP letter (standard)
                </label>
            <?php } ?>
            <?php
            $macro = $correspondence_api->getDefaultMacro($firm, Yii::app()->session['selected_site_id'], $optom_letter_name);
            if ($macro) { ?>
                <label class="highlight inline">
                    <?= \CHtml::hiddenField('auto_generate_optom_letter_after_' . $suffix, 0); ?>
                    <?= \CHtml::checkBox('auto_generate_optom_letter_after_' . $suffix, $optom_setting,
                        ['class' => 'js-auto-generate-event-checkbox', 'data-test' => 'generate-optom-letter',
                            'data-event-type' => EventAutoGenerateEsign::CORRESPONDENCE]); ?>Optom letter
                    (standard)</label>

            <?php } ?>
            <?php if ($drug_set_name) { ?>
                 | 

                <label class="highlight inline">
                    <?= \CHtml::hiddenField('auto_generate_prescription_after_' . $suffix, 0); ?>
                    <?= \CHtml::checkBox('auto_generate_prescription_after_' . $suffix, $prescription_setting,
                        ['class' => 'js-auto-generate-event-checkbox', 'data-test' => 'generate-prescription',
                            'data-event-type' => EventAutoGenerateEsign::PRESCRIPTION]); ?>
                    Generate prescription
                </label>
                <?= \CHtml::dropDownList(
                    'auto_generate_prescription_after_' . $suffix . '_set_id',
                    $default_set_id,
                    \CHtml::listData($sets, 'id', 'name'),
                    [
                        'empty' => '… select set',
                        'style' => 'display:' . ($prescription_setting ? 'inline-block' : 'none'),
                        'data-test' => 'drug-sets-list'
                    ]
                ) ?>
            <?php } ?>
        </div>
    </td>
</tr>
<?php
$this->widget('EventAutoGenerateEsign', [
    'signature_field_name' => EventAutoGenerateCheckboxesWidget::SIGNATURE_INPUT_NAME,
    'save_as_draft_field_name' => EventAutoGenerateCheckboxesWidget::SAVE_AS_DRAFT_INPUT_NAME,
    'container_id' => $this->container_id,

]); ?>