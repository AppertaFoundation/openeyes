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
$name_stub = $element_name . '[' . $side;
if ($pastintervention->is_relevant) {
    $inttype_name = '_relevantinterventions';
    $treatmentattribute = 'relevanttreatment_id';
} else {
    $inttype_name = '_previnterventions';
    $treatmentattribute = 'treatment_id';
}
$name_stub .= $inttype_name . ']';

$show_stop_other = false;
$show_treatment_other = false;
if (
    @$_POST[$element_name] && @$_POST[$element_name][$side . $inttype_name] &&
    @$_POST[$element_name][$side . $inttype_name][$key]
) {
    if ($stop_id = $_POST[$element_name][$side . $inttype_name][$key]['stopreason_id']) {
        $stopreason = OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention_StopReason::model()->findByPk((int)$stop_id);
        if ($stopreason->other) {
            $show_stop_other = true;
        }
    }
    if (
        $pastintervention->is_relevant &&
        $treatment_id = $_POST[$element_name][$side . $inttype_name][$key]['relevanttreatment_id']
    ) {
        $treatment = OphCoTherapyapplication_RelevantTreatment::model()->findByPk((int)$treatment_id);
        if ($treatment->other) {
            $show_treatment_other = true;
        }
    }
} else {
    if ($pastintervention->stopreason && $pastintervention->stopreason->other) {
        $show_stop_other = true;
    }
    if (
        $pastintervention->is_relevant &&
        $pastintervention->relevanttreatment &&
        $pastintervention->relevanttreatment->other
    ) {
        $show_treatment_other = true;
    }
}

// [OE-3421]
// This view is used for:
// 1) Displaying previous interventions on page load.
// 2) As a script template for dynamically adding new previous interventions.
//
// When used as a script template, we don't want to init the datepickers because that will
// generate a Javascript error, and because we already do that "manually" in Javascript land.
// So, we can control what widget is used by supplying the 'dateFieldWidget' template var.
$dateFieldWidget = @$dateFieldWidget ?: 'DatePicker';

/*
 * Am using a bit of a bastardisation of different form field approaches here as this many to many model form is not something that is supported well
 * by the OpenEyes extensions for forms. Will be worth tidying up as and when feasible (off the back of OE-2522)
 */

?>
<table>
  <tbody class="panel previous-interventions pastintervention" data-key="<?php echo $key ?>">
  <tr>
    <td></td>
    <td>
      <a href="#" class="icon-remove-side removePastintervention removeElementForm">
        <i class="oe-i trash"></i>
      </a>
    </td>
  </tr>
    <?php if ($pastintervention && $pastintervention->id) { ?>
    <input type="hidden"
           name="<?php echo $name_stub; ?>[<?php echo $key ?>][id]"
           value="<?php echo $pastintervention->id ?>"/>
    <?php } ?>

    <?php $d_name = $name_stub . "[$key][start_date]";
    $d_id = preg_replace('/\[/', '_', substr($name_stub, 0, -1)) . '_' . $key . '_start_date'; ?>
  <tr>
    <td>
      <label for="<?php echo $d_id ?>">
            <?php echo $pastintervention->getAttributeLabel('start_date'); ?>:
      </label>
    </td>
    <td>
        <?php
        // using direct widget call to allow custom name for the field
        // see comment [OE-3421] above.
        $options = array(
            'element' => $pastintervention,
            'name' => $d_name,
            'field' => 'start_date',
            'htmlOptions' => array(
                'id' => $d_id,
                'nowrapper' => true,
            ),
        );
        if ($dateFieldWidget === 'DatePicker') {
            $options['options'] = array('maxDate' => 'today');
        }
        $form->widget("application.widgets.{$dateFieldWidget}", $options);
        ?>
    </td>
  </tr>

    <?php
    $d_name = $name_stub . "[$key][end_date]";
    $d_id = preg_replace('/\[/', '_', substr($name_stub, 0, -1)) . '_' . $key . '_end_date';
    ?>
  <tr>
    <td>
      <label for="<?php echo $d_id; ?>">
            <?php echo $pastintervention->getAttributeLabel('end_date'); ?>:
      </label>
    </td>
    <td>
        <?php
        // using direct widget call to allow custom name for the field
        // see comment [OE-3421] above.
        $options = array(
            'element' => $pastintervention,
            'name' => $d_name,
            'field' => 'end_date',
            'htmlOptions' => array(
                'id' => $d_id,
                'nowrapper' => true,
            ),
        );
        if ($dateFieldWidget === 'DatePicker') {
            $options['options'] = array('maxDate' => 'today');
        }
        $form->widget("application.widgets.{$dateFieldWidget}", $options);
        ?>
    </td>
  </tr>

  <tr>
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_{$treatmentattribute}"); ?>">
            <?php echo $pastintervention->getAttributeLabel($treatmentattribute); ?>:
      </label>
    </td>
    <td>
        <?php $all_treatments = $pastintervention->getTreatmentOptions($pastintervention->{$treatmentattribute});
        $html_options = array(
            'class' => 'past-treatments',
            'empty' => 'Select',
            'name' => $name_stub . "[$key][$treatmentattribute]",
            'options' => array(),
        );
        if ($pastintervention->is_relevant) {
            foreach ($all_treatments as $treatment) {
                $html_options['options'][$treatment->id] = array(
                    'data-other' => $treatment->other,
                );
            }
        }

        echo CHtml::activeDropDownList(
            $pastintervention,
            $treatmentattribute,
            CHtml::listData($all_treatments, 'id', 'name'),
            $html_options
        ); ?>
    </td>
  </tr>

  <tr class=" <?php if (!$show_treatment_other) {
        echo 'hidden ';
              } ?>treatment-other">
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_relevanttreatment_other"); ?>">
            <?php echo $pastintervention->getAttributeLabel('relevanttreatment_other'); ?>:
      </label>
    </td>
    <td>
        <?=\CHtml::activeTextField($pastintervention, 'relevanttreatment_other', array(
            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
            'name' => $name_stub . "[$key][relevanttreatment_other]",
        )); ?>
    </td>
  </tr>

  <tr>
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_start_va"); ?>">
            <?php echo $pastintervention->getAttributeLabel('start_va'); ?>:
      </label>
    </td>
    <td>
        <?=\CHtml::activeDropDownList(
            $pastintervention,
            'start_va',
            $pastintervention->getVaOptions(),
            array('empty' => 'Select', 'name' => $name_stub . "[$key][start_va]", 'nowrapper' => true)
        );
            ?>
    </td>
  </tr>

  <tr>
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_end_va"); ?>">
            <?php echo $pastintervention->getAttributeLabel('end_va'); ?>:
      </label>
    </td>
    <td>
        <?=\CHtml::activeDropDownList(
            $pastintervention,
            'end_va',
            $pastintervention->getVaOptions(),
            array('empty' => 'Select', 'name' => $name_stub . "[$key][end_va]", 'nowrapper' => true)
        ); ?>
    </td>
  </tr>

  <tr>
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_stopreason_id"); ?>">
            <?php echo $pastintervention->getAttributeLabel('stopreason_id') ?>:
      </label>
    </td>
    <td>
        <?php
        $reasons = OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention_StopReason::model()->findAll();
        $html_options = array(
            'class' => 'stop-reasons',
            'empty' => 'Select',
            'name' => $name_stub . "[$key][stopreason_id]",
            'options' => array(),
        );
        // get the previous injection counts for each of the drug options for this eye
        foreach ($reasons as $reason) {
            $html_options['options'][$reason->id] = array(
                'data-other' => $reason->other,
            );
        }
        echo CHtml::activeDropDownList(
            $pastintervention,
            'stopreason_id',
            CHtml::listData($reasons, 'id', 'name'),
            $html_options
        );
        ?>
    </td>
  </tr>

  <tr class=" <?php if (!$show_stop_other) {
        echo 'hidden ';
              } ?>stop-reason-other">
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_stopreason_other"); ?>">
            <?php echo $pastintervention->getAttributeLabel('stopreason_other'); ?>:
      </label>
    </td>
    <td>
        <?=\CHtml::activeTextArea(
            $pastintervention,
            'stopreason_other',
            array('name' => $name_stub . "[$key][stopreason_other]", 'rows' => 2, 'cols' => 25, 'nowrapper' => true)
        ) ?>
    </td>
  </tr>

  <tr>
    <td>
      <label for="<?php echo str_replace(array('[', ']'), '_', $name_stub . "{$key}_comments"); ?>">
            <?php echo $pastintervention->getAttributeLabel('comments') ?>
      </label>
    </td>
    <td>
        <?=\CHtml::activeTextArea($pastintervention, 'comments', array(
            'placeholder' => 'Please provide pre and post treatment CMT',
            'name' => $name_stub . "[$key][comments]",
            'rows' => 3,
            'nowrapper' => true,
        )) ?>
    </td>
  </tr>
  </tbody>
</table>
