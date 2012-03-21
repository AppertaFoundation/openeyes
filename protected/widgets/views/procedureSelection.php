					<div id="typeProcedure" class="eventDetail">
						<div class="label">Add procedure:</div>
						<div class="data">
							<?php if (!empty($this->subsections) || !empty($this->procedures)) { ?>
								<?php
									if (!empty($this->subsections)) {
										echo CHtml::dropDownList('subsection_id', '', $this->subsections, array('empty' => 'Select a subsection'));
										echo CHtml::dropDownList('select_procedure_id', '', array(), array('empty' => 'Select a commonly used procedure', 'style' => 'display: none;'));
									} else {
										echo CHtml::dropDownList('select_procedure_id', '', $this->procedures, array('empty' => 'Select a commonly used procedure'));
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
												var text = $(this).children('td:first').children('strong:first').children('span:first').text();
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
						<div id="procedureDiv"<?php if ($this->newRecord && empty($this->selected_procedures)) {?> style="display:none;"<?php } ?>>
							<div class="extraDetails grid-view extraDetails-margin">
								<table id="procedure_list" class="grid" style="width:100%; background:#e3f0f2;<?php
							if ($this->newRecord && empty($this->selected_procedures)) { ?> display:none;<?php
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
										if (!empty($this->selected_procedures)) {
											foreach ($this->selected_procedures as $procedure) {
												$display = "<strong><span>".$procedure['term'] . '</span> - <span>' . $procedure['short_format'] .
													'</span></strong> ' . CHtml::link('remove', '#',
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
											<td><span></span><?php echo CHtml::activeTextField($this->model, 'total_duration', array('style'=>'width: 40px;')); ?></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
