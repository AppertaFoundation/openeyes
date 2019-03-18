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
<table class="cols-11">
  <colgroup>
    <col class="cols-5">
  </colgroup>
  <tbody>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('consultant_id')) ?>:
    </td>
    <td>
        <?php echo $element->consultant->fullNameAndTitle ?>
    </td>
    <td></td>
  </tr>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('anaesthetic_leaflet')) ?>:
    </td>
    <td>
        <?php echo $element->anaesthetic_leaflet ? 'Yes' : 'No' ?>
    </td>
    <td></td>
  </tr>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('witness_required')) ?>:
    </td>
    <td>
        <?php echo $element->witness_required ? 'Yes' : 'No' ?>
    </td>
    <td></td>
  </tr>

  <?php if ($element->witness_required) { ?>
    <tr>
      <td>
          <?=\CHtml::encode($element->getAttributeLabel('witness_name')) ?>:
      </td>
      <td>
          <?=\CHtml::encode($element->witness_name) ?>
      </td>
      <td></td>
    </tr>
  <?php } ?>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('interpreter_required')) ?>:
    </td>
    <td>
        <?php echo $element->interpreter_required ? 'Yes' : 'No' ?>
    </td>
    <td></td>
  </tr>
  <?php if ($element->interpreter_required) { ?>
    <tr>
      <td>
          <?=\CHtml::encode($element->getAttributeLabel('interpreter_name')) ?>:
      </td>
      <td>
          <?=\CHtml::encode($element->interpreter_name) ?>
      </td>
      <td></td>
    </tr>
  <?php } ?>
  <?php if ($element->parent_guardian) { ?>
    <tr>
      <td>
          <?=\CHtml::encode($element->getAttributeLabel('parent_guardian')) ?>:
      </td>
      <td>
          <?=\CHtml::encode($element->parent_guardian) ?>
      </td>
      <td></td>
    </tr>
  <?php } ?>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('include_supplementary_consent')) ?>:
    </td>
    <td>
        <?php echo $element->include_supplementary_consent ? 'Yes' : 'No' ?>
    </td>
    <td></td>
  </tr>
  </tbody>
</table>