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
            <strong>Hospital Number</strong>,
            <strong>NHS Number</strong>,
            <strong>Firstname Surname</strong> or
            <strong>Surname, Firstname</strong>.
        </div>

        <div class="cols-9 column">

        <input type="text" name="patient_merge_search" id="patient_merge_search" class="form panel search large ui-autocomplete-input" placeholder="Enter search..." autocomplete="off">

        <div style="display:none" class="data-group no-result-patients warning alert-box">
            <div class="small-12 column text-center"> 
                No results found. 
            </div>

        </div>
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