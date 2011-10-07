<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCSSFile('/css/theatre.css', 'all');
?>
<h3 class="title">Theatre Schedules</h3>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'theatre-filter',
	'action'=>Yii::app()->createUrl('theatre/search'),
	'enableAjaxValidation'=>false,
)); ?>
<div id="search-options">
	<div id="main-search">
	<div id="title">Show schedules by:</div>
	<table>
	<tr>
		<th>Site:</th>
		<th>Service:</th>
		<th>Firm:</th>
		<th>Theatre:</th>
		<th>Ward:</th>
	</tr>
	<tr>
		<td><?php
	echo CHtml::dropDownList('site-id', '', Site::model()->getList(),
		array('empty'=>'All sites', 'onChange' => "js:loadTheatres(this.value); loadWards(this.value);")); ?></td>
		<td><?php
	echo CHtml::dropDownList('service-id', '', Service::model()->getList(),
		array('empty'=>'All services', 'ajax'=>array(
			'type'=>'POST',
			'data'=>array('service_id'=>'js:this.value'),
			'url'=>Yii::app()->createUrl('theatre/filterFirms'),
			'success'=>"js:function(data) {
				if ($('#service-id').val() != '') {
					$('#firm-id').attr('disabled', false);
					$('#firm-id').html(data);
				} else {
					$('#firm-id').attr('disabled', true);
					$('#firm-id').html(data);
				}
			}",
		))); ?></td>
		<td><?php
	echo CHtml::dropDownList('firm-id', '', array(),
		array('empty'=>'All firms', 'disabled'=>(empty($firmId)))); ?></td>
		<td><?php
	echo CHtml::dropDownList('theatre-id', '', array(),
		array('empty'=>'All theatres')); ?></td>
		<td><?php
	echo CHtml::dropDownList('ward-id', '', array(),
		array('empty'=>'All wards')); ?></td>
	</tr>
	</table>
	</div>
	<div id="extra-search">
<?php
	echo CHtml::radioButtonList('date-filter', '', Theatre::getDateFilterOptions(),
		array('separator' => '&nbsp;')); ?>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'date-start',
	'id'=>'date-start',
    // additional javascript options for the date picker plugin
    'options'=>array(
		'changeMonth'=>true,
		'changeYear'=>true,
		'showOtherMonths'=>true,
        'showAnim'=>'fold',
		'dateFormat'=>'yy-mm-dd',
		'onSelect'=>"js:function(selectedDate) {
			var option = this.id == 'date-start' ? 'minDate' : 'maxDate',
				instance = $(this).data('datepicker'),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			if (this.id == 'date-start') {
				$('#date-end').datepicker('option', option, date);
			} else {
				$('#date-start').datepicker('option', option, date);
			}
		}",
		'onClose'=>"js:function(dateText, inst) {
			if (dateText != '') {
				$('input[name=date-filter][value=custom]').attr('checked', true);
			}
		}",
    ),
	'htmlOptions'=>array('size'=>10),
));
?> to
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'date-end',
	'id'=>'date-end',
    // additional javascript options for the date picker plugin
    'options'=>array(
		'changeMonth'=>true,
		'changeYear'=>true,
		'showOtherMonths'=>true,
		'showAnim'=>'fold',
		'dateFormat'=>'yy-mm-dd',
		'onSelect'=>"js:function(selectedDate) {
			var option = this.id == 'date-start' ? 'minDate' : 'maxDate',
				instance = $(this).data('datepicker'),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			if (this.id == 'date-start') {
				$('#date-end').datepicker('option', option, date);
			} else {
				$('#date-start').datepicker('option', option, date);
			}
		}",
		'onClose'=>"js:function(dateText, inst) {
			if (dateText != '') {
				$('input[name=date-filter][value=custom]').attr('checked', true);
			}
		}"
    ),
	'htmlOptions'=>array('size'=>10),
));
?>
<button type="submit" value="submit" class="shinybutton highlighted"><span>Search</span></button>
<?php $this->endWidget(); ?>
	</div>
</div>

<div class="search-options">
</div>
<div class="main-search">
</div>
<div class="cleartall"></div>
<div id="searchResults"></div>
<div class="cleartall"></div>
<script type="text/javascript">
	$('#theatre-filter button[type="submit"]').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('theatre/search'); ?>',
			'type': 'POST',
			'data': $('#theatre-filter').serialize(),
			'success': function(data) {
				$('#searchResults').html(data);
				return false;
			}
		});
		return false;
	});
	$('input[name=date-filter]').change(function() {
		if ($(this).val() != 'custom') {
			$('input[id=date-start]').val('');
			$('input[id=date-end]').val('');
		}
	});
	function loadTheatres(siteId) {
		$.ajax({
			'type': 'POST',
			'data': {'site_id': siteId},
			'url': '<?php echo Yii::app()->createUrl('theatre/filterTheatres'); ?>',
			'success':function(data) {
				$('#theatre-id').html(data);
			}
		});
	}
	function loadWards(siteId) {
		$.ajax({
			'type': 'POST',
			'data': {'site_id': siteId},
			'url': '<?php echo Yii::app()->createUrl('theatre/filterWards'); ?>',
			'success':function(data) {
				$('#ward-id').html(data);
			}
		});
	}
</script>
