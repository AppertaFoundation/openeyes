 <?php
 
    $this->beginWidget('CActiveForm', array(
        'id' => 'patient1-search-form',
        'focus' => '#query',
        'enableAjaxValidation'=>false,

        'htmlOptions' => array(
            'class' => 'form panel search',
            'onsubmit'=>"return false;",

        )
    )); ?>
    <div class="row">
        <div class="search-examples">
            Find a patient by
            <strong>Hospital Number</strong>,
            <strong>NHS Number</strong>,
            <strong>Firstname Surname</strong> or
            <strong>Surname, Firstname</strong>.
        </div>

        <div class="large-9 column">

        <input type="text" name="patient_merge_search" id="patient_merge_search" class="form panel search large ui-autocomplete-input" placeholder="Enter search..." autocomplete="off">

        </div>
        <div class="large-3 column text-right">
             <button type="submit" class="primary">
                Search
            </button>
        </div>

    </div>

    <?php $this->endWidget(); ?>