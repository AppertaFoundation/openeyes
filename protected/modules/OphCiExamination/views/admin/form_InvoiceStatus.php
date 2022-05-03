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


<?php echo $form->hiddenField($model, 'id') ?>

<table class="standard">
    <colgroup>
        <col class="cols-1">
        <col class="cols-3">
    </colgroup>
    <tbody>
    <tr>
        <td>Name</td>
        <td>
            <?= CHtml::activeTextField(
                $model,
                'name',
                [
                    'class' => 'cols-full',
                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
                ]
            ) ?>
        </td>
    </tr>
    <tr>
        <td>Active</td>
        <td>
            <?php
            $isActive = ($model->active == 1 ? 'checked' : '');
            echo CHtml::activeCheckBox(
                $model,
                'active',
                ['checked' => $isActive]
            );
            ?>
        </td>
    </tr>
    </tbody>
</table>
