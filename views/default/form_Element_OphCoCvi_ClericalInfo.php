<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
if ($this->checkClericalEditAccess()) {
    $model = OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo::model();
?>
    <div class="element-fields row">
        <?php
        foreach ($model->patientFactorList($element->id) as $factor) { ?>
            <fieldset class="row field-row ">
                <div class="large-9 column">
                    <label> <?php echo $factor['name'] ?> </label>
                    <?php
                    $is_factor = $factor['is_factor'];
                    $comments = $factor['comments'];
                    $i = $factor['id'];
                    $value = $factor['is_comments'] ? '1' : '0';
                    ?>
                    <?php
                    echo CHtml::hiddenField("ophcocvi_clinicinfo_patient_factor_id[$i]", $factor['id'], array('id' => 'hiddenInput'));
                    echo CHtml::hiddenField("require_comments[$i]", $value, array('id' => 'hiddenInput'));
                    ?>
                </div>
                <div class="large-3 column">
                    <label class="inline highlight">
                        <?php echo CHtml::radioButton("is_factor[$i]", (isset($is_factor) && $is_factor == 1), array('id' => $factor['id'] . '_1', 'value' => 1)) ?>
                        Yes
                    </label>
                    <label class="inline highlight">
                        <?php echo CHtml::radioButton("is_factor[$i]", (isset($is_factor) && $is_factor == 0), array('id' => $factor['id'] . '_0', 'value' => 0)) ?>
                        No
                    </label>
                    <label class="inline highlight">
                        <?php echo CHtml::radioButton("is_factor[$i]", (isset($is_factor) && $is_factor == 2), array('id' => $factor['id'] . '_2', 'value' => 2)) ?>
                        Unknown
                    </label>
                </div>
            </fieldset>
            <?php if ($value == 1) { ?>
                <fieldset class="row field-row">
                    <div class="large-4 column">
                        <label>  <?php echo $factor['label'];?> </label>
                    </div>
                    <div class="large-8 column end">
                        <?php echo CHtml::textArea("comments[$i]", ($comments), array('rows' => 2)); ?>
                    </div>
                </fieldset>
            <?php } ?>
            <hr/><br/>
        <?php } ?>
    </div>
    
    <div class="element-fields row">
        <div class="indent-correct row">
            <div class="large-6 column">
                <?php echo $form->dropDownList($element, 'employment_status_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_EmploymentStatus::model()->findAll('`active` = ?', array(1), array('order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
        </div>
    </div>
    <div class="element-fields row">
        <div class="indent-correct row">
            <div class="large-6 column">
                <?php echo $form->dropDownList($element, 'preferred_info_fmt_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
            <?php
            $preferredInfoFormatEmail = OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll('`require_email` = ?', array(1));
            ?>
            <div class="large-6 column end">
                <?php echo $form->textField($element, 'info_email', array('size' => '20'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
        </div>
    </div>
    <div class="element-fields row">
        <div class="indent-correct row">
            <div class="large-6 column">
                <?php echo $form->dropDownList($element, 'contact_urgency_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_ContactUrgency::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
            <div class="large-6 column end">
                <?php echo $form->dropDownList($element, 'preferred_language_id', CHtml::listData(Language::model()->findAll(array('order' => 'name asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
        </div>
    </div>
    <div class="element-fields row">
        <?php echo $form->textArea($element, 'social_service_comments', array('rows' => 3, 'cols' => 80), false, array('label' => 3, 'field' => 6)) ?>
    </div>
<?php } else {
    $this->renderPartial('view_Element_OphCoCvi_ClericalInfo', array('element' => $element));
}

