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
<div class="element-fields flex-layout full-width">
  <div class="large-1 column">
    <label><?php echo $element->getAttributeLabel('asthma_id') ?>:</label>
  </div>
  <div class="large-2 column">
      <?php $form->radioButtons(
          $element,
          'asthma_id',
          array(
              0 => 'No',
              1 => 'Yes',
          ),
          ($element->asthma_id !== null) ? $element->asthma_id : 0,
          false,
          false,
          false,
          false,
          array(
              'text-align' => 'right',
              'nowrapper' => true,
          ),
          array(
              'label' => 4,
              'field' => 8,
          ));
      ?>
  </div>
  <div class="large-1 column">
    <label><?php echo $element->getAttributeLabel('eczema_id') ?>:</label>
  </div>
  <div class="large-2 column">
      <?php $form->radioButtons(
          $element,
          'eczema_id',
          array(
              0 => 'No',
              1 => 'Yes',
          ),
          ($element->eczema_id !== null) ? $element->eczema_id : 0,
          false,
          false,
          false,
          false,
          array(
              'text-align' => 'right',
              'nowrapper' => true,
          ),
          array(
              'label' => 4,
              'field' => 8,
          ));
      ?>
  </div>
  <div class="large-1 column">
    <label><?php echo $element->getAttributeLabel('eye_rubber_id') ?>:</label>
  </div>
  <div class="large-2 column">
      <?php $form->radioButtons(
          $element,
          'eye_rubber_id',
          array(
              0 => 'No',
              1 => 'Yes',
          ),
          ($element->eye_rubber_id !== null) ? $element->eye_rubber_id : 0,
          false,
          false,
          false,
          false,
          array(
              'text-align' => 'right',
              'nowrapper' => true,
          ),
          array(
              'label' => 4,
              'field' => 8,
          ));
      ?>
  </div>
  <div class="large-1 column">
    <label><?php echo $element->getAttributeLabel('hayfever_id') ?>:</label>
  </div>
  <div class="large-2 column">
      <?php $form->radioButtons(
          $element,
          'hayfever_id',
          array(
              0 => 'No',
              1 => 'Yes',
          ),
          ($element->hayfever_id !== null) ? $element->hayfever_id : 0,
          false,
          false,
          false,
          false,
          array(
              'text-align' => 'right',
              'nowrapper' => true,
          ),
          array(
              'label' => 4,
              'field' => 8,
          ));
      ?>
  </div>
</div>
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

    <?php foreach (['left' => 'right', 'right' => 'left'] as $side => $eye):
        $hasEyeFunc = 'has' . ucfirst($eye);
        $previousCXLEle = $eye . '_previous_cxl_value';
        $previousRefractive = $eye . '_previous_refractive_value';
        $intacsKeraRing = $eye . '_intacs_kera_ring_value';
        $previousHskKeratitis = $eye . '_previous_hsk_keratitis_value';
        ?>
      <div class="element-eye <?= $eye ?>-eye column side <?= $side ?>" data-side="<?= $eye ?>">
        <div class="active-form" style="<?= !$element->$hasEyeFunc() ? "display: none;" : "" ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <table>
            <tbody>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye . '_previous_cxl_value') ?>:</label>
              </td>
              <td>
                  <?php $form->radioButtons(
                      $element,
                      $eye . '_previous_cxl_value',
                      array(
                          0 => 'No',
                          1 => 'Yes',
                      ),
                      ($element->$previousCXLEle !== null) ? $element->$previousCXLEle : 0,
                      false,
                      false,
                      false,
                      false,
                      array(
                          'text-align' => 'right',
                          'nowrapper' => true,
                      ),
                      array(
                          'label' => 4,
                          'field' => 8,
                      ));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye . '_previous_refractive_value') ?>:</label>
              </td>
              <td>
                  <?php $form->radioButtons(
                      $element,
                      $eye . '_previous_refractive_value',
                      array(
                          0 => 'No',
                          1 => 'Yes',
                      ),
                      ($element->$previousRefractive !== null) ? $element->$previousRefractive : 0,
                      false,
                      false,
                      false,
                      false,
                      array(
                          'text-align' => 'right',
                          'nowrapper' => true,
                      ),
                      array(
                          'label' => 4,
                          'field' => 8,
                      ));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye . '_intacs_kera_ring_value') ?>:</label>
              </td>
              <td>
                  <?php $form->radioButtons(
                      $element,
                      $eye . '_intacs_kera_ring_value',
                      array(
                          0 => 'No',
                          1 => 'Yes',
                      ),
                      ($element->$intacsKeraRing !== null) ? $element->$intacsKeraRing : 0,
                      false,
                      false,
                      false,
                      false,
                      array(
                          'text-align' => 'right',
                          'nowrapper' => true,
                      ),
                      array(
                          'label' => 4,
                          'field' => 8,
                      ));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye . '_previous_hsk_keratitis_value') ?>:</label>
              </td>
              <td>
                  <?php $form->radioButtons(
                      $element,
                      $eye . '_previous_hsk_keratitis_value',
                      array(
                          0 => 'No',
                          1 => 'Yes',
                      ),
                      ($element->$previousHskKeratitis !== null) ? $element->$previousHskKeratitis : 0,
                      false,
                      false,
                      false,
                      false,
                      array(
                          'text-align' => 'right',
                          'nowrapper' => true,
                      ),
                      array(
                          'label' => 4,
                          'field' => 8,
                      ));
                  ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="inactive-form side" style="<?= $element->$hasEyeFunc() ? "display: none;" : "" ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye ?> eye <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>