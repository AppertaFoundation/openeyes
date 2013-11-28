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
<div class="row diagnosis-selection<?php echo !$nowrapper?' field-row':'';?>">
	<?php if (!$nowrapper && $label) {?>
		<div class="large-<?php echo $layoutColumns['label'];?> column">
			<label for="<?php echo "{$class}_{$field}";?>">
				<?php echo $element->getAttributeLabel($field)?>:
			</label>
		</div>
	<?php }?>
	<div class="large-<?php if ($label) { echo $layoutColumns['field']; }else{?>12<?php }?> column end">
		<div class="row collapse">
			<div class="large-10 column">
				<div class="dropdown-row">
					<?php echo (!empty($options) || !empty($dropdownOptions)) ? CHtml::dropDownList("{$class}[$field]", $element->$field, $options, empty($dropdownOptions) ? array('empty' => '- Please Select -') : $dropdownOptions) : ""?>
				</div>
				<div class="autocomplete-row hide">
					<?php
					$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
							'name' => "ignore_{$class}[$field]",
							'id' => "{$class}_{$field}_searchbox",
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
										$('#".$class."_".$field."_searchbox').val('').parent().addClass('hide');
										var matched = false;
										$('#".$class."_".$field."').children('option').map(function() {
											if ($(this).val() == ui.item.id) {
												matched = true;
											}
										});
										if (!matched) {
											$('#".$class."_".$field."').append('<option value=\"' + ui.item.id + '\">'+ui.item.value+'</option>');
										}
										$('#".$class."_".$field."').val(ui.item.id).trigger('change');
										return false;
									}",
							),
							'htmlOptions' => array(
								'placeholder' => 'search for diagnosis',
							),
					));
					?>
				</div>
			</div>
			<div class="large-2 column">
				<div class="postfix">
					<button class="small button-icon small" id="<?php echo $class . "_" . $field . "_search"?>">
						<span class="icon-button-small-search"></span>
						<span class="hide-offscreen">Search</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {

		var searchButton = $('#<?php echo $class . "_" . $field . "_search"?>');
		var searchBox = $('#<?php echo $class . "_" . $field . "_searchbox"?>');

		searchButton.on('click', function(e) {
			e.preventDefault();
			searchBox.parent().toggleClass('hide');
			searchBox.focus();
		});
	});
</script>
