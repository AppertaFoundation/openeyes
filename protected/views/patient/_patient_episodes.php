<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

//FIXME
?>
<section class="box patient-info episodes">
	<header class="box-header">
		<h3 class="box-title">All Episodes</h3>
		<div class="box-info">
			<strong>open <?php echo $episodes_open?> &nbsp;|&nbsp;closed <?php echo $episodes_closed?></strong>
		</div>
	</header>
	<?php if (empty($episodes)) {?>
		<div class="summary">No episodes</div>
	<?php } else {?>
	<table class="patient-episodes grid">
		<thead>
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Firm</th>
				<th>Subspecialty</th>
				<th>Eye</th>
				<th>Diagnosis</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($ordered_episodes as $specialty_episodes) {?>
			<tr class="speciality">
				<td colspan="6"><?php echo $specialty_episodes['specialty'] ?></td>
			</tr>
			<?php foreach ($specialty_episodes['episodes'] as $i => $episode) {?>
				<tr id="<?php echo $episode->id?>" class="clickable all-episode <?php if ($episode->end_date !== null) {?> closed<?php }?>">
					<td><?php echo $episode->NHSDate('start_date'); ?></td>
					<td><?php echo $episode->NHSDate('end_date'); ?></td>
					<td><?php echo $episode->firm ? CHtml::encode($episode->firm->name) : 'N/A'; ?></td>
					<td><?php echo CHtml::encode($episode->getSubspecialtyText())?></td>
					<td><?php echo ($episode->diagnosis) ? $episode->eye->name : 'No diagnosis' ?></td>
					<td><?php echo ($episode->diagnosis) ? $episode->diagnosis->term : 'No diagnosis' ?></td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>
	<?php }?>
</section>
<?php
if ($episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
	$latest = $episode->getLatestEvent();
	$subspecialty = $episode->getSubspecialty();
}

$msg = null;

if (@$latest) {
	$msg = "Latest Event";
	if ($subspecialty) {
		// might not be a subspecialty for legacy
		$msg .= " in " . $subspecialty->name;
	}
	$msg .= ": <strong>" . $latest->eventType->name . "</strong> <span class='small'>(" . $latest->NHSDate('event_date') . ")</span>";
	echo '<div class="box patient-info episode-links">' . CHtml::link($msg,Yii::app()->createUrl('/'.$latest->eventType->class_name.'/default/view/'.$latest->id)) . '</div>';
}
else if ($this->checkAccess('OprnCreateEpisode')) {
	$msg = "Create episode / add event";
	echo '<div class="box patient-info episode-links">' . CHtml::link($msg,Yii::app()->createUrl('patient/episodes/'.$this->patient->id)) . '</div>';
}

try {
	echo $this->renderPartial('custom/info');
} catch (Exception $e) {
	// This is our default layout
	$codes = $this->patient->getSpecialtyCodes();
	// specialist diagnoses
	foreach ($codes as $code) {
		try {
			echo $this->renderPartial('_' . $code . '_diagnoses');
		} catch (Exception $e) {}
	}
	$this->renderPartial('_systemic_diagnoses');
	$this->renderPartial('_previous_operations');
	$this->renderPartial('_medications');
	// specialist extra data
	foreach ($codes as $code) {
		try {
			echo $this->renderPartial('_' . $code . '_info');
		} catch (Exception $e) {}
	}
	$this->renderPartial('_allergies');
	$this->renderPartial('_family_history');
	$this->renderPartial('_social_history');
}
?>
