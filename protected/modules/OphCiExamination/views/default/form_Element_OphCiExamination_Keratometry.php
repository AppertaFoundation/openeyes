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
//var_dump($element);
?>
<div class="element-fields flex-layout full-width">
  <div>
    <label><?php echo $element->getAttributeLabel('tomographer_id') ?>:</label>
        <?php
        $allTomographerDevice = \OEModule\OphCiExamination\models\OphCiExamination_Tomographer_Device::model()->findAll(array('order' => 'display_order'));
        echo CHtml::dropDownList(
            'OEModule_OphCiExamination_models_Element_OphCiExamination_Keratometry[tomographer_id]',
            $element->tomographer_id,
            CHtml::listData($allTomographerDevice, 'id', 'name'),
            array('class' => 'MultiSelectList')
        ); ?>
  </div>
  <div class="large-2 column"></div>
</div>

<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?>" data-side="<?= $eye_side ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <div class="flex-layout flex-top">
          <table class="cols-6">
              <colgroup>
                  <col class="cols-4">
                  <col class="cols-2">
              </colgroup>
            <tbody>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye_side . '_anterior_k1_value') ?>:</label>
              </td>
              <td>
                  <?= $form->textField(
                      $element,
                      $eye_side . "_anterior_k1_value",
                      array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                  ) ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye_side . '_anterior_k2_value') ?>:</label>
              </td>
              <td>
                  <?= $form->textField(
                      $element,
                      $eye_side . "_anterior_k2_value",
                      array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                  ) ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye_side . '_quality_front') ?>:</label>
              </td>
              <td>
                  <div class="flex-layout">
                  <?php
                    $allQualScore = \OEModule\OphCiExamination\models\OphCiExamination_CXL_Quality_Score::model()->findAll(array('order' => 'display_order'));
                    echo CHtml::dropDownList(
                        'OEModule_OphCiExamination_models_Element_OphCiExamination_Keratometry[' . $eye_side . '_quality_front]',
                        $element->{$eye_side . '_quality_front'},
                        CHtml::listData($allQualScore, 'id', 'name')
                    ); ?>
                  </div>
              </td>
            </tr>
                  <tr>
                      <td>
                          <label><?php echo $element->getAttributeLabel($eye_side . '_kmax_value') ?>:</label>
                      </td>
                      <td>
                          <?= $form->textField(
                              $element,
                              $eye_side . "_kmax_value",
                              array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                          ) ?>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <label>
                              <?php echo $element->getAttributeLabel($eye_side . '_thinnest_point_pachymetry_value') ?>
                          </label>
                      </td>
                      <td>
                          <?= $form->textField(
                              $element,
                              $eye_side . "_thinnest_point_pachymetry_value",
                              array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                          ) ?>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <label><?php echo $element->getAttributeLabel($eye_side . '_ba_index_value') ?>:</label>
                      </td>
                      <td>
                          <?= $form->textField(
                              $element,
                              $eye_side . "_ba_index_value",
                              array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                          ) ?>
                      </td>
                  </tr>
                  </tbody>
              </table>
              <table class="cols-6">
                  <colgroup>
                      <col class="cols-4">
                      <col class="cols-2">
                  </colgroup>
                  <tbody>
                  <tr>
                      <td>
                          <label><?php echo $element->getAttributeLabel($eye_side . '_axis_anterior_k1_value') ?>:</label>
                      </td>
                      <td>
                          <?= $form->textField(
                              $element,
                              $eye_side . "_axis_anterior_k1_value",
                              array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                          ) ?>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <label><?php echo $element->getAttributeLabel($eye_side . '_axis_anterior_k2_value') ?>:</label>
                      </td>
                      <td>
                          <?= $form->textField(
                              $element,
                              $eye_side . "_axis_anterior_k2_value",
                              array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)
                          ) ?>
                      </td>
                  </tr>
                  <tr>
                      <td>
                          <label><?php echo $element->getAttributeLabel($eye_side . '_quality_back') ?>:</label>
                      </td>
                      <td>
                          <div class="flex-layout">
                              <?php
                                echo CHtml::dropDownList(
                                    'OEModule_OphCiExamination_models_Element_OphCiExamination_Keratometry[' . $eye_side . '_quality_back]',
                                    $element->{$eye_side . '_quality_back'},
                                    CHtml::listData($allQualScore, 'id', 'name')
                                ); ?>
                          </div>
                      </td>
                  </tr>

              <tr>
                <td>
                  <label><?php echo $element->getAttributeLabel($eye_side . '_flourescein_value') ?>:</label>
                </td>
                <td>
                    <div class="flex-layout">
                    <?php $form->radioButtons(
                        $element,
                        $eye_side . '_flourescein_value',
                        array(
                            0 => 'No',
                            1 => 'Yes',
                        ),
                         ($element->{$eye_side . '_flourescein_value'} !== null) ? $element->{$eye_side . '_flourescein_value'} : 0,
                         false,
                         false,
                         false,
                         false,
                         array(
                            'text-align' => $eye_side,
                            'nowrapper' => true,
                        ),
                         array(
                            'label' => 4,
                            'field' => 8,
                        )
                    );
                    ?>
                        <div class="flex-layout">
                </td>
              </tr>
              <tr>
                <td>
                  <label><?php echo $element->getAttributeLabel($eye_side . '_cl_removed') ?>:</label>
                </td>
                <td>
                    <div class="flex-layout">
                    <?php
                    $allCLRemoved = \OEModule\OphCiExamination\models\OphCiExamination_CXL_CL_Removed::model()->findAll(array('order' => 'display_order'));
                    echo CHtml::dropDownList(
                        'OEModule_OphCiExamination_models_Element_OphCiExamination_Keratometry[' . $eye_side . '_cl_removed]',
                        $element->{$eye_side . '_cl_removed'},
                        CHtml::listData($allCLRemoved, 'id', 'name')
                    ); ?>
                    </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> eye <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>