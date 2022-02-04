<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-5">

    <?php
    $columns = [
        'checkboxes' => [
            'header' => '',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return CHtml::checkBox(
                    "OEModule_OphCiExamination_models_OphCiExaminationAllergySet[]",
                    false,
                    ['value' => $data->id]
                );
            },
            'cssClassExpression' => '"checkbox"',
        ],
        'name',
        [
            'header' => 'Institution',
            'name' => 'institution_id',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return $data->institution ? $data->institution->name : null;
            },
        ],
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
                return $data->firm_id ? $data->getFirm()->name : null;
            }
        ],
    ];

    $dataProvider = $model->search(true);
    $dataProvider->pagination = false;
    ?>

    <form id="generic-admin-form"><?php
        $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            //'template' => '{items}',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => '["data-row"=>$row]',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'rowHtmlOptionsExpression' => '["data-id" => $data->id]',
            'rowCssClass' => ['clickable'],
        ]);
        ?>
    </form>

    <?=\CHtml::submitButton(
        'Add',
        [
            'class' => 'button large',
            'name' => 'add',
            'data-uri' => '/OphCiExamination/admin/AllergyAssignment/create/',
            'id' => 'et_add'
        ]
    ); ?>

    <?=\CHtml::submitButton(
        'Delete',
        [
            'class' => 'button large',
            'name' => 'delete',
            'data-uri' => '/OphCiExamination/admin/AllergyAssignment/delete',
            'id' => 'et_delete'
        ]
    ); ?>

</div>

<script>
    $(document).ready(function () {
        $('table.generic-admin tbody').on('click', 'tr td:not(".checkbox")', function () {
            var id = $(this).closest('tr').data('id');
            window.location.href = '/OphCiExamination/admin/AllergyAssignment/update/' + id;
        });
    });
</script>
