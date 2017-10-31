<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div id="postop-complications" class="box admin">
    <div id="draggablelist">
        <h2>Common Post-Operative Complications</h2>
        <?= CHtml::beginForm(array('/OphCiExamination/admin/updatePostOpComplications'), 'post'); ?>
            <?= CHtml::label('Subspecialty', 'subspecialty_id'); ?>
            <?= CHtml::dropDownList('subspecialty_id', $subspecialty_id, CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name', 'specialty.name')); ?>
            <?= CHtml::hiddenField('item_ids'); ?>
                <div id="draggablelist-items" class="row">
                    <div class="large-6 column">
                        <h2>Currently assigned to</h2>
                        <?php $this->renderPartial('_postOpComplications_table', array('id' => 'draggablelist-items-enabled', 'items' => $enabled_items)); ?>
                        <div class="right">
                            <button class="small" type="submit">Save</button>
                            <button id="draggablelist-cancel" class="small warning" type="button">Cancel</button>
                        </div>
                    </div>
                    <div class="large-6 column available-items">
                        <h2>Available items</h2>
                        <?php $this->renderPartial('_postOpComplications_table', array('id' => 'draggablelist-items-available', 'items' => $available_items)); ?>
                    </div>
                </div>
        <?= CHtml::endForm(); ?>
    </div>
</div>
