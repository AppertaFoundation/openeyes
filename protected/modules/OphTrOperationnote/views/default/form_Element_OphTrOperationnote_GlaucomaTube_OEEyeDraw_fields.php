<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
$plate_positions = OphTrOperationnote_GlaucomaTube_PlatePosition::model()->activeOrPk($element->plate_position_id)->findAll();
$html_options = array(
    'label' => $element->getAttributeLabel('plate_position_id'),
    'options' => array(),
    'nowrapper' => true,
);
foreach ($plate_positions as $pp) {
    $html_options['options'][$pp->id] = array('data-value' => $pp->eyedraw_value);
}
?>

<div class="cols-full">
  <table class="cols-8">
    <colgroup>
      <col class="cols-6">
    </colgroup>
    <tbody>
    <tr>
      <td>
            <?php echo $element->getAttributeLabel('plate_position_id'); ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'plate_position_id',
                CHtml::listData($plate_positions, 'id', 'name'),
                $html_options,
                false,
                array('field' => 3)
            ); ?>
      </td>
    </tr>
    <tr>
      <td>
            <?= $element->getAttributeLabel('plate_limbus'); ?>
      </td>
      <td class="flex-layout flex-right">
            <?=\CHtml::activeTextField(
                $element,
                'plate_limbus',
                array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'class' => 'clearWithEyedraw')
            ); ?>
        <span class="field-info postfix align">
          mm
        </span>
      </td>
    </tr>
    <tr>
      <td>
            <?= $element->getAttributeLabel('tube_position_id'); ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'tube_position_id',
                CHtml::listData(
                    OphTrOperationnote_GlaucomaTube_TubePosition::model()->activeOrPk($element->tube_position_id)->findAll(),
                    'id',
                    'name'
                ),
                array('empty' => 'Select', 'nowrapper' => true),
                false,
                array('field' => 3)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?= $element->getAttributeLabel('stent'); ?>
      </td>
      <td>
            <?php echo $form->checkbox($element, 'stent', array('class' => 'clearWithEyedraw', 'nowrapper' => true)) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?= $element->getAttributeLabel('slit'); ?>
      </td>
      <td>
            <?php echo $form->checkbox($element, 'slit', array('class' => 'clearWithEyedraw', 'nowrapper' => true)) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?= $element->getAttributeLabel('visco_in_ac'); ?>
      </td>
      <td>
            <?php echo $form->checkbox(
                $element,
                'visco_in_ac',
                array('class' => 'clearWithEyedraw', 'nowrapper' => true)
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?= $element->getAttributeLabel('flow_tested'); ?>
      </td>
      <td>
            <?php echo $form->checkbox(
                $element,
                'flow_tested',
                array('class' => 'clearWithEyedraw', 'nowrapper' => true)
            ) ?>
      </td>
    </tr>
    </tbody>
  </table>
  <br/><?php echo $form->textArea(
      $element,
      'description',
      array('rows' => 4, 'cols' => 40, 'class' => 'autosize clearWithEyedraw', 'nowrapper' => true),
      false,
      array('placeholder' => 'Description')
  ) ?>
  <div class="data-group">
    <div class="cols-3 column">&nbsp;</div>
    <div class="cols-4 column end">
      <button id="btn-glaucomatube-report" class="ed_report secondary small">Report</button>
      <button class="ed_clear secondary small">Clear</button>
    </div>
  </div>
</div>
