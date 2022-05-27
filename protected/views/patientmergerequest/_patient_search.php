<?php
    $dob_mandatory = \SettingMetadata::model()->checkSetting('dob_mandatory_in_search', 'on');

    $this->beginWidget('CActiveForm', array(
        'id' => 'patient1-search-form',
        'focus' => '#query',
        'enableAjaxValidation' => false,

        'htmlOptions' => array(
            'class' => 'form panel search',
            'onsubmit' => 'return false;',

        ),
    )); ?>
    <div class="data-group">
        <div class="search-examples">
            Find a patient by
            <?php
            $display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
            $display_secondary_number_usage_code = SettingMetadata::model()->getSetting('display_secondary_number_usage_code');
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $this->patient->id, $institution->id, $selected_site_id);
            $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $this->patient->id, $institution->id, $selected_site_id);
            ?>
            <strong><?= $primary_identifier ?></strong>,
            <strong><?= $secondary_identifier ?> </strong>,
            <?php if (!$dob_mandatory) :
                ?><strong>Firstname Surname</strong> or<?php
            endif; ?>
            <strong>Firstname Surname DOB</strong> or
            <?php if (!$dob_mandatory) :
                ?><strong>Surname, Firstname</strong> or<?php
            endif; ?>
            <strong>Surname, Firstname DOB</strong>.
        </div>

        <div class="cols-9 column">

        <?php $this->widget('application.widgets.AutoCompleteSearch');?>

        <div style="display:none" class="timeout no-result-patients warning alert-box">
            <div class="small-12 column text-center">
                Search for a more appropriate, complete name or patient number.
            </div>
        </div>
        
        </div>
        <div class="cols-3 column text-right">
          <i class="spinner" title="Loading..." style="display: none;"></i>
        </div>

    </div>

    <?php $this->endWidget(); ?>
