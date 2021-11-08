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

<h2>Leaflet Subspecialty Context Assignment</h2>

<form id="leaflets_firm" method="GET">
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
                <td class="fade">Context</td>
                <td>
                    <?php if (!@$_POST['subspecialty-id']) { ?>
                        <?= CHtml::dropDownList(
                            'firm-id',
                            '',
                            array(),
                            [
                                'class' => 'cols-full',
                                'empty' => 'All ' . Firm::contextLabel() . 's',
                                'disabled' => 'disabled',
                            ]
                        ) ?>
                    <?php } else { ?>
                        <?= CHtml::dropDownList(
                            'firm-id',
                            @$_POST['firm-id'],
                            Firm::model()->getList(Yii::app()->session['selected_institution_id'], @$_POST['subspecialty-id']),
                            array(
                                'class' => 'cols-full',
                                'empty' => 'All ' . Firm::contextLabel() . 's',
                                'disabled' => (@$_POST['emergency_list'] === 1 ? 'disabled' : ''),
                            )
                        ) ?>
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <td>Search for Leaflets</td>
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
            <th>Id</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody id='leaflets'></tbody>
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
        if ($("#firm-id option:selected").text() === 'All Contexts') {
            getLeaflets($('#subspecialty-id').val(), 'subspecialty', 'subspecialties');
        } else {
            getLeaflets($('#firm-id').val(), 'firm', 'firms');
        }
    }

    // delete a leaflet
    function deleteLeaflet(button) {
        // parse button id for leaflet_id and type: Firm or Subspecialty
        const id_split = button['id'].split("-");
        const leaflet_id = id_split[0];
        const type = id_split[1];
        let type_id;

        // get the id for firm
        if (type === 'firm') {
            type_id = $('#firm-id').val();
        } // or subspecialty
        if (type === 'subspecialty') {
            type_id = $('#subspecialty-id').val();
        }

        $.ajax({
            'type': 'GET',
            'url': `/${OE_module_name}/oeadmin/leafletSubspecialtyFirm/delete?leaflet_id=${leaflet_id}&type=${type}&type_id=${type_id}`,
            'success': function (html) {
                if (html === 'error')
                    showError("Leaflet could not be deleted");
                else
                    reloadLeaflets();
            }
        });
    }

    // load leaflets for Subspecialty/Context(firm) based on id
    function getLeaflets(id, type, types) {
        $.ajax({
            'type': 'GET',
            'url': `/${OE_module_name}/oeadmin/leafletSubspecialtyFirm/getLeaflets?id=${id}&type=${type}&types=${types}`,
            'success': function (html) {
                $('#leaflets').html(html);
            }
        });
    }

    $(document).ready(function () {
        // load table data (leaflets) after main page is loaded
        getLeaflets($('#subspecialty-id').val(), 'subspecialty', 'subspecialties');
        const $firm_id = $('#firm-id');

        // when a subspecialty is chosen
        $(this).on('change', '#subspecialty-id', function () {
            const subspecialty_id = $(this).val();

            if (subspecialty_id === '') {
                $('#firm-id option').remove();
                $firm_id.append($('<option>').text("All Contexts"));
                $firm_id.attr('disabled', 'disabled');
            } else {
                // load default leaflets specific to a Subspecialty (not contexts/firms specific)
                getLeaflets(subspecialty_id, 'subspecialty', 'subspecialties');

                // load contexts (FIRMS)
                $.ajax({
                    'type': 'GET',
                    'url': `/PatientTicketing/default/getFirmsForSubspecialty?subspecialty_id=${subspecialty_id}`,
                    'success': function (data) {

                    }
                });
            }
        });

        // when a context (firm) is chosen
        $(this).on('change', '#firm-id', function () {
            const firm_id = $(this).val();

            if (firm_id === '') {
                // load default leaflets specific to a Subspecialty (not contexts/firms specific)
                getLeaflets($('#subspecialty-id').val(), 'subspecialty', 'subspecialties');
            } else {
                // load leaflets specific to the selected context (firm)
                getLeaflets(firm_id, 'firm', 'firms');
            }
        });

        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#oe-autocompletesearch'),
            url: `/${OE_module_name}/oeadmin/LeafletSubspecialtyFirm/search`,
            onSelect: function(){
                let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                const selectedLeafletId = AutoCompleteResponse.id;
                let type_id;
                let type;

                if ($('#firm-id option:selected').text() === 'All Contexts') {
                    type = 'subspecialty';
                    type_id = $('#subspecialty-id').val();
                } else {
                    type = 'firm';
                    type_id = $('#firm-id').val();
                }
                
                $.ajax({
                    'type': 'GET',
                    'url': `/${OE_module_name}/oeadmin/leafletSubspecialtyFirm/add?leaflet_id=${selectedLeafletId}&type=${type}&type_id=${type_id}`,
                    'success': function () {
                        reloadLeaflets();
                    }
                });
            }
        });
    });
</script>