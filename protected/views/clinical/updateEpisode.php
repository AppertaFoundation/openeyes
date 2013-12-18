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


// Event actions
$this->event_actions[] = EventAction::link('Cancel', Yii::app()->createUrl('/patient/episode/'.$episode->id), array('level' => 'cancel'));
$this->event_actions[] = EventAction::button('Save', 'save', array('id' => 'episode_save', 'level' => 'save'));

if ($episode->diagnosis) {
	$eye = $episode->eye ? $episode->eye->name : 'None';
	$diagnosis = $episode->diagnosis ? $episode->diagnosis->term : 'none';
} else {
	$eye = 'No diagnosis';
	$diagnosis = 'No diagnosis';
}

$episode->audit('episode summary','view',false);

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id'=>'update-episode',
		'enableAjaxValidation'=>false,
		'action'=>array('patient/updateepisode/'.$episode->id),
));
?>

	<div class="element-data">
		<h2>Summary</h2>
		<h3><?php echo $episode->firm->serviceSubspecialtyAssignment->subspecialty->name?></h3>
	</div>

	<?php if ($error) {?>
		<div id="clinical-create_es_" class="alert-box alert with-icon">
			<p>Please fix the following input errors:</p>
			<ul>
				<li>
					<?php echo $error?>
				</li>
			</ul>
		</div>
	<?php }?>

	<section class="element element-data">
		<h3 class="data-title">Principal diagnosis:</h3>
		<div class="row">
			<div class="large-5 column end">
				<?php
				$form->widget('application.widgets.DiagnosisSelection',array(
						'field' => 'disorder_id',
						'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
						'code' => 130,
						'layout' => 'episodeSummary',
				));
				?>
			</div>
		</div>
	</section>

	<?php
	if (!empty($_POST)) {
		$eye_id = @$_POST['eye_id'];
	} else {
		$eye_id = $episode->eye_id;
	}
	?>
	<section class="element element-data">
		<fieldset>
			<legend class="data-title">Principal eye:</legend>
			<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {?>
				<label class="inline">
					<?php echo CHtml::radioButton('eye_id', ($eye->id == $eye_id), array('value' => $eye->id,'class'=>'episodeSummaryRadio'))?>
					<?php echo $eye->name?>
				</label>
			<?php }?>
		</fieldset>
	</section>

	<section class="element element-data">
		<div class="row">
			<div class="large-6 column">
				<h3 class="data-title">Start Date</h3>
				<div class="data-value"><?php echo $episode->NHSDate('start_date')?></div>
			</div>
			<div class="large-6 column">
				<h3 class="data-title">End date:</h3>
				<div class="data-value"><?php echo !empty($episode->end_date) ? $episode->NHSDate('end_date') : '(still open)'?></div>
			</div>
		</div>
	</section>

	<section class="element element-data">
		<div class="row">
			<div class="large-6 column">
				<h3 class="data-title">Subspecialty:</h3>
				<div class="data-value"><?php echo $episode->firm->serviceSubspecialtyAssignment->subspecialty->name?></div>
			</div>
			<div class="large-6 column">
				<h3 class="data-title">Consultant firm:</h3>
				<div class="data-value"><?php echo $episode->firm->name?></div>
			</div>
		</div>
	</section>

	<?php
	try {
		echo $this->renderPartial('/clinical/episodeSummaries/' . $episode->firm->serviceSubspecialtyAssignment->subspecialty_id, array('episode' => $episode));
	} catch (Exception $e) {
		// If there is no extra episode summary detail page for this subspecialty we don't care
	}
	?>

	<div class="metadata">
		<span class="info">
			<?php echo $episode->firm->serviceSubspecialtyAssignment->subspecialty->name?>: created by <span class="user"><?php echo $episode->user->fullName?></span>
			on <?php echo $episode->NHSDate('created_date')?> at <?php echo substr($episode->created_date,11,5)?>
		</span>
	</div>

	<section class="element element-data">
		<h3 class="data-title">Episode Status:</h3>
		<div class="row">
			<div class="large-2 column">
				<label for="episode_status_id"><?php echo CHtml::encode($episode->getAttributeLabel('episode_status_id'))?>:</label>
			</div>
			<div class="large-3 column end">
				<?php echo CHtml::dropDownList('episode_status_id', $episode->episode_status_id, EpisodeStatus::Model()->getList())?>
			</div>
		</div>
	</section>

	<div class="metadata">
		<span class="info">
			Status last changed by <span class="user"><?php echo $episode->usermodified->fullName?></span>
			on <?php echo $episode->NHSDate('last_modified_date')?> at <?php echo substr($episode->last_modified_date,11,5)?>
		</span>
	</div>

	<?php if ($error) {?>
		<div id="clinical-create_es_" class="alert-box alert with-icon">
			<p>Please fix the following input errors:</p>
			<ul>
				<li>
					<?php echo $error?>
				</li>
			</ul>
		</div>
	<?php }?>

<?php $this->endWidget()?>


<script type="text/javascript">
	$('#closelink').click(function() {
		$('#dialog-confirm').dialog({
			resizable: false,
			height: 140,
			modal: false,
			buttons: {
				"Close episode": function() {
					$.ajax({
						url: $('#closelink').attr('href'),
						type: 'GET',
						success: function(data) {
							$('#episodes_details').show();
							$('#episodes_details').html(data);
						}
					});
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
				open: function() {
					$(this).parents('.ui-dialog-buttonpane button:eq(1)').focus();
				}
		});
		return false;
	});
</script>

<?php if (empty($episode->end_date)) {?>
	<div style="text-align:right; position:relative;">
		<!--button id="close-episode" type="submit" value="submit" class="wBtn_close-episode ir">Close Episode</button-->

		<div id="close-episode-popup" class="popup red" style="display: none;">
			<p style="text-align:left;">You are closing this episode. This can not be undone. Once an episode is closed it can not be re-opened.</p>
			<p><strong>Are you sure?</strong></p>
			<div class="action_options">
				<span class="aBtn"><a id="yes-close-episode" href="#"><strong>Yes, I am</strong></a></span>
				<span class="aBtn"><a id="no-close-episode" href="#"><strong>No, cancel this.</strong></a></span>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('#close-episode').unbind('click').click(function(e) {
			e.preventDefault();
			$('#close-episode-popup').slideToggle(100);
			return false;
		});

		$('#no-close-episode').unbind('click').click(function(e) {
			e.preventDefault();
			$('#close-episode-popup').slideToggle(100);
			return false;
		});

		$('#yes-close-episode').unbind('click').click(function(e) {
			e.preventDefault();
			$('#close-episode-popup').slideToggle(100);
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('clinical/closeepisode/'.$episode->id)?>',
				success: function(data) {
					$('#event-content').html(data);
					return false;
				}
			});

			return false;
		});

		handleButton($('#episode_cancel'),function(e) {
			window.location.href = window.location.href.replace(/updateepisode/,'episode');
			e.preventDefault();
		});

		handleButton($('#et_save'),function(e) {
			$('#update-episode').submit();
		});
	</script>
<?php }?>
