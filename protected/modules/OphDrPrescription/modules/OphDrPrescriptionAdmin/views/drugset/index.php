<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="row divider">
    <h2>Drug Sets</h2>
</div>


<div class="row divider">

    <form id="drug_set_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

        <div id="set-filters" class="flex-layout row">
            <button data-usage_code="" type="button" class="large js-set-select js-all-sets selected green hint">All
                Sets
            </button>
            <button data-usage_code="COMMON_OPH" type="button" class="large js-set-select">Common Ophthalmic Drug Sets
            </button>
            <button data-usage_code="COMMON_SYSTEMIC" type="button" class="large js-set-select">Common Systemic Drug
                Sets
            </button>
            <button data-usage_code="PRESCRIPTION_SET" type="button" class="large js-set-select">Prescription Drug
                Sets
            </button>
            <button data-usage_code="Drug" type="button" class="large js-set-select">Drug</button>
            <button data-usage_code="DrugTag" type="button" class="large js-set-select">Drug Tags</button>
            <button data-usage_code="Formulary" type="button" class="large js-set-select">Formulary Drugs</button>
            <button data-usage_code="MedicationDrug" type="button" class="large js-set-select">MedicationDrug</button>
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
                            ['class' => 'cols-full', 'placeholder' => "Id, Name"]
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
                        <button class="blue hint" type="submit" id="et_search">Search</button>
                    </td>
                </tr>
                </tbody>
            </table>
    </form>
</div>

<div class="cols-12">
    <form>
        <table id="drugset-list" class="standard">
            <colgroup>
                <col class="cols-1" style="width:3.33333%">
                <col class="cols-1" style="width:3.33333%">
                <col class="cols-2">
                <col class="cols-4">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <thead>
            <tr>
                <th><?= \CHtml::checkBox('selectall'); ?></th>
                <th>Id</th>
                <th>Name</th>
                <th>Rule</th>
                <th>Count</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($dataProvider->getData() as $set) {
                        $this->renderPartial('_row', ['set' => $set]);
                    }
                ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="4">
                    <?= \CHtml::submitButton('Add', [
                        'id' => 'et_add',
                        'data-uri' => "/OphDrPrescription/admin/drugSet/edit",
                        'class' => 'button large'
                    ]); ?>
                    <?= \CHtml::submitButton('Delete', [
                        'id' => 'et_delete',
                        'data-uri' => '/OphDrPrescription/admin/drugSet/delete',
                        'class' => 'button large',
                        'data-object' => 'DrugSet'
                    ]); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $dataProvider->pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/html" id="medication_set_template" style="display:none">
    <tr>
        <td><input checked="checked" type="checkbox" value="{{set_id}}" name="delete-ids[]" /></td>
        <td>{{set_id}}</td>
        <td>{{set_name}}</td>
        <td>{{set_rule}}</td>
        <td>{{set_item_count}}</td>
        <td>{{set_hidden_string}}</td>
        <td>
            <a href="/OphDrPrescription/refSetAdmin/edit/{{set_id}}" class="button">Edit</a>
            <a href="/OphDrPrescription/refMedicationSetAdmin/list?ref_set_id={{set_id}}" class="button">List
                medications</a>
        </td>
    </tr>
</script>

<script>

    var OpenEyes = OpenEyes || {};

    OpenEyes.OphDrPrescriptionAdmin = OpenEyes.OphDrPrescriptionAdmin || {};

    (function (exports) {
        function DrugSetController(options) {
            this.options = $.extend(true, {}, DrugSetController._defaultOptions, options);

            this.initFilters();
        }

        DrugSetController._defaultOptions = {};

        DrugSetController.prototype.initFilters = function () {
            var controller = this;
            $('#set-filters').on('click', 'button', function () {
                $(this).toggleClass('selected green hint').blur();

                if ($(this).hasClass('js-all-sets')) {
                    $('#set-filters button:not(.js-all-sets)').removeClass('selected green hint').blur();
                } else {
                    $('#set-filters button.js-all-sets').removeClass('selected green hint').blur();
                }

                if (!$('#set-filters button.selected').length) {
                    $('#set-filters button.js-all-sets').addClass('selected green hint').blur();
                }

                controller.refreshResult();
            });

            $('#drugset-list').on('click', 'a:not(.selected)', function (e) {
                e.preventDefault();
                e.stopPropagation();

                let url = new URL(`${window.location.protocol}//${window.location.host}${$(this).attr('href')}`);
                let search_params = new URLSearchParams(url.search);

                controller.refreshResult(search_params.get('page'));
            });
        };

        DrugSetController.prototype.refreshResult = function (page = 1) {
            let data = {};
            let usage_codes = $('#set-filters button.selected').map(function (m, button) {
                let usage_code = $(button).data('usage_code');
                return usage_code ? usage_code : null;
            }).get();

            data.page = page;

            data.search = {};
            if (usage_codes.length) {
                data.search.usage_codes = usage_codes
            }

            $.ajax({
                url: '/OphDrPrescription/admin/DrugSet/search',
                dataType: "json",
                data: data,
                beforeSend: function() {

                    // demo load spinner
                    let $overlay = $('<div>', {class: 'oe-popup-wrap'});
                    let $spinner = $('<div>', {class: 'spinner'});
                    $overlay.append($spinner);
                 //   $overlay.click(function(){ $(this).remove(); });
                    $('body').prepend($overlay);
                },
                success: function(data) {
                    let $template = $('#medication_set_template');
                    let rows = $.map(data.sets, function(set) {
                        return Mustache.render($('#medication_set_template').html(), {
                            set_id: set.id,
                            set_name: set.name,
                            set_rule: set.rules,
                        });
                    });

                    $('#drugset-list tbody').html(rows.join(''));
                    $('.pagination-container').find('.pagination').replaceWith(data.pagination);

                },
                complete: function() {
                    $('.oe-popup-wrap').remove();
                }
            });
        };

        exports.DrugSetController = DrugSetController;

    })(OpenEyes.OphDrPrescriptionAdmin);


    var drugSetController = new OpenEyes.OphDrPrescriptionAdmin.DrugSetController();

</script>
