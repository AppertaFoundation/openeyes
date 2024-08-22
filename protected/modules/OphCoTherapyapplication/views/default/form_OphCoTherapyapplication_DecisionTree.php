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
$decisiontree = null;
$treatment = $element->{$side . '_treatment'};

if ($treatment && $treatment->decisiontree) {
    $decisiontree = $treatment->decisiontree;
}
?>

<div id="OphCoTherapyapplication_ComplianceCalculator_<?php echo $side ?>"
    <?php if ($decisiontree) {
        echo " data-defn='" . CJSON::encode($decisiontree->getDefinition()) . "'";
    } ?>>
    <?php if ($decisiontree) { ?>
        <?php foreach ($decisiontree->nodes as $node) { ?>
        <div class="dt-node data-group" id="<?php echo $side; ?>_node_<?php echo $node->id ?>" style="display: none;"
             data-defn='<?php echo CJSON::encode($node->getDefinition()); ?>'>
            <?php if ($node->question) { ?>
              <label style="white-space: normal"
                  for="Element_OphCoTherapyapplication_PatientSuitability_<?php echo $side; ?>_DecisionTreeResponse_<?php echo $node->id; ?>">
                  <?php echo $node->question ?>
              </label>
                <?php $val = $this->getNodeResponseValue($element, $side, $node->id);
                if ($node->response_type->datatype == 'bool') { ?>
                  <select
                      id="Element_OphCoTherapyapplication_PatientSuitability_<?php echo $side; ?>_DecisionTreeResponse_<?php echo $node->id; ?>"
                      name="Element_OphCoTherapyapplication_PatientSuitability[<?php echo $side; ?>_DecisionTreeResponse][<?php echo $node->id; ?>]">
                    <option value="">Select</option>
                    <option value="0" <?php if ($val == '0') {
                        echo 'selected';
                                      } ?>>No
                    </option>
                    <option value="1" <?php if ($val == '1') {
                        echo 'selected';
                                      } ?>>Yes
                    </option>
                  </select>
                <?php } elseif ($node->response_type->datatype == 'va') { ?>
                  <select
                      id="Element_OphCoTherapyapplication_PatientSuitability_<?php echo $side; ?>_DecisionTreeResponse_<?php echo $node->id; ?>"
                      name="Element_OphCoTherapyapplication_PatientSuitability[<?php echo $side; ?>_DecisionTreeResponse][<?php echo $node->id; ?>]">
                    <option value="">Select</option>
                      <?php foreach ($node->response_type->getChoices() as $id => $label) { ?>
                        <option value="<?php echo $id; ?>" <?php if ($val == $id) {
                            echo 'selected';
                                       } ?>><?php echo $label; ?></option>
                      <?php } ?>
                  </select>
                <?php } else { ?>
                  <input type="text"
                         id="Element_OphCoTherapyapplication_PatientSuitability_<?php echo $side; ?>_DecisionTreeResponse_<?php echo $node->id; ?>"
                         name="Element_OphCoTherapyapplication_PatientSuitability[<?php echo $side; ?>_DecisionTreeResponse][<?php echo $node->id; ?>]"
                         autocomplete="<?php echo SettingMetadata::model()->getSetting('html_autocomplete') ?>"
                         value="<?php echo $val; ?>"/>
                <?php } ?>
            <?php } ?>
        </div>
        <?php } ?>
    <?php } else { ?>
      <div class="field-value">Please select a treatment to determine compliance.</div>
    <?php } ?>
  <span id="<?php echo $side; ?>_outcome_unknown" style="display: none;" class="outcome unknown highlighter orange">Unknown</span>
    <?php foreach (OphCoTherapyapplication_DecisionTreeOutcome::model()->findAll() as $outcome) { ?>
      <span id="<?php echo $side; ?>_outcome_<?php echo $outcome->id ?>" style="display: none;"
           class="outcome highlighter <?php echo $outcome->isCompliant() ? 'compliant good' : 'non-compliant warning'; ?>"
           data-comp-val="<?php echo $outcome->isCompliant() ? '1' : '0'; ?>">
          <?php echo $outcome->name; ?>
      </span>
    <?php } ?>
    <?php echo $form->hiddenInput($element, $side . '_nice_compliance') ?>
</div>
