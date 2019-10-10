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
$form_id = 'clinical-create';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));
$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => $form_id,
            'enableAjaxValidation' => false,
    ));

    // Event actions

    $this->event_actions[] = EventAction::button('Save', '#', null, array('name' => 'scheduleLater', 'id' => 'et_save', 'class' => 'button small', 'form' => $form_id, 'style' => $this->checkScheduleAccess() ? 'display: none;' : ''));
    $this->event_actions[] = EventAction::button('Save and Schedule later', '#', null, array('name' => 'scheduleLater', 'id' => 'et_save_and_schedule_later', 'class' => 'button small', 'form' => $form_id, 'style' => $this->checkScheduleAccess() ? '' : 'display: none;'));
    $this->event_actions[] = EventAction::button('Save and Schedule now', '#', array('level' => 'secondary'), array('name' => 'scheduleNow', 'id' => 'et_save_and_schedule', 'class' => 'button small', 'form' => $form_id, 'style' => $this->checkScheduleAccess() ? '' : 'display: none;'));
    ?>
    <input type="hidden" name="schedule_now" id="schedule_now" value="0" />
<?php if ($warnings) { ?>
        <div class="cols-12 column">
            <div class="alert-box patient with-icon">
                <?php foreach ($warnings as $warn) {?>
                    <strong><?php echo $warn['long_msg']; ?></strong>
                    - <?php echo $warn['details'];
                }?>
            </div>
        </div>
<?php }?>
    <?php
    $this->displayErrors($errors);
    $this->renderOpenElements($this->action->id, $form);
    $this->renderOptionalElements($this->action->id, $form);
    $this->displayErrors($errors, true);
    $this->endWidget()?>

<?php $this->endContent();?>
