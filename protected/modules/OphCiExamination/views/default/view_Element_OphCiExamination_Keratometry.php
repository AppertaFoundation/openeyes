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

<div class="element-data">
  <div class="data-row">
      <?php echo $element->getAttributeLabel('tomographer_id') ?>:
      <?php echo OEModule\OphCiExamination\models\OphCiExamination_Tomographer_Device::model()->getName($element->tomographer_id); ?>
  </div>
</div>

<div class="element-data element-eyes row">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $side => $eye):
        $hasEyeFunc = 'has' . ucfirst($eye);
        $anteriorK1 = $eye . '_anterior_k1_value';
        $anteriorK2 = $eye . '_anterior_k2_value';
        $qualityFront = $eye . '_quality_front';
        $axisAnteriorK1 = $eye . '_axis_anterior_k1_value';
        $axisAnteriorK2 = $eye . '_axis_anterior_k2_value';
        $qualityBack = $eye . '_quality_back';
        $kmaxValue = $eye . '_kmax_value';
        $thinnestPointPachymetry = $eye . '_thinnest_point_pachymetry_value';
        $baIndexValue = $eye . '_ba_index_value';
        $flouresceinValue = $eye . '_flourescein_value';
        $clRemoved = $eye . '_cl_removed';
        ?>
      <div class="element-eye <?= $eye ?>-eye column">
          <?php if ($element->$hasEyeFunc()) { ?>
            <table>
              <tbody>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_anterior_k1_value') ?>:
                </td>
                <td>
                    <?php echo $element->$anteriorK1; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_anterior_k2_value') ?>:
                </td>
                <td>
                    <?php echo $element->$anteriorK2; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_quality_front') ?>:
                </td>
                <td>
                    <?php
                    if ($element->$qualityFront) {
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->$qualityFront);
                    }
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_axis_anterior_k1_value') ?>:
                </td>
                <td>
                    <?php echo $element->$axisAnteriorK1; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel('right_axis_anterior_k2_value') ?>:
                </td>
                <td>
                    <?php echo $element->$axisAnteriorK2; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel('right_quality_back') ?>:
                </td>
                <td>
                    <?php
                    if ($element->$qualityBack) {
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->getName($element->$qualityBack);
                    }
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_kmax_value') ?>:
                </td>
                <td>
                    <?php echo $element->$kmaxValue; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_thinnest_point_pachymetry_value') ?>:
                </td>
                <td>
                    <?php
                    echo $element->$thinnestPointPachymetry
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_ba_index_value') ?>:
                </td>
                <td>
                    <?php echo $element->$baIndexValue; ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_flourescein_value') ?>:
                </td>
                <td>
                    <?php echo yesOrNo($element->$flouresceinValue); ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_cl_removed') ?>:
                </td>
                <td>
                    <?php if ($element->right_cl_removed) {
                        echo OEModule\OphCiExamination\models\OphCiExamination_CXL_CL_Removed::model()->getName($element->$clRemoved);
                    } ?>
                </td>
              </tr>
              </tbody>
            </table>
          <?php } else { ?> Not recorded
          <?php } ?>
      </div>
    <?php endforeach; ?>
</div>
