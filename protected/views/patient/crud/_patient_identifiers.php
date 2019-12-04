<?php
/**
 * @var PatientIdentifier[] $patient_identifiers
 */
?>
<?php if (isset(Yii::app()->params['patient_identifiers'])) { ?>
    <?php $index = 0;
    foreach (Yii::app()->params['patient_identifiers'] as $identifier_code => $identifier_config) {
        $patient_identifier = null;
        foreach ($patient_identifiers as $pi) {
            if ($pi->code === $identifier_code) {
                $patient_identifier = $pi;
                break;
            }
        }

        if ($patient_identifier === null) {
            $patient_identifier = new PatientIdentifier();
            $patient_identifier->code = $identifier_code;
        }
        ?>
      <tr class="patient-identifier-duplicate-check">
        <td class="<?= $patient_identifier->isRequired() ? 'required' : '' ?>">
            <?= $patient_identifier->getLabel() ?>
          <br/>
            <?= $form->error($patient_identifier, 'value') ?>
        </td>
        <td>
            <?php

            echo $form->textField($patient_identifier, 'value', array(
                    'placeholder' => $patient_identifier->getPlaceholder(),
                    'maxlength' => 255,
                    'name' => 'PatientIdentifier['.$index.'][value]',
                    'onblur' => "findDuplicatesByPatientIdentifier($patient->id);",
                    !$patient_identifier->isEditable() ? 'readonly' : '' => 'readonly',
            ));
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][id]', $patient_identifier->id);
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][code]', $patient_identifier->code);
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][null_check]', $patient_identifier->nullCheck());
            ?>
        </td>
      </tr>
<?php } ?>

<script>
    function findDuplicatesByPatientIdentifier( id ) {
        var identifier_value = $('#PatientIdentifier_<?= $index ?>_value').val();
        var null_check = $('#PatientIdentifier_<?= $index ?>_null_check').val();
        $.ajax({
            url: "<?php echo Yii::app()->controller->createUrl('patient/findDuplicatesByIdentifier');?>",
            data: {identifier_code: '<?= $identifier_code ?>',identifier_value: identifier_value, id: id, null_check: null_check},
            type: 'GET',
            success: function (response) {
                $('#conflicts').remove();
                $('.patient-identifier-duplicate-check').after(response);
            }
        });
    }
</script>
    <?php $index++; } ?>