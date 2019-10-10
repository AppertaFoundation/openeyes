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
<table class="borders">
  <tbody>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_pre_antisept_drug_id') ?>:
      </div>
    </td>
    <td><?php echo $element->{$side . '_pre_antisept_drug'}->name ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_pre_skin_drug_id') ?>:<div class="data-label">
        </div>
    </td>
    <td><?php echo $element->{$side . '_pre_skin_drug'}->name ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_pre_ioplowering_required') ?>:
      </div>
    </td>
    <td><?php echo $element->{$side . '_pre_ioplowering_required'} ? 'Yes' : 'No' ?></td>
  </tr>
    <?php if ($element->{$side . '_pre_ioploweringdrugs'}) { ?>
    <tr>
      <td>
        <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_pre_ioploweringdrugs') ?>:
        </div>
      </td>
      <td>
          <?php
            foreach ($element->{$side . '_pre_ioploweringdrugs'} as $item) {
                echo $item->name . '<br />';
            }
            ?>
      </td>
    </tr>
    <?php } ?>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_drug_id') ?>:
      </div>
    </td>
    <td><?php echo $element->{$side . '_drug'}->name ?>    </td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_number') ?>:
      </div>
    </td>
    <td><?=\CHtml::encode($element->{$side . '_number'}) ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_batch_number') ?>:
      </div>
    </td>
    <td><?=\CHtml::encode($element->{$side . '_batch_number'}) ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_batch_expiry_date') ?>:
      </div>
    </td>
    <td><?php echo $element->NHSDate($side . '_batch_expiry_date') ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_injection_given_by_id') ?>:
      </div>
    </td>
    <td><?php echo $element->{$side . '_injection_given_by'}->ReversedFullName ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_injection_time') ?>:
      </div>
    </td>
    <td><?php echo date('H:i', strtotime($element->{$side . '_injection_time'})); ?></td>
  </tr>
  <tr>
    <td>
      <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_post_ioplowering_required') ?>:
      </div>
    </td>
    <td><?php echo $element->{$side . '_post_ioplowering_required'} ? 'Yes' : 'No' ?></td>
  </tr>
    <?php if ($element->{$side . '_post_ioploweringdrugs'}) { ?>
    <tr>
      <td>
        <div class="data-label">
            <?php echo $element->getAttributeLabel($side . '_post_ioploweringdrugs') ?>:
        </div>
      </td>
      <td>
          <?php
            foreach ($element->{$side . '_post_ioploweringdrugs'} as $item) {
                echo $item->name . '<br />';
            }
            ?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
