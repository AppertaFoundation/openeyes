<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$fields = OphInVisualfields_Field_Measurement::model()->getUnattachedForPatient(
    $this->patient, $side == 'left' ? Eye::LEFT : Eye::RIGHT, $this->event
);
if (!$element->{"{$side}_field_id"} && $fields) {
    $element->{"{$side}_field_id"} = end($fields)->id;
}

$field_data = array();
foreach ($fields as $field) {
    $field_data[$field->id] = array(
        'id' => $field->id,
        'url' => Yii::app()->baseUrl . "/file/view/{$field->cropped_image_id}/400/img.gif",
        'date' => date(Helper::NHS_DATE_FORMAT . ' H:i:s', strtotime($field->study_datetime)),
        'strategy' => $field->strategy->name,
        'pattern' => $field->pattern->name,
        'image_id' => $field->image_id,
    );
}
$current_field = $element->{"{$side}_field_id"} ? $field_data[$element->{"{$side}_field_id"}] : null;

Yii::app()->clientScript->registerScript(
    "OphInVisualfields_available_fields_{$side}",
    "var OphInVisualfields_available_fields_{$side} = " . CJSON::encode($field_data),
    CClientScript::POS_END
);

?>
<div class="js-element-eye <?= $side ?>-eye column">
    <?php if ($current_field) : ?>
      <div class="data-group">
        <div class="cols-5 column">
            <?= $form->dropDownList($element, "{$side}_field_id", CHtml::listData($field_data, 'id', 'date'),
                array('nowrapper' => true)) ?>
        </div>
        <div class="cols-7 column OphInVisualfields_field_image_wrapper">
          <a id="Element_OphInVisualfields_Image_image_<?= $side ?>" class="OphInVisualfields_field_image"
             data-image-id="<?= $current_field['image_id'] ?>" href="#">
            <img src="<?= CHtml::encode($current_field['url']) ?>">
          </a>
        </div>
      </div>
      <div class="data-group">
        <table class="label-value cols-10">
          <tbody>
          <tr>
            <td>
              <div class="data-label">Strategy</div>
            </td>
            <td>
              <div class="data-value" id="Element_OphInVisualfields_Image_strategy_<?= $side ?>">
                  <?= CHtml::encode($current_field['strategy']) ?>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label">Test Name</div>
            </td>
            <td>
              <div class="data-value" id="Element_OphInVisualfields_Image_pattern_<?= $side ?>">
                  <?= CHtml::encode($current_field['pattern']) ?>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    <?php else : ?>
      <p>There are no fields to view for the <?= $side ?> eye.</p>
    <?php endif; ?>
</div>
