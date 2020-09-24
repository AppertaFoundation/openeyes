<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$form_id = 'operationchecklists-step';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id)); ?>

<?php
$this->event_actions[] = EventAction::button('Save draft', 'savedraft', array('level' => 'secondary'), array('id' => 'et_save_draft', 'class' => 'button small', 'form' => $form_id));
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id));
?>
<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 4,
        'field' => 8,
    ),
));
?>
<?php $this->displayErrors($errors, false, $customErrorHeaderMessage)?>
<input type="hidden" id="isDraft" name="isDraft" value="<?php echo isset($_POST['isDraft']) ? $_POST['isDraft'] : '' ?>" />
<input type="hidden" name="isDocumentationResponseDifferent" value="<?php echo isset($_POST['isDocumentationResponseDifferent']) ? $_POST['isDocumentationResponseDifferent'] : '' ?>" />
<input type="hidden" name="isClinicalResponseDifferent" value="<?php echo isset($_POST['isClinicalResponseDifferent']) ? $_POST['isClinicalResponseDifferent'] : '' ?>" />
<input type="hidden" name="isNursingResponseDifferent" value="<?php echo isset($_POST['isNursingResponseDifferent']) ? $_POST['isNursingResponseDifferent'] : '' ?>" />
<input type="hidden" name="isDVTResponseDifferent" value="<?php echo isset($_POST['isDVTResponseDifferent']) ? $_POST['isDVTResponseDifferent'] : '' ?>" />
<input type="hidden" name="isPatientSupportResponseDifferent" value="<?php echo isset($_POST['isPatientSupportResponseDifferent']) ? $_POST['isPatientSupportResponseDifferent'] : '' ?>" />
<?php $this->renderPartial('//patient/event_elements', array('form' => $form));?>
<?php $this->displayErrors($errors, true, $customErrorHeaderMessage)?>

<?php $this->endWidget() ?>
<?php $this->endContent(); ?>
