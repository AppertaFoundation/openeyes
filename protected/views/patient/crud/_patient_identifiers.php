<?php
/**
 * @var PatientIdentifier[] $patient_identifiers
 */
?>
<?php if (isset(Yii::app()->params['patient_identifiers'])) { ?>
    <?php $index = 0;
    foreach (Yii::app()->params['patient_identifiers'] as $identifier_code => $identifier_config) {

        $existing_identifier = null;
        foreach ($patient_identifiers as $patient_identifier) {
            if ($patient_identifier->code === $identifier_code) {
                $existing_identifier = $patient_identifier;
                break;
            }
        }
        ?>
      <tr>
        <td class="<?= $identifier_config['required'] ? 'required' : '' ?>">
            <?= $identifier_config['label'] ?>
          <br/>
            <?php if ($existing_identifier) {
                echo $form->error($existing_identifier, 'value');
            } ?>
        </td>
        <td>
            <?php
            $placeholder = isset($identifier_config['placeholder']) ? $identifier_config['placeholder'] : $identifier_config['label'];
            $value = $existing_identifier ? $existing_identifier->value : null;
            $id = $existing_identifier ? $existing_identifier->id : null;
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][id]', $id);
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][code]', $identifier_code);
            echo CHtml::textField('PatientIdentifier[' . $index . '][value]',
                $value,
                array(
                    'placeholder' => $placeholder,
                    'maxlength' => 50
                )); ?>
        </td>
      </tr>
        <?php
        $index++;
    } ?>
<?php } ?>

