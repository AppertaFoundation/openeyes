<?php
/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-4 column end">
    <?=\CHtml::htmlButton('Add macro', [
            'class' => 'button small js-addLetterMacro',
            'data-institution-input-id' => 'institution_id'
        ]) ?>
</div>

<form id="admin_sessions_filters" class="panel">
    <div class="cols-full">
        <table class="standard" style="margin-bottom:0px">
            <colgroup>
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr>
                <td><?=\CHtml::dropDownList(
                    'type',
                    '',
                    ['site' => 'Site', 'subspecialty' => 'Subspecialty', 'firm' => Firm::contextLabel()],
                    ['empty' => '- Type -']
                ) ?></td>
                <td><?=\CHtml::dropDownList(
                    'institution_id',
                    $default_institution_id,
                    Institution::model()->getTenantedList(!Yii::app()->user->checkAccess('admin'))
                ) ?></td>
                <td><?=\CHtml::dropDownList(
                    'site_id',
                    @$_GET['site_id'],
                    Site::model()->getListForCurrentInstitution(),
                    ['empty' => '- Site -']
                ) ?></td>
                <td><?=\CHtml::dropDownList(
                    'subspecialty_id',
                    @$_GET['subspecialty_id'],
                    CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                    ['empty' => '- Subspecialty -']
                ) ?></td>
                <td><?=\CHtml::dropDownList(
                        'firm_id',
                        @$_GET['firm_id'],
                        Firm::model()->getListWithSpecialties(Yii::app()->session['selected_institution_id'], true),
                        ['empty' => '- ' . Firm::contextLabel() . ' -']
                    ) ?>
                </td>
            </tr>
            </tbody>
        </table>
            <table class="standard cols-full" style="margin-top:0px">
                <col class="cols-2">
                <col class="cols-3">
                <tbody>
                <tr>
                    <td><?=\CHtml::dropDownList(
                            'name',
                            @$_GET['name'],
                            $unique_names,
                            ['empty' => '- Name -']
                        ) ?></td>
                    <td><?=\CHtml::dropDownList(
                            'episode_status_id',
                            @$_GET['episode_status_id'],
                            $episode_statuses,
                            ['empty' => '- Episode status -']
                        ) ?></td>
                    <td></td>
                </tr>
                </tbody>
            </table>


        </table>
    </div>
</form>

<form id="admin_letter_macros">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <input type="hidden" name="page" value="1">
    <div class="data-group">
        <table class="standard generic-admin sortable" id="et_sort" data-uri = "/OphCoCorrespondence/admin/sortLetterMacros">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Display order</th>
                <th>ID</th>
                <th>Owner</th>
                <th>Name</th>
                <th>Recipient</th>
                <th>CC patient</th>
                <th>CC doctor</th>
                <th>CC DRSS</th>
                <th>CC Optometrist</th>
                <th>Use nickname</th>
                <th>Episode status</th>
            </tr>
            </thead>
            <tbody>
            <?php $this->renderPartial('_macros', array('macros' => $macros)) ?>
            </tbody>
        </table>
    </div>
</form>

<div class="cols-4 column end">
    <?=\CHtml::htmlButton('Delete macros', array('class' => 'button large deleteMacros')) ?>
</div>

<script>
    $('.generic-admin.sortable tbody').sortable({
        stop: OpenEyes.Admin.saveSorted
    });
</script>
