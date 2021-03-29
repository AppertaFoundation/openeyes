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
            <!--            Parameterised for CERA-519-->
            <strong><?php echo (\SettingMetadata::model()->getSetting('hos_num_label'))?></strong>,
            <strong><?php echo \SettingMetadata::model()->getSetting('nhs_num_label')?> </strong>,
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
          <button type="submit" class="primary">
            Search
          </button>
        </div>

    </div>

    <?php $this->endWidget(); ?>