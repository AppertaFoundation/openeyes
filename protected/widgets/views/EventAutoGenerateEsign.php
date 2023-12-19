<?php
$pin_field_name = 'js-event-auto-generate-pin-field';
$draft_field_name = 'js-event-auto-generate-draft-field';
$is_signature_signed = $this->signature->isSigned(); ?>

<tr class="<?= $pin_field_name ?>">
    <?php
    if ($this->fieldIsShown()) {
        $this->widget('EventAutoGenerateEsignField', [
            'field_name' => $this->signature_field_name, 'signature' => $this->signature,
            'is_signed' => $is_signature_signed]);
    } ?>
</tr>
<tr class="<?= $draft_field_name ?>">
    <td>Save as Draft</td>
    <td>
        <div class="flex-l">
            <label class="highlight inline js-save-as-draft-containers">
                <?= CHtml::hiddenField($this->save_as_draft_field_name . "[" . $this::CORRESPONDENCE . "]", 0,
                    ['data-event-type' => $this::CORRESPONDENCE]); ?>
                <?= CHtml::checkBox($this->save_as_draft_field_name . "[" . $this::CORRESPONDENCE . "]",
                    ${'save_as_draft_value_' . $this::CORRESPONDENCE},
                    ['class' => 'js-save-as-draft-inputs', 'data-event-type' => $this::CORRESPONDENCE, 'data-test' => 'save-as-draft-' . $this::CORRESPONDENCE]) ?>
                Save as draft <?= $this::CORRESPONDENCE ?>
            </label>
            <?php
            $prescription_hidden_input_value = 0;
            if (!$this->user_can_create_prescriptions) {
                $prescription_hidden_input_value = 1;
            }
            ?>
            <label class="highlight inline js-save-as-draft-containers">
                <?= CHtml::hiddenField($this->save_as_draft_field_name . "[" . $this::PRESCRIPTION . "]",
                    $prescription_hidden_input_value,
                    ['data-event-type' => $this::PRESCRIPTION]); ?>
                <?= CHtml::checkBox($this->save_as_draft_field_name . "[" . $this::PRESCRIPTION . "]",
                    ${'save_as_draft_value_' . $this::PRESCRIPTION},
                    [
                        'class' => 'js-save-as-draft-inputs',
                        'data-cannot-be-enabled' => !$this->user_can_create_prescriptions,
                        'data-event-type' => $this::PRESCRIPTION,
                        'data-test' => 'save-as-draft-' . $this::PRESCRIPTION,
                        'disabled' => !$this->user_can_create_prescriptions
                    ]) ?>
                Save as draft <?= $this::PRESCRIPTION ?>

            </label>
            <?php if (!$this->user_can_create_prescriptions) { ?>
                <i class='oe-i info small pad js-has-tooltip js-no-prescribe-rights-tooltip'
                   data-test="no-prescribe-rights-tooltip"
                   data-tooltip-content='Because you do not have prescribe rights, you can only generate draft prescriptions.'></i>
            <?php } ?>
        </div>
    </td>
</tr>
<script>
    const options = {
        'pin_required_for_event_type': <?=json_encode($this->pin_required_for_event_type)?>,
        'container_selector': '#<?=$this->container_id?>',
        'pin_container_selector': '.<?=$pin_field_name?>',
        'draft_container_selector': '.<?=$draft_field_name?>',
    };

    new OpenEyes.UI.EventAutoGenerateEsign(options)
</script>
