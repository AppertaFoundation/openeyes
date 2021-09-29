<?php if ($element_both_eyes) : ?>
<div id="wrapper-auto-generate-events-selector" class="element-both-eyes" data-suffix="<?= $suffix; ?>">
    <div class="flex-layout">
        <?php endif; ?>
        <div class="<?= $width_class; ?>">
            <hr/>
            <?php
            $correspondence_api = \Yii::app()->moduleAPI->get('OphCoCorrespondence');
            $firm = \Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            $macro = $correspondence_api->getDefaultMacro($firm, Yii::app()->session['selected_site_id'], $macro_name);
            if ($macro) { ?>
                <label class="inline highlight">
                    <?= \CHtml::hiddenField('auto_generate_gp_letter_after_' . $suffix, 0); ?>
                    <?= \CHtml::checkBox('auto_generate_gp_letter_after_' . $suffix, $gp_letter_setting); ?>Generate
                    standard GP letter
                </label>
            <?php }
            $macro = $correspondence_api->getDefaultMacro($firm, Yii::app()->session['selected_site_id'], $optom_letter_name);
            if ($macro) { ?>
                <label class="inline highlight">
                    <?= \CHtml::hiddenField('auto_generate_optom_letter_after_' . $suffix, 0); ?>
                    <?= \CHtml::checkBox('auto_generate_optom_letter_after_' . $suffix, $optom_setting); ?>Generate
                    standard Optom letter
                </label>
            <?php } ?>
            <?php if ($drug_set_name) : ?>
                <label class="inline highlight">
                    <?= \CHtml::hiddenField('auto_generate_prescription_after_' . $suffix, 0); ?>
                    <?= \CHtml::checkBox('auto_generate_prescription_after_' . $suffix, $prescription_setting); ?>
                    Generate prescription
                </label>
            <?php endif; ?>
            <?= \CHtml::dropDownList(
                'auto_generate_prescription_after_' . $suffix . '_set_id',
                $default_set_id,
                \CHtml::listData($sets, 'id', 'name'),
                [
                    'empty' => '- Standard Sets -',
                    'style' => 'display:' . ($prescription_setting ? 'inline-block' : 'none')
                ]
            ) ?>

        </div>
        <?php if ($element_both_eyes) : ?>
    </div>
</div>
<?php endif; ?>

