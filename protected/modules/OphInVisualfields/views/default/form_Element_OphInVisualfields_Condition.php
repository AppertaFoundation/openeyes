<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-fields">
  <table class="cols-7">
    <colgroup>
      <col class="cols-4">
    </colgroup>
    <tbody>
    <tr>
      <td>Ability</td>
      <td >
            <?= $form->multiSelectList(
              $element,
              'MultiSelect_ability',
              'abilitys',
              'ophinvisualfields_condition_ability_id',
              CHtml::listData(OphInVisualfields_Condition_Ability::model()->findAll(array('order' => 'display_order asc')),
                  'id', 'name'),
              $element->ophinvisualfields_condition_ability_defaults,
              array(
                  'empty' => 'Select',
                  'class' => 'linked-fields',
                  'data-linked-fields' => 'other',
                  'data-linked-values' => 'Other',
                  'nowrapper' => true,
              )
          ) ?>
      </td>
    </tr>
    <tr style="<?= $element->hasMultiSelectValue('abilitys', 'Other') ? '' : 'display: none;' ?> ">
      <td></td>
      <td>
            <?= $form->textArea($element, 'other', array('rows' => 4, 'nowrapper' => true), true, array('placeholder' => 'Other - please specify')) ?>
      </td>
    </tr>
    <tr>
      <td><?= $element->getAttributeLabel('glasses') ?></td>
      <td>
            <?= $form->radioBoolean($element, 'glasses', array('nowrapper' => true)) ?>
      </td>
    </tr>
    </tbody>
  </table>
</div>
