<?php

/**
 * (C) OpenEyes Foundation, 2019
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

$Element = Element_OphDrPrescription_Details::model()->find('event_id=?', array($this->event->id));
$settings = new SettingMetadata();
$form_format = $settings->getSetting('prescription_form_format');
$form_option = OphDrPrescription_DispenseCondition::model()->findByAttributes(array('name' => 'Print to {form_type}'));

?>
<?php $this->beginContent('//patient/event_container', ['Element' => $Element, 'no_face' => true]); ?>

<?php
// Event actions
$elementEditable = $Element->isEditableByMedication();
$editAccess = $this->checkEditAccess();
if (!$elementEditable) {
    $this->event_actions = array_filter($this->event_actions, function ($action) {
        return $action->label !== 'Delete';
    });
}
if (($Element->draft) && (!$elementEditable) && $editAccess) {
    if (!$Element->isSignedByMedication()) {
        $this->event_actions[] = EventAction::button(
            'Save as final',
            'save',
            array('level' => 'secondary'),
            array(
                'id' => 'et_save_final',
                'class' => 'button small',
                'type' => 'button',
                'data-element' => $Element->id
            )
        );
    }
}

if ($this->checkPrintAccess()) {
    if (!$Element->draft || $editAccess) {
        foreach ($Element->items as $item) {
            // If at least one prescription item has 'Print to FP10' selected as the dispense condition, display the Print FP10 button.
            if ($item->dispense_condition->id === $form_option->id && $settings->getSetting('enable_prescription_overprint') === 'on') {
                $this->event_actions[] = EventAction::button("Print $form_format", 'print_' . strtolower($form_format));
                break;
            }
        }
    }
    if (!$Element->draft && $editAccess) {
        $this->event_actions[] = EventAction::printButton();
    }
}
?>

<?php $this->renderPartial('//base/_messages'); ?>

<?php $this->renderOpenElements($this->action->id, null, array('form_setting' => $form_format)); ?>
<?php $this->renderOptionalElements($this->action->id); ?>

<?php
if (isset(Yii::app()->session['print_prescription'])) { ?>
    <?php unset(Yii::app()->session['print_prescription']); ?>
    <script>
        $(document).ready(function() {
            do_print_prescription();
        });
    </script>
<?php } elseif (isset(Yii::app()->session['print_prescription_fp10']) || isset(Yii::app()->session['print_prescription_wp10'])) {
    ?>
    <script>
        $(document).ready(function() {
            do_print_fpTen('<?= isset(Yii::app()->session['print_prescription_fp10']) ? 'FP10' : 'WP10' ?>');
        });
        <?php
        unset(Yii::app()->session['print_prescription_fp10'], Yii::app()->session['print_prescription_wp10']); ?>
    </script>
<?php } ?>

<?php $this->renderPartial('//default/delete'); ?>
<?php $this->endContent(); ?>
