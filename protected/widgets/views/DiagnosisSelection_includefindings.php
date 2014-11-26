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
<?php $class_field = "{$class}_{$field}"; ?>

<?php if (!$nowrapper) {?>
<div class="row field-row diagnosis-selection">
	<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
		<label for="<?php echo $class_field ;?>">Diagnosis:</label>
	</div>
	<div class="large-<?php echo $layoutColumns['field'];?> column end">
		<?php }?>
		<?php
		$list_options = array('empty' => 'Select a commonly used diagnosis');
		echo CHtml::dropDownList("{$class}[$field]", '', array(), $list_options);
		if (!$nowrapper) {?>
	</div>
</div>
<?php }?>

	<?php if (!$nowrapper) {?>
		<div id="div_<?php echo "{$class_field}_secondary_to"?>" class="row field-row hidden">
		<?php if (!$nowrapper) {?>
			<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
				<label for="<?php echo "{$class_field}_secondary_to";?>">Associated diagnosis:</label>
			</div>
		<?php }?>
		<div class="large-<?php echo $layoutColumns['field'];?> column end">
	<?php }?>
	<?php echo CHtml::dropDownList("{$class}[{$field}_secondary_to]", '', array(), array())?>
	<?php if (!$nowrapper) {?>
		</div>
		</div>
	<?php }?>

<?php if (!$nowrapper) {?>
<div class="row field-row">
		<div class="large-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {?> hide<?php }?>">
			<label></label>
		</div>
	<div class="large-<?php echo $layoutColumns['field'];?> column end">
<?php }?>
		<div class="autocomplete-row" id="div_<?php echo "{$class}_{$field}_autocomplete_row"?>">
			<?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => "{$class}[$field]",
					'id' => "{$class_field}_0",
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
									".($callback ? $callback."('disorder', ui.item.id, ui.item.value);" : '')."
									$('#{$class_field}_0').val('');
									$('#{$class_field}').children('option').map(function() {
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
	var selectionConfig = <?= CJSON::encode($options); ?>;
	var firstSelection = $('#<?= $class_field ?>');
	var secondarySelection = $('#<?= "{$class_field}_secondary_to"?>');

	/**
	 * Generates an object from the given jquery selector for a select list
	 *
	 * @param selectionList
	 * @return {id:string, type: 'disorder'|'finding', label: string}
 	 */
	function getSelectedObj(selectionList)
	{
		var obj = {};
		var selected = selectionList.children('option:selected');

		if (selected) {
			selSplit = selected.val().split('-');
			obj.type = selSplit[0];
			obj.id = selSplit[1];
			obj.label = selected.text();
 		}
		return obj;
	}

	/**
	 * function to check whether the given object should be filtered or not given the filter list
	 *
	 * @param filterList
	 * @param obj
	 */
	function checkFilter(filterList, obj)
	{
		if (obj.type == 'disorder') {
			for (var i in filterList.disorders) {
				if (filterList.disorders[i].id == obj.id) {
					return true;
				}
			}
		}
		else if (obj.type == 'finding') {
			for (var i in filterList.findings) {
				if (filterList.findings[i].id == obj.id) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Regenerate selection options for the first list
	 *
	 * @param filterList {id: string, type: 'disorder'|'finding', label: string}[]
	 */
	function updateFirstList(filterList) {
		// FIXME: need the empty selection
		var html = '';

		for (var i in selectionConfig) {
			var obj = selectionConfig[i];
			if (!checkFilter(filterList, obj)) {
				html += '<option value="' + obj.type + '-' + obj.id + '">' + obj.label + '</option>';
			}
		}
		debugger;
		firstSelection.html(html);
	}

	/**
	 * Regenerate selection options for the second list. Will check the current selection in the first list to
	 * determine what should be keyed from. If the list is empty, the list will be blanked out.
	 *
	 * @param filterList {id: string, type: 'disorder'|'finding', label: string}[]
	 */
	function updateSecondList(filterList) {
		currentFirst = firstSelection.val();
		var options = null;

		if (currentFirst) {
			curr = getSelectedObj(firstSelection);

			var type = curr.type;
			var id = curr.id;
			// need to look for the list for this primary selection
			for (var i in selectionConfig) {
				var obj = selectionConfig[i];
				if (obj.type == type && obj.id == id) {
					options = obj.secondary;
					break;
				}
			}
		}
		else {
			for (var i in selectionConfig) {
				var obj = selectionConfig[i];
				if (obj.type == 'none') {
					options = obj.secondary;
					break;
				}
			}
		}
		if (options) {
			var html = '<option value="">- Please Select -</option>';
			for (var i in options) {
				var obj = options[i];
				if (!checkFilter(filterList, obj)) {
					html += '<option value="' + obj.type + '-' + obj.id + '">' + obj.label + '</option>';
				}
			}
			secondarySelection.html(html);
			$('#div_<?= "{$class_field}_secondary_to"?>').show();
		}
		else {
			secondarySelection.html('');
			$('#div_<?= "{$class_field}_secondary_to"?>').hide();
		}
	}

	function DiagnosisSelection_updateSelections() {
		var filterConditions = [];
		<?php if (@$filterCallback) { ?>
			filterConditions = <?= @$filterCallback . "();" ?>
		<?php } ?>
		var firstVal = firstSelection.val();
		updateFirstList(filterConditions);
		firstSelection.val(firstVal);
		updateSecondList(filterConditions);

	}

	DiagnosisSelection_updateSelections();
	$('#<?php echo $class?>_<?php echo $field?>').on('change', function() {
		var filterConditions = [];
		<?php if (@$filterCallback) { ?>
		filterConditions = <?= @$filterCallback . "();" ?>
		<?php } ?>
		updateSecondList(filterConditions);
		//FIXME: if there are no secondary options we need to just select the condition chosen
	});
	$('#<?= "{$class_field}_secondary_to"?>').on('change', function() {
		<?php if (@$callback) { ?>
			curr = getSelectedObj(secondarySelection);
			if (curr.id) {
				<?=$callback . "(curr.type, curr.id, curr.label);"?>
			}
		<?php } ?>
	});

</script>
