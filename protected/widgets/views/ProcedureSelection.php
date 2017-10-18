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
<div class="row field-row procedure-selection eventDetail<?php if ($last) {?> eventDetailLast<?php }?>" id="typeProcedure"<?php if ($hidden) {?> style="display: none;"<?php }?>>
	<div class="large-2 column">
		<label for="select_procedure_id_<?php echo $identifier;?>">
			<?php echo $label?>:
		</label>
	</div>
	<div class="large-4 column">
		<fieldset>
			<legend><em>Add a procedure:</em></legend>
			<?php if ($headertext) {?>
				<p><em><?php echo $headertext?></em></p>
			<?php }?>
			<?php
            if (!empty($subsections) || !empty($procedures)) {
                if (!empty($subsections)) {?>
					<div class="field-row">
						<?php echo CHtml::dropDownList('subsection_id_'.$identifier, '', $subsections, array('empty' => 'Select a subsection'));?>
					</div>
					<div class="field-row hide">
						<?php echo CHtml::dropDownList('select_procedure_id_'.$identifier, '', array(), array('empty' => 'Select a commonly used procedure'));?>
					</div>
				<?php } else { ?>
					<div class="field-row">
						<?php echo CHtml::dropDownList('select_procedure_id_'.$identifier, '', $procedures, array('empty' => 'Select a commonly used procedure'));?>
					</div>
				<?php }
            }
            ?>
			<div class="field-row">
				<?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'procedure_id_'.$identifier,
                        'id' => 'autocomplete_procedure_id_'.$identifier,
                        'source' => "js:function(request, response) {

						var existingProcedures = $('#procedureList_$identifier .procedure .value')
							.map(function() { return $(this).text(); })
							.get();

						$.ajax({
							'url': '".Yii::app()->createUrl('procedure/autocomplete')."',
							'type':'GET',
							'data':{'term': request.term, 'restrict': '$restrict'},
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
                        'options' => array(
                            'minLength' => '2',
                            'select' => "js:function(event, ui) {
							if (typeof(window.callbackVerifyAddProcedure) == 'function') {
								window.callbackVerifyAddProcedure(ui.item.value,".($durations ? '1' : '0').",function(result) {
									if (result != true) {
										$('#autocomplete_procedure_id_$identifier').val('');
										return;
									}
									ProcedureSelectionSelectByName(ui.item.value,true,'$identifier');
								});
							} else {
								ProcedureSelectionSelectByName(ui.item.value,true,'$identifier');
							}
						}",
                        ),
                        'htmlOptions' => array('placeholder' => 'or enter procedure here'),
                    )); ?>
			</div>
		</fieldset>
	</div>

	<?php
    $totalDuration = 0;
    ?>
	<div class="large-6 column">
		<div id="procedureList_<?php echo $identifier?>" class="panel procedures" style="<?php if (empty($selected_procedures)) {?> display: none;<?php }?>">
			<table class="plain">
				<thead>
				<tr>
					<th>Procedure</th>
					<?php if ($durations) {?>
						<th>Duration</th>
					<?php }?>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody class="body">
				<?php
                if (!empty($selected_procedures)) {
                    foreach ($selected_procedures as $procedure) {
                        $totalDuration += $procedure['default_duration'];
                        ?>
						<tr class="item">
							<td class="procedure">
								<span class="field"><?= CHtml::hiddenField('Procedures_'.$identifier.'[]', $procedure['id']); ?></span>
								<span class="value"><?= $procedure['term']; ?></span>
							</td>
							<?php if ($durations) {?>
								<td class="duration">
									<?php echo $procedure['default_duration']?> mins
								</td>
							<?php } ?>
							<td>
								<a href="#" class="removeProcedure">Remove</a>
							</td>
						</tr>
					<?php	}
                    if (isset($_POST[$class]['total_duration_'.$identifier])) {
                        $total_duration = $_POST[$class]['total_duration_'.$identifier];
                    }
                }?>
				</tbody>
			</table>
			<?php if ($durations) {?>
				<table class="grid durations">
					<tfoot>
					<tr>
						<td>
							Calculated Total Duration:
						</td>
						<td id="projected_duration_<?php echo $identifier?>">
							<?php echo CHtml::encode($totalDuration)?> mins
						</td>
						<td>
							Estimated Total Duration:
						</td>
						<td>
							<input
								type="text"
								value="<?php echo $total_duration?>"
								id="<?php echo $class?>_total_duration_<?php echo $identifier?>"
								name="<?php echo $class?>[total_duration_<?php echo $identifier?>]"
								style="width:60px"
								/>
						</td>
					</tr>
					</tfoot>
				</table>
			<?php }?>
		</div>
	</div>
</div>

<script type="text/javascript">
// Note: Removed_stack is probably not the best name for this. Selected procedures is more accurate.
// It is used to suppress procedures from the add a procedure inputs
var removed_stack_<?php echo $identifier?> = [<?php echo implode(',', $removed_stack); ?>];

function updateTotalDuration(identifier)
{

	// update total duration
	var totalDuration = 0;
	$('#procedureList_'+identifier).find('.item').map(function() {
		$(this).find('.duration').map(function() {
			totalDuration += parseInt($(this).html().match(/[0-9]+/));
		});
	});
	if ($('input[name=\"<?php echo $class?>[eye_id]\"]:checked').val() == 3) {
		$('#projected_duration_'+identifier).text(totalDuration + ' * 2');
		totalDuration *= 2;
	}
	$('#projected_duration_'+identifier).text(totalDuration+" mins");
	$('#<?php echo $class?>_total_duration_'+identifier).val(totalDuration);
}

$('a.removeProcedure').die('click').live('click',function() {
	var m = $(this).parent().parent().parent().parent().parent().attr('id').match(/^procedureList_(.*?)$/);
	removeProcedure($(this),m[1]);
	return false;
});

function removeProcedure(element, identifier)
{

	var len = element.parent().parent().parent().children('tr').length;
	var procedure_id = element.parent().parent().find('input[type="hidden"]:first').val();

	element.parent().parent().remove();

	<?php if ($durations) {?>
	updateTotalDuration(identifier);
	<?php }?>

	if (len <= 1) {
		$('#procedureList_'+identifier).hide();
		<?php if ($durations) {?>
		$('#procedureList_'+identifier).find('.durations').hide();
		<?php }?>
	}

	if (typeof(window.callbackRemoveProcedure) == 'function') {
		callbackRemoveProcedure(procedure_id);
	}

	// Remove removed procedure from the removed_stack
	var stack = [];
	var popped = null;
	$.each(window["removed_stack_"+identifier], function(key, value) {
		if (value["id"] != procedure_id) {
			stack.push(value);
		} else {
			popped = value;
		}
	});
	window["removed_stack_"+identifier] = stack;

	// Refresh the current procedure select box in case the removed procedure came from there
	if ($('#subsection_id_'+identifier).length) {
		// Procedures are in subsections, so fetch a clean list via ajax (easier than trying to work out if it's the right list)
		updateProcedureSelect(identifier);
	} else if (popped) {
		// No subsections, so we should be safe to just push it back into the list
		$('#select_procedure_id_'+identifier).append('<option value="'+popped["id"]+'">'+popped["name"]+'</option>').removeAttr('disabled');
		sort_selectbox($('#select_procedure_id_'+identifier));
	}

	return false;
}

function selectSort(a, b)
{
	if (a.innerHTML == rootItem) {
		return -1;
	} else if (b.innerHTML == rootItem) {
		return 1;
	}
	return (a.innerHTML > b.innerHTML) ? 1 : -1;
};

$('select[id^=subsection_id]').unbind('change').change(function() {
	var m = $(this).attr('id').match(/^subsection_id_(.*)$/);
	updateProcedureSelect(m[1]);
});

function updateProcedureSelect(identifier)
{
	var subsection_field = $('select[id=subsection_id_'+identifier+']');
	var subsection = subsection_field.val();
	if (subsection != '') {


		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('procedure/list')?>',
			'type': 'POST',
			'data': {'subsection': subsection, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
			'success': function(data) {
				$('select[name=select_procedure_id_'+identifier+']').attr('disabled', false);
				$('select[name=select_procedure_id_'+identifier+']').html(data);

				// remove any items in the removed_stack
				$('select[name=select_procedure_id_'+identifier+'] option').map(function() {
					var obj = $(this);

					$.each(window["removed_stack_"+identifier], function(key, value) {
						if (value["id"] == obj.val()) {
							obj.remove();
						}
					});
				});

				$('select[name=select_procedure_id_'+identifier+']').parent().show();
			}
		});
	} else {
		$('select[name=select_procedure_id_'+identifier+']').parent().hide();
	}
}

$('select[id^="select_procedure_id"]').unbind('change').change(function() {
	var m = $(this).attr('id').match(/^select_procedure_id_(.*)$/);
	var identifier = m[1];
	var select = $(this);
	var procedure = $('select[name=select_procedure_id_'+m[1]+'] option:selected').text();
	if (procedure != 'Select a commonly used procedure') {

		if (typeof(window.callbackVerifyAddProcedure) == 'function') {
			window.callbackVerifyAddProcedure(procedure,"<?php echo $durations ? '1' : '0';?>",function(result) {
				if (result != true) {
					select.val('');
					return;
				}

				if (typeof(window.callbackAddProcedure) == 'function') {
					var procedure_id = $('select[name=select_procedure_id_'+identifier+'] option:selected').val();
					callbackAddProcedure(procedure_id);
				}

				ProcedureSelectionSelectByName(procedure,false,m[1]);
			});
		} else {
			if (typeof(window.callbackAddProcedure) == 'function') {
				var procedure_id = $('select[name=select_procedure_id_'+identifier+'] option:selected').val();
				callbackAddProcedure(procedure_id);
			}

			ProcedureSelectionSelectByName(procedure,false,m[1]);
		}
	}
	return false;
});

<?php if ($durations): ?>
	$(document).ready(function() {
		if ($('input[name="<?php echo $class?>[eye_id]"]:checked').val() == 3) {
			$('#projected_duration_<?php echo $identifier?>').html((parseInt($('#projected_duration_<?php echo $identifier?>').html().match(/[0-9]+/)) * 2) + " mins");
		}
		$('input[name="<?php echo $class?>[eye_id]"]').click(function() {
			updateTotalDuration('<?php echo $identifier?>');
		});
	});
<?php endif ?>

function ProcedureSelectionSelectByName(name, callback, identifier)
{
	$.ajax({
		'url': baseUrl + '/procedure/details?durations=<?php echo $durations ? '1' : '0'?>&identifier='+identifier,
		'type': 'GET',
		'data': {'name': name},
		'success': function(data) {
			var enableDurations = <?php echo $durations ? 'true' : 'false'?>;

			// append selection onto procedure list
			$('#procedureList_'+identifier).find('.body').append(data);
			$('#procedureList_'+identifier).show();

			if (enableDurations) {
				updateTotalDuration(identifier);
				$('#procedureList_'+identifier).find('.durations').show();
			}

			// clear out text field
			$('#autocomplete_procedure_id_'+identifier).val('');

			// remove selection from the filter box
			if ($('#select_procedure_id_'+identifier).children().length > 0) {
				m = data.match(/<span class="value">(.*?)<\/span>/);

				$('#select_procedure_id_'+identifier).children().each(function () {
					if ($(this).text() == m[1]) {
						var id = $(this).val();
						var name = $(this).text();

						window["removed_stack_"+identifier].push({name: name, id: id});

						$(this).remove();
					}
				});
			}

			if (callback && typeof(window.callbackAddProcedure) == 'function') {
				m = data.match(/<input type=\"hidden\" value=\"([0-9]+)\"/);
				var procedure_id = m[1];
				callbackAddProcedure(procedure_id);
			}
		}
	});
}
</script>
