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
      <tr>
        <td class="<?= $patient_identifier->isRequired() ? 'required' : '' ?>">
            <?= $patient_identifier->getLabel() ?>
          <br/>
            <?= $form->error($patient_identifier, 'value') ?>
        </td>
        <td>
            <?php
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][id]', $patient_identifier->id);
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][code]', $patient_identifier->code);
            echo CHtml::textField('PatientIdentifier[' . $index . '][value]',
                $patient_identifier->value,
                array(
                    'placeholder' => $patient_identifier->getPlaceholder(),
                    'maxlength' => 255,
                    !$patient_identifier->isEditable() ? 'readonly' : '' => 'readonly',
                )); ?>
        </td>
      </tr>
        <?php
        $index++;
    } ?>
<?php } ?>

