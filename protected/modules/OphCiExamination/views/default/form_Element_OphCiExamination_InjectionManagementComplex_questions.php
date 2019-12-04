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
<?php $bottom_pad = isset($bottom_pad)?$bottom_pad:false;?>
<div id="<?=\CHtml::modelName($element) ?>_<?php echo $side ?>_Questions">
    <?php
    $name_stub = CHtml::modelName($element) . '[' . $side . '_Answer]';
    foreach ($questions as $question) {?>
      <fieldset class="flex-layout"
                style="<?= $bottom_pad && $question !== end($questions) ?"padding-bottom: 1px":""?>">
        <label class="cols-9 column">
            <?php echo $question->question ?>
        </label>
          <?php
            $name = $name_stub . '[' . $question->id . ']';
            $value = $element->getQuestionAnswer($side, $question->id);
          // update with POST values if available
            if (isset($_POST[CHtml::modelName($element)][$side . '_Answer'][$question->id])) {
                $value = $_POST[CHtml::modelName($element)][$side . '_Answer'][$question->id];
            }
            ?>
        <div class="cols-3 column">
          <label class="inline highlight">
              <?=\CHtml::radioButton($name, $value, array('id' => CHtml::modelName($element) . '_' . $side . '_Answer_' . $question->id . '_1', 'value' => 1)) ?>
            Yes
          </label>
          <label class="inline highlight">
              <?=\CHtml::radioButton($name, (!is_null($value) && !$value), array('id' => CHtml::modelName($element) . '_' . $side . '_Answer_' . $question->id . '_0', 'value' => 0)) ?>
            No
          </label>
        </div>
      </fieldset>
        <?php
    } ?>
</div>
