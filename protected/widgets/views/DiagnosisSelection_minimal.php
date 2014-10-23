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
<?php $primary_selector_id = "{$class}_{$field}"; ?>

<?php if (!$nowrapper) {?>
	<div class="row field-row diagnosis-selection">
		<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
			<label for="<?php echo $primary_selector_id ;?>">Diagnosis:</label>
		</div>
		<div class="large-<?php echo $layoutColumns['field'];?> column end">
<?php }?>
			<?php
			$list_options = array('empty' => 'Select a commonly used diagnosis');

			if ($secondary_to) {
				$list_options['options'] = array();
				foreach ($secondary_to as $id => $lst) {
					if (count($lst)) {
						$list_options['options'][$id] = array();
					}
					$data = array();
					$second_order = 1;
					foreach ($lst as $sid => $term) {
						$data[] = array('id' => $sid, 'term' => $term, 'order' => $second_order++);
					}
					$list_options['options'][$id]['data-secondary-to'] = CJSON::encode($data);
				}
			}

			$order = 1;
			foreach ($options as $i => $opt) {
				$list_options['options'][$i]['data-order'] = $order++;
			}
			?>
			<?php echo !empty($options) ? CHtml::dropDownList("{$class}[$field]", '', $options, $list_options) : ""?>
<?php if (!$nowrapper) {?>
		</div>
	</div>
<?php }?>

	<?php if ($secondary_to) {?>
		<?php if (!$nowrapper) {?>
			<div id="div_<?php echo "{$primary_selector_id}_secondary_to"?>" class="row field-row hidden">
				<?php if (!$nowrapper) {?>
					<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
						<label for="<?php echo "{$primary_selector_id}_secondary_to";?>">Associated with:</label>
					</div>
				<?php }?>
				<div class="large-<?php echo $layoutColumns['field'];?> column end">
		<?php }?>
				<?php echo CHtml::dropDownList("{$class}[{$field}_secondary_to]", '', array(), array())?>
		<?php if (!$nowrapper) {?>
				</div>
			</div>
		<?php }?>
	<?php }?>

	<?php if (!$nowrapper) {?>
		<div class="row field-row">
			<?php if (!$nowrapper) {?>
				<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
					<label></label>
				</div>
			<?php }?>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
	<?php }?>
			<div class="autocomplete-row" id="div_<?php echo "{$class}_{$field}_autocomplete_row"?>">
				<?php
				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
						'name' => "{$class}[$field]",
						'id' => "{$primary_selector_id}_0",
						'value'=>'',
						'source'=>"js:function(request, response) {
							$.ajax({
								'url': '" . Yii::app()->createUrl('/disorder/autocomplete') . "',
								'type':'GET',
								'data':{'term': request.term, 'code': '$code'},
								'success':function(data) {
									data = $.parseJSON(data);

									var result = [];

									for (var i = 0; i < data.length; i++) {
										var ok = true;
										$('#selected_diagnoses').children('input').map(function() {
											if ($(this).val() == data[i]['id']) {
												ok = false;
											}
										});
										if (ok) {
											result.push(data[i]);
										}
									}

									response(result);
								}
							});
						}",
						'options' => array(
								'minLength'=>'3',
								'select' => "js:function(event, ui) {

									if ($('#DiagnosisSelection_disorder_id_secondary_to').is(':visible')) {
										var primary_selected = $('#{$primary_selector_id}').children('option:selected');
										if (primary_selected.val() != 'NONE') {
											".($callback ? $callback."(primary_selected.val(), primary_selected.text());" : "")."
										}
									}
									".($callback ? $callback."(ui.item.id, ui.item.value);" : '')."
									$('#{$primary_selector_id}_0').val('');
									$('#{$primary_selector_id}').children('option').map(function() {
										if ($(this).val() == ui.item.id) {
											$(this).remove();
										}
									});
									return false;
								}",
						),
						'htmlOptions' => array(
								'placeholder' => $placeholder,
						),
				));
				?>
			</div>
	<?php if (!$nowrapper) {?>
			</div>
		</div>
	<?php }?>
<script type="text/javascript">
	function updatePrimaryList(disorder, secondary_to) {
		var html = '<option value="'+disorder.id+'" data-order="'+disorder.order+'"';
		if (secondary_to) {
			html += ' data-secondary-to="'+JSON.stringify(secondary_to).replace(/\"/g, "&quot;")+'"';
		}
		html += '>'+disorder.term+'</option>';

		var none = '';
		var empty = '';
		$('#<?= $primary_selector_id ?>').children().each(function() {
			if ($(this).val() == 'NONE') {
				none = $(this)[0].outerHTML;
			}
			else if ($(this).val()) {
				html += $(this)[0].outerHTML;
			}
			else {
				empty = $(this)[0].outerHTML;
			}
		});
		// sort_selectbox keeps the first element at the top.
		$('#<?= $primary_selector_id ?>').html(empty + html);
		sort_selectbox($('#<?= $primary_selector_id ?>'));
		//prepend none
		$('#<?= $primary_selector_id ?> option').eq(1).before($(none));
	}

	<?php if ($secondary_to) {?>
	function updateSecondaryList(data, include_none) {
		debugger;
		var options = '<option value="">- Please Select -</option>';
		if (include_none) {
			options += '<option value="NONE">None</option>';
		}
		data.sort(function(a, b) { return a.order < b.order ? -1 : 1});
		for (var i in data) {
			if (data[i].id == 'NONE') {
				options += '<option value="' + data[i].id + '">' + data[i].term + '</option>';
			}
		}
		for (var i in data) {
			if (data[i].id != 'NONE' && $('input[type="hidden"][name="selected_diagnoses[]"][value="' + data[i].id + '"]').length == 0) {
				options += '<option value="' + data[i].id + '">' + data[i].term + '</option>';
			}
		}
		$('#<?= "{$primary_selector_id}_secondary_to"?>').html(options);
	}

	$('#<?= "{$primary_selector_id}_secondary_to"?>').change(function() {
		var primary_selected = $('#<?= $primary_selector_id ?>').children('option:selected');
		var selected = $(this).children('option:selected');
		if (selected.val()) {
			if (primary_selected.val() != 'NONE') {
				<?= $callback?>(primary_selected.val(), primary_selected.text());
			}
			if (selected.val() != 'NONE') {
				<?= $callback?>(selected.val(), selected.text());
			}
			$('#div_<?= "{$primary_selector_id}_secondary_to"?>').hide();
			if (primary_selected.val() != 'NONE') {
				primary_selected.remove();
			}
			$('#<?php echo $class?>_<?php echo $field?>').val('');
		}
	});
	<?php }?>

	<?php if ($secondary_to || $callback) {?>
		$('#<?php echo $class?>_<?php echo $field?>').change(function() {
			if ($(this).children('option:selected').val()) {
				var selected = $(this).children('option:selected');
				<?php if ($secondary_to) {?>
					if (selected.data('secondary-to')) {
						updateSecondaryList(selected.data('secondary-to'), selected.val() != 'NONE');
						$('#div_<?= "{$primary_selector_id}_secondary_to"?>').show();
					}
					else {
						$('#div_<?= "{$primary_selector_id}_secondary_to"?>').hide();
						<?php echo $callback?>(selected.val(), selected.text());
						selected.remove();
						$('#<?= $primary_selector_id ?>').val('');
					}
				<?php } else {?>
					<?php echo $callback?>(selected.val(), selected.text());
					selected.remove();
					$('#<?= $primary_selector_id ?>').val('');
				<?php }?>
			}
			else {
				// reset form
				$('#div_<?= "{$primary_selector_id}_secondary_to"?>').hide();
			}
		});
	<?php }?>
</script>
