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

if ($this->nopost) {
	$selected_procedures = $model->procedures;
} else {
	$selected_procedures = array();

	if (isset($_POST['Procedures']) && is_array($_POST['Procedures'])) {
		foreach ($_POST['Procedures'] as $proc_id) {
			$selected_procedures[] = Procedure::model()->findByPk($proc_id);
		}
	}
}

if (!isset($_POST['ElementOperation']['decision_date'])) {
	if ($model->decision_date) {
		$_POST['ElementOperation']['decision_date'] = $model->decision_date;
	} else {
		$_POST['ElementOperation']['decision_date'] = date('j M Y',time());
	}
}
?>
					<script type="text/javascript" src="/js/element_operation.js"></script>
					<h4>Operation details</h4>
					<div id="editEyeOperation" class="eventDetail">
						<div class="label">Eye(s):</div>
						<div class="data">
							<input id="ytElementOperation_eye" type="hidden" value="" name="ElementOperation[eye]" />
							<span class="group">
							<input id="ElementOperation_eye_0" value="1" <?php if ($model->eye == '1') {?>checked="checked" <?php }?>type="radio" name="ElementOperation[eye]" />
							<label for="ElementOperation_eye_0">Right</label>
							</span>
							<span class="group">
							<input id="ElementOperation_eye_2" value="2" <?php if ($model->eye == '2') {?>checked="checked" <?php }?>type="radio" name="ElementOperation[eye]" />
							<label for="ElementOperation_eye_2">Both</label>
							</span>
							<span class="group">
							<input id="ElementOperation_eye_1" value="0" <?php if (empty($model->eye)) {?>checked="checked" <?php }?>type="radio" name="ElementOperation[eye]" />
							<label for="ElementOperation_eye_1">Left</label>
							</span>
						</div>
					</div>

					<div id="typeProcedure" class="eventDetail">
						<div class="label">Add procedure:</div>
						<div class="data">
							<?php if (!empty($subsections) || !empty($procedures)) { ?>
								<?php
									if (!empty($subsections)) {
										echo CHtml::dropDownList('subsection_id', '', $subsections, array('empty' => 'Select a subsection'));
										echo CHtml::dropDownList('select_procedure_id', '', array(), array('empty' => 'Select a commonly used procedure', 'style' => 'display: none;'));
									} else {
										echo CHtml::dropDownList('select_procedure_id', '', $procedures, array('empty' => 'Select a commonly used procedure'));
									} ?> &nbsp;
							<?php } ?>
							<span style="display:block; margin-top:10px; margin-bottom:10px;"><strong>or</strong></span>

<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name'=>'procedure_id',
				'id'=>'autocomplete_procedure_id',
				'source'=>"js:function(request, response) {
								var existingProcedures = [];
								$('#procedure_list tbody').children().each(function () {
												var text = $(this).children('td:first').children('span:first').text();
												existingProcedures.push(text.replace(/ remove$/i, ''));
								});

								$.ajax({
												'url': '" . Yii::app()->createUrl('procedure/autocomplete') . "',
												'type':'GET',
												'data':{'term': request.term},
												'success':function(data) {
																data = $.parseJSON(data);

																var result = [];

																for (var i = 0; i < data.length; i++) {
																				var index = $.inArray(data[i], existingProcedures);
																				if (index == -1) {
																								result.push(data[i]);
																				}
																}

																response(result);
												}
								});
				}",
				'options'=>array(
								'minLength'=>'2',
								'select'=>"js:function(event, ui) {
												$.ajax({
																'url': '" . Yii::app()->createUrl('procedure/details') . "',
																'type': 'GET',
																'data': {'name': ui.item.value},
																'success': function(data) {
																			// append selection onto procedure list
																			$('#procedure_list tbody').append(data);
																			$('#procedureDiv').show();
																			$('#procedure_list').show();

																			updateTotalDuration();

																			// clear out text field
																			$('#autocomplete_procedure_id').val('');

																			// remove selection from the filter box
																			if ($('select[name=procedure]').children().length > 0) {
																							var name = $('#procedure_list tbody').children().children(\":nth-child(2)\").text().replace(/ remove$/i, '');
																							$('select[name=procedure] option').each(function () {
																											if ($(this).text() == name) {
																															$(this).remove();
																											}
																							});
																			}
																}
												});
								}",
				),
				'htmlOptions'=>array('style'=>'width: 300px;')
)); ?>
						</div>

						<div id="procedureDiv"<?php if ($newRecord && empty($selected_procedures)) {?> style="display:none;"<?php	} ?>>
							<div class="extraDetails grid-view extraDetails-margin">
								<table id="procedure_list" class="grid" style="width:100%; background:#e3f0f2;<?php
							if ($newRecord && empty($selected_procedures)) { ?> display:none;<?php
							} ?>" title="Procedure List">
									<thead>
										<tr>
											<th>Procedures Added</th>
											<th>Duration</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$totalDuration = 0;
										if (!empty($selected_procedures)) {
											foreach ($selected_procedures as $procedure) {
												$display = "<span>".$procedure['term'] . '</span> - <span>' . $procedure['short_format'] .
													'</span> ' . CHtml::link('remove', '#',
													array('onClick' => "js:return removeProcedure(this);", 'class'=>'removeLink'));
												$totalDuration += $procedure['default_duration']; ?>
										<tr>
											<?php echo CHtml::hiddenField('Procedures[]', $procedure['id']); ?>
											<td><?php echo $display; ?></td>
											<td><?php echo $procedure['default_duration']; ?></td>
										</tr>
									<?php } } ?>
									</tbody>
									<tfoot style="border-top:2px solid #CCC;">
										<tr>
											<td class="topPadded">Calculated Total Duration:</td>
											<td id="projected_duration"><?php echo $totalDuration; ?></td>
										</tr>
										<tr>
											<td>Estimated Total Duration:</td>
											<td><span></span><?php echo CHtml::activeTextField($model, 'total_duration', array('style'=>'width: 40px;')); ?></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

					</div>
					
					<div id="consultRequired" class="eventDetail">
						<div class="label">Consultant required?</div>
						<div class="data">
							<input id="ytElementOperation_consultant_required" type="hidden" value="<?php echo $model->consultant_required?>" name="ElementOperation[consultant_required]" />
							<span class="group">
							<input id="ElementOperation_consultant_required_0" value="1" <?php if ($model->consultant_required) {?>checked="checked" <?php }?>type="radio" name="ElementOperation[consultant_required]" />
							<label for="ElementOperation_consultant_required_0">Yes</label>
							</span>
							<span class="group">
							<input id="ElementOperation_consultant_required_1" value="0" <?php if (!$model->consultant_required) {?>checked="checked" <?php }?>type="radio" name="ElementOperation[consultant_required]" />
							<label for="ElementOperation_consultant_required_1">No</label>
							</span>
						</div>
					</div>
					<div id="anaestheticType" class="eventDetail">
						<div class="label">Anaesthetic type:</div>
						<div class="data">
							<?php foreach ($model->getAnaestheticOptions() as $id => $value) {?>
								<span class="group">
								<input id="ElementOperation_anaesthetic_type_<?php echo $id?>" <?php if ($model->anaesthetic_type == $id){?>checked="checked" <?php }?>value="<?php echo $id?>" type="radio" name="ElementOperation[anaesthetic_type]" />
								<label for="ElementOperation_anaesthetic_type_<?php echo $id?>"><?php echo $value?></label>
								</span>
							<?php }?>
						</div>
					</div>
					<div id="overnightStay" class="eventDetail">
						<div class="label">Post operative stay required?</div>
						<div class="data">
							<input id="ytElementOperation_overnight_stay" type="hidden" value="" name="ElementOperation[overnight_stay]" />
							<span class="group">
								<input id="ElementOperation_overnight_stay_0" value="1" <?php if ($model->overnight_stay == 1){?>checked="checked" <?php }?>type="radio" name="ElementOperation[overnight_stay]" />
								<label for="ElementOperation_overnight_stay_0">Yes</label>
							</span>
							<span class="group">
								<input id="ElementOperation_overnight_stay_1" value="0" <?php if ($model->overnight_stay == 0){?>checked="checked" <?php }?>type="radio" name="ElementOperation[overnight_stay]" />
								<label for="ElementOperation_overnight_stay_1">No</label>
							</span>
						</div>
					</div>

					<div id="site" class="eventDetail">
						<div class="label"><?php echo $form->label(ElementOperation::model(),'site_id'); ?></div>
						<div class="data">
							<?php 
							if (!$model->site_id) {
								$active_site_id = Yii::app()->request->cookies['site_id']->value;	
							} else {
								$active_site_id = $model->site_id;
							}
							echo CHtml::dropDownList('ElementOperation[site_id]', $active_site_id, Site::model()->getList());
							?>
						</div>
						
					</div>

					<div id="urgent" class="eventDetail">
						<div class="label">Priority</div>
						<div class="data">
							<input id="ytElementOperation_urgent" type="hidden" value="<?php echo $model->urgent ?>" name="ElementOperation[urgent]" />
							<span class="group">
								<input id="ElementOperation_urgent_0" value="0" <?php if(!$model->urgent) { ?>checked="checked" <?php } ?>type="radio" name="ElementOperation[urgent]" />
								<label for="ElementOperation_urgent_0">Routine</label>
							</span>
							<span class="group">
								<input id="ElementOperation_urgent_1" value="1" <?php if($model->urgent) { ?>checked="checked" <?php } ?>type="radio" name="ElementOperation[urgent]" />
								<label for="ElementOperation_urgent_1">Urgent</label>
							</span>
						</div>
					</div>
					
					<div id="decisionDate" class="eventDetail">
						<div class="label">Decision Date:</div>
						<div class="data">
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'ElementOperation[decision_date]',
								'id'=>'ElementOperation_decision_date_0',
								// additional javascript options for the date picker plugin
								'options'=>array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
									'maxDate'=>'today'
								),
								'value' => $_POST['ElementOperation']['decision_date'],
								'htmlOptions'=>array('style'=>'width: 110px;')
							)); ?>
						</div>
					</div>

					<div id="addComments" class="eventDetail">
						<div class="label">Add comments:</div>
						<div class="data">
							<textarea rows="4" cols="50" name="ElementOperation[comments]" id="ElementOperation_comments"><?php echo strip_tags($model->comments)?></textarea>
						</div>
					</div>
