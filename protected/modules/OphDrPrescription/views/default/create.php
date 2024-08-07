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

/**
 * @var $errors
 */
$form_id = 'prescription-create';
$this->beginContent('//patient/event_container', array('no_face' => true, 'form_id' => $form_id));
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
));

$settings = new SettingMetadata();
$form_format = $settings->getSetting('prescription_form_format');

// Event actions
$this->event_actions[] = EventAction::button(
    'Save draft',
    'savedraft',
    array('level' => 'primary'),
    array('id' => 'et_save_draft', 'class' => 'button small', 'form' => $form_id)
);
$this->event_actions[] = EventAction::button(
    'Save',
    'save',
    array('level' => 'secondary'),
    array('id' => 'et_save', 'class' => 'button small', 'style' => 'display: none;', 'form' => $form_id)
);
if ($cocoa_api = \Yii::app()->moduleAPI->get("OphInCocoa")) {
    if ($event_button =  $cocoa_api->prescriptionEventAction($form_id)) {
        $this->event_actions[] = $event_button;
    }
}
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::button(
        'Save and print',
        'saveprint',
        array('level' => 'secondary'),
        array('id' => 'et_save_print', 'class' => 'button small', 'style' => 'display: none;', 'form' => $form_id)
    );
    $this->event_actions[] = EventAction::button(
        "Save and print $form_format",
        'saveprintform',
        array('level' => 'secondary'),
        array(
            'id' => 'et_save_print_form',
            'class' => 'button small',
            'style' => 'display: none;',
            'form' => $form_id,
            'data-enabled' => $settings->getSetting('enable_prescription_overprint')
        )
    );
}

Yii::app()->user->setFlash('info.info', 'To finalise this prescription, please sign below. You may save as a draft without a signature.');

$this->displayErrors($errors) ?>

<?php $this->renderOpenElements($this->action->id, $form); ?>
<?php $this->renderOptionalElements($this->action->id, $form); ?>

<?php $this->displayErrors($errors, true) ?>
<?php $this->endWidget(); ?>

<script>
    $(document).ready(function() {
        // excuting toggleActionBtns makes sure that the buttons are at correct state
        // after validation error
        toggleActionBtns();

        $(document).on('signatureAdded', function() {
            toggleActionBtns();
        });

        function toggleActionBtns(){
            const proofs = $('.js-proof-field');
            const proofsWithValues = $('.js-proof-field[value!=""]');
            if (proofs.length === proofsWithValues.length) {
                $('#et_save_draft, #et_save_draft_footer').hide();
                $('#et_save, #et_save_footer, #et_save_print, #et_save_print_footer').show();
            }
        }
    });
</script>

<?php $this->endContent();
