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
$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);
?>

<?php $this->beginContent('//patient/event_container'); ?>

	<?php
    $this->moduleNameCssClass .= ' highlight-fields';
    $this->title .= ' ('.Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($this->event->id))->status->name.')'?>

<?php if ($warnings) { ?>
	<div class="row">
		<div class="large-12 column">
			<div class="alert-box patient with-icon">
				<?php foreach ($warnings as $warn) {?>
					<strong><?php echo $warn['long_msg']; ?></strong>
					- <?php echo $warn['details'];
				}?>
			</div>
		</div>
	</div>
<?php }?>

	<?php if (!$operation->has_gp) {?>
		<div class="alert-box alert with-icon">
			Patient has no GP practice address, please correct in PAS before printing GP letter.
		</div>
	<?php } ?>
	<?php if (!$operation->has_address) { ?>
		<div class="alert-box alert with-icon">
			Patient has no address, please correct in PAS before printing letter.
		</div>
	<?php } ?>

	<?php if ($operation->event->hasIssue()) {?>
		<div class="alert-box issue with-icon">
			<?php echo CHtml::encode($operation->event->getIssueText())?>
		</div>
	<?php }?>

	<?php if ($this->event->delete_pending) {?>
		<div class="alert-box alert with-icon">
			This event is pending deletion and has been locked.
		</div>
	<?php }?>

	<?php
    $this->renderOpenElements($this->action->id);
    $this->renderOptionalElements($this->action->id);
    ?>

<?php $this->endContent();?>
