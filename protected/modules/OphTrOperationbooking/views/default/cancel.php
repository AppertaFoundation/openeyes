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
$this->beginContent('//patient/event_container'); ?>

	<?php $this->title = 'Cancel operation' ?>

	<?php
    echo CHtml::form(Yii::app()->createUrl('/'.$operation->event->eventType->class_name.'/default/cancel'), 'post', array('id' => 'cancelForm', 'class' => 'edit cancel'));
    echo CHtml::hiddenField('operation_id', $operation->id); ?>

	<div class="alert-box alert with-icon hide">
		<p>Please fix the following input errors:</p>
	</div>

	<div class="element-fields">
		<div class="row field-row">
			<div class="large-2 column">
				<div class="field-label">
					Patient:
				</div>
			</div>
			<div class="large-10 column end">
				<div class="field-value">
					<?php echo $patient->getDisplayName().' ('.$patient->hos_num.')'; ?>
				</div>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Cancellation reason: ', 'cancellation_reason'); ?>
			</div>
			<div class="large-5 column end">
				<?php if (!empty($operation->booking) && (strtotime($operation->booking->session->date) <= strtotime('now'))) {
    $listIndex = 3;
} else {
    $listIndex = 2;
} ?>
				<?php echo CHtml::dropDownList('cancellation_reason', '', OphTrOperationbooking_Operation_Cancellation_Reason::getReasonsByListNumber($listIndex),
                    array('empty' => 'Select a reason')
                ); ?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Comments: ', 'cancellation_comment'); ?>
			</div>
			<div class="large-5 column end">
				<textarea id="cancellation_comment" name="cancellation_comment" rows="6"></textarea>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-10 column large-offset-2">
				<button type="submit" class="warning" id="cancel">Cancel operation</button>
				<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
	<?php echo CHtml::endForm(); ?>
<?php $this->endContent();?>
