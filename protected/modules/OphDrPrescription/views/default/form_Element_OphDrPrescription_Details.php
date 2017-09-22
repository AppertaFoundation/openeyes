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
<?php
// we need to separate the public and admin view
if (is_a(Yii::app()->getController(), 'DefaultController')) {
    echo $form->hiddenInput($element, 'draft', 1);
}
if (is_a(Yii::app()->getController(), 'DefaultController')) {?>
<section class="element <?php echo $element->elementType->class_name?>"
		 data-element-type-id="<?php echo $element->elementType->id ?>"
		 data-element-type-class="<?php echo $element->elementType->class_name ?>"
		 data-element-type-name="<?php echo $element->elementType->name ?>"
		 data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<?php } else {?>
	<section class="element">
	<?php }?>
	<div id="div_Element_OphDrPrescription_Details_prescription_items" class="element-fields">
        <div class="row field-row">
			<div class="large-6 column">
				<fieldset class="row field-row">
					<legend class="large-4 column">
						Add Item
					</legend>
					<div class="large-8 column">
						<div class="field-row">
							<?php echo CHtml::dropDownList('common_drug_id', null, CHtml::listData($element->commonDrugs(), 'id', 'tallmanlabel'), array('empty' => '-- Select common --')); ?>
						</div>
						<div class="field-row">
							<?php
                            // we need to separate the public and admin view
                            if (is_a(Yii::app()->getController(), 'DefaultController')) {
                                $searchListURL = $this->createUrl('DrugList');
                            } else {
                                $searchListURL = '/'.Yii::app()->getModule('OphDrPrescription')->id.'/'.Yii::app()->getModule('OphDrPrescription')->defaultController.'/DrugList';
                            }

                            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                'name' => 'drug_id',
                                'id' => 'autocomplete_drug_id',
                                'source' => "js:function(request, response) {
									$.getJSON('".$searchListURL."', {
										term : request.term,
										type_id: $('#drug_type_id').val(),
										preservative_free: ($('#preservative_free').is(':checked') ? '1' : ''),
									}, response);
								}",
                                'options' => array(
                                    'select' => "js:function(event, ui) {
										addItem(ui.item.value, ui.item.id);
										$(this).val('');
										return false;
									}",
                                ),
                                'htmlOptions' => array(
                                    'placeholder' => 'or search formulary',
                                ),
                            ));?>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="large-6 column">
				<fieldset>
					<legend><em>Filtered by:</em></legend>
					<label class="inline" for="drug_type_id">Type:</label>
					<?php echo CHtml::dropDownList('drug_type_id', null, $element->drugTypes(), array('class' => 'inline drugFilter', 'empty' => '-- Select --')); ?>
					<label class="inline highlight">
						No preservative
						<?php echo CHtml::checkBox('preservative_free', null, array('class' => 'drugFilter'))?>
					</label>
				</fieldset>
			</div>
		</div>
		<?php
        // we need to separate the public and admin view
        if (is_a(Yii::app()->getController(), 'DefaultController')) {
            ?>
			<div class="row field-row">
				<div class="large-2 column">
					<label for="drug_set_id">Add Standard Set:</label>
				</div>
				<div class="large-3 column end">
					<?php echo CHtml::dropDownList('drug_set_id', null,
                        CHtml::listData($element->drugSets(), 'id', 'name'), array('empty' => '-- Select --')); ?>
				</div>
			</div>
		<?php

        }
        ?>
		<div class="row field-row">
			<div class="large-2 column">
				<div class="field-label">Other Actions</div>
			</div>
			<div class="large-10 column">
				<?php
                // we need to separate the public and admin view
                if (is_a(Yii::app()->getController(), 'DefaultController')) {
                    if ($this->getPreviousPrescription($element->id)) { ?>
						<button type="button" class="button small"
								id="repeat_prescription" name="repeat_prescription">
							Add Repeat Prescription
						</button>
					<?php

                    }
                }
                ?>

				<button type="button" class="small"
						id="clear_prescription" name="clear_prescription">
					Clear <?php if (is_a(Yii::app()->getController(), 'DefaultController')) {
    echo 'Prescription';
} ?>
				</button>
			</div>
		</div>
	</div>
</section>

<input type="hidden" name="prescription_items_valid" value="1" />
<table class="prescriptions" id="prescription_items">
	<thead>
	<tr>
		<th>Drug</th>
		<th>Dose</th>
		<th>Route</th>
		<?php if (strpos($this->uniqueid, 'default')) { // we need to display this column on the front-end only?>
			<th>Options</th>
		<?php } ?>
		<th>Frequency</th>
		<th>Duration</th>
		<th></th>
		<th>Dispense Condition/Location</th>
	</tr>
	</thead>
	<tbody>
	<?php
    foreach ($element->items as $key => $item) {
        $this->renderPartial('form_Element_OphDrPrescription_Details_Item', array('key' => $key, 'item' => $item, 'patient' => $this->patient));
} ?>
	</tbody>
</table>

<?php
// we need to separate the public and admin view
if (is_a(Yii::app()->getController(), 'DefaultController')) {
    ?>
	<section class="element">
		<div class="element-fields">
			<?php echo $form->textArea($element, 'comments', array('rows' => 4)) ?>
		</div>
	</section>
<?php

}
?>

<?php

/*
 * We need to decide which JS file need to be loaded regarding to the controller
 * Unfortunatelly jsVars[] won't work from here because processJsVars function already called
 */

$modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphDrPrescription.assets'));

Yii::app()->getClientScript()->registerScript('scr_controllerName',
    "controllerName = '".get_class(Yii::app()->getController())."';", CClientScript::POS_HEAD);

Yii::app()->clientScript->registerScriptFile($modulePath.'/js/defaultprescription.js', CClientScript::POS_END);

?>

