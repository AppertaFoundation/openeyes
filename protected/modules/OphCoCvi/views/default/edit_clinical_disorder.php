<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$version = $disorder->id ? $disorder->event_type_version : Yii::app()->request->getQuery('event_type_version');
$patient_type = $disorder->id ? $disorder->patient_type : Yii::app()->request->getQuery('patient_type');
?>
<div class="box admin">
    <h2><?php echo $disorder->id ? 'Edit' : 'Add' ?> Clinical Disorder</h2>
    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    )) ?>
    <?php echo $form->textField($disorder, 'name', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
    <?php echo $form->textField($disorder, 'code', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>

    <div id="div_OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder_disorder_id" class="row field-row">
        <div class="large-2 column">
            <label for="OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder_disorder_id">Disorder:</label>
        </div>
        <div class="large-5 column end">
            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name' => 'autocomplete_disorder_id',
                'id' => 'autocomplete_disorder_id',
                'value' => ($disorder->disorder_id) ? $disorder->disorder->term : '' ,
                'sourceUrl' => array('admin/cilinicalDisorderAutocomplete'),
                'options' => array(
                    'minLength' => '2',
                    'select' => "js:function(event, ui) {
                                $('#disorder_id').val(ui.item.id);
                                $('#disorder_code_box').show();
                                $('#disorder_code').html(ui.item.id);
                                }",
                ),
                'htmlOptions' => array('placeholder' => 'or enter disorder here'),
            ));
            ?>
            <div id="disorder_code_box" class="<?= isset($disorder->disorder_id) && $disorder->disorder_id != 0 ? 'show' : 'hide' ?>">SNOMED Code: <span id="disorder_code"><?=CHtml::encode($disorder->disorder_id)?></span>
                <button id="js-clear-disorder" class="button warning tiny">X</button>
            </div>
        </div>
    </div>
    <input type="hidden" id="disorder_id" name="OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder[disorder_id]" value="<?=($disorder->disorder_id) ? CHtml::encode($disorder->disorder_id) : '' ?>">

    <div id="div_ClinicalDisorder_subspecialty_id" class="row field-row">
        <div class="large-2 column">
            <label for="ClinicalDisorder_subspecialty_id">Section:</label>
        </div>
        <div class="large-5 column end">
            <?php
            $criteria = new CDbCriteria();
            $criteria->condition = 'event_type_version = '.$version.' AND patient_type='.$patient_type.' AND active=1';
            echo CHtml::dropDownList('OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder[section_id]',
                $disorder->section_id,
                CHtml::listData(\OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section::model()->findAll($criteria,array('patient_type' => $patient_type)), 'id', 'name'), array('empty' => '- None -')) ?>
        </div>
    </div>
    <div id="div_ClinicalInfo_Disorder_Section_consultant_id" class="row field-row">
        <div class="large-2 column">
            <label for="ClinicalInfo_Disorder_Section_active">Active:</label>
        </div>
        <div class="large-5 column end">
            <?php echo CHtml::activeCheckBox($disorder, 'active') ?>
        </div>
    </div>

    <?php echo $form->formActions(); ?>

    <?php $this->endWidget() ?>
</div>
