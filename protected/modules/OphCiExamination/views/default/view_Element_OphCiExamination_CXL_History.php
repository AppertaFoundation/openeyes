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
function yesOrNo($item)
{
    if ($item == 1) {
        echo "Yes";
    } else {
        echo "No";
    }
}

?>
<table class="cols-6">
  <tbody>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel('asthma_id') ?>:
        <?php echo yesOrNo($element->asthma_id); ?>
    </td>
    <td>
        <?php echo $element->getAttributeLabel('eczema_id') ?>:
        <?php echo yesOrNo($element->eczema_id); ?>
    </td>
    <td>
        <?php echo $element->getAttributeLabel('hayfever_id') ?>:
        <?php echo yesOrNo($element->hayfever_id); ?>
    </td>
    <td>
        <?php echo $element->getAttributeLabel('eye_rubber_id') ?>:
        <?php echo yesOrNo($element->eye_rubber_id); ?>
    </td>
    <td></td>
  </tr>
  </tbody>
</table>
<div class="element-data element-eyes row">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $side => $eye):
        $hasEyeFunc = 'has' . ucfirst($eye);
        $previousCXLEle = $eye . '_previous_cxl_value';
        $previousRefractive = $eye . '_previous_refractive_value';
        $intacsKeraRing = $eye . '_intacs_kera_ring_value';
        $previousHskKeratitis = $eye . '_previous_hsk_keratitis_value';
        ?>
      <div class="element-eye <?= $eye ?>-eye column">
          <?php if ($element->$hasEyeFunc()) { ?>
            <table>
              <tbody>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_previous_cxl_value') ?>:
                </td>
                <td>
                    <?php echo yesOrNo($element->$previousCXLEle); ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel($eye . '_previous_refractive_value') ?>:
                </td>
                <td>
                    <?php
                    echo yesOrNo($element->$previousRefractive);
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel('right_intacs_kera_ring_value') ?>:
                </td>
                <td>
                    <?php
                    echo yesOrNo($element->$intacsKeraRing);
                    ?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php echo $element->getAttributeLabel('right_previous_hsk_keratitis_value') ?>:
                </td>
                <td>
                    <?php
                    echo yesOrNo($element->$previousHskKeratitis);
                    ?>
                </td>
              </tr>
              </tbody>
            </table>
          <?php } else { ?> Not recorded
          <?php } ?>
      </div>
    <?php endforeach; ?>
</div>