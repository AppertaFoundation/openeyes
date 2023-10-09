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
<?php $fieldset_class = isset($htmlOptions['fieldset-class']) ? $htmlOptions['fieldset-class'] : '';
$label_class = isset($htmlOptions['label-class']) ? $htmlOptions['label-class'] : '';
?>

<?php if (@$htmlOptions['nowrapper']) { ?>
    <?php if (!$no_element) { ?>
    <input type="hidden" value="" name="<?=\CHtml::modelName($element) ?>[<?php echo $field ?>]">
    <?php } ?>

    <?php foreach ($data as $id => $data_value) { ?>
        <?php
        $options = array('value' => $id, 'id' => CHtml::modelName($element) . '_' . $id);

        if (@$htmlOptions['options'] && array_key_exists($id, @$htmlOptions['options'])) {
            foreach ($htmlOptions['options'][$id] as $k => $v) {
                $options[$k] = $v;
            }
        }
        if (array_key_exists('test', $htmlOptions) && $htmlOptions['test'] !== null) {
            $options['data-test'] = $htmlOptions['test'];
        }
        ?>
    <label class="inline highlight <?= $label_class ?>">
        <?php
        if (is_array($selected_items)) {
            $is_checked = in_array($id, $selected_items);
        } else {
            $is_checked = (!is_null($value) && $value == $id) && (!is_string($value) || $value != '');
        }

        if (array_key_exists('prefillable', $htmlOptions) && $htmlOptions['prefillable'] && $is_checked) {
            if (@$options['class']) {
                $options['class'] .= ' prefilled ';
            } else {
                $options['class'] = 'prefilled ';
            }
        }
        ?>
        <?=\CHtml::checkBox(
            "{$name}[]",
            $is_checked,
            $options
        ); ?>
        <?=\CHtml::encode($data_value) ?>
    </label>
    <?php } ?>

<?php } else { ?>
  <fieldset id="<?=\CHtml::modelName($element) . '_' . $name ?>"
            class="cols-full flex-layout flex-left <?= $fieldset_class ?>"
            <?= $hidden ? 'style="display: none;"' : ''?>
            <?php
            if (array_key_exists('extra_fieldset_attributes', $htmlOptions)) {
                foreach ($htmlOptions['extra_fieldset_attributes'] as $attr => $attr_val) {
                    echo $attr . "=" . '"' . $attr_val . '"';
                }
            }
            ?>
    >
      <?php
      // Added hidden input below to enforce posting of current form element name.
      // When using radio or checkboxes if no value is selected then nothing is posted
      // not triggereing server side validation.
        ?>
      <?php if ($label) : ?>
        <label class="cols-<?php echo $layoutColumns['label']; ?> column">
            <?php if ($field_value) {
                ?><?=\CHtml::encode($label); ?>
            <?php } elseif (!$label_above) {
                ?><?=\CHtml::encode($label); ?>:<?php
            } ?>
        </label>
      <?php endif; ?>
      <?php if (!$no_element && $field) { ?>
        <input type="hidden" value="" name="<?=\CHtml::modelName($element) ?>[<?php echo $field ?>]">
      <?php } ?>
    <div class="cols-<?php echo $layoutColumns['field']; ?>">
        <?php $i = 0; ?>
        <?php if ($label_above) { ?>
          <label for="">
              <?=\CHtml::encode($label) ?>
          </label>
        <?php } ?>
        <?php foreach ($data as $id => $data_value) { ?>
          <label class="inline highlight <?= $label_class ?>" id="">
              <?php
                $options = array('value' => $id, 'id' => CHtml::modelName($element) . '_' . $id);

                if (@$htmlOptions['options'] && array_key_exists($id, @$htmlOptions['options'])) {
                    foreach ($htmlOptions['options'][$id] as $k => $v) {
                        $options[$k] = $v;
                    }
                }
                if (array_key_exists('test', $htmlOptions) && $htmlOptions['test'] !== null) {
                    $options['data-test'] = $htmlOptions['test'];
                }
                $class = isset($options['class']) ? ($options['class'] . " ") : '';

                $options['id'] = CHtml::modelName($element) . '_' . @$htmlOptions['field'] . '_' . $data_value;
                $is_checked = in_array($id, $selected_items);
                if (array_key_exists('prefillable', $htmlOptions) && $htmlOptions['prefillable'] && $is_checked) {
                    $class .= ' prefilled ';
                }
                $options['class'] = $class . str_replace(' ', '', $data_value);
                echo CHtml::checkBox("{$name}[]", $is_checked, $options);
                ?>
              <?=\CHtml::encode($data_value) ?>
          </label>
        <?php } ?>
    </div>
  </fieldset>
<?php } ?>
