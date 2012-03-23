					<div class="eventDetail" id="typeProcedure">
						<div class="label">Procedures:</div>
						<div class="data limitWidth">
							<?php
							$totalDuration = 0;
							?>
							<div id="procedureList" class="eventHighlight big" style="width:auto; line-height:1.6;<?php if (empty($selected_procedures)){?> display: none;<?php }?>">
								<h4>
									<?php
									if (!empty($selected_procedures)) {
										foreach ($selected_procedures as $procedure) {?>
											<div class="procedureItem">
												<?php
												$totalDuration += $procedure['default_duration'];
												echo CHtml::hiddenField('Procedures[]', $procedure['id']);
												echo "<span>".$procedure['term'].'</span> - <span>'.$procedure['short_format']?></span>
												<a href="#" class="small removeProcedure"><strong>(remove)</strong></a>
												<?php if ($durations) {?>
													<span style="float:right;"><?php echo $procedure['default_duration']?> mins</span><br />
												<?php }?>
											</div>
										<?php	}
									}
									?>
								</h4>
							</div>
								
							<div class="extraDetails grid-view"<?php if (empty($selected_procedures) || !$durations){?> style="display: none;"<?php }?>>
								<table class="grid">
									<tfoot>
										<tr>
											<th>Calculated Total Duration:</th>
											<th id="projected_duration"><?php echo $totalDuration?> mins</th>
											<th>Estimated Total Duration:</th>
											<th><input type="text" value="<?php echo $total_duration?>" id="<?php echo $class?>_total_duration" name="<?php echo $class?>[total_duration]" style="width: 60px;"></th>
										</tr>
									</tfoot>
								</table>
							</div>
							
							<h5 class="normal"><em>Add a procedure:</em></h5>

							<?php
							if (!empty($subsections) || !empty($procedures)) {
								if (!empty($subsections)) {
									echo CHtml::dropDownList('subsection_id', '', $subsections, array('empty' => 'Select a subsection', 'style' => 'width: 525px; margin-bottom:10px;'));
									echo CHtml::dropDownList('select_procedure_id', '', array(), array('empty' => 'Select a commonly used procedure', 'style' => 'display: none; width: 525px; margin-bottom:10px;'));
								} else {
									echo CHtml::dropDownList('select_procedure_id', '', $procedures, array('empty' => 'Select a commonly used procedure', 'style' => 'width: 525px; margin-bottom:10px;'));
								}
							}
							?>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name'=>'procedure_id',
				'id'=>'autocomplete_procedure_id',
				'source'=>"js:function(request, response) {
								var existingProcedures = [];
								$('div.procedureItem').map(function() {
												var text = $(this).children('span:first').text();
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
																'url': '" . Yii::app()->createUrl('procedure/details') . "?durations=".($durations?'1':'0')."',
																'type': 'GET',
																'data': {'name': ui.item.value},
																'success': function(data) {
																			var enableDurations = ".($durations?'true':'false').";

																			// append selection onto procedure list
																			$('#procedureList').children('h4').append(data);
																			$('#procedureList').show();

																			if (enableDurations) {
																				updateTotalDuration();
																				$('div.extraDetails').show();
																			}

																			// clear out text field
																			$('#autocomplete_procedure_id').val('');

																			// remove selection from the filter box
																			if ($('#select_procedure_id').children().length > 0) {
																							m = data.match(/<span>(.*?)<\/span>/);

																							$('#select_procedure_id').children().each(function () {
																											if ($(this).text() == m[1]) {
																															$(this).remove();
																											}
																							});
																			}

																		if (typeof(window.callbackAddProcedure) == 'function') {
																			m = data.match(/<input type=\"hidden\" value=\"([0-9]+)\"/);
																			var procedure_id = m[1];
																			callbackAddProcedure(procedure_id);
																		}
																}
												});
								}",
				),
				'htmlOptions'=>array('style'=>'width: 520px;','placeholder'=>'or type the first few characters of a procedure')
)); ?>
						</div>
					</div>
<script type="text/javascript">
	function updateTotalDuration() {
		// update total duration
		var totalDuration = 0;
		$('div.procedureItem').map(function() {
			$(this).children('span:last').map(function() {
				totalDuration += parseInt($(this).html().match(/[0-9]+/));
			});
		});
		if ($('input[name=\"<?php echo $class?>[eye_id]\"]:checked').val() == 3) {
			$('#projected_duration').text(totalDuration + ' * 2');
			totalDuration *= 2;
		}
		$('#projected_duration').text(totalDuration+" mins");
		$('#<?php echo $class?>_total_duration').val(totalDuration);
	}

	$('a.removeProcedure').live('click',function() {
		var len = $(this).parent().parent().children('div').length;

		var procedure_id = $(this).parent().children('input:first').val();

		$(this).parent().remove();

		<?php if ($durations) {?>
			updateTotalDuration();
		<?php }?>

		if (len <= 1) {
			$('#procedureList').hide();
			<?php if ($durations) {?>
				$('div.extraDetails').hide();
			<?php }?>
		}

		if (typeof(window.callbackAddProcedure) == 'function') {
			callbackRemoveProcedure(procedure_id);
		}

		return false;
	});

	$('select[id=subsection_id]').change(function() {
		var subsection = $('select[name=subsection_id] option:selected').val();
		if (subsection != '') {
			var existingProcedures = [];
			$('div.procedureItem').map(function() {
				existingProcedures.push($(this).children('span:first').text().replace(/ remove$/i, ''));
			});
			$.ajax({
				'url': '/procedure/list',
				'type': 'POST',
				'data': {'subsection': subsection, 'existing': existingProcedures},
				'success': function(data) {
					$('select[name=select_procedure_id]').attr('disabled', false);
					$('select[name=select_procedure_id]').html(data);
					$('select[name=select_procedure_id]').show();
				}
			});
		} else {
			$('select[name=select_procedure_id]').hide();
		}
	});

	$('#select_procedure_id').change(function() {
		var procedure = $('select[name=select_procedure_id] option:selected').text();
		if (procedure != 'Select a commonly used procedure') {

			if (typeof(window.callbackAddProcedure) == 'function') {
				var procedure_id = $('select[name=select_procedure_id] option:selected').val();
				callbackAddProcedure(procedure_id);
			}

			$.ajax({
				'url': '/procedure/details?durations=<?php echo ($durations?'1':'0')?>',
				'type': 'GET',
				'data': {'name': procedure},
				'success': function(data) {
					// append selection onto procedure list
					$('#procedureList').children('h4').append(data);
					$('#procedureList').show();

					<?php if ($durations) {?>
						$('div.extraDetails').show();
						updateTotalDuration();
					<?php }?>

					// clear out text field
					$('#autocomplete_procedure_id').val('');

					// remove the procedure from the options list
					$('select[name=select_procedure_id] option:selected').remove();

					// disable the dropdown if there are no items left to select
					if ($('select[name=select_procedure_id] option').length == 1) {
						$('select[name=select_procedure_id]').attr('disabled', true);
					}
				}
			});
		}
		return false;
	});

	$(document).ready(function() {
		if ($('input[name=\"<?php echo $class?>[eye_id]\"]:checked').val() == 3) {
			$('#projected_duration').html((parseInt($('#projected_duration').html().match(/[0-9]+/)) * 2) + " mins");
		}
	});
</script>
