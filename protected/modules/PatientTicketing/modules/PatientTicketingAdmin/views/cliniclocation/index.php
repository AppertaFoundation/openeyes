<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\PatientTicketing\models\ClinicLocation;

?>
<div class="row divider cols-9">
    <h2>Filter</h2>
</div>
<div class="row divider cols-9">
    <?=\CHtml::errorSummary(
        $options,
        null,
        null,
        ["class" => "alert-box alert with-icon"]
    ); ?>
    <?php $this->renderPartial('/cliniclocation/_filter', [
            'institution' => $institution,
            'queueset_id' => $queueset_id
    ]) ?>
</div>
<div class="clinic-locations-wrapper" style="display:<?=($queueset_id ? 'block' : 'none')?>">
<?php $this->renderPartial('//base/_messages') ?>
<div class="clinic-locations-wrapper" style="display:<?=($queueset_id ? 'block' : 'none')?>">
    <div class="row divider cols-9">
        <h2>Clinic Locations</h2>
    </div>
    <div class="cols-3">
        <form id="clinic-locations" method="post">
            <input type="hidden" value="<?= Yii::app()->request->csrfToken ?>" name="YII_CSRF_TOKEN">
            <?=\CHtml::hiddenField('institution_id', $institution->id);?>
            <table id="options" class="standard generic-admin sortable" data-sort-uri="/PatientTicketing/PatientTicketingAdmin/ClinicLocations/sortOptions">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($options as $i => $option) : ?>
                    <tr class="" data-row="<?=$i;?>">
                        <td class="reorder">
                            <span>↑↓</span>
                            <?=\CHtml::activeHiddenField($option, "[{$i}]id");?>
                            <?=\CHtml::activeHiddenField($option, "[{$i}]queueset_id");?>
                        </td>
                        <td>
                            <?=\CHtml::activeTextField($option, "[{$i}]name");?>
                        </td>
                        <td>
                            <button type="button"><a href="#" class="deleteRow">delete</a></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="pagination-container">
                <tr>
                    <td colspan="10">
                        <input name="admin-add" id="et_admin-add" class="generic-admin-add button large" type="button" value="Add">&nbsp;
                        <input name="admin-save" id="et_admin-save" class="generic-admin-save button large" type="submit" value="Save">&nbsp;
                        <input type="hidden" value="/PatientTicketing/PatientTicketingAdmin/ClinicLocations/index" name="return_url" id="return_url">
                    </td>
                </tr>
                </tfoot>
            </table>
            <div>
            </div>
        </form>
    </div>
</div>
<script>
    function updateQueueSetOptions(queue_sets) {
        const addOption = function (id, name) {
            let option = Mustache.render('<option value="{{id}}">{{name}}</option>', {
                name: name,
                id: id
            });
            $queueset_select.insertAdjacentHTML('beforeend', option);
        }

        $queueset_select.innerHTML = '';
        addOption(null, '-');
        queue_sets.forEach(set => {
            addOption(set.id, set.name);
        });
    }

    const $filer_btn = document.querySelector('.js-filter');
    const $institution_select = document.getElementById('filter_institution');
    const $queueset_select = document.getElementById('queueset_institution');
    const $form = document.getElementById('clinic-locations');
    const $table = document.getElementById('options');
    const $add_btn = document.getElementById('et_admin-add');

    OpenEyes.UI.DOM.addEventListener($institution_select, 'change', null, (e) => {

        const institution_id = e.target.value;
        const url = `/PatientTicketing/PatientTicketingAdmin/Default/getQueueSets?institution_id=${institution_id}`;
        $queueset_select.disabled = true;
        fetch(url)
            .then(response => response.json())
            .then(response => {
                updateQueueSetOptions(response.queue_sets);
                $queueset_select.disabled = false;
            });
    });

    OpenEyes.UI.DOM.addEventListener($filer_btn, 'click', null, () => {
        document.getElementById('filter').submit();
    });

    OpenEyes.UI.DOM.addEventListener($form, 'submit', null, function(e) {
        e.preventDefault();
        $form.submit();
    });

    OpenEyes.UI.DOM.addEventListener($table, 'click', '.deleteRow', (e) => {
        const $button = e.target;
        const $tr = $button.closest('tr');
        $tr.remove();
    });

    OpenEyes.UI.DOM.addEventListener($add_btn, 'click', null, (e) => {
        const template = document.getElementById('add_template');
        const tr = Mustache.render(template.innerHTML, {
            row: OpenEyes.Util.getNextDataKey($('#options').find('tbody tr'), 'row'),
            queueset_id: '<?= $queueset_id;?>'
        });
        $table.querySelector('tbody').insertAdjacentHTML('beforeend', tr);
    });

    $('#options tbody').sortable();

</script>
<script id="add_template" type="text/html">
    <tr class="" data-row="{{row}}">
        <td class="reorder">
            <span>↑↓</span>
            <input name="OEModule_PatientTicketing_models_ClinicLocation[{{row}}][id]" id="OEModule_PatientTicketing_models_ClinicLocation_{{row}}_id" type="hidden" value="">
            <input name="OEModule_PatientTicketing_models_ClinicLocation[{{row}}][queueset_id]" id="OEModule_PatientTicketing_models_ClinicLocation_{{row}}_queueset_id" type="hidden" value="{{queueset_id}}">
        </td>
        <td>
            <input name="OEModule_PatientTicketing_models_ClinicLocation[{{row}}][name]" id="OEModule_PatientTicketing_models_ClinicLocation_{{row}}_name" type="text" value="">
        </td>
        <td>
            <button type="button"><a href="#" class="deleteRow">delete</a></button>
        </td>
        <td></td>
    </tr>
</script>
