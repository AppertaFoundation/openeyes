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

<div class="cols-5">

    <div class="row divider">
        <h2>Required Systemic Surgical History Sets</h2>
    </div>

    <?php
    $columns = [
        'checkboxes' => [
            'header' => '',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return CHtml::checkBox(
                    "OEModule_OphCiExamination_models_SystemicSurgerySet[]",
                    false,
                    ['value' => $data->id]
                );
            },
            'cssClassExpression' => '"checkbox"',
        ],
        'name',
        [
            'header' => 'Subspecialty',
            'name' => 'subspecialty_id',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return $data->subspecialty ? $data->subspecialty->name : null;
            },
        ],
        [
            'header' => \Firm::contextLabel(),
            'name' => 'firm_id',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return $data->firm ? $data->firm->name : null;
            }
        ],
    ];

    $dataProvider = $model->search();
    $dataProvider->pagination = false;

    ?>

    <form id="generic-admin-form"><?php
        $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => '["data-row"=>$row, "data-id" => $data->id]',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'rowCssClass' => ['clickable'],
        ]);
        ?>
    </form>

    <?=\CHtml::submitButton(
        'Add',
        [
            'class' => 'button large',
            'name' => 'add',
            'data-uri' => '/OphCiExamination/admin/SystemicSurgicalHistoryAssignment/create',
            'id' => 'et_add'
        ]
    ); ?>

    <?=\CHtml::submitButton(
        'Delete',
        [
            'class' => 'button large',
            'name' => 'delete',
            'data-uri' => '/OphCiExamination/admin/SystemicSurgicalHistoryAssignment/delete',
            'id' => 'et_delete'
        ]
    ); ?>

</div>

<script>
    $(document).ready(function () {
        $('table.generic-admin tbody').on('click', 'tr td:not(".checkbox")', function () {
            var id = $(this).closest('tr').data('id');
            window.location.href = '/OphCiExamination/admin/SystemicSurgicalHistoryAssignment/update/' + id;
        });
    });
</script>