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

<h2>Medications in set</h2>
<div class="row flex-layout flex-top col-gap">
    <div class="cols-6">

        <input type="text"
               class="search cols-12"
               autocomplete=""
               name="search"
               id="search_query"
               placeholder="Search medication in set..."
               <?= !$medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>
        >
        <small class="empty-set" <?= $medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>>Empty set</small>

        <div class="alert-box success" style="display:none"><b>Success!</b> Medication added to the set.</div>

        <div class="alert-box loading" style="display:none">
            Saving... <span class="js-spinner-as-icon"><i class="spinner as-icon"></i></span>
        </div>
    </div>

    <div class="cols-6">
        <div class="flex-layout flex-right">
            <button class="button hint green" id="add-medication-btn" type="button"><i class="oe-i plus pro-theme"></i> Add medication</button>
        </div>
    </div>
</div>
<div class="row flex-layout flex-stretch flex-right">
    <div class="cols-12">
        <table id="meds-list" class="standard" id="rule_tbl" <?= !$medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>>
            <thead>
                <tr>
                    <th>Preferred Term</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($medication_data_provider->getData() as $k => $med) : ?>
                <tr>
                    <td><?= $med->preferred_term; ?></td>
                    <td style="text-align:center"><a data-med_id="<?=$med->id;?>" class="js-delete-set-medication"><i class="oe-i trash"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <td colspan="5">
                <?php $this->widget('LinkPager', ['pages' => $medication_data_provider->pagination]); ?>
            </td>
            </tfoot>
        </table>
    </div>
</div>

<script>
    new OpenEyes.UI.AdderDialog({
        openButton: $('#add-medication-btn'),
        onReturn: function (adderDialog, selectedItems) {
            const $table = $(drugSetController.options.tableSelector + ' tbody');

            selectedItems.forEach(item => {
                const $tr = Mustache.render($('#medication_template').html(), {
                    id: item.id,
                    preferred_term: item.preferred_term,
                });
                $table.append($tr);

                $('.alert-box.loading').slideDown();
                $.post('/OphDrPrescription/admin/drugset/addMedicationToSet', {
                    set_id: $('#MedicationSet_id').val(),
                    medication_id: item.id,
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                }, function(result) {
                    $('.alert-box.loading').hide();
                    $('.alert-box.success').show();

                    $table.find('tr.no-result').remove();
                    $('.empty-set').remove();
                    $(drugSetController.options.tableSelector).show();
                    result = JSON.parse(result);
                    if (result.success === true) {
                        setTimeout(function(){
                            $('.alert-box.success').fadeOut(1500);
                        }, 2000);
                    }
                });
            });
        },
        searchOptions: {
            searchSource: '/medicationManagement/findRefMedications',
        },
        enableCustomSearchEntries: true,
        searchAsTypedItemProperties: {id: "<?php echo EventMedicationUse::USER_MEDICATION_ID ?>"},
        booleanSearchFilterEnabled: true,
        booleanSearchFilterLabel: 'Include branded',
        booleanSearchFilterURLparam: 'include_branded'
    });

    $(document).ready(function(){
        $(drugSetController.options.tableSelector).on('click', '.js-delete-set-medication', function() {
            const $tr = $(this).closest('tr');
            const $a = $(this);
            const $trash = $a.find('.oe-i.trash');

            $.ajax({
                'type': 'POST',
                'data': {
                    set_id: $('#MedicationSet_id').val(),
                    medication_id: $a.data('med_id'),
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                },
                'url': '/OphDrPrescription/admin/drugset/removeMedicationFromSet',
                'dataType': 'json',
                'beforeSend': function() {
                    $trash.toggleClass('oe-i trash spinner as-icon');
                },
                'success': function (resp) {
                    if (resp.success === true) {
                        $a.replaceWith("<small style='color:red'>Removed</small>");
                        $tr.fadeOut(1000, function(){ $(this).remove(); });
                    }
                },
                'error': function(resp){
                    alert('Remove medication from set FAILED. Please try again.');
                    console.error(resp);
                }
            });
        });
    });
</script>
