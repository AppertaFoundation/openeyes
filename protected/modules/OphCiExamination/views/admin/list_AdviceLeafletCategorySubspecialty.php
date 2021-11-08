<?php
/**
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

<h2>Advice Leaflet Subspecialty Assignment</h2>

<form id="leaflets_subspecialty" method="GET">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <div class="cols-3">
        <table class="standard cols-full" id="finding-table">
            <colgroup>
                <col class="cols-1">
                <col class="cols-3">
            </colgroup>
            <tbody>
            <tr>
                <td class="fade">Subspecialty:</td>
                <td>
                    <?= CHtml::dropDownList(
                        'subspecialty-id',
                        @$_POST['subspecialty-id'],
                        Subspecialty::model()->getList(),
                        [
                            'empty' => 'Select',
                            'class' => 'cols-full',
                            'disabled' => ((int)@$_POST['emergency_list'] === 1 ? 'disabled' : ''),
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Search for Leaflet categories</td>
                <td>
                    <div class="flex-layout flex-left">
                        <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
                        <div style="display:none" class="js-spinner-as-icon"><i class="spinner as-icon"></i></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>

<div class="cols-7">
    <table class="standard cols-full" id="finding-table">
        <colgroup>
            <col class="cols-1">
            <col class="cols-5">
            <col class="cols-1">
        </colgroup>

        <thead>
        <tr>
            <th></th>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody id='categories' class="sortable"></tbody>
    </table>
</div>

<script>
    // show a new alert message
    function showError(error_message) {
        new OpenEyes.UI.Dialog.Alert({
            content: error_message
        }).open();
    }

    // reload table data
    function reloadLeaflets() {
        getLeaflets($('#subspecialty-id').val());
    }

    $('.sortable').sortable({
        stop: function (e, ui) {
            $('#categories tr').each(function (index, tr) {
                $.ajax({
                    url: '/OphCiExamination/admin/setCategoryOrder',
                    type: 'POST',
                    data: {
                        'id': $(tr).find('.js-category-id').val(),
                        'display_order': index,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    success: function () {
                        $(tr).find('.js-category-display-order').val(index);
                    }
                })
            });
        }
    });

    // delete a leaflet
    function deleteLeaflet(button) {
        // parse button id for category_id
        const id_split = button['id'].split("-");
        const id = id_split[0];

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphCiExamination/admin/deleteAdviceLeafletSubspecialty',
            'data': {
                'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
                'id': id,
            },
            'success': function (html) {
                if (html === 'error')
                    showError("Category could not be deleted");
                else
                    reloadLeaflets();
            }
        });
    }

    // load categories for Subspecialty based on id
    function getLeaflets(id) {
        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/OphCiExamination/admin/getLeafletCategories?subspecialty_id=' + id,
            'success': function (html) {
                $('#categories').html(html);
            }
        });
    }

    $(document).ready(function () {
        // load table data (leaflets) after main page is loaded
        getLeaflets($('#subspecialty-id').val());

        // when a subspecialty is chosen
        $(this).on('change', '#subspecialty-id', function () {
            const subspecialty_id = $(this).val();

            if (subspecialty_id !== '') {
                // load default leaflets specific to a Subspecialty
                getLeaflets(subspecialty_id);
            }
        });

        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#oe-autocompletesearch'),
            url: '/OphCiExamination/admin/searchAdviceLeafletCategories',
            onSelect: function(){
                let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                const selectedCategoryId = AutoCompleteResponse.id;
                let subspecialty_id = $('#subspecialty-id').val();

                $.ajax({
                    'type': 'POST',
                    'url': baseUrl + '/OphCiExamination/admin/addAdviceLeafletSubspecialty',
                    'data': {
                        'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
                        'category_id': selectedCategoryId,
                        'subspecialty_id': subspecialty_id
                    },
                    'success': function () {
                        reloadLeaflets();
                    }
                });
            }
        });
    });
</script>