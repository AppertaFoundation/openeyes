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
?>
		<h2>Partial bookings waiting List</h2>

		<div class="fullWidth fullBox clearfix">
			<div id="whiteBox">
				<p><strong>Use the filters below to find patients:</strong></p>
			</div>

		<div id="waitinglist_display">
			<form method="post" action="<?php echo Yii::app()->createUrl('waitingList/search')?>" id="waitingList-filter">
				<div id="search-options">

					<div id="main-search" class="grid-view">
						<h3>Search partial bookings waiting lists by:</h3>
									<table>
									<tbody>
										<tr>
													<th>Service:</th>
													<th>Firm:</th>
													<th>Next letter due:</th>
													<th>Site:</th>
													<th>Hospital no:</th>
										</tr>
										<tr  class="even">
													<td>
														<?php
														echo CHtml::dropDownList('subspecialty-id', @$_POST['subspecialty-id'], Subspecialty::model()->getList(),
																		array('empty'=>'All sub-specialties', 'ajax'=>array(
																						'type'=>'POST',
																						'data'=>array('subspecialty_id'=>'js:this.value'),
																						'url'=>Yii::app()->createUrl('waitingList/filterFirms'),
																						'success'=>"js:function(data) {
																										if ($('#subspecialty-id').val() != '') {
																														$('#firm-id').attr('disabled', false);
																														$('#firm-id').html(data);
																										} else {
																														$('#firm-id').attr('disabled', true);
																														$('#firm-id').html(data);
																										}
																						}",
													))); ?>
									</td>
													<td>

														<?php
														echo CHtml::dropDownList('firm-id', @$_POST['firm-id'], array(), array('empty'=>'All firms', 'disabled'=>(empty($firmId))));
														?>
													</td>
													<td>
														<?php
													echo CHtml::dropDownList('status', @$_POST['status'], ElementOperation::getLetterOptions())
											?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('site_id',@$_POST['site_id'],Site::model()->getListForCurrentInstitution(),array('empty'=>'All sites'))?>
									</td>
									<td>
										<input type="text" size="12" name="hos_num" id="hos_num" value="<?php echo @$_POST['hos_num']?>" /><br/>
										<span id="hos_num_error" class="red"<?php if (!@$_POST['hos_num'] || ctype_digit($_POST['hos_num'])){?> style="display: none;"<?php }?>>Invalid hospital number</span>
									</td>
									<td width="20px;" style="margin-left: 50px; border: none;">
										<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="float: right; margin-left: 0px; display: none;" />
									</td>
									<td style="padding: 0;" width="70px;">
										<button type="submit" class="classy green tall" style="float: right;"><span class="button-span button-span-green">Search</span></button>
									</td>
								</tr>
								</tbody>
							</table>
									</div> <!-- #main-search -->
									<!-- extra search currently just used as padding but could be used like Theatre Management for extra filtering -->
									<div id="extra-search" class="eventDetail clearfix">
										<h5>Search Results:</h5>

								<!--<div class="data">
									no extra search filters
								</div>-->
							</div> <!-- #extra-search -->
						</div> <!-- #search-options -->
					</form>

				<div id="searchResults" class="whiteBox">
				</div> <!-- #searchResults -->
			</div> <!-- #waitinglist_display -->
			<div style="float: right; margin-right: 18px;">
				<button style="margin-right: 15px;" type="submit" class="classy blue tall" id="btn_print_all"><span class="button-span button-span-blue">Print all</span></button>
				<button style="margin-right: 15px;" type="submit" class="classy blue grande" id="btn_print"><span class="button-span button-span-blue">Print selected</span></button>
				<?php if (Yii::app()->user->checkAccess('admin')) { ?>

						<span class="data admin-confirmto">Set latest letter sent to be:
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'adminconfirmdate',
								'id'=>'adminconfirmdate',
								// additional javascript options for the date picker plugin
								'options'=>array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
									'maxDate'=>'today'
								),
								'value' => date("j M Y"),
								'htmlOptions'=>array('style'=>'width: 110px;')
							)); ?>
						</span>

				<span class="admin-confirmto">
					<select name="adminconfirmto" id="adminconfirmto">
						<option value="OFF">Off</option>
						<option value="noletters">No letters sent</option>
						<option value="0">Invitation letter</option>
						<option value="1">1st reminder letter</option>
						<option value="2">2nd reminder letter</option>
						<option value="3">GP letter</option>
					</select>
				</span>
				<?php } ?>
				<button type="submit" class="classy green venti" id="btn_confirm_selected"><span class="button-span button-span-green">Confirm selected</span></button>
			</div>
		</div> <!-- .fullWidth -->
<script type="text/javascript">
	$('#waitingList-filter button[type="submit"]').click(function() {
		if (!$(this).hasClass('inactive')) {
			if ($('#hos_num').val().length <1 || $('#hos_num').val().match(/^[0-9]+$/)) {
				$('#hos_num_error').hide();
			} else {
				$('#hos_num_error').show();
				return false;
			}

			disableButtons();
			$('#searchResults').html('<div id="waitingList" class="grid-view-waitinglist"><table><tbody><tr><th>Letters sent</th><th>Patient</th><th>Hospital number</th><th>Location</th><th>Procedure</th><th>Eye</th><th>Firm</th><th>Decision date</th><th>Priority</th><th>Book status (requires...)</th><th><input style="margin-top: 0.4em;" type="checkbox" id="checkall" value=""></th></tr><tr><td colspan="7" style="border: none; padding-top: 10px;"><img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" /> Searching, please wait ...</td></tr></tbody></table></div>');

			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('waitingList/search'); ?>',
				'type': 'POST',
				'data': $('#waitingList-filter').serialize(),
				'success': function(data) {
					$('#searchResults').html(data);
					enableButtons();
					return false;
				}
			});
		}
		return false;
	});

	$('#btn_print').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			print_items_from_selector('input[id^="operation"]:checked',false);
			enableButtons();
		}
	});

	$('#btn_print_all').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			print_items_from_selector('input[id^="operation"]:enabled',true);
			enableButtons();
		}
	});

	function print_items_from_selector(sel,all) {
		var operations = new Array();

		var nogp = 0;

		var operations = $(sel).map(function(i,n) {
			var no_gp = $(n).parent().parent().hasClass('waitinglistOrange') && $(n).parent().html().match(/>NO GP</)

			if (no_gp) nogp += 1;

			if (!no_gp) {
				return $(n).attr('id').replace(/operation/,'');
			}
		}).get();

		if (operations.length == 0) {
			if (nogp == 0) {
				alert("No items selected for printing.");
			} else {
				show_letter_warnings(nogp);
			}
		} else {
			show_letter_warnings(nogp);
			printPDF('<?php echo Yii::app()->createUrl('waitingList/printletters')?>', {'operations': operations, 'all': all});
		}
	}

	function show_letter_warnings(nogp) {
		var msg = '';

		if (nogp >0) {
			msg += nogp+" item"+(nogp == 1 ? '' : 's')+" could not be printed as the patient has no GP practice.";
		}

		if (msg.length >0) {
			alert(msg);
		}
	}

	$('#btn_confirm_selected').click(function() {
		if (!$(this).hasClass('inactive')) {
			var data = '';
			var operations = 0;
			data += "adminconfirmto=" + $('#adminconfirmto').val() + "&adminconfirmdate=" + $('#adminconfirmdate').val();
			$('input[id^="operation"]:checked').map(function() {
				if (data.length >0) {
					data += '&';
				}
				data += "operations[]=" + $(this).attr('id').replace(/operation/,'');
				operations += 1;
			});

			if (operations == 0) {
				alert('No items selected.');
			} else {
				disableButtons();

				$.ajax({
					url: '<?php echo Yii::app()->createUrl('waitingList/confirmPrinted')?>',
					type: "POST",
					data: data,
					success: function(html) {
						enableButtons();
						$('#waitingList-filter button[type="submit"]').click();
					}
				});
			}
		}

		return false;
	});

	$(document).ready(function() {
		$('#hos_num').focus();

		if ($('#subspecialty-id').val() != '') {
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('waitingList/filterFirms')?>',
				type: "POST",
				data: "subspecialty_id="+$('#subspecialty-id').val(),
				success: function(data) {
					$('#firm-id').attr('disabled', false);
					$('#firm-id').html(data);
					$('#firm-id').val(<?php echo @$_POST['firm-id']?>);
					$('#waitingList-filter button[type="submit"]').click();
				}
			});
		} else {
			$('#waitingList-filter button[type="submit"]').click();
		}

		$('#firm-id').bind('change',function() {
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('waitingList/filterSetFirm')?>',
				type: "POST",
				data: "firm_id="+$('#firm-id').val(),
				success: function(data) {
				}
			});
		});

		$('#status').bind('change',function() {
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('waitingList/filterSetStatus')?>',
				type: "POST",
				data: "status="+$('#status').val(),
				success: function(data) {
				}
			});
		});

		$('#site_id').bind('change',function() {
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('waitingList/filterSetSiteId')?>',
				type: "POST",
				data: "site_id="+$('#site_id').val(),
				success: function(data) {
				}
			});
		});

		$('#hos_num').bind('keyup',function() {
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('waitingList/filterSetHosNum')?>',
				type: "POST",
				data: "hos_num="+$('#hos_num').val(),
				success: function(data) {
				}
			});
		});
	});
</script>
