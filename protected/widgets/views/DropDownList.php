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
<?php if (@$htmlOptions['nowrapper']) {
    echo CHtml::activeDropDownList($element, $field, $data, $htmlOptions);
} else {
?>
<div id="div_<?php echo CHtml::modelName($element) ?>_<?php echo $field ?>"
     class="row field-row cols-12"<?php if (@$hidden) { ?> style="display: none;"<?php } ?>
>
<?php if (@$htmlOptions['layout'] !== 'vertical') { ?>
  <div class="cols-12 column">
    <?php if (!@$htmlOptions['nolabel']) { ?>
      <label for="<?= CHtml::modelName($element)."_".$field ?>">
          <?= $element->getAttributeLabel($field) ?>:
      </label>
    <?php } ?>
<?php } else { ?>
  <div class="cols-<?php echo $layoutColumns['label']; ?> column">
      <?php if (!@$htmlOptions['nolabel']) { ?>
        <label for="<?= CHtml::modelName($element)."_".$field ?>">
            <?= $element->getAttributeLabel($field) ?>:
        </label>
      <?php } ?>
  </div>
  <div class="cols-<?= $layoutColumns['field'] ?> column">
<?php } ?>

<?php if (@$htmlOptions['divided']) { ?>
    <select name="<?= CHtml::modelName($element) . '[' . $field . ']' ?>"
            id="<?= CHtml::modelName($element) . "_" . $field ?>">
        <?php if (isset($htmlOptions['empty'])) { ?>
          <option value=""><?php echo $htmlOptions['empty'] ?></option>
        <?php }
        foreach ($data as $i => $optgroup) { ?>
          <optgroup label="---------------">
              <?php foreach ($optgroup as $id => $option) { ?>
                <option
                    value="<?= $id ?>"
                    <?= $id == $value ? "selected=\"selected\"" : "" ?>
                >
                    <?php echo CHtml::encode($option) ?>
                </option>
              <?php } ?>
          </optgroup>
        <?php } ?>
    </select>
<?php } else {
    if (@$htmlOptions['textAttribute']) {
        $html_options = array();
        foreach ($data as $i => $item) {
            $html_options[(string)$i] = array($htmlOptions['textAttribute'] => $item);
        }
        $htmlOptions['options'] = $html_options;
    }
    echo CHtml::activeDropDownList($element, $field, $data, $htmlOptions) ?>
<?php } ?>
  </div>
</div>
<?php }
