<?php
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
?>
<tr id="conflicts" class="cols-full alert-box error" style="font-style: italic; font-size: small;">
    <?php if (isset($patients)) { ?>
        <td class="row field-row">
            <p>Duplicate patient detected.</p>
        </td>
        <td>
            <table class="last-left">
                <thead>
                <tr>
                    <th><?= PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id) ?></th>
                    <th>Name</th>
                    <th>Born</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php foreach ($patients as $patient) {
                        $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $patient->id, $institution_id, $site_id);
                        ?>
                <tr>
              <td><?php echo CHtml::link(
                  PatientIdentifierHelper::getIdentifierValue($primary_identifier),
                  Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)),
                  array('target' => '_blank')
              );
                  $this->widget(
                      'application.widgets.PatientIdentifiers',
                      [
                          'patient' => $patient,
                          'show_all' => true
                      ]
                  ); ?>
              </td>
                    <td>
                        <?php echo CHtml::link(
                            $patient->getFullName(),
                            Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)),
                            array('target' => '_blank')
                        ); ?>
                    </td>
                    <td>
                        <?php echo CHtml::link(
                            date('d/m/Y', strtotime($patient->getDOB())),
                            Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)),
                            array('target' => '_blank')
                        ); ?>
                    </td>
                </tr>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </td>
    <?php } else { ?>
        <p>No conflicts found.</p>
    <?php } ?>
</tr>
