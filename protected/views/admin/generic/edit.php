<?php

/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$assetManager = Yii::app()->getAssetManager();
?>

<div class="<?= $admin->div_wrapper_class ?>">
    <div class="row divider">
        <h2><?php echo ($admin->getModel()->id ? 'Edit' : 'Add') . ' ' . $admin->getModelDisplayName() ?></h2>
    </div>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
    <?php
    if ($admin->getCustomSaveURL() !== '') {
        $formAction = $admin->getCustomSaveURL();
    } else {
        $formAction = '#';
    }
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'generic-admin-form',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'action' => $formAction,
        'layoutColumns' => array(
            'label' => 7,
            'field' => 5,
        ),
    ));
    $autoComplete = array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'));
    echo $form->hiddenInput($admin->getModel(), 'id');
    if (Yii::app()->request->getParam('returnUri')) {
        echo CHtml::hiddenField('returnUriEdit', Yii::app()->request->getParam('returnUri'));
    }
    ?>

    <table class="standard">
        <tbody>
        <?php foreach ($admin->getEditFields() as $field => $type) { ?>
            <tr>
                <td>
                    <?php
                    if (is_array($type)) {
                        switch ($type['widget']) {
                            case 'GenericAdmin':
                                echo CHtml::label($type['label'] . ':', '');
                                $this->widget('GenericAdmin', $type['options']);
                                break;

                            case 'RefMedicationLookup':
                                echo CHtml::label($type['label'] . ':', '');
                                $this->widget('RefMedicationLookup', $type['options']);
                                break;

                            case 'TagsInput':
                                $form->TagsInput(
                                    $type['label'],
                                    $admin->getModel(),
                                    $field,
                                    //$admin->getModelName().'['.$field.']',
                                    $type['relation'],
                                    $type['relation_field_id'],
                                    $type['htmlOptions']
                                );
                                break;

                            case 'MultiSelectList':
                                ?>
                                <div class="data-group multi-select multi-select-list align-left">
                                    <?php
                                    $through = array();
                                    $link = '';
                                    if (array_key_exists('through', $type) && is_array($type['through'])) {
                                        $through = $type['through'];
                                    }
                                    if (array_key_exists('link', $type) && $type['link']) {
                                        $link = $type['link'];
                                    }
                                    if (array_key_exists('htmlOptions', $type) && is_array($type['htmlOptions'])) {
                                        $htmlOptions = $type['htmlOptions'];
                                    } else {
                                        $htmlOptions = [
                                            'empty' => '',
                                            'label' => $type['label'],
                                            'searchable' => true,
                                        ];
                                    }
                                    echo $form->multiSelectList(
                                        $admin->getModel(),
                                        $admin->getModelName() . '[' . $field . ']',
                                        $field,
                                        $type['relation_field_id'],
                                        $type['options'],
                                        array(),
                                        $htmlOptions,
                                        false,
                                        true,
                                        null,
                                        false,
                                        false,
                                        array(),
                                        $through,
                                        $link
                                    );
                                    ?>
                                </div>
                                <?php
                                break;
                            case 'DropDownList':
                                $form->dropDownList(
                                    $admin->getModel(),
                                    $field,
                                    $type['options'],
                                    $type['htmlOptions'],
                                    $type['hidden'],
                                    $type['layoutColumns']
                                );
                                break;
                            case 'CustomView':
                                // arguments: (string) viewName, (array) viewArguments
                                $type['viewArguments'] = is_array($type['viewArguments']) ? $type['viewArguments'] : array();
                                $type['viewArguments']['form'] = $form;
                                $this->renderPartial($type['viewName'], $type['viewArguments']);
                                break;
                            case 'RelationList':
                                if (isset($admin->getModel()->id)) {
                                    $assetManager->registerScriptFile('js/oeadmin/list.js');
                                    $subAdmin = $admin->generateAdminForRelationList($type['relation'], $type['listFields']);
                                    if (isset($type['search'])) {
                                        $subAdmin->getSearch()->setSearchItems($type['search']);
                                    }
                                    $this->renderPartial('//admin/generic/list', array(
                                        'admin' => $subAdmin,
                                        'uniqueid' => $type['action'],
                                    ));
                                }
                                break;
                            case 'PatientLookup':
                                $this->renderPartial('//admin//generic/patientLookup', array(
                                    'model' => $admin->getModel(),
                                    'extras' => array_key_exists('extras', $type) ? $type['extras'] : false,
                                ));
                                break;
                            case 'DisorderLookup':
                                if (!is_array($admin->getModel()->{$type['relation']})) {
                                    $relations = array($admin->getModel()->{$type['relation']});
                                } else {
                                    $relations = $admin->getModel()->{$type['relation']};
                                }
                                ?>
                                <div class="data-group">
                                    <div class="cols-2 column">&nbsp;</div>
                                    <div class="cols-5 column end">
                                        <hr>
                                    </div>
                                </div>
                                <div class="data-group flex-layout cols-full">
                                    <div class="cols-7 column">
                                        <label>Diagnosis</label>
                                    </div>
                                    <div class="cols-5 column end">


                                        <?php
                                        $htmlOptions['empty'] = $type['empty_text'];
                                        $htmlOptions['id'] = isset($type['id']) ? $type['id'] : 'disorder_dropdown';
                                        echo CHtml::dropDownList(null, null, $type['options'], $htmlOptions);
                                        ?>
                                        <script>
                                            $('#<?=$htmlOptions['id']?>').on('change', function () {
                                                if ($(this).val()) {
                                                    //using the disorderAutoComplete.php's select() function which was written for the autocomplete
                                                    select(
                                                        '#<?=$htmlOptions['id']; ?>',
                                                        {
                                                            id: $(this).val(),
                                                            value: $(this).find('option:selected').text(),
                                                        });
                                                    $(this).val(null);
                                                }
                                            });
                                        </script>
                                        <div style="padding-bottom:5px;"></div>
                                        <?php
                                        $this->renderPartial('//disorder/disorderAutoComplete', array(
                                            'class' => get_class($admin->getModel()),
                                            'name' => $field,
                                            'code' => '',
                                            'value' => $admin->getModel()->$field,
                                            'clear_diagnosis' => '&nbsp;<i class="oe-i remove-circle small clear-diagnosis-widget" aria-hidden="true" data-diagnosis-id=""></i>',
                                            'placeholder' => 'Search for a diagnosis',
                                        ));
                                        ?>
                                        <br>
                                        <div id="enteredDiagnosisText" style="font-size:13px;">
                                            <?php
                                            foreach ($relations as $relation) {
                                                if ($relation) {
                                                    echo '<span>' . $relation->term .
                                                        '&nbsp;<i class="oe-i remove-circle small clear-diagnosis-widget" aria-hidden="true" data-diagnosis-id="' . $relation->id . '"></i><br>' .
                                                        '</span>';
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                    <div style="padding-bottom:15px;"></div>
                                </div>
                                <div class="data-group">
                                    <div class="cols-2 column">&nbsp;</div>
                                    <div class="cols-5 column end">
                                        <hr>
                                    </div>
                                </div>
                                <?php
                                break;
                            case 'LinkTo':
                                ?>
                                <div class="data-group">
                                    <div class="cols-2 column">
                                        <label></label>
                                    </div>
                                    <div class="cols-5 column end">
                                        <?php
                                        echo CHtml::link($type['label'], array($type['linkTo']));
                                        ?>
                                    </div>
                                </div>
                                <?php
                                break;
                            case 'text':
                                echo $form->textField($admin->getModel(), $field, $type['htmlOptions']);
                                break;
                            case 'checkbox':
                                echo $form->checkBox($admin->getModel(), $field, $type['htmlOptions']);
                                break;
                        }
                    } else {
                        switch ($type) {
                            case 'checkbox':
                                echo $form->checkBox($admin->getModel(), $field, $autoComplete);
                                break;
                            case 'date':
                                echo $form->datePicker($admin->getModel(), $field, $autoComplete, array('null' => true));
                                break;
                            case 'label':
                                echo $form->textField($admin->getModel(), $field, array('readonly' => true));
                                break;
                            case 'textarea':
                                echo $form->textArea($admin->getModel(), $field);
                                break;
                            case 'referer':
                                echo CHtml::hiddenField('referer', Yii::app()->request->getUrlReferrer());
                                break;
                            case 'hidden':
                                echo $form->hiddenInput($admin->getModel(), $field);
                                break;
                            case 'text':
                            default:
                                echo $form->textField($admin->getModel(), $field, array_merge($autoComplete, array('class' => 'cols-full')));
                                break;
                        }
                    } ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tfoot>
        <tr>
            <td>

                <?php
                $form_actions = array();
                if ($admin->getCustomCancelURL() != '') {
                    $form_actions = array('cancel-uri' => $admin->getCustomCancelURL());
                } else {
                    $form_actions = array('cancel-uri' => (Yii::app()->request->getParam('returnUri')) ? Yii::app()->request->getParam('returnUri') : '/' . $this->uniqueid . '/list');
                }

                $extra_buttons = [];
                if (isset($this->admin) && method_exists($this->admin, 'getExtraButton')) {
                    $extra_buttons = $this->admin->getExtraButton();
                }
                $form_actions = array_merge($extra_buttons, $form_actions);
                echo $form->formActions($form_actions);

                ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>
