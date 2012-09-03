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

$this->header();

Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile(Yii::app()->createUrl('css/theatre_calendar.css'), 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
	<p>
		Patient: <?php echo $patient->getDisplayName() . ' (' . $patient->hos_num . ')'; ?>
	</p>
	<div id="operation">
		<h1>Cancel operation</h1>
<?php
echo CHtml::form(array('booking/cancelOperation'), 'post', array('id' => 'cancelForm'));
echo CHtml::hiddenField('operation_id', $operation->id); ?>
<?php echo CHtml::label('Cancellation reason: ', 'cancellation_reason'); ?>
<?php if (!empty($operation->booking) && (strtotime($operation->booking->session->date) <= strtotime('now'))) {
	$listIndex = 3;
} else {
	$listIndex = 2;
} ?>
<?php echo CHtml::dropDownList('cancellation_reason', '',
	CancellationReason::getReasonsByListNumber($listIndex),
	array('empty'=>'Select a reason')
); ?>
		<br/>
<?php echo CHtml::label('Comments: ', 'cancellation_comment'); ?>
		<div style="height: 0.4em;"></div>
		<textarea name="cancellation_comment" rows=6 cols=40></textarea>
		<div style="height: 0.4em;"></div>
		<div class="buttonwrapper">
			<button type="submit" class="classy red venti"><span class="button-span button-span-red">Cancel operation</span></button>
			<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
<?php echo CHtml::endForm(); ?>
	</div>
</div>
<div class="alertBox" style="margin-top: 10px; display:none"><p>Please fix the following input errors:</p>
<ul><li>&nbsp;</li></ul></div>
<script type="text/javascript">
	$('#cancelForm button[type="submit"]').click(function () {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			$.ajax({
				type: 'POST',
				url: '<?php echo Yii::app()->createUrl('booking/cancelOperation')?>',
				data: $('#cancelForm').serialize(),
				dataType: 'json',
				success: function(data) {
					var n=0;
					var html = '';
					$.each(data, function(key, value) {
						html += '<ul><li>'+value+'</li></ul>';
						n += 1;
					});

					if (n == 0) {
						window.location.href = '<?php echo Yii::app()->createUrl('patient/event/'.$operation->event->id)?>';
					} else {
						$('div.alertBox').show();
						$('div.alertBox').html(html);
					}

					enableButtons();
					return false;
				}
			});
		}

		return false;
	});
</script>
<?php $this->footer()?>
