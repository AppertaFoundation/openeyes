<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="row">
    <div class="large-8 large-centered column">
        <?php $this->renderPartial('//base/_messages'); ?>
    </div>
</div>
<div class="box content">
	<div class="oe-header-panel row">
		<div class="large-12 column">
			<h1>Theatre Diaries</h1>
			<div class="panel actions">
				<div class="label">
					Use the filters below to view Theatre schedules:
				</div>
				<?php if ($this->checkAccess('OprnPrint')) {?>
					<div class="button-bar">
						<button id="btn_print_diary" class="small">Print</button>
						<button id="btn_print_diary_list" class="small">Print list</button>
					</div>
				<?php }?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="large-12 column">
			<h2>Search schedules by:</h2>
		</div>
	</div>
	<div class="search-filters theatre-diaries">
		<?php $this->beginWidget('CActiveForm', array(
            'id' => 'theatre-filter',
            'htmlOptions' => array(
                'class' => 'row',
            ),
            'enableAjaxValidation' => false,
        ))?>
			<div class="large-12 column">
				<div class="panel">
					<div class="row">
						<div class="large-12 column">

							<table class="grid">
								<thead>
								<tr>
									<th>Site:</th>
									<th>Theatre:</th>
									<th>Subspeciality:</th>
									<th>Firm:</th>
									<th>Ward:</th>
									<th>Emergency list:</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>
										<?php echo CHtml::dropDownList('site-id', @$_POST['site-id'], Site::model()->getListForCurrentInstitution(), array('empty' => 'All sites', 'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : '')))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('theatre-id', @$_POST['theatre-id'], $theatres, array('empty' => 'All theatres', 'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : '')))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('subspecialty-id', @$_POST['subspecialty-id'], Subspecialty::model()->getList(), array('empty' => 'All specialties', 'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : '')))?>
									</td>
									<td>
										<?php if (!@$_POST['subspecialty-id']) {?>
											<?php echo CHtml::dropDownList('firm-id', '', array(), array('empty' => 'All firms', 'disabled' => 'disabled'))?>
										<?php } else {?>
											<?php echo CHtml::dropDownList('firm-id', @$_POST['firm-id'], Firm::model()->getList(@$_POST['subspecialty-id']), array('empty' => 'All firms', 'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : '')))?>
										<?php }?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('ward-id', @$_POST['ward-id'], $wards, array('empty' => 'All wards', 'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : '')))?>
									</td>
									<td>
										<?php echo CHtml::checkBox('emergency_list', (@$_POST['emergency_list'] == 1))?>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">

						<div class="large-10 column">

							<div class="search-filters-extra clearfix">
								<label class="inline highlight">
									<input type="radio" name="date-filter" id="date-filter_0" value="today"<?php if (@$_POST['date-filter'] == 'today') {?> checked="checked"<?php }?>>
									Today
								</label>
								<label class="inline highlight">
									<input type="radio" name="date-filter" id="date-filter_1" value="week"<?php if (@$_POST['date-filter'] == 'week') {?> checked="checked"<?php }?>>
									Next 7 days
								</label>
								<label class="inline highlight">
									<input type="radio" name="date-filter" id="date-filter_2" value="month"<?php if (@$_POST['date-filter'] == 'month') {?> checked="checked"<?php }?>>
									Next 30 days
								</label>
								<fieldset class="inline highlight">
									<label>
										<input type="radio" name="date-filter" id="date-filter_3" value="custom"<?php if (@$_POST['date-filter'] == 'custom') {?> checked="checked"<?php }?>>
										or select date range:
									</label>
									<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                            'name' => 'date-start',
                                            'id' => 'date-start',
                                            'options' => array(
                                                'showAnim' => 'fold',
                                                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                            ),
                                            'value' => @$_POST['date-start'],
                                            'htmlOptions' => array('class' => 'small fixed-width'),
                                        ))?>
									<span class="to">to</span>
									<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                            'name' => 'date-end',
                                            'id' => 'date-end',
                                            'options' => array(
                                                'showAnim' => 'fold',
                                                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                            ),
                                            'value' => @$_POST['date-end'],
                                            'htmlOptions' => array('class' => 'small fixed-width'),
                                        ))?>
									<ul class="button-group small">
										<li><a href="#" id="last_week" class="small button">Last week</a></li>
										<li><a href="#" id="next_week" class="small button">Next week</a></li>
									</ul>
								</fieldset>
							</div>
						</div>
						<div class="large-2 column text-right">

							<span style="width: 30px;">
								<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
							</span>

							<button id="search_button" class="secondary" type="submit">
								Search
							</button>
						</div>
					</div>
				</div>
			</div>
		<?php $this->endWidget()?>
	</div>

	<div class="row hide" id="theatre-search-loading">
		<div class="large-12 column">
			<div class="alert-box">
				<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" class="spinner" /> <strong>Please wait...</strong>
			</div>
		</div>
	</div>

	<div id="theatreList" class="theatres-list"></div>
	<div class="printable" id="printable"></div>
</div>

<div id="iframeprintholder" style="display: none;"></div>

<script type="text/javascript">
	$(document).ready(function() {
		return getDiary();
	});
</script>
