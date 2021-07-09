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
    $model = OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo_V1::model();
?>
    <div class="element-fields row">
        <?php
        foreach ($this->getPatientFactors() as $factor) { ?>
            <fieldset class="row field-row ">
                <div class="large-6 column">
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
                <?php if(!$factor->comments_only){ ?>
                    <div class="large-6 column">
                        <label class="inline highlight">
                            <?php echo CHtml::radioButton($factor_field_name, ($value === 1), array('id' => $factor_field_name . '_1', 'value' => 1)) ?>
                            Yes
                        </label>
                        <label class="inline highlight">
                            <?php echo CHtml::radioButton($factor_field_name, ($value === 0), array('id' => $factor_field_name . '_0', 'value' => 0)) ?>
                            No
                        </label>
                        <?php  if(!$factor->yes_no_only){ ?>
                            <label class="inline highlight">
                                <?php echo CHtml::radioButton($factor_field_name, ($value === 2), array('id' => $factor_field_name . '_2', 'value' => 2)) ?>
                                Don't know
                            </label>
                        <?php }?>
                    </div>
                <?php }?>
            </fieldset>
            <?php if ($factor->require_comments) { ?>
                <fieldset class="row field-row <?=$factor->code == '15v1' ? 'hide' : ''?>" id="comment_<?= CHtml::encode($factor->code) ?>">
                    <div class="large-6 column">
                        <label>  <?php echo CHtml::encode($factor->comments_label); ?> </label>
                    </div>
                    <div class="large-6 column end">
                        <?php echo CHtml::textArea("{$field_base_name}[comments]", $comments, array('rows' => 2)); ?>
                    </div>
                </fieldset>
            <?php } ?>
            <hr/><br/>
        <?php } ?>
    </div>

    <div class="element-fields row">
        <div class="large-12">
            <div class="indent-correct row">
                <div class="large-6 column">
                    <div class="large-12">
                        <?php echo $form->dropDownList($element, 'preferred_info_fmt_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll(array("condition"=>"version =  1",'order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
                    </div>
                </div>
                <div class="large-6 column">
                    <div class="large-12">
                        <?php
                        $html_options = array(
                            'options' => array(),
                            'empty' => '- Please select -',
                            'div_id' => CHtml::modelName($element). '_preferred_format_id',
                            'div_class' => 'elementField',
                            'label' => $element->getAttributeLabel('preferred_format_id'),
                        );

                        $element->preferred_format_ids = $element->preferred_format_assignments;
                        echo $form->multiSelectList(
                            $element,
                            CHtml::modelName($element).'[preferred_format_ids]',
                            'preferred_format_ids',
                            'preferred_format_id',
                            CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredFormat::model()->findAll(array("condition"=>"version =  1",'order' => 'display_order asc')), 'id', 'name'),
                            array(),
                            $html_options,
                            false,
                            false,
                            false,
                            false,
                            false,
                            array(
                                'label' => 6,
                                'field' => 6,
                            )
                        );
                        ?>
                    </div>
                    <div class="large-12">
                        <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_V1_preferred_format_id" class="row field-row">
                            <div class="large-6 column">
                                <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_format_other')) ?></div>
                            </div>
                            <div class="large-6 column end">
                                <?php echo CHtml::textField( CHtml::modelName($element).'[preferred_format_other]', $element->preferred_format_other) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="large-12">
            <div class="indent-correct row">
                <div class="large-6 column">
                    <div class="large-12">
                        <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_V1_preferred_comm" class="row field-row">
                            <div class="large-6 column">
                                <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_comm')) ?>:</div>
                            </div>
                            <div class="large-6 column end">
                                <?php echo CHtml::textArea(CHtml::modelName($element).'[preferred_comm]', $element->preferred_comm, array('rows' => 2)); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="large-6 column">
                    <div class="large-12">
                        <?php echo $form->dropDownList($element, 'preferred_language_id',
                            CHtml::listData(Language::model()->findAll(array('order' => 'name asc')), 'id', 'name') + array('0'=>'Other'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
                    <div class="large-12">
                        <?php echo $form->textField($element, 'preferred_language_text', array('size' => '20'), false, array('label' => 6, 'field' => 6)) ?>                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="large-12">
            <div class="indent-correct row">
                <div class="large-6 column">
                    <div class="large-12">
                        <div id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_V1_interpreter_required" class="row field-row">
                        <div class="large-6 column">
                            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('interpreter_required')) ?></div>
                        </div>
                        <div class="large-6 column end">
                            <label class="inline highlight">
                                <?php echo CHtml::radioButton( CHtml::modelName($element) . "[interpreter_required]", ($element->interpreter_required == 1), array( 'value' => 1)) ?>
                                Yes
                            </label>
                            <label class="inline highlight">
                                <?php echo CHtml::radioButton(CHtml::modelName($element) . "[interpreter_required]",  ($element->interpreter_required == 0), array( 'value' => 0)) ?>
                                No
                            </label>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } else {
    $this->renderPartial('view_Element_OphCoCvi_ClericalInfo_V1', array('element' => $element));
}

