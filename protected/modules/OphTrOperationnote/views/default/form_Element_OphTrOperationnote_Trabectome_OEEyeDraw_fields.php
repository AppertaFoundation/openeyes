<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php echo $form->dropDownList($element, 'power_id', CHtml::listData(OphTrOperationnote_Trabectome_Power::model()->activeOrPk($element->power_id)->findAll(),'id','name'),array('empty'=>'- Please select -'),false,array('field'=>3))?>
<?php echo $form->checkbox($element, 'blood_reflux', array('class' => 'clearWithEyedraw'))?>
<?php echo $form->checkbox($element, 'hpmc', array('class' => 'clearWithEyedraw'))?>
<?php echo $form->textArea($element, 'description', array('rows' => 4, 'class' => 'autosize clearWithEyedraw'))?>
<div class="row field-row">
	<div class="large-3 column">&nbsp;</div>
	<div class="large-4 column end">
		<button id="btn-trabectome-report" class="ed_report secondary small">Report</button>
		<button class="ed_clear secondary small">Clear</button>
	</div>
</div>
<?php
$complications =  OphTrOperationnote_Trabectome_Complication::model()->activeOrPk($element->getComplicationIDs())->findAll(array('order'=>'display_order asc'));
$html_options = array('empty' => '- Complications -', 'label' => 'Complications', 'options' => array());
foreach ($complications as $comp) {
	$html_options['options'][$comp->id] = array(
		'data-other' => $comp->other
	);
}
echo $form->multiSelectList($element, CHtml::modelName($element) . '[complications]', 'complications', 'id', CHtml::listData($complications, 'id', 'name'), null, $html_options,false,false,null,false,false,array('field'=>4))?>
<div class="row field-row<?php if (!$element->hasOtherComplication()) { echo ' hidden'; }?>" id="div_<?= CHtml::modelName($element)?>_complication_other">
	<div class="large-3 column">
		<label for="<?php echo CHtml::modelName($element)?>_complication_other">
			<?php echo $element->getAttributeLabel('complication_other')?>
		</label>
	</div>
	<div class="large-4 column end">
		<?php $form->textArea($element, 'complication_other', array('rows' => 2, 'class' => 'autosize', 'nowrapper' => true)); ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$(this).delegate('.trabectome .MultiSelectList', 'MultiSelectChanged', function() {
			var container = $(this).closest('.multi-select');
			var selections = container.find('.multi-select-selections');
			var showOther = false;
			selections.find('input').each(function(){
				if ($(this).data('other')) {
					showOther = true;
				}
			});
			if (showOther) {
				$('#div_<?= CHtml::modelName($element)?>_complication_other').show();
			}
			else {
				$('#div_<?= CHtml::modelName($element)?>_complication_other').hide();
				$('#div_<?= CHtml::modelName($element)?>_complication_other').find('textarea').val('');
			}
		});
	});
</script>
