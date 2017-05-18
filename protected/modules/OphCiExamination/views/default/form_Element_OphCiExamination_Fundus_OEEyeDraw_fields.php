<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="eyedraw-fields">

    <?php echo CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
    <div class="row">
        <div class="large-6 column end">
            <label>
                <?php echo $element->getAttributeLabel($side . '_ed_report') ?>:
            </label>
        </div>
        <div class="large-10 column end" style="line-height: 1; margin-bottom:10px;">
            <span class="data-value" id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display'?>"></span>
        </div>
    </div>

    <div class="field-row">
        <?php echo $form->multiSelectList($element, CHtml::modelName($element) . '[' . $side . '_vitreous]', $side . '_vitreous', 'id',
            CHtml::listData(OEModule\OphCiExamination\models\Vitreous::model()->findAll(array('order' => 'display_order asc')), 'id', 'name')
            , array(), array('empty' => '- Select -', 'label' => $element->getAttributeLabel('vitreous')), false,
    		false, null, false, false, array('label' => 10, 'field' => 12)) ?>
    </div>

    <div class="field-row">
        <label for="<?php echo get_class($element).'_'.$side.'_description';?>">
            <?php echo $element->getAttributeLabel($side.'_description'); ?>:
        </label>
        <?php echo CHtml::activeTextArea($element, $side.'_description', array('rows' => '2', 'cols' => '20', 'class' => 'autosize')) ?>
    </div>
</div>
