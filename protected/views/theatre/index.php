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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

?>
		<h2>Theatre Schedules</h2>

		<div class="fullWidth fullBox clearfix">
			<div id="whiteBox">
				<p><strong>Use the filters below to view Theatre schedules:</strong></p>
			</div>

			<div id="theatre_display">
				<?php $this->beginWidget('CActiveForm', array('id'=>'theatre-filter', 'action'=>Yii::app()->createUrl('theatre/search'), 'enableAjaxValidation'=>false))?>
				<div id="search-options">
					<div id="main-search" class="grid-view">
						<h3>Search schedules by:</h3>
							<table>
								<tbody>
								<tr>
									<th>Site:</th>
									<th>Theatre:</th>
									<th>Firm:</th>
									<th>Specialty:</th>
									<th>Ward:</th>
									<th>Emergency List:</th>
								</tr>
								<tr class="even">
									<td>
										<?php echo CHtml::dropDownList('site-id', '', Site::model()->getList(), array('empty'=>'All sites', 'onChange' => "js:loadTheatres(this.value); loadWards(this.value);"))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('theatre-id', '', array(), array('empty'=>'All theatres'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('firm-id', '', array(), array('empty'=>'All firms', 'disabled'=>(empty($firmId))))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('specialty-id', '', Specialty::model()->getList(), array('empty'=>'All specialties', 'ajax'=>array('type'=>'POST', 'data'=>array('specialty_id'=>'js:this.value'), 'url'=>Yii::app()->createUrl('theatre/filterFirms'), 'success'=>"js:function(data) {
				if ($('#specialty-id').val() != '') {
					$('#firm-id').attr('disabled', false);
					$('#firm-id').html(data);
				} else {
					$('#firm-id').attr('disabled', true);
					$('#firm-id').html(data);
				}
			}",
		)))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('ward-id', '', array(), array('empty'=>'All wards'))?>
									</td>
									<td>
										<?php echo CHtml::checkBox('emergency_list')?>
									</td>
								</tr>
								</tbody>
							</table>
					</div> <!-- #main-search -->
					<div id="extra-search" class="eventDetail clearfix">
						<div class="data">
							<span class="group">
							<input type="radio" name="date-filter" id="date-filter_0" value="today">
							<label for="date-filter_0">Today</label>
							</span>
							<span class="group">
							<input type="radio" name="date-filter" id="date-filter_1" value="week">
							<label for="date-filter_1">This week</label>
							</span>
							<span class="group">
							<input type="radio" name="date-filter" id="date-filter_2" value="month">
							<label for="date-filter_2">This month</label>
							</span>
							<span class="group">
							<input type="radio" name="date-filter" id="date-filter_3" value="custom">
							<label for="date-filter_3">or select date range:</label>
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
?>
							to
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
							</span>

							<button value="submit" type="submit" class="btn_search ir" style="float:right;">Search</button>
							<button value="print" type="button" style="float:right;" id="btn-print">Print</button>
							<?php $this->endWidget()?>
						</div>

					</div> <!-- #extra-search -->
					</form>
				</div> <!-- #search-options -->

				<div id="theatreList">
				</div>
			</div> <!-- #theatre_display -->

		</div> <!-- .fullWidth -->
<script type="text/javascript">
	$(document).ready(function() {
		return getList();
	});
	$('#theatre-filter button[type="submit"]').click(function() {
		return getList();
	});
        $('#btn-print').click(function() {
                alert('a');
                printContent();
        });
	function getList() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('theatre/search'); ?>',
			'type': 'POST',
			'data': $('#theatre-filter').serialize(),
			'success': function(data) {
				$('#theatreList').html(data);
				return false;
			}
		});
		return false;
	}

	$('input[name=emergency_list]').change(function() {
		if ($(this).is(':checked')) {
			$('#site-id').attr("disabled", true);
			$('#specialty-id').attr("disabled", true);
			$('#theatre-id').attr("disabled", true);
			$('#firm-id').attr("disabled", true);
			$('#ward-id').attr("disabled", true);
		} else {
			$('#site-id').attr("disabled", false);
			$('#specialty-id').attr("disabled", false);
			$('#theatre-id').attr("disabled", false);
			$('#firm-id').attr("disabled", false);
			$('#ward-id').attr("disabled", false);
		}
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
