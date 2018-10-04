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
        <td class="<?= isset($identifier_config['required']) && $identifier_config['required'] ? 'required' : '' ?>">
            <?= $identifier_config['label'] ?>
          <br/>
            <?php if ($existing_identifier) {
                echo $form->error($existing_identifier, 'value');
            } ?>
        </td>
        <td>
            <?php
            $placeholder = isset($identifier_config['placeholder']) ? $identifier_config['placeholder'] : $identifier_config['label'];
            $value = null;
            if ($existing_identifier) {
                $value = $existing_identifier->value;
            } elseif (isset($identifier_config['auto_increment']) && $identifier_config['auto_increment']) {
                $last_identifier = PatientIdentifier::model()->find(array(
                        'condition' => 'code = :code',
                        'order' => 'CONVERT(value, INTEGER) DESC',
                        'params' => array(':code' => $identifier_code),
                    )
                );

                if ($last_identifier) {
                    $value = $last_identifier->value + 1;
                } elseif (isset($identifier_config['start_val'])) {
                    $value = $identifier_config['start_va'];
                }
            }

            $id = $existing_identifier ? $existing_identifier->id : null;
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][id]', $id);
            echo CHtml::hiddenField('PatientIdentifier[' . $index . '][code]', $identifier_code);
            echo CHtml::textField('PatientIdentifier[' . $index . '][value]',
                $value,
                array(
                    'placeholder' => $placeholder,
                    'maxlength' => 50,
                    isset($identifier_config['editable']) && !$identifier_config['editable'] ? 'disabled'  : '' => '1',
                )); ?>
        </td>
      </tr>
        <?php
        $index++;
    } ?>
<?php } ?>

