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

$centralAreas = CHtml::listData(
    \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_CentralArea::model()->activeOrPk(
        array(
            $element->right_central_area_id,
            $element->left_central_area_id,
        ))->findAll(array('order' => 'display_order')
    ), 'id', 'area'
);
$centralAreaFieldImages = \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_CentralArea::model()->getFieldImages();

$maxAreas = CHtml::listData(
    \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_MaxArea::model()->activeOrPk(
        array($element->right_max_area_id, $element->left_max_area_id))->findAll(array('order' => 'display_order')
    ), 'id', 'area'
);
$maxAreaFieldImages = \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_MaxArea::model()->getFieldImages();

$heights = CHtml::listData(
    \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height::model()->activeOrPk(
        array($element->right_height_id, $element->left_height_id))->findAll(array('order' => 'display_order')
    ), 'id', 'height'
);
$heightFieldImages = \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Height::model()->getFieldImages();

$vascularities = CHtml::listData(
    \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Vascularity::model()->activeOrPk(
        array($element->right_vasc_id, $element->left_vasc_id))->findAll(array('order' => 'display_order')
    ), 'id', 'vascularity'
);
$vascularitiesFieldImages = \OEModule\OphCiExamination\models\OphCiExamination_BlebAssessment_Vascularity::model()->getFieldImages();

?>
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
  <script type="text/javascript">
    var idToImagesArr = {
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_left_central_area_id':
        {id: 'centralArea', selects:<?php echo json_encode($centralAreas);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_right_central_area_id':
        {id: 'centralArea', selects:<?php echo json_encode($centralAreas);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_left_max_area_id':
        {id: 'maxArea', selects:<?php echo json_encode($maxAreas);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_right_max_area_id':
        {id: 'maxArea', selects:<?php echo json_encode($maxAreas);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_left_height_id':
        {id: 'height', selects:<?php echo json_encode($heights);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_right_height_id':
        {id: 'height', selects:<?php echo json_encode($heights);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_left_vasc_id':
        {id: 'vascularity', selects:<?php echo json_encode($vascularities);?>},
      'OEModule_OphCiExamination_models_Element_OphCiExamination_BlebAssessment_right_vasc_id':
        {id: 'vascularity', selects:<?php echo json_encode($vascularities);?>}
    };
    var FieldImages = <?php
        $fieldImages = array(
            'height' => $heightFieldImages,
            'maxArea' => $maxAreaFieldImages,
            'centralArea' => $centralAreaFieldImages,
            'vascularity' => $vascularitiesFieldImages,
        );
        echo json_encode($fieldImages);
        ?>;
    var oeFieldImages = new OpenEyes.UI.FieldImages({idToImages: idToImagesArr, images: FieldImages});
    $(document).ready(function () {
      oeFieldImages.setFieldButtons();
    });
  </script>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side): ?>
      <div class="element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side" data-side="<?= $eye_side ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <table class="bleb-assessment">
            <thead>
            <tr>
              <th width="25%">Area (Central)</th>
              <th width="25%">Area (Maximal)</th>
              <th width="25%">Height</th>
              <th width="25%">Vascularity</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <!-- Area (Central) -->
              <td>
                  <?php
                  echo $form->dropDownList($element, $eye_side . '_central_area_id', $centralAreas,
                      array('empty' => '-', 'nowrapper' => true, 'class' => 'ui-field-images-dropdown'), false,
                      array('label' => 0, 'field' => 6)
                  ); ?>
              </td>
              <!-- Area (Maximal) -->
              <td>
                  <?php
                  echo $form->dropDownList($element, $eye_side . '_max_area_id', $maxAreas,
                      array('empty' => '-', 'nowrapper' => true, 'class' => 'ui-field-images-dropdown'), false,
                      array('label' => 0, 'field' => 6)
                  ); ?>
              </td>
              <!-- Height -->
              <td>
                  <?php
                  echo $form->dropDownList($element, $eye_side . '_height_id', $heights,
                      array('empty' => '-', 'nowrapper' => true, 'class' => 'ui-field-images-dropdown'), false,
                      array('label' => 0, 'field' => 6)
                  ); ?>
              </td>
              <!-- Vascularity -->
              <td>
                  <?php
                  echo $form->dropDownList($element, $eye_side . '_vasc_id', $vascularities,
                      array('empty' => '-', 'nowrapper' => true, 'class' => 'ui-field-images-dropdown'), false,
                      array('label' => 0, 'field' => 6)
                  ); ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>

<style>

  .ui-field-image {
    background: #666;
    width: 200px;
    height: 129px;
    margin: 3px;
    float: left;
    text-align: center;
    font-size: 16px;
  }

  .ui-field-image-val {
    margin: 1px;
    width: 20px;
  }

  .ui-field-image-no-preview {
    position: relative;
    top: 30%;
    background-color: #fff;
    width: 100px;
    margin-left: 50px;
    padding: 5px 0;
  }

  .ui-field-images-icon {
    padding: 3px 0 3px 3px;
    cursor: pointer;
  }

  .ui-field-images-dropdown {
    width: 50px;
  }
</style>