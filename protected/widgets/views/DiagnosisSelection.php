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
<div class="eventDetail" id="editDiagnosis">
	<div class="label">Diagnosis:</div>
	<div class="data">
		<div id="enteredDiagnosisText" class="eventHighlight big"
		<?php if (!$label) {?> style="display: none;" <?php }?>>
			<h4>
				<?php echo $label?>
			</h4>
		</div>
		<h5 class="small">
			<em>Change diagnosis:</em>
		</h5>
		<?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis', 'style' => 'width: 525px; margin-bottom:10px;'))?>
		<br />
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => "{$class}[$field]",
				'id' => "{$class}_{$field}_0",
				'value'=>'',
				'source'=>"js:function(request, response) {
					$.ajax({
						'url': '" . Yii::app()->createUrl('/disorder/autocomplete') . "',
						'type':'GET',
						'data':{'term': request.term, 'code': ".CJavaScript::encode($code)."},
						'success':function(data) {
							data = $.parseJSON(data);
							response(data);
						}
					});
				}",
				'options' => array(
						'minLength'=>'3',
						'select' => "js:function(event, ui) {
							$('#".$class."_".$field."_0').val('');
							$('#enteredDiagnosisText h4').html(ui.item.value);
							$('#enteredDiagnosisText').show();
							$('input[id=savedDiagnosis]').val(ui.item.id);
							$('#".$class."_".$field."').focus();
							return false;
						}",
				),
				'htmlOptions' => array(
						'style'=>'width: 520px;',
						'placeholder' => 'or type the first few characters of a diagnosis',
				),
		));
		?>
		<input type="hidden" name="<?php echo $class?>[<?php echo $field?>]"
			id="savedDiagnosis" value="<?php echo $value?>" />
	</div>
</div>
<script type="text/javascript">
	$('#<?php echo $class?>_<?php echo $field?>').change(function() {
		$('#enteredDiagnosisText h4').html($('option:selected', this).text());
		$('#savedDiagnosis').val($(this).val());
		$('#enteredDiagnosisText').show();
	});
</script>
