<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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

<?php if (!$procedures) : ?>
<div class="row divider">
    <div class="alert-box issue"><b>No results found</b></div>
</div>
<?php endif; ?>

<div class="row divider cols-full">
    <form id="procedures_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
        <table class="cols-full">
            <colgroup>
                <col class="cols-10">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody>
                <tr class="col-gap">
                    <td>
                        <?=\CHtml::textField(
                            'search[query]',
                            $search['query'],
                            [
                            'class' => 'cols-full',
                            'placeholder' => "Term, Snomed Code, OPCS Code, Default Duration, Aliases"
                            ]
                        ); ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'search[active]',
                            $search['active'],
                            [
                            1 => 'Only Active',
                            0 => 'Exclude Active',
                            ],
                            ['empty' => 'All']
                        ); ?>
                    </td>
                    <td>
                        <button class="blue hint" type="submit" id="et_search">Search
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>


<form id="admin_procedures" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />

    <table class="standard">
        <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall" /></th>
                <th>Term</th>
                <th>Snomed Code</th>
                <th>OPCS Code</th>
                <th>Default Duration</th>
                <th>Aliases</th>
                <th>Has Benefits</th>
                <th>Has Complications</th>
                <th>Whiteboard Risks</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            <?php

            foreach ($procedures as $key => $procedure) { ?>
            <tr id="$key" class="clickable" data-id="<?= $procedure->id ?>"
                data-uri="oeadmin/ExtraProcedures/edit/<?= $procedure->id ?>?returnUri=">
                <td>
                        <?php if ($this->isProcedureDeletable($procedure)) : ?>
                    <input type="checkbox" name="select[]" value="<?= $procedure->id ?>"
                        id="select[<?=$procedure->id ?>]" />
                        <?php endif; ?>
                </td>
                <td><?= $procedure->term ?></td>
                <td><?= $procedure->snomed_code ?></td>
                <td><?= implode(", ", array_map(function ($code) {
                        return $code->name;
                    }, $procedure->opcsCodes)); ?>
                </td>
                <td><?= $procedure->default_duration ?></td>
                <td><?= $procedure->aliases ?></td>
                <td><?= implode(", ", array_map(function ($benefit) {
                        return $benefit->name;
                    }, $procedure->benefits)); ?>
                </td>
                <td><?= implode(", ", array_map(function ($complication) {
                        return $complication->name;
                    }, $procedure->complications)); ?>
                </td>
                <td><?= implode(", ", array_map(function ($risk) {
                        return $risk->name;
                    }, $procedure->risks)); ?>
                </td>
                <td>
                        <?= ($procedure->active) ?
                        ('<i class="oe-i tick small"></i>') :
                        ('<i class="oe-i remove small"></i>'); ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>

        <tfoot class="pagination-container">
            <tr>
                <td colspan="4">
                    <?=\CHtml::button(
                        'Add',
                        [
                        'class' => 'button large',
                        'data-uri' => '/oeadmin/ExtraProcedures/edit',
                        'name' => 'add',
                        'id' => 'et_add',
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Delete',
                        [
                        'class' => 'button large disabled',
                        'data-uri' => '/oeadmin/ExtraProcedures/delete',
                        'name' => 'delete',
                        'data-object' => 'procedures',
                        'id' => 'et_delete',
                        'disabled' => true,
                        ]
                    ); ?>
                </td>
                <td colspan="9">
                    <?php $this->widget(
                        'LinkPager',
                        ['pages' => $pagination]
                    ); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

<script>
$(document).ready(function() {

    /**
     * Deactivate button when no checkbox is selected.
     */
    $(this).on('change', $('input[type="checkbox"]'), function(e) {
        var checked_boxes = $('#admin_procedures').find(
            'table.standard tbody input[type="checkbox"]:checked');

        if (checked_boxes.length <= 0) {
            $('#et_delete').attr('disabled', true).addClass('disabled');
        } else {
            $('#et_delete').attr('disabled', false).removeClass('disabled');
        }
    });
});
</script>