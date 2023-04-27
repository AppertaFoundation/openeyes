<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$form_id = 'examination-update';
$this->beginContent('//patient/event_container', array('no_face'=>false , 'form_id' => $form_id)); ?>
<?php
    $this->breadcrumbs = array($this->module->id);

    $this->event_actions[] =
        EventAction::button(
            'Cancel',
            'draft-cancel',
            ['level' => 'cancel'],
            [
                'class' => 'js-event-action-draft-cancel',
                'type' => 'button',
                'style' => 'display: none'
            ]
        );

    $this->event_actions[] =
        EventAction::button(
            'Save Draft',
            'draft',
            ['level' => 'draft'],
            [
                'class' => 'js-event-action-save-draft',
                'icon-class' => 'draft',
                'style' => 'display: none'//Button hidden until draft save functionality is fully implemented
            ]
        );

    $this->event_actions[] =
        EventAction::button(
            'Confirm & Save',
            'save',
            ['level' => 'save'],
            [
                'form' => $form_id,
                'class' => 'js-event-action-save-confirm'
            ]
        );

    $this->event_actions[] =
        EventAction::button(
            'Confirm',
            'confirm',
            ['level' => 'confirm'],
            [
                'class' => 'js-event-action-save-confirm-popup',
                'type' => 'button',
                'style' => 'display: none'
            ]
        );

    $this->event_tabs[] = [
        'label' => 'OE Connection Error',
        'class' => 'js-connection-error-tab sync-error hidden',
        'hidden' => true,
        'icon-class' => 'sync',
        'href' => '#'
    ];

    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => $form_id,
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
            'label' => 4,
            'field' => 8,
        ),
    ));
    ?>

<input type="hidden" name="auto-save-enabled" class="js-auto-save-enabled" value="true">

<?php $this->renderPartial('auto_save_connection_error');?>
<?php $this->renderPartial('auto_save_discard_draft');?>
<?php $this->renderPartial('auto_save_warnings', ['form_id' => $form_id]);?>

<script type='text/javascript'>
    $(document).ready( function(){
        window.formHasChanged = true;

        let eventDraftController = new OpenEyes.EventDraftController({formId: '<?= $form_id ?>'});

        $('.js-auto-save-enabled').data('controller', eventDraftController);
    });
</script>
<?php $this->displayErrors($errors)?>
<?php $this->renderPartial('//patient/event_elements', array('form' => $form));?>
<?php $this->displayErrors($errors, true)?>

<?php $this->endWidget()?>
<?php $this->endContent();?>

<?php Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/VisualAcuity.js", CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/ExaminationSaveHandler.js", CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/ElementFormJSONConverter.js", CClientScript::POS_HEAD); ?>
