<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<tr>
    <td><?=$model->getAttributeLabel('institution_id')?></td>
    <td>
        <?= CHtml::activeDropDownList(
            $model,
            'institution_id',
            Institution::model()->getTenantedList(true),
            ['class' => 'cols-full']
        ) ?>
    </td>
</tr>
<tr>
    <td><?=$model->getAttributeLabel('xpath');?></td>
    <td>
        <?= $form->hiddenField($model, 'id')?>
        <?=\CHtml::activeTextField(
            $model,
            'xpath',
            ['autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'class' => 'cols-full']
        ); ?>
    </td>

</tr>
<tr>
    <td><?=$model->getAttributeLabel('name');?></td>
    <td>
        <?=\CHtml::activeTextField(
            $model,
            'name',
            ['autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'class' => 'cols-full']
        ); ?>
    </td>
</tr>
