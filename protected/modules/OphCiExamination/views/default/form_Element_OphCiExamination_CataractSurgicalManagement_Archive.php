<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="sub-element-fields">
  <div class="data-group" style="position: relative; z-index: 3">
        <?php echo $form->radioButtons(
            $element,
            'eye_id',
            CHtml::listData(
                \OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::model()->findAll(),
                'id',
                'name'
            ),
            null,
            false,
            false,
            false,
            false,
            array('nowrapper' => true)
        ) ?>

  </div>
  <div class="cols-12 data-group flex-layout flex-top">
    <table class="cols-6">
      <tbody>
      <tr>
        <td>
          <div>
                <?php
                if ($active_check === 'on') {
                    echo $form->checkbox($element, 'city_road', array('nowrapper' => true));
                    echo $form->checkbox($element, 'satellite', array('nowrapper' => true));
                }
                ?>
                <?php echo $form->checkbox($element, 'fast_track', array('nowrapper' => true)) ?>
          </div>
        </td>
        <td></td>
      </tr>
      <tr>
        <td class="">
            <?php echo $form->textfield($element, 'target_postop_refraction', array(), array(), array('label' => 8, 'field' => 4));?>

        </td>
        <td></td>
      </tr>
      <tr>
        <td>
            <?php echo $form->radioBoolean($element, 'correction_discussed', array(), array('label' => 9, 'field' => 3)) ?>
        </td><td></td>
      </tr><tr></tr>
      </tbody>
    </table>
    <table class="cols-6">
      <tbody>
      <tr class="flex-layout ">
        <td>
          <label for="<?php echo get_class($element) . '_suitable_for_surgeon_id'; ?>">
                <?php echo $element->getAttributeLabel('suitable_for_surgeon_id') ?>:
          </label>
        </td>
        <td class="flex-layout">
          <div class="cols-6">
                <?php echo $form->dropDownList(
                    $element,
                    'suitable_for_surgeon_id',
                    '\OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_SuitableForSurgeon',
                    array('class' => 'inline', 'empty' => 'Select', 'nowrapper' => true)
                ) ?>
          </div>
          <label class="inline cols-6" style="padding-left: 4px">
                <?php echo $form->checkbox($element, 'supervised', array('nowrapper' => true, 'no-label' => true)) ?>
                <?php echo $element->getAttributeLabel('supervised') ?>
          </label>
        </td>
      </tr>
      <tr>
        <td>
            <?php echo $form->radioBoolean($element, 'previous_refractive_surgery', array(), array('label' => 9, 'field' => 3)) ?>
        </td><td></td>
      </tr>
      <tr>
        <td>
            <?php echo $form->radioBoolean($element, 'vitrectomised_eye', array(), array('label' => 9, 'field' => 3)) ?>
        </td><td></td>
      </tr>
      <tr></tr>
      </tbody>
    </table>
  </div>

  <div class="data-group flex-layout cols-6 flex-left flex-top">
    <div class="cols-6 column">
        <label for="<?php echo get_class($element) . 'reasonForSurgery'; ?>">
            <?php echo $element->getAttributeLabel('reasonForSurgery') ?>:
        </label>
    </div>
    <div class="cols-6 column">
            <?php
            echo $form->multiSelectList(
                $element,
                'OEModule_OphCiExamination_models_Element_OphCiExamination_CataractSurgicalManagement_Archive[reasonForSurgery]',
                'reasonForSurgery',
                'id',
                \CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery::model()->findAllByAttributes(array(), 'active=1'), 'id', 'name'),
                array(),
                array(
                  'empty' => '',
                  'label' => 'Primary Reason For Cataract Surgery',
                  'nowrapper' => true,
                ),
                false,
                true,
                null,
                false,
                false,
                array('label' => 3, 'field' => 9)
            );
            ?>
    </div>
  </div>
</div>
