<?php
/**
 * @var PatientIdentifier[] $patient_identifiers
 * @var array $pid_type_necessity_values
 */
?>

<?php foreach ($patient_identifiers as $index => $patient_identifier) { ?>
    <tr class="js-patient-identifier-duplicate-check-<?= $index ?>">
        <td class=<?= $pid_type_necessity_values[$patient_identifier->patient_identifier_type_id]['necessity'] === 'mandatory' ? 'required' : '' ?>>
            <?= $patient_identifier->patientIdentifierType->short_title ?>
            <br/>
            <?= $form->error($patient_identifier, 'value') ?>
        </td>
        <td>
            <?php
                echo $form->textField($patient_identifier, 'value', [
                    'placeholder' => $patient_identifier->patientIdentifierType->long_title,
                    'maxlength' => 40,
                    'size' => 40,
                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                    'name' => 'PatientIdentifier[' . $index . '][value]',
                    'onblur' => "findDuplicatesByPatientIdentifier($index);",
                ]);
                echo CHtml::hiddenField('PatientIdentifier[' . $index . '][patient_identifier_type_id]', $patient_identifier->patient_identifier_type_id);
            ?>
        </td>
    </tr>
    <?php if ($pid_type_necessity_values[$patient_identifier->patient_identifier_type_id]['status_necessity'] !== 'hidden') { ?>
        <tr>
            <td class=<?= $pid_type_necessity_values[$patient_identifier->patient_identifier_type_id]['status_necessity'] === 'mandatory' ? 'required' : '' ?>>
                <?= $patient_identifier->patientIdentifierType->short_title . ' Status' ?>
                <br/>
                <?= $form->error($patient_identifier, 'patient_identifier_status_id') ?>
            </td>
            <td>
                <?= $form->dropDownList(
                    $patient_identifier,
                    'patient_identifier_status_id',
                    CHtml::listData($patient_identifier->patientIdentifierType->patientIdentifierStatuses, 'id', 'description'),
                    [
                            'empty' => '-- select --',
                            'name' => 'PatientIdentifier[' . $index . '][patient_identifier_status_id]',
                        ],
                );
                ?>
            </td>
        </tr>
    <?php } ?>
<?php } ?>

<script>
    function findDuplicatesByPatientIdentifier(index) {
        let identifier_value = $('#PatientIdentifier_' + index + '_value').val();
        let identifier_type_id = $('#PatientIdentifier_' + index + '_patient_identifier_type_id').val();
        let patient_id = <?= $patient->id ?: 'null' ?>;
        $.ajax({
            url: "<?php echo Yii::app()->controller->createUrl('patient/findDuplicatesByIdentifier');?>",
            data: {identifier_type_id: identifier_type_id ,identifier_value: identifier_value, id: patient_id},
            type: 'GET',
            success: function (response) {
                let response_class = 'js-patient-identifier-duplicate-check-response-' + index;
                $('.' + response_class).remove();
                let $response = $(response);
                $('.js-patient-identifier-duplicate-check-' + index).after($response);
                $response.addClass(response_class)
            }
        });
    }
</script>
