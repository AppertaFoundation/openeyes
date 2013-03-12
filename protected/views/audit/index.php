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
<h2>Audit log</h2>
<div class="fullWidth fullBox clearfix">
	<div id="whiteBox">
		<p><strong></strong></p>
	</div>

	<div id="waitinglist_display">
		<form method="post" action="/audit/search" id="auditList-filter">
			<input type="hidden" id="page" name="page" value="1" />
			<div id="search-options">

				<div id="main-search" class="grid-view">
					<h3>Filter by:</h3>
						<table>
							<tbody>
								<tr>
									<th>Site:</th>
									<th>Firm:</th>
									<th>User:</th>
									<th>Action:</th>
									<th>Target type:</th>
									<th>Event type:</th>
								</tr>
								<tr class="even">
									<td>
										<?php echo CHtml::dropDownList('site_id',@$_POST['site_id'],Site::model()->getListForCurrentInstitution(),array('empty'=>'All sites'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('firm_id', @$_POST['firm_id'], Firm::model()->getListWithoutDupes(), array('empty'=>'All firms'))?>
									</td>
									<td>
										<?php
											$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
												'id'=>'user',
												'name'=>'user',
												'value'=>'',
												'sourceUrl'=>array('audit/users'),
												'options'=>array(
													'minLength'=>'3',
												),
												'htmlOptions'=>array(
													'style'=>'width: 260px; padding-top: 2px;',
													'placeholder' => 'type to search for users'
												),
											));
										?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('action', @$_POST['action'], $actions, array('empty' => 'All actions'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('target_type', @$_POST['target_type'], $targets, array('empty' => 'All targets'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('event_type_id', @$_POST['event_type_id'], EventType::model()->getActiveList(), array('empty' => 'All event types'))?>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
						<div class="extra-search eventDetail clearfix">
							<label for="date_from">
								From:
							</label>
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name' => 'date_from',
								'id' => 'date_from',
								'options' => array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
								),
								'value' => @$_POST['date_from'],
								'htmlOptions' => array('style' => "width: 95px;"),
							))?>
							<label for="date_to">
								To:
							</label>
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name' => 'date_to',
								'id' => 'date_to',
								'options' => array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
								),
								'value' => @$_POST['date_to'],
								'htmlOptions' => array('style' => "width: 95px;"),
							))?>
							&nbsp;&nbsp;
							Hos num:
							<?php echo CHtml::textField('hos_num',@$_POST['hos_num'],array('style'=>'width: 100px;'))?>
							&nbsp;&nbsp;
							<?php echo CHtml::link('View all',array('audit/'))?>
							&nbsp;&nbsp;&nbsp;
							<?php echo CHtml::link("Auto update on",'#',array('id'=>'auto_update_toggle'))?>
							<div id="audit_filter_button">
								<button type="submit" class="classy green tall" style="float: right;"><span class="button-span button-span-green">Filter</span></button>
								<img class="loader" src="/img/ajax-loader.gif" alt="loading..." style="float: right; margin-left: 0px; margin-right: 10px; margin-top: 8px; display: none;" />
							</div>
							<div class="whiteBox pagination" style="display: none; margin-top: 10px;">
							</div>
						</div>
					</div>
					<input type="hidden" id="previous_site_id" value="<?php echo @$_POST['site_id']?>" />
					<input type="hidden" id="previous_firm_id" value="<?php echo @$_POST['firm_id']?>" />
					<input type="hidden" id="previous_user" value="<?php echo @$_POST['user']?>" />
					<input type="hidden" id="previous_action" value="<?php echo @$_POST['action']?>" />
					<input type="hidden" id="previous_target_type" value="<?php echo @$_POST['target_type']?>" />
					<input type="hidden" id="previous_event_type_id" value="<?php echo @$_POST['event_type_id']?>" />
					<input type="hidden" id="previous_date_from" value="<?php echo @$_POST['date_from']?>" />
					<input type="hidden" id="previous_date_to" value="<?php echo @$_POST['date_to']?>" />
					<input type="hidden" id="previous_hos_num" value="<?php echo @$_POST['hos_num']?>" />
				</form>
				<div id="searchResults" class="whiteBox">
				</div>
				<div id="lower_pagination">
					<div class="extra-search eventDetail clearfix">
						<div class="whiteBox pagination" style="display: none; margin-top: 10px;">
						</div>
					</div>
				</div>
			</div>
			<div style="float: right; margin-right: 18px;">
			</div>
		</div> <!-- .fullWidth -->
<script type="text/javascript">
	handleButton($('#auditList-filter button[type="submit"]'),function(e) {
		$('#searchResults').html('<div id="auditList" class="grid-view"><ul id="auditList"><li class="header"><span>Searching...</span></li></ul></div>');

		$('#page').val(1);

		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('audit/search'); ?>',
			'type': 'POST',
			'data': $('#auditList-filter').serialize(),
			'success': function(data) {
				$('#previous_site_id').val($('#site_id').val());
				$('#previous_firm_id').val($('#firm_id').val());
				$('#previous_user').val($('#user').val());
				$('#previous_action').val($('#action').val());
				$('#previous_target_type').val($('#target_type').val());
				$('#previous_event_type_id').val($('#event_type_id').val());
				$('#previous_date_from').val($('#date_from').val());
				$('#previous_date_to').val($('#date_to').val());

				var s = data.split('<!-------------------------->');

				$('#searchResults').html(s[0]);
				$('div.pagination').html(s[1]).show();

				enableButtons();
			}
		});

		e.preventDefault();
	});

	$(document).ready(function() {
		$('#auditList-filter button[type="submit"]').click();

		$('#auto_update_toggle').click(function() {
			if ($(this).text().match(/update on/)) {
				$(this).text('Auto update off');
				auditLog.run = false;
			} else {
				$(this).text('Auto update on');
				auditLog.run = true;
				auditLog.refresh();
			}
			return false;
		});
	});

	$('#date_from').bind('change',function() {
		$('#date_to').datepicker('option','minDate',$('#date_from').datepicker('getDate'));
	});

	$('#date_to').bind('change',function() {
		$('#date_from').datepicker('option','maxDate',$('#date_to').datepicker('getDate'));
	});
</script>
