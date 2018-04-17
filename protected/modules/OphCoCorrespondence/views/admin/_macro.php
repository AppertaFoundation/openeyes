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
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/InitMethod.js", \CClientScript::POS_HEAD);
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
        <?php echo $form->dropDownList($macro, 'type', array('site' => 'Site', 'subspecialty' => 'Subspecialty', 'firm' => Firm::contextLabel()), array('empty' => '- Type -'))?>
        <?php echo $form->dropDownList($macro, 'letter_type_id', CHtml::listData(LetterType::model()->getActiveLetterTypes(), 'id', 'name'), array('empty' => '- Letter type -'))?>
        <?php echo $form->dropDownList($macro, 'site_id', Site::model()->getListForCurrentInstitution(), array('empty' => '- Site -', 'div-class' => 'typeSite'), $macro->type != 'site')?>
        <?php echo $form->dropDownList($macro, 'subspecialty_id', CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name asc')), 'id', 'name'), array('empty' => '- Subspecialty -', 'div-class' => 'typeSubspecialty'), $macro->type != 'subspecialty')?>
        <?php echo $form->dropDownList($macro, 'firm_id', Firm::model()->getListWithSpecialties(true), array('empty' => '- ' . Firm::contextLabel() . ' -', 'div-class' => 'typeFirm'), $macro->type != 'firm')?>
        <?php echo $form->textField($macro, 'name', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        <?php echo $form->radioButtons($macro, 'recipient_id', CHtml::listData(LetterRecipient::model()->findAll(array('order' => 'display_order asc')), 'id', 'name') + ['' => 'None'], null, false, false, false, false, array('empty' => 'None', 'empty-after' => true))?>
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

        <?php
            $model_init_method = CHtml::modelName('OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod');
            $model_associated_content = CHtml::modelName('OEModule_OphCoCorrespondence_models_MacroInitAssociatedContent');
        ?>

        <div class="field-row">
            <p>Attachments</p>
            <table class="grid" id="OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod_table">
                <thead>
                    <tr>
                        <td>Hidden</td>
                        <td>Print appended</td>
                        <td>Event</td>
                        <td>Title</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody class="sortable ui-sortable">
                <?php
                    $row_count = 0;
                    if(isset($associated_content)){
                        foreach($associated_content as $content){
                            $this->renderPartial(
                                'init_method_row',
                                array(
                                    'form' => $form,
                                    'init_method_model_name' => $model_init_method,
                                    'associated_model_name' => $model_associated_content,
                                    'model_associated_content' => $content,
                                    'prefix_init_method' => $model_init_method.'[' . ($row_count) . ']',
                                    'prefix_associated' => $model_associated_content.'[' . ($row_count) . ']',
                                    'row_count' => $row_count
                                )
                            );
                            $row_count++;
                        }
                    }

                    ?>
                </tbody>
                <tfoot>
                    <td class="text-right" colspan="6"><button class="button small primary event-action" name="save" type="button" id="OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod_add">Add</button></td>
                </tfoot>
            </table>

            <script type="text/template" id="OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod_template" class="hidden">
                <?php
                $this->renderPartial(
                    'init_method_row',
                    array(
                        'form' => $form,
                        'init_method_model_name' => $model_init_method,
                        'associated_model_name' => $model_associated_content,
                        'model_associated_content' => $associated_content,
                        'prefix_init_method' => $model_init_method.'[{{row_count}}]',
                        'prefix_associated' => $model_associated_content.'[{{row_count}}]',
                        'row_count' => '{{row_count}}',
                        'values' => array(
                            'id' => '',
                            'is_system_hidden' => '{{is_system_hidden}}',
                            'is_print_appended' => '{{is_print_appended}}',
                            'method_id' => '{{method_id}}',
                            'short_code' => '{{short_code}}',
                            'title' => '{{title}}',
                            'is_print_appended_js' => '{{is_print_appended_js}}'
                        ),
                    )
                );
                ?>
            </script>
        </div>


    <div class="row field-row">
            <div class="large-10 large-offset-2 column">
                <button class="button small primary event-action" name="save" type="submit" id="et_save">Save</button>
                <button class="warning button small primary cancelEditMacro" name="cancel" type="submit">Cancel</button>
            </div>
        </div>
    <?php $this->endWidget()?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCoCorrespondence.InitMethodController();
        $( ".sortable" ).sortable();
        $( ".sortable" ).disableSelection();
    });
</script>