<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<script type="text/javascript" src="<?=$this->getJsPublishedPath('VanHerick.js')?>"></script>
<?php
    $model_name = CHtml::modelName($element);
?>

<div class="element-fields element-eyes row" id="<?= $model_name ?>_element">

        <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
        <div class="element-eye right-eye column side right<?php if (!$element->hasRight()) {
            ?> inactive<?php
        }?>" data-side="right">
            <div class="active-form">
                <a href="#" class="icon-remove-side remove-side">Remove side</a>


                <div class="van_herick field-row">
                    <label for="<?php echo CHtml::modelName($element).'_right_van_herick_id';?>">
                        <?php echo $element->getAttributeLabel('right_van_herick_id'); ?>
                        (<?php echo CHtml::link('images', '#', array('class' => 'foster_images_link')); ?>):
                    <?php echo CHtml::activeDropDownList($element, 'right_van_herick_id', array(0 => 'NR') + CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_Van_Herick::model()->findAll(), 'id', 'name'), array('class' => 'inline clearWithEyedraw')); ?>
                    </label>
                    <div data-side="right" class="foster_images_dialog right"
                         title="Foster Images">
                        <img usemap="#right_foster_images_map"
                             src="<?php echo $this->getImgPublishedPath("gonioscopy.png");?>">
                        <map name="right_foster_images_map">
                            <area data-vh="5" shape="rect" coords="0,0,225,225" />
                            <area data-vh="15" shape="rect" coords="0,225,225,450" />
                            <area data-vh="25" shape="rect" coords="0,450,225,675" />
                            <area data-vh="30" shape="rect" coords="225,0,450,225" />
                            <area data-vh="75" shape="rect" coords="225,225,450,450" />
                            <area data-vh="100" shape="rect" coords="225,450,450,675" />
                        </map>
                    </div>
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
        <div class="element-eye right-eye column side left<?php if (!$element->hasLeft()) {
            ?> inactive<?php
        }?>" data-side="left">
            <div class="active-form">
                <a href="#" class="icon-remove-side remove-side">Remove side</a>
                <div class="van_herick field-row">
                    <label for="<?php echo CHtml::modelName($element).'_left_van_herick_id';?>">
                        <?php echo $element->getAttributeLabel('left_van_herick_id'); ?>
                        (<?php echo CHtml::link('images', '#', array('class' => 'foster_images_link')); ?>):
                        <?php echo CHtml::activeDropDownList($element, 'left_van_herick_id', array(0 => 'NR') + CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_Van_Herick::model()->findAll(), 'id', 'name'), array('class' => 'inline clearWithEyedraw')); ?>
                    </label>
                    <div data-side="left" class="foster_images_dialog left"
                         title="Foster Images">
                        <img usemap="#left_foster_images_map"
                             src="<?php echo $this->getImgPublishedPath("gonioscopy.png");?>">
                        <map name="left_foster_images_map">
                            <area data-vh="5" shape="rect" coords="0,0,225,225" />
                            <area data-vh="15" shape="rect" coords="0,225,225,450" />
                            <area data-vh="25" shape="rect" coords="0,450,225,675" />
                            <area data-vh="30" shape="rect" coords="225,0,450,225" />
                            <area data-vh="75" shape="rect" coords="225,225,450,450" />
                            <area data-vh="100" shape="rect" coords="225,450,450,675" />
                        </map>
                    </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.VanHerickController();
    });
</script>
