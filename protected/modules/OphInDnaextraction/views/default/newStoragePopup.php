<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form id="dnaextraction_addNewStorageForm">
    <table class="standard row printLabelPanel">
        <tbody>
        <tr>
            <td class="data-group">
                <h3 class="cols-3 column">Box:</h3>
                <div class="cols-9 column end">
                    <?=\CHtml::dropDownList('dnaextraction_box_id', $element->box_id, CHtml::listData(OphInDnaextraction_DnaExtraction_Box::model()->findAll(array('order' => 'display_order asc')), 'id', 'value'), array('empty' => '- Select -', 'onchange' => 'getAvailableLetterNumberToBox( this )'))?>
                </div>
            </td>
            <td class="data-group">
                <h3 class="cols-3 column">Letter:</h3>
                <div class="cols-9 column end">
                    <?=\CHtml::textField('dnaextraction_letter', $element->letter, array('onkeyup' => "setUppercase( this )"))?>
                </div>
            </td>
            <td class="data-group">
                <h3 class="cols-3 column">Number:</h3>
                <div class="cols-9 column end">
                    <?=\CHtml::textField('dnaextraction_number', $element->number)?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="data-group">
                <?= CHtml::button('Save', [
                    'class' => 'button small secondary',
                    'name' => 'save',
                    'id' => 'save-new-storage-btn'
                ]);
?>
            </td>
        </tr>
</form>

