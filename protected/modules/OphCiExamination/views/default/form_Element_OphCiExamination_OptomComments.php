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
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width ">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <div class="data-group">
        <div class="cols-2 column">
            <label>Ready for second eye?</label>
        </div>
        <div class="cols-10 column end">
            <?php if ($element->ready_for_second_eye === '1'):?>
                Yes
            <?php elseif ($element->ready_for_second_eye === '0'):?>
                No
            <?php else: ?>
                Not Applicable
            <?php endif;?>
        </div>
    </div>
    <div class="data-group">
        <div class="cols-2 column">
            <label>Comment</label>
        </div>
        <div class="cols-10 column end">
            <?= ($element->comment ? Yii::app()->format->Ntext($element->comment) : 'No Comment') ?>
        </div>
    </div>
</div>