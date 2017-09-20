<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
    <h2>Edit macro</h2>
    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors))?>
    <?php

    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 4,
        ),
    ))?>
        <?php echo $form->dropDownList($macro, 'type', array('site' => 'Site', 'subspecialty' => 'Subspecialty', 'firm' => 'Firm'), array('empty' => '- Type -'))?>
        <?php echo $form->dropDownList($macro, 'letter_type_id', CHtml::listData(LetterType::model()->getActiveLetterTypes(), 'id', 'name'), array('empty' => '- Letter type -'))?>
        <?php echo $form->dropDownList($macro, 'site_id', Site::model()->getListForCurrentInstitution(), array('empty' => '- Site -', 'div-class' => 'typeSite'), $macro->type != 'site')?>
        <?php echo $form->dropDownList($macro, 'subspecialty_id', CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name asc')), 'id', 'name'), array('empty' => '- Subspecialty -', 'div-class' => 'typeSubspecialty'), $macro->type != 'subspecialty')?>
        <?php echo $form->dropDownList($macro, 'firm_id', Firm::model()->getListWithSpecialties(true), array('empty' => '- Firm -', 'div-class' => 'typeFirm'), $macro->type != 'firm')?>
        <?php echo $form->textField($macro, 'name', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        <?php echo $form->radioButtons($macro, 'recipient_id', CHtml::listData(LetterRecipient::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'), null, false, false, false, false, array('empty' => 'None', 'empty-after' => true))?>
        <?php echo $form->checkBox($macro, 'cc_patient', array('text-align' => 'right'))?>
        <?php echo $form->checkBox($macro, 'cc_doctor', array('text-align' => 'right'))?>
        <?php echo $form->checkBox($macro, 'cc_drss', array('text-align' => 'right'))?>
        <?php echo $form->checkBox($macro, 'use_nickname', array('text-align' => 'right'))?>
        <?php echo $form->dropDownList($macro, 'episode_status_id', CHtml::listData(EpisodeStatus::model()->findAll(array('order' => 'id asc')), 'id', 'name'), array('empty' => '- None -'))?>
        <?php echo $form->textArea($macro, 'body')?>

        <div class="row field-row">
            <div class="large-10 large-offset-2 column shortCodeDescription">
                &nbsp;
            </div>
        </div>
        <div class="row field-row">
            <div class="large-8 large-offset-2 column">
                <div class="row field-row">
                    <div class="large-3 column">
                        <label for="shortcode">
                            Add shortcode:
                        </label>
                    </div>
                    <div class="large-6 column end">
                        <?php echo CHtml::dropDownList('shortcode', '', CHtml::listData(PatientShortcode::model()->findAll(array('order' => 'description asc')), 'code', 'description'), array('empty' => '- Select -'))?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row field-row">
            <div class="large-10 large-offset-2 column">
                <button class="button small primary event-action" name="save" type="submit" id="et_save">Save</button>
                <button class="warning button small primary cancelEditMacro" name="cancel" type="submit">Cancel</button>
            </div>
        </div>
    <?php $this->endWidget()?>
</div>
