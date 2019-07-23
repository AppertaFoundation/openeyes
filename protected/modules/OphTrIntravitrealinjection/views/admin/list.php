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

<div class="box admin">

    <header class="box-header">
        <h2 class="box-title"><?php echo $title ? $title : 'Intravitreal Injection Admin' ?></h2>
        <div class="box-actions">
            <a class="button small" href="<?php echo Yii::app()->createUrl('OphTrIntravitrealinjection/admin/create'.$model_class); ?>">Add New</a>
        </div>
    </header>

    <table class="standard">
        <thead>
            <tr>
                <th>Name</th>
                <th>Available?</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($model_list as $i => $model) {?>
                <tr data-attr-id="<?php echo $model->id?>">
                    <?php
                    $this->renderPartial('list_'.get_class($model), array(
                        'model' => $model,
                        ));
                    ?>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>