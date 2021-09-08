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
        foreach ($this->getPatientFactors() as $factor) { ?>
            <fieldset class="row field-row ">
                <div class="large-9 column">
                    <label> <?php echo CHtml::encode($factor->name) ?> </label>
                    <?php
                    $field_base_name = CHtml::modelName($element) . "[patient_factors][{$factor->id}]";
                    $factor_field_name = "{$field_base_name}[is_factor]";
                    $answer = $element->getPatientFactorAnswer($factor);
                    $value = $answer ? $answer->is_factor : null;
                    if (!is_null($value)) {
                        $value = (integer) $value;
                    }
                    $comments = $answer ? $answer->comments : null;
                    ?>

                </div>
                <div class="large-3 column">
                    <label class="inline highlight">
                        <?php echo CHtml::radioButton($factor_field_name, ($value === 1), array('id' => $factor_field_name . '_1', 'value' => 1)) ?>
                        Yes
                    </label>
                    <label class="inline highlight">
                        <?php echo CHtml::radioButton($factor_field_name, ($value === 0), array('id' => $factor_field_name . '_0', 'value' => 0)) ?>
                        No
                    </label>
                    <label class="inline highlight">
                        <?php echo CHtml::radioButton($factor_field_name, ($value === 2), array('id' => $factor_field_name . '_2', 'value' => 2)) ?>
                        Unknown
                    </label>
                </div>
            </fieldset>
            <?php if ($factor->require_comments) { ?>
                <fieldset class="row field-row">
                    <div class="large-4 column">
                        <label>  <?php echo CHtml::encode($factor->comments_label); ?> </label>
                    </div>
                    <div class="large-8 column end">
                        <?php echo CHtml::textArea("{$field_base_name}[comments]", $comments, array('rows' => 2)); ?>
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
                <?php echo $form->dropDownList($element, 'preferred_info_fmt_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll(array("condition"=>"version =  0",'order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
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
                <?php echo $form->dropDownList($element, 'contact_urgency_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_ContactUrgency::model()->findAll(array("condition"=>"version =  0",'order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
            <div class="large-6 column end">
                <?php echo $form->dropDownList($element, 'preferred_language_id',
                    CHtml::listData(Language::model()->findAll(array('order' => 'name asc')), 'id', 'name') + array('0'=>'Other'), array(), false, array('label' => 6, 'field' => 6)) ?>
                <?php echo $form->textField($element, 'preferred_language_text', array('size' => '20'), false, array('label' => 6, 'field' => 6)) ?>
            </div>
        </div>
    </div>
    <div class="element-fields row">
        <?php echo $form->textArea($element, 'social_service_comments', array('rows' => 3, 'cols' => 80), false, array('label' => 3, 'field' => 6)) ?>
    </div>
<?php } else {
    $this->renderPartial('view_Element_OphCoCvi_ClericalInfo', array('element' => $element));
}

