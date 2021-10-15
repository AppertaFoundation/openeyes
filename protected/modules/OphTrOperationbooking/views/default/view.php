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
$this->beginContent('//patient/event_container', array('no_face'=>true));

if (isset($eur)) {
    $this->renderPartial('view_eur', array('eur' => $eur));
}
if (isset($operation) && $operation) {
    $clinical = $clinical = $this->checkAccess('OprnViewClinical');
    
    $warnings = $this->patient->getWarnings($clinical);
    $this->moduleNameCssClass .= ' highlight-fields';
    $this->title .= ' ('.Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($this->event->id))->status->name.')';
    if ($warnings) { ?>
        <div class="cols-12">
            <div class="alert-box patient with-icon">
                <?php foreach ($warnings as $warn) {?>
                    <strong><?php echo $warn['long_msg']; ?></strong>
                    - <?php echo $warn['details'];
                }?>
            </div>
        </div>
    <?php }?>
    <?php if (!$operation->has_gp) {?>
        <div class="alert-box alert with-icon">
            Patient has no <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> practice address, please correct in PAS before printing <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> letter.
        </div>
    <?php } ?>
    <?php if (!$operation->has_address) { ?>
        <div class="alert-box alert with-icon">
            Patient has no address, please correct in PAS before printing letter.
        </div>
    <?php } ?>

    <?php if ($operation->event->hasIssue()) {?>
        <div class="alert-box issue with-icon">
            <?=\CHtml::encode($operation->event->getIssueText())?>
        </div>
    <?php }?>

    <?php if ($operation->status->name === "On-Hold") { ?>
        <div class="alert-box issue with-icon">
            This event is On-hold: <?= $operation->on_hold_reason . (isset($operation->on_hold_comment) ? ' - ' . $operation->on_hold_comment : "");?>
        </div>
    <?php } ?>

    <?php if ($this->event->delete_pending) {?>
        <div class="alert-box alert with-icon">
            This event is pending deletion and has been locked.
        </div>
    <?php }?>

    <?php
    $this->renderOpenElements($this->action->id);
    $this->renderOptionalElements($this->action->id);
    ?>
    <?php
    $this->renderPartial('//default/delete');
    $this->renderPartial('on_hold');
    // Event actions
    if ($this->checkPrintAccess()) {
        $this->event_actions[] = EventAction::printButton();
    }
}?>
<?php $this->endContent();?>
