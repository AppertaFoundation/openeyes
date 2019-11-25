<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php $is_selected = function ($usage_code_id) use ($search) {
    if (isset($search['usage_code_ids']) && in_array($usage_code_id, $search['usage_code_ids'])) {
        return 'green hint';
    }

    return '';
} ?>
<form id="drug_set_search" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

    <div id="set-filters" class="flex-layout row">

        <table class="standard">
            <colgroup>
                <col class="cols-12">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        <button
                                data-usage_code_id='<?= AutoSetRuleController::FILTER_USAGE_CODE_ID_FOR_ALL ?>'
                                id="usage_code_button_all"
                                type="button"
                                class="large js-set-select <?=$is_selected(AutoSetRuleController::FILTER_USAGE_CODE_ID_FOR_ALL);?>"
                                style="margin-right:15px"
                        >All
                        </button>
                    <?php foreach (MedicationUsageCode::model()->findAll(['condition' => 'active = 1']) as $usage_code) :?>
                        <button
                                data-usage_code_id="<?=$usage_code->id;?>"
                                id="usage_code_button_<?= strtolower($usage_code->usage_code); ?>"
                                type="button"
                                class="large js-set-select <?=$is_selected($usage_code->id);?>"
                                style="margin-right:15px"
                        ><?=$usage_code->name;?>
                        </button>
                    <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="">

    <table class="cols-8">
        <colgroup>
            <col class="cols-6">
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>

        <tbody>
        <tr class="col-gap">
            <td>
                <?= \CHtml::textField(
                    'search[query]',
                    $search['query'],
                    ['class' => 'cols-full', 'placeholder' => "Name"]
                ); ?>
            </td>

            <td><?= \CHtml::dropDownList('search[subspecialty_id]', $search['subspecialty_id'],
                    \CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
                    ['empty' => '- Subspecialty -']
                ) ?>
            </td>
            <td><?= \CHtml::dropDownList('search[site_id]', $search['site_id'],
                    \CHtml::listData(Site::model()->findAll(), 'id', 'name'),
                    ['empty' => '- Site -']
                ) ?>
            </td>
            <td>
                <button class="blue hint" type="button" id="et_search">Search</button>
            </td>
        </tr>
        </tbody>
    </table>
</form>
