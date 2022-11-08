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
if (isset($htmlOptions['options'])) {
    $opts = $htmlOptions['options'];
} else {
    $opts = array();
    if ($auto_data_order) {
        $data_order = 0;
        foreach ($options as $id => $option) {
            ++$data_order;
            $opts[(string)$id] = array('data-order' => $data_order);
        }
    }
}

if (isset($htmlOptions['div_id'])) {
    $div_id = $htmlOptions['div_id'];
} else {
    // for legacy, this is the original definition of the div id that was created for the multiselect
    // not recommended as it doesn't allow for sided uniqueness
    $div_id = 'div_' . CHtml::modelName($element) . '_' . @$htmlOptions['label'];
}

if (isset($htmlOptions['div_class'])) {
    $div_class = $htmlOptions['div_class'];
} else {
    $div_class = 'eventDetail';
}

$found = false;
foreach ($selected_ids as $id) {
    if (isset($options[$id])) {
        $found = true;
        break;
    }
}

$widgetOptionsJson = json_encode(array(
    'sorted' => $sorted,
));
?>

<?php if (!@$htmlOptions['nowrapper']) { ?>
<div id="<?php echo $div_id ?>"
     class="<?php echo $div_class ?> row widget flex-layout" <?php if ($hidden) {
            ?>hidden<?php
            } ?>>
  <div class="cols-<?php echo $layoutColumns['label']; ?> column">
    <label for="<?php echo $field ?>">
        <?php echo @$htmlOptions['label'] ?>:
    </label>
  </div>
  <div class="cols-<?= $layoutColumns['field']; ?> column end">
<?php } ?>
    <div class="multi-select<?php if (!$inline) {
        echo ' multi-select-list';
                            } ?>"
         data-options='<?php echo $widgetOptionsJson; ?>'
            <?php if ($through) :
                ?>data-statuses='<?= json_encode($through['options']) ?>' <?php
            endif; ?>
    >
      <input type="hidden" name="<?=\CHtml::modelName($element) ?>[MultiSelectList_<?php echo $field ?>]"
             class="multi-select-list-name"/>
      <div class="multi-select-dropdown-container">
        <select id="<?=\CHtml::getIdByName($field) ?>"
                class="MultiSelectList
                    <?=($showRemoveAllLink)?' inline':''?>
                    <?= isset($htmlOptions['class'])?$htmlOptions['class']:''?>
                "
                name=""
                style="<?= isset($htmlOptions['style'])?$htmlOptions['style']:''?>"
            <?php if (isset($htmlOptions['data-linked-fields'])) { ?>
              data-linked-fields="<?php echo $htmlOptions['data-linked-fields'] ?>"
            <?php } ?>
            <?php if (isset($htmlOptions['data-linked-values'])) { ?>
              data-linked-values="<?php echo $htmlOptions['data-linked-values'] ?>"
            <?php } ?>
                data-searchable="<?php echo isset($htmlOptions['searchable']) && $htmlOptions['searchable'] ?>"
                data-placeholder="Add <?php echo (isset($htmlOptions['label']) && $htmlOptions['label']) ? 'a ' . $htmlOptions['label'] : '' ?>"
        >
          <option value=""><?php echo $htmlOptions['empty'] ?></option>
            <?php
            foreach ($filtered_options as $value => $option) {
                $attributes = array('value' => $value);
                if (isset($opts[$value])) {
                    $attributes = array_merge($attributes, $opts[$value]);
                }
                echo '<option';
                foreach ($attributes as $att => $att_val) {
                    echo ' ' . $att . '="' . $att_val . '"';
                }
                echo '>' . strip_tags($option) . ' </option>';
            } ?>
        </select>
            <?php if ($showRemoveAllLink) { ?>
            <a href="#" class="remove-all" style="display:  <?php echo !$found ? ' none' : 'inline'; ?>">Remove all</a>
            <?php } ?>
      </div>
        <?php if ($noSelectionsMessage) { ?>
          <div
              class="no-selections-msg pill" style="display: <?php echo $found ? ' none' : 'inline';  ?>"><?php echo $noSelectionsMessage; ?></div>
        <?php } ?>

        <?php if (Yii::app()->request->isPostRequest && empty($selected_ids)) : ?>
          <input type="hidden" name="<?php echo $field ?>">
        <?php endif; ?>
      <ul class="MultiSelectList multi-select-selections <?= !$found?' hide':''?><?= $sortable?' sortable':''?>">
            <?php foreach ($selected_ids as $id) {
                if (isset($options[$id])) { ?>
                <li>
                      <?php
                        if (isset($link) && $link) : ?>
                  <a href="<?= sprintf($link, $id) ?>">
                        <?php endif; ?>
                    <span class="text">
                        <?php echo strip_tags($options[$id]) ?>
                    </span>
                        <?php if (isset($link) && $link) : ?>
                  </a>
                        <?php endif; ?>

                  <span data-text="<?php echo $options[$id] ?>"
                      class="multi-select-remove remove-one <?php if (isset($htmlOptions['class']) && $htmlOptions['class'] !== "hidden") {
                            ?><?php echo $htmlOptions['class'] ?><?php
                                                            } ?>"
                        <?php if (isset($htmlOptions['data-linked-fields'])) {
                            ?> data-linked-fields="<?php echo $htmlOptions['data-linked-fields'] ?>"<?php
                        } ?>
                        <?php if (isset($htmlOptions['data-linked-values'])) {
                            ?> data-linked-values="<?php echo $htmlOptions['data-linked-values'] ?>"<?php
                        } ?>>
                    <i class="oe-i remove-circle small"></i>
                  </span>
                  <input type="hidden" name="<?php echo $field ?>[]" value="<?php echo $id ?>"
                      <?php if (isset($opts[$id])) {
                            foreach ($opts[$id] as $key => $val) {
                                echo ' ' . $key . '="' . $val . '"';
                            }
                      } ?>
                  />
                      <?php
                        if ($through) {
                            $currentField = isset($through['default_option']) ? $through['default_option'] : 0;
                            foreach ($through['current'] as $current) {
                                if ($current->{$through['related_by']} === $id) {
                                    $currentField = $current->{$through['field']} ? $current->{$through['field']} : $currentField;
                                    break;
                                }
                            }
                            ?>

                      <select name="<?= preg_replace(
                          '#\[(.*)\]#',
                          '[${1}_through]',
                          $field
                                    ) ?>[<?= $id ?>][<?= $through['field'] ?>]">
                              <?php foreach ($through['options'] as $option_id => $option) { ?>
                            <option
                                value="<?= $option_id ?>" <?php if ($currentField && $currentField == $option_id) :
                                    ?> selected <?php
                                       endif; ?>><?= $option ?></option>
                              <?php } ?>
                      </select>
                        <?php } ?>
                </li>
                <?php } ?>
            <?php } ?>
      </ul>
    </div>
        <?php if (!@$htmlOptions['nowrapper']) { ?>
  </div>
</div>
        <?php } ?>
<?php
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js', true);
$assetManager->registerScriptFile('components/chosen/chosen.jquery.min.js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/MultiSelectList.js');
?>
