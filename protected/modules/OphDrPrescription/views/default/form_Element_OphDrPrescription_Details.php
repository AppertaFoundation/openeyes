<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
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
            <?php if (strpos($this->uniqueid, 'default')) { // we need to display this column on the front-end only
                ?>
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
                $this->renderPartial(
                    'form_Element_OphDrPrescription_Details_Item',
                    array('key' => $key, 'item' => $item, 'patient' => $this->patient, 'unit_options' => $unit_options)
                );
            } ?>
        </tbody>
      </table>
    </div>

    <div class="flex-layout">
      <div>
        <button type="button" class="button hint blue" id="clear_prescription" name="clear_prescription">
          Clear all
        </button>

        <?php
        // we need to separate the public and admin view
        if (
          is_a(Yii::app()->getController(), 'DefaultController') &&
          $this->getPreviousPrescription($element->id) && \Yii::app()->user->checkAccess('Prescribe')
        ) : ?>
          <button type="button" class="button hint blue" id="repeat_prescription" name="repeat_prescription">
            Add repeat prescription
          </button>
        <?php endif; ?>
      </div>

      <div>
        <?php if (\Yii::app()->user->checkAccess('Prescribe')) { ?>
            <button id="add-standard-set-btn" class="button hint green" data-test="add-standard-set-button" type="button">Add standard set</button>

            <button class="button hint green" id="add-prescription-btn" data-test="add-prescription-button" type="button">
              <i class="oe-i plus pro-theme"></i>
            </button>
        <?php } else {?>
          <button id="add-PGD-btn" class="button hint green" type="button">Add PGD Set</button>
        <?php }?>
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
      <?php echo $form->textArea($element, 'comments', array('rows' => 4, 'nowrapper' => true), false) ?>
    </div>
  </section>
<?php } ?>
<?php if ($element->elementType->custom_hint_text) { ?>
  <div class="alert-box info <?= CHtml::modelName($element->elementType->class_name) ?>">
    <div class="user-tinymce-content">
      <?= $element->elementType->custom_hint_text ?>
    </div>
  </div>
<?php } ?>

<script type="text/javascript">
  <?php
    $firm = $this->getApp()->session->getSelectedFirm();
    $site = $this->getApp()->session->getSelectedSite();

    $subspecialty_id = $firm ? $firm->getSubspecialtyID() : null;

    $common_systemic = Medication::model()->listCommonSystemicMedications($subspecialty_id, true, $site->id ?? null, true);
    foreach ($common_systemic as &$medication) {
        $medication['prepended_markup'] = $this->widget('MedicationInfoBox', array('medication_id' => $medication['id']), true);
    }

    if ($firm) {
        $common_ophthalmic = Medication::model()->listBySubspecialtyWithCommonMedications($subspecialty_id, true, $site->id ?? null, true);
        foreach ($common_ophthalmic as &$medication) {
            $medication['prepended_markup'] = $this->widget('MedicationInfoBox', array('medication_id' => $medication['id']), true);
        }
    } else {
        $common_ophthalmic = array();
    }
    ?>
  new OpenEyes.UI.AdderDialog({
    id: 'add-prescription',
    openButton: $('#add-prescription-btn'),
    itemSets: [
      new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
          $common_systemic
      ) ?>, {
        'multiSelect': true,
        id: 'common-systemic',
        header: "Common Systemic"
      }),
      new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
          $common_ophthalmic
      ) ?>, {
        'multiSelect': true,
        id: 'common-opthalmic',
        header: "Common Ophthalmic"
      })
    ],
    onReturn: function(adderDialog, selectedItems) {
      addItems(selectedItems);
      return true;
    },
    searchOptions: {
      searchSource: '/medicationManagement/findRefMedications?source=prescription',
    },
    enableCustomSearchEntries: true,
    searchAsTypedItemProperties: {
      id: "<?php echo EventMedicationUse::USER_MEDICATION_ID ?>"
    },
    booleanSearchFilterEnabled: true,
    booleanSearchFilterLabel: 'Include brand names',
    booleanSearchFilterURLparam: 'include_branded'
  });

  let prescription_drug_sets = <?= CJSON::encode(
      array_map(function ($drugSet) {
                                    return [
                                      'label' => $drugSet->name,
                                      'id' => $drugSet->id
                                    ];
      }, $element->drugSets())
  ) ?>;

  let pgd_meds = <?= json_encode($element->pgds()); ?>;
  <?php if (isset($this->patient)) : ?>
    let patient_allergies = <?= CJSON::encode($this->patient->getAllergiesId()) ?>;
  <?php endif; ?>

  // This case handles displaying the button correctly whenever changing the dispense condition.
  $('#prescription_items tbody').on('change', '.dispenseCondition', function() {
    fpTenPrintOption();
  });

  // This case handles displaying the button correctly when first accessing the edit screen.
  $(document).ready(function() {
    fpTenPrintOption();
  })
</script>

<?php
/*
 * We need to decide which JS file need to be loaded regarding to the controller
 * Unfortunately jsVars[] won't work from here because processJsVars function already called
 */
$modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphDrPrescription.assets'), true);

Yii::app()->getClientScript()->registerScript(
    'scr_controllerName',
    "controllerName = '" . get_class(Yii::app()->getController()) . "';",
    CClientScript::POS_HEAD
);

Yii::app()->clientScript->registerScriptFile($modulePath . '/js/allergicDrugs.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($modulePath . '/js/defaultprescription.js', CClientScript::POS_END);

?>