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
?>
<h1 class="badge">Partial bookings waiting list</h1>

<div class="box content">
	<div class="panel panel actions row">
		<div class="large-12 column">
			<div class="label">
				Use the filters below to find patients:
			</div>
			<div class="button-bar">

				<?php if ($this->checkAccess('OprnPrint')) {?>
					<button id="btn_print_all" class="small">Print all</button>
					<button id="btn_print" class="small">Print selected</button>
				<?php }?>
				<?php if (Yii::app()->user->checkAccess('admin')) {?>
					<div class="panel admin">
						<label for="adminconfirmdate">Set latest letter sent to be:</label>
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
								'htmlOptions'=>array(
									'class' => 'small fixed-width'
								)
							))?>
					</div>
					<div class="panel admin">
						<select name="adminconfirmto" id="adminconfirmto">
							<option value="OFF">Off</option>
							<option value="noletters">No letters sent</option>
							<option value="0">Invitation letter</option>
							<option value="1">1st reminder letter</option>
							<option value="2">2nd reminder letter</option>
							<option value="3">GP letter</option>
						</select>
					</div>
				<?php }?>
				<?php if ($this->checkAccess('OprnConfirmBookingLetterPrinted')) { ?>
					<button type="submit" class="small secondary" id="btn_confirm_selected">
						Confirm selected
					</button>
				<?php }?>
			</div>
		</div>
	</div>


	<div id="waitinglist_display" class="row">
		<div class="large-12 column">
			<h2>Search partial bookings waiting lists by:</h2>
		</div>
	</div>
	<form class="row search-filters waiting-list" method="post" action="<?php echo Yii::app()->createUrl('/OphTrOperationbooking/waitingList/search')?>" id="waitingList-filter">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<div class="large-12 column">
			<div class="panel">
				<table class="grid">
					<thead>
					<tr>
						<th>Service:</th>
						<th>Firm:</th>
						<th>Next letter due:</th>
						<th>Site:</th>
						<th>Hospital no:</th>
						<th>&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>
							<?php echo CHtml::dropDownList('subspecialty-id', @$_POST['subspecialty-id'], Subspecialty::model()->getList(),
								array('empty'=>'All specialties', 'ajax'=>array(
									'type'=>'POST',
									'data'=>array('subspecialty_id'=>'js:this.value','YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken),
									'url'=>Yii::app()->createUrl('/OphTrOperationbooking/waitingList/filterFirms'),
									'success'=>"js:function(data) {
											if ($('#subspecialty-id').val() != '') {
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
							<?php echo CHtml::dropDownList('firm-id', @$_POST['firm-id'], $this->getFilteredFirms(@$_POST['subspecialty-id']), array('empty'=>'All firms', 'disabled'=>!@$_POST['firm-id']))?>
						</td>
						<td>
							<?php echo CHtml::dropDownList('status', @$_POST['status'], Element_OphTrOperationbooking_Operation::getLetterOptions())?>
						</td>
						<td>
							<?php echo CHtml::dropDownList('site_id',@$_POST['site_id'],Site::model()->getListForCurrentInstitution(),array('empty'=>'All sites'))?>
						</td>
						<td>
							<?php echo CHtml::textField('hos_num',@$_POST['hos_num'],array('autocomplete' => Yii::app()->params['html_autocomplete'], 'size' => 12))?>
							<span id="hos_num_error" class="red"<?php if (!@$_POST['hos_num'] || ctype_digit($_POST['hos_num'])) {?> style="display: none;"<?php }?>>Invalid hospital number</span>
						</td>
						<td class="text-right">
							<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
							<button type="submit" class="secondary">Search</button>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div class="row">
		<div id="searchResults" class="large-12 column">

		</div>
		<div id="search-loading-msg" class="large-12 column hide">
			<div class="alert-box">
				<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" class="spinner" /> <strong>Searching, please wait...</strong>
			</div>
		</div>
	</div>
</div>

