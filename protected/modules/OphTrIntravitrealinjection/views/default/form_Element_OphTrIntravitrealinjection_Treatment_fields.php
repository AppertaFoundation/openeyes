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
$antiseptic_drugs = OphTrIntravitrealinjection_AntiSepticDrug::model()->with('allergies')->activeOrPk($element->{$side . '_pre_antisept_drug_id'})->findAll();
$antiseptic_drugs_opts = array(
    'empty' => 'Select',
    'nowrapper' => true,
    'options' => array(),
);
$antiseptic_allergy = null;
foreach ($antiseptic_drugs as $drug) {
    $opts = array();
    foreach ($drug->allergies as $allergy) {
        if ($this->patient->hasAllergy($allergy)) {
            $opts['data-allergic'] = 1;
            $opts['data-allergy'] = $allergy->name;
            if ($drug->id == $element->{$side . '_pre_antisept_drug_id'}) {
                $antiseptic_allergy = $allergy->name;
            }
        }
    }
    $antiseptic_drugs_opts['options'][(string)$drug->id] = $opts;
}
$skin_drugs = OphTrIntravitrealinjection_SkinDrug::model()->with('allergies')->activeOrPk($element->{$side . '_pre_skin_drug_id'})->findAll();
$skin_drugs_opts = array('empty' => 'Select', 'nowrapper' => true, 'options' => array());
$skin_allergy = null;
foreach ($skin_drugs as $drug) {
    $opts = array();
    foreach ($drug->allergies as $allergy) {
        if ($this->patient->hasAllergy($allergy)) {
            $opts['data-allergic'] = 1;
            $opts['data-allergy'] = $allergy->name;
            if ($drug->id == $element->{$side . '_pre_skin_drug_id'}) {
                $skin_allergy = $allergy->name;
            }
        }
    }
    $skin_drugs_opts['options'][(string)$drug->id] = $opts;
}
?>
<div class="data-group">
  <table>
    <tbody>
    <tr id="div_<?php echo get_class($element) ?>_<?php echo $side ?>_pre_antisept_drug_id"
        class="data-group">
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_pre_antisept_drug_id">
            <?php echo $element->getAttributeLabel($side . '_pre_antisept_drug_id') ?>:
        </label>
      </td>
      <td class="wrapper">
            <?php if (isset($antiseptic_allergy)) {
                echo '<i class="oe-i warning pad-right js-allergy-warning js-has-tooltip" 
          data-tooltip-content="Allergic to ' .  $antiseptic_allergy . '"></i>';
            } ?>
            <?php echo $form->dropDownList(
                $element,
                $side . '_pre_antisept_drug_id',
                CHtml::listData($antiseptic_drugs, 'id', 'name'),
                $antiseptic_drugs_opts
            ); ?>
      </td>
    </tr>
    <tr id="div_<?php echo get_class($element) ?>_<?php echo $side ?>_pre_skin_drug_id"
        class="data-group">
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_pre_skin_drug_id">
            <?php echo $element->getAttributeLabel($side . '_pre_skin_drug_id') ?>:
        </label>
      </td>
      <td class="wrapper">
            <?php if (isset($skin_allergy)) {
                echo '<i class="oe-i warning pad-right js-allergy-warning js-has-tooltip"
           data-tooltip-content="Allergic to ' .  $skin_allergy . '"></i>';
            } ?>
            <?php echo $form->dropDownList(
                $element,
                $side . '_pre_skin_drug_id',
                CHtml::listData($skin_drugs, 'id', 'name'),
                $skin_drugs_opts
            ); ?>
      </td>
    </tr>
    <tr>
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_pre_ioplowering_required">
            <?php echo $element->getAttributeLabel($side . '_pre_ioplowering_required') ?>:
        </label>
      </td>
      <td>
            <?php echo $form->checkbox(
                $element,
                $side . '_pre_ioplowering_required',
                array('nowrapper' => true, 'no-label' => true)
            ); ?>
      </td>
    </tr>
    <?php
    $show = $element->{$side . '_pre_ioplowering_required'};
    if (isset($_POST[get_class($element)])) {
        $show = $_POST[get_class($element)][$side . '_pre_ioplowering_required'];
    }
    $div_class = 'eventDetail';
    if (!$show) {
        $div_class .= ' hidden';
    }
    $div_id = 'div_' . CHtml::modelName($element) . '_' . $side . '_pre_ioploweringdrugs';
    ?>
    <tr id="<?= $div_id ?>"
        class="<?php echo $div_class ?> row widget">
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_pre_ioploweringdrugs">
            <?php echo $element->getAttributeLabel($side . '_pre_ioploweringdrugs') ?>:
        </label>
      </td>
      <td>
            <?php
            $html_options = array(
              'options' => array(),
              'empty' => 'Select',
              'div_id' => 'div_' . get_class($element) . '_' . $side . '_pre_ioploweringdrugs',
              'label' => '',
              'div_class' => $div_class,
              'nowrapper' => true,
            );
            $ioplowering_drugs = OphTrIntravitrealinjection_IOPLoweringDrug::model()->activeOrPk($element->iopLoweringDrugValues)->findAll(array('order' => 'display_order asc'));
            foreach ($ioplowering_drugs as $drug) {
                $html_options['options'][(string)$drug->id] = array('data-order' => $drug->display_order);
            }

            echo $form->multiSelectList(
                $element,
                get_class($element) . '[' . $side . '_pre_ioploweringdrugs]',
                $side . '_pre_ioploweringdrugs',
                'id',
                CHtml::listData($ioplowering_drugs, 'id', 'name'),
                array(),
                $html_options,
                false,
                false,
                null,
                false,
                false,
                array()
            );
            ?>
      </td>
    </tr>
    <?php
    $drugs = OphTrIntravitrealinjection_Treatment_Drug::model()->activeOrPk($element->{$side . '_drug_id'})->findAll();

    $html_options = array(
        'empty' => 'Select',
        'nowrapper' => true,
        'options' => array(),
    );
    // get the previous injection counts for each of the drug options for this eye
    $drug_history = array();

    foreach ($drugs as $drug) {
        if ($element->event_id) {
            $previous = $injection_api->previousInjectionsByEvent($element->event_id, $side, $drug);
        } else {
            $previous = $injection_api->previousInjections($this->patient, $episode, $side, $drug);
        }
        $count = 0;
        if (count($previous)) {
            $count = $previous[count($previous) - 1][$side . '_number'];
        }
        $drug_history[$drug->id] = $previous;

        $html_options['options'][$drug->id] = array(
            'data-previous' => $count,
        );

        // if this is an edit, we want to know what the original count was so that we don't replace it
        if ($element->{$side . '_drug_id'} && $element->{$side . '_drug_id'} == $drug->id) {
            $html_options['options'][$drug->id]['data-original-count'] = $element->{$side . '_number'};
        }
    }
    ?>
    <tr>
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_drug_id">
            <?php echo $element->getAttributeLabel($side . '_drug_id') ?>:
        </label>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                $side . '_drug_id',
                CHtml::listData($drugs, 'id', 'name'),
                $html_options,
                false,
                array()
            ); ?>
      </td>
    </tr>

    <?php
    $selected_drug = null;
    if (@$_POST['Element_OphTrIntravitrealinjection_Treatment']) {
        $selected_drug = $_POST['Element_OphTrIntravitrealinjection_Treatment'][$side . '_drug_id'];
    } else {
        $selected_drug = $element->{$side . '_drug_id'};
    }
    ?>
    <tr id="div_<?php echo get_class($element); ?>_<?php echo $side ?>_number" class="data-group">
      <td class="cols-<?php echo $form->columns('label'); ?>">
        <label for="<?php echo get_class($element); ?>_<?php echo $side ?>_number">
            <?php echo $element->getAttributeLabel($side . '_number'); ?>:
        </label>
      </td>
      <td class="cols-<?php echo $form->columns('field'); ?>">
        <div class="collapse in">
          <div class="flex-layout">
                <?php echo $form->textField($element, $side . '_number', array('size' => '10', 'nowrapper' => true)) ?>
            <span id="<?php echo $side; ?>_number_history_icon"
                  class="postfix number-history-icon<?php if (!$selected_drug) {
                        echo ' hidden';
                                                    } ?>">
                <?php $tooltip_info = "";
                foreach ($drugs as $drug) {
                    if (count($drug_history[$drug->id])) {
                        $tooltip_info = $tooltip_info . '<b>Previous ' . $drug->name . ' treatments</b><br />';
                        foreach ($drug_history[$drug->id] as $previous) {
                            $tooltip_info = $tooltip_info . Helper::convertDate2NHS($previous['date']) . ' (' . $previous[$side . '_number'] . ')<br />';
                        }
                    }
                }
                if ($tooltip_info == "") {
                    $tooltip_info = "The patient has no drug history";
                }?>
                <i class="oe-i info small-icon js-has-tooltip"
                   data-tooltip-content="<?php echo $tooltip_info; ?>">
                 </i>
            </span>
          </div>
        </div>
      </td>
    </tr>

    <tr>
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_batch_number">
            <?php echo $element->getAttributeLabel($side . '_batch_number') ?>:
        </label>
      </td>
      <td>
            <?php echo $form->textField(
                $element,
                $side . '_batch_number',
                array('nowrapper' => true),
                array(),
                array('field' => 6)
            ) ?>
      </td>
    </tr>
    <?php
    if (!$element->getIsNewRecord()) {
        $expiry_date_params = array('minDate' => Helper::convertDate2NHS($element->created_date));
    } else {
        $expiry_date_params = array('minDate' => 'yesterday');
    }
    ?>
    <tr>
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_batch_expiry_date">
            <?php echo $element->getAttributeLabel($side . '_batch_expiry_date') ?>:
        </label>
      </td>
      <td>
            <?php echo $form->datePicker(
                $element,
                $side . '_batch_expiry_date',
                $expiry_date_params,
                array('nowrapper' => true),
                array(
                  'label' => $form->layoutColumns['label'],
                  'field' => 3,
                )
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_injection_given_by_id">
            <?php echo $element->getAttributeLabel($side . '_injection_given_by_id') ?>:
        </label>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                $side . '_injection_given_by_id',
                CHtml::listData(
                    OphTrIntravitrealinjection_InjectionUser::model()->getUsers(),
                    'id',
                    'ReversedFullName'
                ),
                array('empty' => 'Select', 'nowrapper' => true),
                false,
                array('field' => 6)
            )
            ?>
      </td>
    </tr>
    <tr id="div_<?php echo get_class($element) ?>_<?php echo $side ?>_injection_time"
        class="data-group">
      <td class="<?php echo $form->columns('label'); ?>">
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_injection_time">
            <?php echo $element->getAttributeLabel($side . '_injection_time') ?>:
        </label>
      </td>
      <td class="<?php echo $form->columns(3, true); ?>">
            <?php
            if ($element->{$side . '_injection_time'} != null) {
                $val = date('H:i', strtotime($element->{$side . '_injection_time'}));
            } else {
                $val = date('H:i');
            }

            if (isset($_POST[get_class($element)])) {
                $val = $_POST[get_class($element)][$side . '_injection_time'];
            }
            echo CHtml::textField(
                get_class($element) . '[' . $side . '_injection_time]',
                $val,
                array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))
            );
            ?>
      </td>
    </tr>
    <tr>
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_post_ioplowering_required">
            <?php echo $element->getAttributeLabel($side . '_post_ioplowering_required') ?>:
        </label>
      </td>
      <td>
            <?php echo $form->checkbox(
                $element,
                $side . '_post_ioplowering_required',
                array('nowrapper' => true, 'no-label' => true)
            ); ?>
      </td>
    </tr>
    <?php
    $div_class = 'eventDetail';
    $show = $element->{$side . '_post_ioplowering_required'};

    if (isset($_POST[get_class($element)])) {
        $show = $_POST[get_class($element)][$side . '_post_ioplowering_required'];
    }

    if (!$show) {
        $div_class .= ' hidden';
    }
    $div_id = 'div_' . CHtml::modelName($element) . '_' . $side . '_post_ioploweringdrugs';

    ?>
    <tr id="<?= $div_id ?>"
        class="<?php echo $div_class ?> row widget">
      <td>
        <label for="<?php echo get_class($element) ?>_<?php echo $side ?>_post_ioploweringdrugs">
            <?php echo $element->getAttributeLabel($side . '_post_ioploweringdrugs') ?>:
        </label>
      </td>
      <td>
            <?php
            $html_options = array(
              'options' => array(),
              'empty' => 'Select',
              'div_id' => 'div_' . get_class($element) . '_' . $side . '_post_ioploweringdrugs',
              'label' => $element->getAttributeLabel($side . '_post_ioploweringdrugs'),
              'div_class' => $div_class,
              'nowrapper' => true,
            );
            $ioplowering_drugs = OphTrIntravitrealinjection_IOPLoweringDrug::model()->activeOrPk($element->iopLoweringDrugValues)->findAll(array('order' => 'display_order asc'));
            foreach ($ioplowering_drugs as $drug) {
                $html_options['options'][(string)$drug->id] = array('data-order' => $drug->display_order);
            }
            echo $form->multiSelectList(
                $element,
                get_class($element) . '[' . $side . '_post_ioploweringdrugs]',
                $side . '_post_ioploweringdrugs',
                'id',
                CHtml::listData($ioplowering_drugs, 'id', 'name'),
                array(),
                $html_options,
                false,
                false,
                null,
                false,
                false,
                array()
            );
            ?>
      </td>
    </tr>
    </tbody>
  </table>
</div>
