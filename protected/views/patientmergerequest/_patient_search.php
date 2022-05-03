<?php

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
                $primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->selectedInstitutionId, $this->selectedSiteId);
                $secondary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $this->selectedInstitutionId, $this->selectedSiteId);
            ?>
            <strong><?= $primary_identifier_prompt ?></strong>,
            <strong><?= $secondary_identifier_prompt ?> </strong>,
            <strong>Firstname Surname</strong> or
            <strong>Surname, Firstname</strong>.
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
