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

<div class="element-data full-width">
  <div class="data-group">
        <?php echo $element->getAttributeLabel('tomographer_id') ?>:
        <?php echo OEModule\OphCiExamination\models\OphCiExamination_Tomographer_Device::model()->getName($element->tomographer_id); ?>
  </div>
</div>

<div class="element-data element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column">
          <?php if ($element->hasEye($eye_side)) { ?>
            <table>
              <tbody>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_anterior_k1_value') ?>:
                </td>
                <td>
                    <?php echo $element->{$eye_side . '_anterior_k1_value'}; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_anterior_k2_value') ?>:
                </td>
                <td>
                    <?php echo $element->{$eye_side . '_anterior_k2_value'}; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_quality_front') ?>:
                </td>
                <td>
                    <?php
                    if ($element->{$eye_side . '_quality_front'}) {
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->{$eye_side . '_quality_front'});
                    }
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_axis_anterior_k1_value') ?>:
                </td>
                <td>
                    <?php echo $element->{$eye_side . '_axis_anterior_k1_value'}; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_axis_anterior_k2_value') ?>:
                </td>
                <td>
                    <?php echo $element->{$eye_side . '_axis_anterior_k2_value'}; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_quality_back') ?>:
                </td>
                <td>
                    <?php
                    if ($element->{$eye_side . '_quality_back'}) {
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->{$eye_side . '_quality_back'});
                    }
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_kmax_value') ?>:
                </td>
                <td>
                    <?php echo $element->{$eye_side . '_kmax_value'}; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_thinnest_point_pachymetry_value') ?>:
                </td>
                <td>
                    <?php
                    echo $element->{$eye_side . '_thinnest_point_pachymetry_value'}
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_ba_index_value') ?>:
                </td>
                <td>
                    <?php echo $element->{$eye_side . '_ba_index_value'}; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_flourescein_value') ?>:
                </td>
                <td>
                    <?php echo ($element->{$eye_side . '_flourescein_value'}) === '1' ? 'Yes' : 'No'; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye_side . '_cl_removed') ?>:
                </td>
                <td>
                    <?php if ($element->{$eye_side . '_cl_removed'}) {
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_CL_Removed::model()->getName($element->{$eye_side . '_cl_removed'});
                    } ?>
                </td>
              </tr>
              </tbody>
            </table>
            <?php } else {
                ?> Not recorded
            <?php } ?>
      </div>
    <?php endforeach; ?>
</div>
