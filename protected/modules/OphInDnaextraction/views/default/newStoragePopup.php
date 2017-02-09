<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form id="dnaextraction_addNewStorageForm">
    <div class="row data-row">
        <div class="large-3 column">Box:</div>
        <div class="large-9 column end">
            <?php echo CHtml::dropDownList('dnaextraction_box_id', $element->box_id, CHtml::listData(OphInDnaextraction_DnaExtraction_Box::model()->findAll(array('order' => 'display_order asc')), 'id', 'value'), array('empty' => '- Select -', 'onchange' => 'getAvailableLetterNumberToBox( this )'))?>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-3 column">Letter:</div>
        <div class="large-9 column end">
            <?php echo CHtml::textField('dnaextraction_letter', $element->letter, array('onkeyup' => "setUppercase( this )"))?>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-3 column">Number:</div>
        <div class="large-9 column end">
           <?php echo CHtml::textField('dnaextraction_number', $element->number)?>
        </div>
    </div>
</form>

