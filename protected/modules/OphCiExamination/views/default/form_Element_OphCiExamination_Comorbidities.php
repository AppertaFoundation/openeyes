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
if (isset($_POST['comorbidities_items_valid']) && $_POST['comorbidities_items_valid']) {
    $item_ids = @$_POST['comorbidities_items'];
} else {
    $item_ids = $element->getItemIds();
}
?>
<div class="sub-element-fields">
    <?php echo CHtml::hiddenField('comorbidities_items_valid', 1, array('id' => 'comorbidities_items_valid')) ?>
  <div class="field-row comorbidities-multi-select">
      <?php echo $form->multiSelectList(
          $element,
          CHtml::modelName($element) . '[items]',
          'items',
          'id',
          CHtml::encodeArray(
              CHtml::listData(
                  OEModule\OphCiExamination\models\OphCiExamination_Comorbidities_Item::model()
                      ->activeOrPk($element->comorbidityItemValues)
                      ->bySubspecialty($this->firm->getSubspecialty())
                      ->findAll(),
                  'id',
                  'name')
          ),
          array(),
          array('empty' => '-- Add --', 'label' => 'Comorbidities', 'nowrapper' => true),
          false,
          true,
          'No comorbidities',
          true,
          true
      ) ?>
  </div>
  <div class="field-row">
      <?php echo $form->textArea($element, 'comments', array('rows' => '1', 'cols' => '80', 'class' => 'autosize', 'nowrapper' => true), false,
          array('placeholder' => 'Enter comments here')) ?>
  </div>
</div>
