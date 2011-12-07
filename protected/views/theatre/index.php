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
										<?php echo CHtml::dropDownList('firm-id', $firm->id, Firm::model()->getList($firm->serviceSpecialtyAssignment->specialty_id), array('empty'=>'All firms'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('specialty-id', $firm->serviceSpecialtyAssignment->specialty_id, Specialty::model()->getList(), array('empty'=>'All specialties', 'ajax'=>array('type'=>'POST', 'data'=>array('specialty_id'=>'js:this.value'), 'url'=>Yii::app()->createUrl('theatre/filterFirms'), 'success'=>"js:function(data) {
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
							<label for="date-filter_1">Next 7 days</label>
							</span>
							<span class="group">
							<input type="radio" name="date-filter" id="date-filter_2" value="month">
							<label for="date-filter_2">Next 30 days</label>
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
									'showAnim'=>'fold',
									'dateFormat'=>'d-M-yy'
								),
								'value' => '',
								'htmlOptions'=>array('style'=>'width: 110px;')
							));
?>
							to
<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'date-end',
								'id'=>'date-end',
								// additional javascript options for the date picker plugin
								'options'=>array(
									'showAnim'=>'fold',
									'dateFormat'=>'d-M-yy'
								),
								'value' => '',
								'htmlOptions'=>array('style'=>'width: 110px;')
							));
?>
							</span>
							<span class="group">
							<a href="" id="last_week">Last week</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" id="next_week">Next week</a>
							</span>

							<button value="submit" type="submit" class="btn_search ir" style="float:right;">Search</button>
							<?php $this->endWidget()?>
						</div>

					</div> <!-- #extra-search -->
					</form>
				</div> <!-- #search-options -->

				<div id="theatreList">
				</div>

				<!-- ====================================================  P R I N T	S T U F F ============	-->
				<div class="printable" id="printable">

				</div> <!-- end of printable area -->
				<!-- ====================================================  end of P R I N T  S T U F F ============  -->

			</div> <!-- #theatre_display -->
			<div style="text-align:right; margin-right:10px;">
				<button type="submit" value="submit" class="btn_save ir" id="btn_save" style="display: none;">Save</button>
				<button type="submit" value="submit" class="btn_cancel ir" id="btn_cancel" style="display: none;">Cancel</button>
				<button type="submit" value="submit" class="btn_print ir" id="btn_print">Print</button>
			</div>
		</div> <!-- .fullWidth -->
		<div id="iframeprintholder" style="display: none;"></div>
<script type="text/javascript">
	var searchData;

	$(document).ready(function() {
		return getList();
	});
	$('#theatre-filter button[type="submit"]').click(function() {
		return getList();
	});

	$(document).ready(function() {
		$("#btn_print").click(function() {
			printElem(
			{
						pageTitle:'openeyes printout',
						printBodyOptions:{styleToAdd:'width:auto !important; margin: 0.75em !important;',classNameToAdd : 'openeyesPrintout'},
									overrideElementCSS:['css/style.css',{href:'css/style.css',media:'print'}]
			 });
		});
	});

	function printElem(options){
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('theatre/printList'); ?>',
			'type': 'POST',
			'data': searchData,
			'success': function(data) {
				$('#printable').html(data);
				$('#printable').printElement(options);
				return false;
			}
		});
	}

	/*$(document).ready(function() {
		$("#btn_print").click(function() {
			window.location.href = '<?php echo Yii::app()->createUrl('theatre/printList')?>?'+searchData;
		});
	});*/

	function getList() {
		searchData = $('#theatre-filter').serialize();

		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('theatre/search'); ?>',
			'type': 'POST',
			'data': searchData,
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

	$('#btn_save').click(function() {
		var data = {}

		$('input[name^="admitTime"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			data["operation_"+id] = $(this).val();
		});

		$('textarea[name^="comments"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			data["comments_"+id] = $(this).val();
		});

		$('input[name^="confirm_"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			data["confirm_"+id] = $(this).val();
		});

		$.ajax({
			'type': 'POST',
			'data': data,
			'url': '<?php echo Yii::app()->createUrl('theatre/saveSessions'); ?>',
			'success': function(data) {
				$('#updated-flash').show();

				// Apply changes to the read-only values in the dom
				$('input[name^="admitTime"]').map(function() {
					var id = $(this).attr('id').match(/[0-9]+/);
					$('#admitTime_ro_'+id).html($(this).val());
				});
				$('textarea[name^="comments"]').map(function() {
					var id = $(this).attr('id').match(/[0-9]+/);
					$('#comments_ro_'+id).html($(this).val());
				});

				view_mode();
				load_table_states();
			}
		});
	});

	function getmonth(i) {
		switch (i) {
			case 0: return 'Jan';
			case 1: return 'Feb';
			case 2: return 'Mar';
			case 3: return 'Apr';
			case 4: return 'May';
			case 5: return 'Jun';
			case 6: return 'Jul';
			case 7: return 'Aug';
			case 8: return 'Sep';
			case 9: return 'Oct';
			case 10: return 'Nov';
			case 11: return 'Dec';
		}
	}

	function getmonth_r(m) {
		switch (m) {
			case 'Jan': return 0;
			case 'Feb': return 1;
			case 'Mar': return 2;
			case 'Apr': return 3;
			case 'May': return 4;
			case 'Jun': return 5;
			case 'Jul': return 6;
			case 'Aug': return 7;
			case 'Sep': return 8;
			case 'Oct': return 9;
			case 'Nov': return 10;
			case 'Dec': return 11;
		}
	}

	function format_date(d) {
		return d.getDate()+"-"+getmonth(d.getMonth())+"-"+d.getFullYear();
	}

	$('#date-filter_0').click(function() {
		today = new Date();

		$('#date-start').val(format_date(today));
		$('#date-end').val(format_date(today));

		return true;
	});

	$('#date-filter_1').click(function() {
		today = new Date();

		$('#date-start').val(format_date(today));

		$('#date-end').val(format_date(returnDateWithInterval(today, 6)));

		return true;
	});

	$('#date-filter_2').click(function() {
		today = new Date();

		$('#date-start').val(format_date(today));

		$('#date-end').val(format_date(returnDateWithInterval(today, 29)));

		return true;
	});

	$('#last_week').click(function() {
		// Calculate week before custom date or week before today if no custom date
		sd = $('#date-start').val();

		if (sd == '') {
			// No date-start. Make date-start one week before today, date-end today
			today = new Date();
	
			$('#date-end').val(format_date(today));
	
			$('#date-start').val(format_date(returnDateWithInterval(today, -7)));
		} else {
			// Make date-end date-start, make date-start one week before date-start
			$('#date-end').val(sd);

			$('#date-start').val(format_date(returnDateWithIntervalFromString(sd, -7)));
		}

		// Perform search

		return false;
	});

	$('#next_week').click(function() {
		// Calculate week before custom date or week before today if no custom date
		ed = $('#date-end').val();

		if (ed == '') {
			// No date-start. Make date-start one week before today, date-end today
			today = new Date();

			$('#date-start').val(format_date(today));

			$('#date-end').val(format_date(returnDateWithInterval(today, 7)));
		} else {
			// Make date-start date-end, make date-end one week after date-end

			$('#date-start').val(ed);

			$('#date-end').val(format_date(returnDateWithIntervalFromString(ed, 7)));
		}

		return false;
	});

	function returnDateWithInterval(d, interval) {
		// Uses javascript date format (months from 0 to 11)
		dateWithInterval = new Date(d.getTime() + (86400000 * interval));
		return dateWithInterval;
	}

	function returnDateWithIntervalFromString(ds, interval) {
		// Uses real date format (months from 1 to 12)
		times = ds.split('-');

		// Convert to javascript date format
		date = new Date(times[2], getmonth_r(times[1]), times[0], 0, 0, 0, 0);

		dateWithInterval = new Date(date.getTime() + (86400000 * interval));

		return dateWithInterval;
	}

	function dateString(date) {
		m = date.getMonth() + 1;
		if (m < 10) {
			m = '0' + m;
		}

		d = date.getDate();
		if (d < 10) {
			d = '0' + d;
		}

		return date.getFullYear() + '-' + m + '-' + d;
	}
</script>
