<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

Yii::app()->clientScript->registerCSSFile(Yii::app()->createUrl('css/theatre_calendar.css'), 'all'); ?>
<h3>Operation Details</h3>
<div class="view">
	<strong><?php echo $data->getEyeLabelText(); ?></strong>
	<?php echo $data->eye->name?>
</div>
<div class="view"><strong>Procedures:</strong><br />
<?php
	$totalDuration = 0;
	if (!empty($data->procedures)) {
		foreach ($data->procedures as $procedure) {
			$totalDuration += $procedure->default_duration;

			echo "{$procedure->short_format} ({$procedure->default_duration} minutes)<br />";
		} ?>
	<p/>
	<p>Calculated Total Duration: <?php echo $totalDuration; ?> min</p>
	<p>Estimated Total Duration: <?php echo $data->total_duration; ?> min</p>
<?php
	} ?>
</div>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('decision_date')); ?>:</b>
	<?php echo CHtml::encode($this->getNHSDate('decision_date')); ?>
	<br />
</div>
<?php
	if ($data->consultant_required) { ?>
<div class="view">
	<strong><?php echo CHtml::encode($data->getAttributeLabel('consultant_required'))?></strong>
	<br />
</div>
<?php
	} ?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('anaesthetic_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->anaesthetic_type->name);

	if ($data->anaesthetist_required) { ?>
	<br />
	<strong><?php echo CHtml::encode($data->getAttributeLabel('anaesthetist_required'))?></strong>
	<br />
<?php
	} ?>
</div>
<?php if (!empty($data->comments)) { ?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('comments')); ?>:</b>
	<?php echo nl2br(CHtml::encode($data->comments)); ?>
	<br />
</div>
<?php
	} ?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('overnight_stay')); ?>:</b>
	<?php echo CHtml::encode($data->getBooleanText('overnight_stay')); ?>
	<br />
</div>
<?php
	if (!empty($data->booking)) { ?>
<div class="view">
	<b><?php echo CHtml::encode('Ward'); ?>:</b>
	<?php echo CHtml::encode($data->booking->ward->name); ?>
	<br />
</div>
<?php
	}
	?>
<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('schedule_timeframe')); ?>:</b>
	<?php echo CHtml::encode($data->getScheduleText()); ?>
	<br />
</div>
<?php
if ($data->status != ElementOperation::STATUS_CANCELLED && !empty($data->booking)) { ?>
<div class="view"><?php $this->renderPartial('/booking/_session',array('operation' => $data));
} ?>
</div>
<div class="cleartall"></div>
<?php
if ($data->schedule_timeframe != $data::SCHEDULE_IMMEDIATELY) {
	Yii::app()->user->setFlash('info',"Patient Request: Schedule On/After " . Helper::convertDate2NHS($data->getMinDate()));
} ?>
<script type="text/javascript">
	$('#procedureDiv').show();
	$('#procedure_list').show();
</script>
