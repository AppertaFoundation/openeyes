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

$institution_list_options = ['class' => 'cols-full'];

if ($model->canChangeInstitution()) {
    if ($this->checkAccess('admin')) {
        $institution_list_data = Institution::model()->getTenantedList(false);
        $institution_list_options['empty'] = '- All institutions -';
    } else {
        $institution_list_data = Institution::model()->getTenantedList(true);
    }
} else {
    // See the documentation for OphCiExamination_Workflow::canChangeInstitution for the reasons for the following.
    if ($model->institution) {
        $institution_list_data = [$model->institution->id => $model->institution->name];
    } else {
        $institution_list_data = [];
        $institution_list_options['empty'] = '- All institutions -';
    }
}
?>

<div class="row divider">
    <h2><?php echo $title ?></h2>
</div>

<?php if ($model->hasErrors()) { ?>
<div class="alert-box error">
    <?= $form->errorSummary($model) ?>
</div>
<?php } ?>

<table class="standard cols-full">
    <tbody>
    <tr>
        <td>
            <?= $form->dropDownList(
                $model,
                'institution_id',
                $institution_list_data,
                $institution_list_options,
                )
?>
        </td>
    </tr>
    <tr>
        <td >
            <?php echo $form->hiddenField($model, 'id')?>
            <?php echo $form->textField(
                $model,
                'name',
                ['class' => 'cols-full', 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')]
            ) ?>
        </td>
    </tr>
    </tbody>
</table>
