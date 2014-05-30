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
<?php if (!$nowrapper) {?>
	<div class="row field-row diagnosis-selection">
		<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
			<label for="<?php echo "{$class}_{$field}";?>">Diagnosis:</label>
		</div>
		<div class="large-<?php echo $layoutColumns['field'];?> column end">
<?php } ?>
		<?php
			$list_options = array('empty' => 'Select a commonly used diagnosis');
			if ($secondary_to) {
				$list_options['options'] = array();
				foreach ($secondary_to as $id => $lst) {
					if (count($lst)) {
						$list_options['options'][$id] = array();
					}
					$data = array();
					foreach ($lst as $sid => $term) {
						$data[] = array('id' => $sid, 'term' => $term);
					}
					$list_options['options'][$id]['data-secondary-to'] = CJSON::encode($data);
				}
			}?>
		<div class="dropdown-row">
			<?php echo !empty($options) ? CHtml::dropDownList("{$class}[$field]", '', $options, $list_options) : ""?>
		</div>
		<div class="autocomplete-row" id="div_<?php echo "{$class}_{$field}_autocomplete_row"?>">
			<?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => "{$class}[$field]",
					'id' => "{$class}_{$field}_0",
					'value'=>'',
					'source'=>"js:function(request, response) {
						$.ajax({
							'url': '" . Yii::app()->createUrl('/disorder/autocomplete') . "',
							'type':'GET',
							'data':{'term': request.term, 'code': '".$code."'},
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
								".($callback ? $callback."(ui.item.id, ui.item.value);" : '')."
								$('#".$class."_".$field."_0').val('');
								$('#".$class."_".$field."').children('option').map(function() {
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
		<?php }
		if ($secondary_to) {?>
		<div id="div_<?php echo "{$class}_{$field}_secondary_to"?>" class="row field-row hidden">
		<?php if (!$nowrapper) {?>
			<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
				<label for="<?php echo "{$class}_{$field}_secondary_to";?>">Secondary To:</label>
			</div>
		<?php } ?>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
				<?php echo CHtml::dropDownList("{$class}[{$field}_secondary_to]", '', array(), array())?>
			</div>
		</div>
		<?php } ?>
<script type="text/javascript">
	<?php if ($secondary_to) { ?>
	function updateSecondaryList(data) {
		var options = '<option value="">- Please Select -</option><option value="NONE">None</option>';
		data.sort(function(a, b) { return a.term < b.term ? -1 : 1});
		for (var i in data) {
			options += '<option value="' + data[i].id + '">' + data[i].term + '</option>';
		}
		$('#<?= "{$class}_{$field}_secondary_to"?>').html(options);
	}

	$('#<?= "{$class}_{$field}_secondary_to"?>').change(function() {
		var primary_selected = $('#<?php echo $class?>_<?php echo $field?>').children('option:selected');
		var selected = $(this).children('option:selected');
		if (selected.val()) {
			<?php echo $callback?>(primary_selected.val(), primary_selected.text());
			if (selected.val() != 'NONE') {
				<?php echo $callback?>(selected.val(), selected.text());
			}
			$('#div_<?= "{$class}_{$field}_secondary_to"?>').hide();
			$('#div_<?= "{$class}_{$field}_autocomplete_row"?>').show();
			primary_selected.remove();
			$('#<?php echo $class?>_<?php echo $field?>').val('');
		}
	});
	<?php } ?>

	<?php if ($secondary_to || $callback) {?>
		$('#<?php echo $class?>_<?php echo $field?>').change(function() {
			if ($(this).children('option:selected').val()) {
				var selected = $(this).children('option:selected');
				<?php if ($secondary_to) {?>
					if (selected.data('secondary-to')) {
						updateSecondaryList(selected.data('secondary-to'));
						$('#div_<?= "{$class}_{$field}_secondary_to"?>').show();
						$('#div_<?= "{$class}_{$field}_autocomplete_row"?>').hide();
					}
					else {
						$('#div_<?= "{$class}_{$field}_secondary_to"?>').hide();
						$('#div_<?= "{$class}_{$field}_autocomplete_row"?>').show();
						<?php echo $callback?>(selected.val(), selected.text());
						selected.remove();
						$('#<?php echo $class?>_<?php echo $field?>').val('');
					}
				<?php } else { ?>
					<?php echo $callback?>(selected.val(), selected.text());
					selected.remove();
					$('#<?php echo $class?>_<?php echo $field?>').val('');
				<?php } ?>
			}
			else {
				// reset form
				$('#div_<?= "{$class}_{$field}_secondary_to"?>').hide();
				$('#div_<?= "{$class}_{$field}_autocomplete_row"?>').show();
			}
		});
	<?php }?>
</script>
