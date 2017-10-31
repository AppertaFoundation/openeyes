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
<div class="diagnosis-selection">
	<div id="<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText"<?php if (!$label) {?> style="display: none;" <?php }?>>
		<h4>
			<?php echo $label?>
		</h4>
	</div>
	<div class="dropdown-row">
		<?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis'))?>
	</div>
	<div class="autocomplete-row">
		<?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => "{$class}[$field]",
            'id' => "{$class}_{$field}_0",
            'value' => '',
            'source' => "js:function(request, response) {
				$.ajax({
					'url': '".Yii::app()->createUrl('/disorder/autocomplete')."',
					'type':'GET',
					'data':{'term': request.term, 'code': '".$code."'},
					'success':function(data) {
						data = $.parseJSON(data);
						response(data);
					}
				});
			}",
            //'sourceUrl'=>array('/disorder/autocomplete'.($restrict ? '?restrict='.$restrict : '')),
            'options' => array(
                'minLength' => '3',
                'select' => "js:function(event, ui) {
					$('#".$class.'_'.$field."_0').val('');
					$('#".$class.'_'.$field."_enteredDiagnosisText h4').html(ui.item.value);
					$('#".$class.'_'.$field."_enteredDiagnosisText').show();
					$('input[id=".$class.'_'.$field."_savedDiagnosis]').val(ui.item.id);
					$('#".$class.'_'.$field."').focus();
					return false;
				}",
            ),
            'htmlOptions' => array(
                'placeholder' => 'or type the first few characters of a diagnosis',
            ),
    ));
        ?>
	</div>
</div>
<input type="hidden" name="<?php echo $class?>[<?php echo $field?>]"
	id="<?php echo $class?>_<?php echo $field?>_savedDiagnosis" value="<?php echo $value?>" />
<script type="text/javascript">
	$('#<?php echo $class?>_<?php echo $field?>').change(function() {
		$('#<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText h4').html($('option:selected', this).text());
		$('#<?php echo $class?>_<?php echo $field?>_savedDiagnosis').val($(this).val());
		$('#<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText').show();
	});
</script>
