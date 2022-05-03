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
<div class="eyedraw-fields">
  <div class="data-group">
    <label for="<?php echo $side . '_opticdisc_mode'; ?>">
      Mode:
    </label>
        <?=\CHtml::dropDownList(
            $side . '_opticdisc_mode',
            'Basic',
            array('Basic' => 'Basic', 'Expert' => 'Expert'),
            array(
              'class' => 'opticdisc-mode',
              'options' => array(
                  'Basic' => array('data-value' => 'Basic'),
                  'Expert' => array('data-value' => 'Expert'),
              ),
            )
        ) ?>
  </div>
  <div class="data-group">
    <label for="<?=\CHtml::modelName($element) . '_' . $side . '_cd_ratio_id'; ?>">
        <?php echo $element->getAttributeLabel($side . '_cd_ratio_id') ?>:
    </label>
        <?php
        $options = \OEModule\OphCiExamination\models\OphCiExamination_OpticDisc_CDRatio::model()->findAll();
        $cd_ratio_html_options = array('class' => 'cd-ratio', 'options' => array());
        foreach ($options as $ratio) {
            $cd_ratio_html_options['options'][(string)$ratio->id] = array('data-value' => $ratio->name);
        }
        ?>
        <?=\CHtml::activeDropDownList(
            $element,
            $side . '_cd_ratio_id',
            CHtml::listData($options, 'id', 'name'),
            $cd_ratio_html_options
        ) ?>
  </div>
  <div class="data-group">
    <label for="<?=\CHtml::modelName($element) . '_' . $side . '_diameter'; ?>">
        <?php echo $element->getAttributeLabel($side . '_diameter') ?>:
    </label>
    <div class="data-group collapse in">
      <div class="cols-3 column">
            <?=\CHtml::activeTextField(
                $element,
                $side . '_diameter',
                array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'class' => 'diameter')
            ) ?>
      </div>
      <div class="cols-9 column">
        <div class="field-info postfix align">
          mm (lens) <?= $form->dropDownList(
              $element,
              $side . '_lens_id',
              '\OEModule\OphCiExamination\models\OphCiExamination_OpticDisc_Lens',
              array('empty' => '--', 'class' => 'inline', 'nowrapper' => true)
          ) ?>
        </div>
      </div>
    </div>
  </div>
    <?=\CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
  <div class="data-group">
    <div class="cols-6 column end">
      <label>
            <?php echo $element->getAttributeLabel($side . '_ed_report') ?>:
      </label>
    </div>
    <div class="cols-10 column end autoreport-display">
      <span class="data-value" id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display' ?>"></span>
    </div>
  </div>
  <div class="data-group">
        <?=\CHtml::activeTextArea($element, $side . '_description', array(
          'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
          'rows' => 1,
          'placeholder' => $element->getAttributeLabel($side . '_description'),
      )) ?>
  </div>
</div>
