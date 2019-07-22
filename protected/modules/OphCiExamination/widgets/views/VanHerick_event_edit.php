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

<?php
    $model_name = CHtml::modelName($element);
?>
<script type="text/javascript" src="<?=$this->getJsPublishedPath('VanHerick.js')?>"></script>

<div class="element-fields element-eyes" id="<?= $model_name ?>_element">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

<?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
    <div class="js-element-eye <?=$eye_side?>-eye <?=$page_side?> <?=(!$element->hasEye($eye_side))? "inactive":""?>" data-side="<?=$eye_side?>">

        <div class="active-form flex-layout" style="<?= $element->hasEye($eye_side)? '': 'display: none;'?>">
            <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
            <table class="cols-10">
                <colgroup>
                    <col class="cols-3">
                </colgroup>

                <thead>
                <tr>
                    <th><?=$element->getAttributeLabel("{$eye_side}_van_herick_id"); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=\CHtml::activeDropDownList($element, "{$eye_side}_van_herick_id", $this->getVanHerickValues(), array('class' => 'inline')); ?></td>
                </tr>
                </tbody>
            </table>
          <div class="add-data-actions flex-item-bottom ">
            <button class="button hint green js-foster-images-link" type="button"><i class="oe-i plus pro-theme"></i></button>
          </div>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side)? 'display: none;': ''?> ">
            <div class="add-side">
                <a href="#">
                    Add <?=$eye_side?> side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
  <div style="display: none;">
    <div class="js-foster-images-dialog">
        <img usemap="#foster_images_map" src="<?php echo $this->getImgPublishedPath("gonioscopy.png");?>">
        <map name="foster_images_map">
            <area data-vh="Grade 0 (0-5%)" shape="rect" coords="0,0,225,225" />
            <area data-vh="Grade 1 (6-15%)" shape="rect" coords="0,225,225,450" />
            <area data-vh="Grade 1 (16-25%)" shape="rect" coords="0,450,225,675" />
            <area data-vh="Grade 2 (26-40%)" shape="rect" coords="225,0,450,225" />
            <area data-vh="Grade 3 (41-75%)" shape="rect" coords="225,225,450,450" />
            <area data-vh="Grade 4 (76-100%)" shape="rect" coords="225,450,450,675" />
        </map>
    </div>
  </div>
</div>



<?php if (false) : ?>
<div class="element-fields element-eyes row" id="<?= $model_name ?>_element">

        <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

        <div class="js-element-eye right-eye column left<?=!$element->hasRight() ? ' inactive': '';?>" data-side="right">

            <div class="active-form">
                <a href="#" class="icon-remove-side remove-side">Remove side</a>

                <div class="van_herick field-row">
                    <label for="<?=\CHtml::modelName($element).'_right_van_herick_id';?>">
                        <?php echo $element->getAttributeLabel('right_van_herick_id'); ?>
                        (<?=\CHtml::link('images', 'javascript:void(0)', array('class' => 'js-foster_images_link')); ?>):
                         <?=\CHtml::activeDropDownList($element, 'right_van_herick_id', $this->getVanHerickValues(), array('class' => 'inline')); ?>
                    </label>
                </div>
            </div>
            <div class="inactive-form">
                <div class="add-side">
                    <a href="#">
                        Add right side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="js-element-eye left-eye column right<?=!$element->hasLeft() ? ' inactive': '';?>" data-side="left">
            <div class="active-form">
                <a href="#" class="icon-remove-side remove-side">Remove side</a>
                <div class="van_herick field-row">
                    <label for="<?=\CHtml::modelName($element).'_left_van_herick_id';?>">
                        <?php echo $element->getAttributeLabel('left_van_herick_id'); ?>
                        (<?=\CHtml::link('images', 'javascript:void(0)', array('class' => 'js-foster_images_link')); ?>):
                        <?=\CHtml::activeDropDownList($element, 'left_van_herick_id', $this->getVanHerickValues(), array('class' => 'inline')); ?>
                    </label>
                </div>
            </div>
            <div class="inactive-form">
                <div class="add-side">
                    <a href="#">
                        Add left side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>
        </div>



</div>

<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.VanHerickController({
            element: $('#<?=$model_name?>_element')
        });
    });
</script>

