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
} ?>

<section class="element edit full  edit-details">
  <header class="element-header">
    <h3 class="element-title">Details</h3>
  </header>
  <div id="div_Element_OphDrPrescription_Details_prescription_items" class="element-fields full-width">

    <div class="data-group">
      <table id="prescription_items" class="cols-full">
        <colgroup>
          <col>
          <col class="cols-3">
          <col class="cols-1">
          <col>
          <col class="cols-1">
        </colgroup>
        <thead>
        <tr>
          <th colspan="2">Drug</th>
          <th>Dose</th>
          <th>Route</th>
            <?php if (strpos($this->uniqueid, 'default')) { // we need to display this column on the front-end only?>
              <th>Options</th>
            <?php } ?>
          <th>Frequency</th>
          <th>Duration</th>
          <th>Dispense Condition</th>
          <th>Location</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
		$unit_options = MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions;
        foreach ($element->items as $key => $item) {
            $this->renderPartial('form_Element_OphDrPrescription_Details_Item',
                array('key' => $key, 'item' => $item, 'patient' => $this->patient, 'unit_options' => $unit_options));
        } ?>
        </tbody>
      </table>
    </div>

    <div class="flex-layout">
      <div>
        <button type="button" class="button hint blue"
                id="clear_prescription" name="clear_prescription">
          Clear all
        </button>

          <?php
          // we need to separate the public and admin view
          if (is_a(Yii::app()->getController(), 'DefaultController') &&
              $this->getPreviousPrescription($element->id)): ?>
            <button type="button" class="button hint blue"
                    id="repeat_prescription" name="repeat_prescription">
              Add repeat prescription
            </button>
          <?php endif; ?>
      </div>

      <div>
        <button id="add-standard-set-btn" class="button hint green" type="button">Add standard set</button>
        <button class="button hint green" id="add-prescription-btn" type="button"><i class="oe-i plus pro-theme"></i>
        </button>
      </div>
    </div>

  </div>
</section>

<?php
// we need to separate the public and admin view
if (is_a(Yii::app()->getController(), 'DefaultController')) { ?>
  <section class="element full">
    <header class="element-header">
      <h3 class="element-title">Comments</h3>
    </header>
    <div class="element-fields flex-layout full-width">
        <?php echo $form->textArea($element, 'comments', array('rows' => 4, 'nowrapper' => true)) ?>
    </div>
  </section>
<?php } ?>

<script type="text/javascript">
    <?php
    // we need to separate the public and admin view
    if (is_a(Yii::app()->getController(), 'DefaultController')): ?>
    var searchListUrl = '<?= $this->createUrl('DrugList') ?>';
    <?php else: ?>
    var searchListUrl = '<?='/' . Yii::app()->getModule('OphDrPrescription')->id . '/' . Yii::app()->getModule('OphDrPrescription')->defaultController . '/DrugList'; ?>';
    <?php endif; ?>

    var prescriptionDrugSets = <?= CJSON::encode(
        array_map(function ($drugSet) {
            return [
                'label' => $drugSet->name,
                'id' => $drugSet->id
            ];
        }, $element->drugSets())
    ) ?>;

    var prescriptionElementCommonDrugs = <?= CJSON::encode(
        array_map(function ($drug) {
            return [
                'label' => $drug['preferred_term'],
                'id' => $drug['id'],
                'prepended_markup' => $this->widget('MedicationInfoBox', array('medication_id' => $drug['id']), true),
                'allergies' => array_map(function($e) { return $e->id; }, $drug['allergies']),
            ];
        }, $element->commonDrugs())
    ) ?>;

    <?php
        $drugTypes = [];
        foreach ($element->drugTypes() as $key=>$drugType) {
            $drugTypes[] = [
                'label' => $drugType,
                'id' => $key
            ];
        }
        ?>

    var prescriptionElementDrugTypes = <?= CJSON::encode($drugTypes) ?>;

	<?php if(isset($this->patient)){ ?>
    var patientAllergies = <?= CJSON::encode( $this->patient->getAllergiesId()); ?>
	<?php } ?>

</script>

<?php
/*
 * We need to decide which JS file need to be loaded regarding to the controller
 * Unfortunately jsVars[] won't work from here because processJsVars function already called
 */
$modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphDrPrescription.assets'));

Yii::app()->getClientScript()->registerScript('scr_controllerName',
    "controllerName = '" . get_class(Yii::app()->getController()) . "';", CClientScript::POS_HEAD);

Yii::app()->clientScript->registerScriptFile($modulePath . '/js/allergicDrugs.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($modulePath . '/js/defaultprescription.js', CClientScript::POS_END);

?>
