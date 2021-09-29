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
if (@$htmlOptions['nowrapper']) :
    echo CHtml::activeDropDownList($element, $field, $data, $htmlOptions);
else :
    $stretch = @$layoutColumns['stretch']; //whether or not to fill the container
    $stretch = $stretch?:false;
    $full_dropdown = @$layoutColumns['full_dropdown'];
    $full_dropdown = $full_dropdown?:false;
    ?>
<div id="div_<?=\CHtml::modelName($element)?>_<?=$field?>"
     class="data-group cols-full <?php
        echo !@$htmlOptions['vertical']?" flex-layout ":"";
        echo !$stretch?' ':''
        ?> "
     style="<?=(@$hidden) ?"display: none;":""?>"
>
    <?php if (!@$htmlOptions['nolabel']) : ?>
  <div class="<?= $stretch?'': 'cols-'.$layoutColumns['label']; ?> column">
    <label for="<?= CHtml::modelName($element)."_".$field ?>">
        <?= (isset($htmlOptions['label']) ? $htmlOptions['label'] : $element->getAttributeLabel($field)) ?>:
    </label>
  </div>
    <?php endif ?>
  <div class="<?= $stretch?'': 'cols-'.$layoutColumns['field']; ?> column">
    <?php if (@$htmlOptions['divided']) : ?>
    <select name="<?= CHtml::modelName($element) . '[' . $field . ']' ?>"
            id="<?= CHtml::modelName($element) . "_" . $field ?>"
    >
        <?php if (isset($htmlOptions['empty'])) : ?>
      <option value=""><?= $htmlOptions['empty'] ?></option>
        <?php endif;
        foreach ($data as $i => $optgroup) : ?>
        <optgroup label="---------------">
              <?php foreach ($optgroup as $id => $option) { ?>
              <option
                  value="<?= $id ?>"
                    <?= $id == $value ? "selected=\"selected\"" : "" ?>
              >
                    <?= CHtml::encode($option) ?>
              </option>
                <?php } ?>
        </optgroup>
        <?php endforeach; ?>
    </select>
    <?php elseif ($full_dropdown) :?>
    <select name="<?= CHtml::modelName($element) . '[' . $field . ']' ?>"
            id="<?= CHtml::modelName($element) . "_" . $field ?>"
            style="width: 100%"
    >
        <?php if (isset($htmlOptions['empty'])) : ?>
          <option value=""><?= $htmlOptions['empty'] ?></option>
        <?php endif;
        foreach ($data as $id => $option) : ?>
          <option
              value="<?= $id ?>"
              <?= $id == $value ? "selected=\"selected\"" : "" ?>
          >
              <?= CHtml::encode($option) ?>
          </option>
        <?php endforeach; ?>
    </select>
    <?php else :
        if (@$htmlOptions['textAttribute']) {
            $html_options = array();
            foreach ($data as $i => $item) {
                $html_options[(string)$i] = array($htmlOptions['textAttribute'] => $item);
            }
            $htmlOptions['options'] = $html_options;
        }
        echo CHtml::activeDropDownList($element, $field, $data, $htmlOptions) ?>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
