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
							currFirst = getSelectedObj(firstSelection);
							DiagnosisSelection_addCondition(currFirst);
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
	var firstEmpty = 'Select a commonly used diagnosis';

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
			if (obj.type == 'disorder' || obj.type == 'finding' || obj.type == 'alternate') {
				obj.id = selSplit[1];
			}
			else {
				obj.type = 'none';
			}
			obj.label = selected.text();
 		}
		return obj;
	}

	/**
	 * Checks if there is a second list for the given type and id.
	 */
	function hasSecondList(condition, filterList)
	{
		for (var i in selectionConfig) {
			var obj = selectionConfig[i];
			if (obj.type == condition.type && obj.id == condition.id) {
				// check at least one of the second options is not currently filtered out.
				if (obj.secondary) {
					for (var j in obj.secondary) {
						if (!checkFilter(filterList, obj.secondary[j])) {
							return true;
						}
					}
				}
				return false;
			}
		}
		return false;
	}

	/**
	 * Goes through the configuration to find the alternate condition
	 */
	function getAlternate(condition) {
		for (var i in selectionConfig) {
			var obj = selectionConfig[i];
			if (obj.type == condition.type && obj.id == condition.id) {
				return obj.alternate;
			}
		}
	}

	/**
	 * Check whether the given object should be filtered or not given the filter list
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
		var html = '';
		// FIXME: doesn't support complete empty lists for the dropdown
		if (firstEmpty.length) {
			html += '<option>' + firstEmpty + '</option>';
		}

		currentGroup = null;
		for (var i in selectionConfig) {
			var obj = selectionConfig[i];
			var group = obj.group;
			// don't show the null option in the first list
			if (!checkFilter(filterList, obj) && obj.type != 'none') {
				// filter out if the alternate is already present as well
				if (obj.alternate && checkFilter(filterList, obj.alternate)) {
					continue;
				}
				if(currentGroup !== group) {
					html += '<option disabled="disabled">----------</option>';
				}
				html += '<option value="' + obj.type + '-' + obj.id + '">' + obj.label + '</option>';
				currentGroup = group;
			}
		}

		firstSelection.html(html);
	}

	/**
	 * Regenerate selection options for the second list. Will check the current selection in the first list to
	 * determine what should be keyed from. If the list is empty, the list will be blanked out.
	 *
	 * @param filterList {id: string, type: 'disorder'|'finding', label: string}[]
	 */
	function updateSecondList(filterList) {
		currentFirst = getSelectedObj(firstSelection);
		var options = null;
		var alternate = null;
		if (currentFirst.id) {

			var type = currentFirst.type;
			var id = currentFirst.id;
			// need to look for the list for this primary selection
			for (var i in selectionConfig) {
				var obj = selectionConfig[i];
				if (obj.type == type && obj.id == id) {
					options = obj.secondary;
					alternate = obj.alternate;
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
			if (alternate && !checkFilter(filterList, alternate)) {
				html += '<option value="alternate-' + alternate.id + '">' + alternate.selection_label + '</option>';
			}
			for (var i in options) {
				var obj = options[i];
				if (!checkFilter(filterList, obj)) {
					html += '<option value="' + obj.type + '-' + obj.id + '">' + obj.label + '</option>';
				}
			}
			secondarySelection.html(html);
			$('#div_<?= "{$class_field}_secondary_to"?>').slideDown();
		}
		else {
			secondarySelection.html('');
			$('#div_<?= "{$class_field}_secondary_to"?>').slideUp();
		}
	}

	/**
	 * Will set up both dropdown lists based on the config.
	 */
	function DiagnosisSelection_updateSelections()
	{
		var filterConditions = [];
		<?php if (@$filterCallback) {?>
			filterConditions = <?= @$filterCallback . "();" ?>
		<?php }?>
		var firstVal = firstSelection.val();
		$('#div_<?= "{$class_field}_secondary_to"?>').slideUp(function() {
			updateFirstList(filterConditions);
			firstSelection.val(firstVal);
			updateSecondList(filterConditions);
		});

	}

	/**
	 * Wrapper to the callback function for adding condition from dropdown.
	 *
	 * @param condition {id: string, type: 'disorder'|'finding', label: string}
	 */
	function DiagnosisSelection_addCondition(condition)
	{
		if (condition.id) {
			<?php if (@$callback) {
				echo $callback . "(condition.type, condition.id, condition.label);";
			} else {
				echo "console.log('NO CALLBACK SPECIFIED');";
			}?>
		}
	}

	/**
	 * Reset the dropdown selections
	 */
	function DiagnosisSelection_reset()
	{
		$('#<?php echo $class?>_<?php echo $field?>').val('');
		$('#<?= "{$class_field}_secondary_to"?>').val('');
		firstSelection.removeAttr('disabled');
		secondarySelection.removeAttr('disabled');
		// FIXME: this might be duplication here?
		DiagnosisSelection_updateSelections();
	}

	/**
	 * Turn off the selectors
	 */
	function DiagnosisSelection_disable()
	{
		firstSelection.attr('disabled', 'disabled');
		secondarySelection.attr('disabled', 'disabled');
	}

	// call straight away to set up the dropdowns correctly.
	$(document).ready(function() {
		DiagnosisSelection_updateSelections();
	});

	$('#<?php echo $class?>_<?php echo $field?>').on('change', function() {
		var filterConditions = [];
		<?php if (@$filterCallback) {?>
		filterConditions = <?= @$filterCallback . "();" ?>
		<?php }?>
		curr = getSelectedObj(firstSelection);
		if (hasSecondList(curr, filterConditions)) {
			$('#div_<?= "{$class_field}_secondary_to"?>').slideUp(function() {
				updateSecondList(filterConditions);
			});
		}
		else {
			if (curr.id) {
				// slide this up just to give additional visual cue that a single selection has been made
				// (for when the second list is showing)
				$('#div_<?= "{$class_field}_secondary_to"?>').slideUp(function() {
					DiagnosisSelection_addCondition(curr);
					DiagnosisSelection_reset();
				});
			}
		}
	});

	$('#<?= "{$class_field}_secondary_to"?>').on('change', function() {
			currSecond = getSelectedObj(secondarySelection);
			if (currSecond.id) {
				DiagnosisSelection_disable();
				// check if the type is alternate
				// if it is, select that, otherwise do the normal addition
				currFirst = getSelectedObj(firstSelection);
				if (currSecond.type == 'alternate') {
					DiagnosisSelection_addCondition(getAlternate(currFirst));
				}
				else {
					DiagnosisSelection_addCondition(currFirst);
					DiagnosisSelection_addCondition(currSecond);
				}
				DiagnosisSelection_reset();
			}
	});

</script>
