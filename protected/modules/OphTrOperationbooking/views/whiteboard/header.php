<?php
    $date = date_create_from_format('Y-m-d H:i:s', $data->last_modified_date);
$institution = Institution::model()->getCurrent();

$event = Event::model()->findByPk($data->event_id);
$display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $event->episode->patient->id, $institution->id, Yii::app()->session['selected_site_id']);

?>
<div class="oe-hd-title">
    <ul class="dot-list">
        <li><?= $data->patient_name ?></li>
        <li><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier); ?>
            <?php $this->widget(
                'application.widgets.PatientIdentifiers',
                [
                    'patient' => $event->episode->patient,
                    'show_all' => true
                ]
            ); ?>
        </li>
    </ul>
</div>
<div class="oe-hd-actions">
    <small>updated at: &nbsp;</small>
    <b><?=$date->format('H:i')?></b>
    &nbsp;&nbsp;<?=$date->format('j M Y')?>
</div>
