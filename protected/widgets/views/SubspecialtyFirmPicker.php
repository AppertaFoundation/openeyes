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
    $this->widget('application.widgets.DropDownList', array(
        'element' => $model,
        'field' => 'subspecialty_id',
        'data' => \CHtml::listData($subspecialties, 'id', 'name'),
        'htmlOptions' => [
            'id' => 'subspecialty_firm_picker_subspecialty_id',
            'empty' => '-- select --',
            'class' => 'subspecialty'
        ],
        'hidden' => $hidden,
        'layoutColumns' => $layoutColumns,
    ));
?>
<div class="row field-row">
    <div class="large-<?=$layoutColumns['label'];?> column">
        <label for="<?=CHtml::modelName($model);?>_firm_id">Context:</label>
    </div>

    <div class="large-<?=$layoutColumns['field'];?> column">
        <?php
            $is_disabled = !(bool)$model->subspecialty_id;
            echo CHtml::activeDropDownList($model, "firm_id", $firms, [
                'empty' => '-- select --',
                'disabled' => $is_disabled,
                'style' => ($is_disabled ? 'background-color:lightgray;':''), // oh where is the visual effect chrome, please ? @TODO:move to css input[diabled] {}
                'id' => 'subspecialty_firm_picker_firm_id'

            ]);
        ?>
    </div>
    <div class="large-1 column end" style="padding-left:0"><img class="loader" style="margin-top:0px;width:20%;display:none" src="<?php echo \Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." /></div>
</div>