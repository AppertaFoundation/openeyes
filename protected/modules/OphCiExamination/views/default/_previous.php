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
<table>
  <colgroup>
    <col class="cols-6">
    <col class="cols-5">
  </colgroup>
  <tbody>
    <?php foreach ($elements as $element) { ?>
    <tr>
      <td class="element <?=\CHtml::modelName($element) ?>"
          data-element-id="<?php echo $element->id ?>"
          data-element-type-id="<?php echo $element->elementType->id ?>"
          data-element-type-class="<?=\CHtml::modelName($element) ?>"
          data-element-type-name="<?php echo $element->elementType->name ?>"
          data-element-display-order="<?php echo $element->display_order ?>">

        <?php $this->renderElement($element, 'view', null, null) ?>

        <div class="flex-layout">
          <div class="metadata">
            <div class="info">Examination created by
              <span class="user"><?php echo $element->event->user->fullname ?></span>
              on <?php echo $element->event->NHSDate('created_date') ?>
              at <?php echo date('H:i', strtotime($element->event->created_date)) ?></div>
            <div class="info">Examination last modified by
              <span class="user"><?php echo $element->event->usermodified->fullname ?></span>
              on <?php echo $element->event->NHSDate('last_modified_date') ?>
              at <?php echo date('H:i', strtotime($element->event->last_modified_date)) ?></div>
          </div>
            <?php if ($element->canCopy()) { ?>
              <button name="copy" class="copy_element small"
                      data-element-id="<?php echo $element->id ?>"
                      data-element-type-class="<?=\CHtml::modelName($element) ?>"
                      data-test="copy-previous-element">
                Copy
              </button>
            <?php } ?>
        </div>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
