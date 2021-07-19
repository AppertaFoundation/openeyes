<?php
    $route_conditions = array(
        'condition' => 'source_type =:source_type',
        'params' => [':source_type' => 'DM+D'],
        'order' => "term ASC"
    );
    $route_options = \CHtml::listData(\MedicationRoute::model()->findAll($route_conditions), 'id', 'term');
    $fpten_setting = SettingMetadata::model()->getSetting('prescription_form_format');
    $overprint_setting = SettingMetadata::model()->getSetting('enable_prescription_overprint');
    $fpten_dispense_condition = OphDrPrescription_DispenseCondition::model()->findByAttributes(array('name' => 'Print to {form_type}'));
    $dispense_conditions = OphDrPrescription_DispenseCondition::model()->withSettings($overprint_setting, $fpten_dispense_condition->id)->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION);

    $dispense_condition_options = array(
        $fpten_dispense_condition->id => array('label' => "Print to $fpten_setting")
    );
    $dispense_location_options = \CHtml::listData(\OphDrPrescription_DispenseLocation::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION), 'id', 'name');
    $unit_options = \CHtml::listData(\MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions, 'description', 'description');
    $duration_options = \CHtml::listData(\MedicationDuration::model()->findAll('deleted_date IS NULL'), 'id', 'name');
    $frequency_options = \CHtml::listData(\MedicationFrequency::model()->findAll('deleted_date IS NULL'), 'id', 'term');
    ?>
<div class="row divider">
    <div class="cols-12">
        <h3>Medications Assignment</h3>
        <table class="standard">
            <colgroup>
                <col>
                <col class="cols-1">
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>Preferred Term</th>
                    <th>Default dose</th>
                    <th>Default Unit</th>
                    <th>Default Route</th>
                    <th>Default Frequency</th>
                    <th>Default Duration</th>
                    <th>Default Dispense Condition</th>
                    <th>Default Dispense Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="pgdpsd-meds-selections">
                <?php foreach ($assigned_meds as $key => $med) {
                    $dose_unit_term = $med->dose_unit_term ? : '';
                    $comment_cls = 'no-comment';
                    $comment_input_ctn_style = 'style="display:none"';
                    $comment_btn_style = '';
                    if ($med->comments) {
                        $comment_cls = 'has-comment';
                        $comment_input_ctn_style = '';
                        $comment_btn_style = 'style="display:none"';
                    }
                    ?>
                <tr 
                    data-key="<?=$key?>" 
                    data-preferred_term="<?=$med->medication->getLabel(true)?>" 
                    data-medication_id="<?=$med->medication_id?>" 
                    data-default_dose="<?=$med->dose?>"
                    data-default_dose_unit_term="<?=$med->dose_unit_term?>"
                    class="meds-entry"
                >
                    <input type="hidden" value="<?=$key?>">
                    <td>
                        <?=$med->medication->getLabel(true)?>
                        <input class="js-input" name="<?=$prefix?>[meds][<?=$key?>][medication_id]" type="hidden" value="<?=$med->medication_id?>">
                    </td>
                    <td class="js-input-wrapper">
                        <input class="js-input cols-full" name="<?=$prefix?>[meds][<?=$key?>][dose]" type="text" value="<?=$med->dose?>">
                    </td>
                    <td class="js-input-wrapper">
                        <?=\CHtml::dropDownList("{$prefix}[meds][$key][dose_unit_term]", $med->dose_unit_term, $unit_options, ['empty' => '-Unit-', 'class' => 'js-unit-dropdown',])?>
                    </td>
                    <td class="js-input-wrapper">
                        <?=\CHtml::dropDownList("{$prefix}[meds][$key][route_id]", $med->route_id, $route_options, ['id' => null, 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
                    </td>
                    <td class="js-input-wrapper js-pgd-only">
                        <?=\CHtml::dropDownList("{$prefix}[meds][$key][frequency_id]", $med->frequency_id, $frequency_options, ['id' => null, 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
                    </td>
                    <td class="js-input-wrapper js-pgd-only">
                        <?=\CHtml::dropDownList("{$prefix}[meds][$key][duration_id]", $med->duration_id, $duration_options, ['id' => null, 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
                    </td>
                    <td class="js-input-wrapper js-pgd-only">
                        <?= CHtml::dropDownList(
                            "{$prefix}[meds][$key][dispense_condition_id]",
                            $med->dispense_condition_id,
                            CHtml::listData(
                                $dispense_conditions,
                                'id',
                                'name'
                            ),
                            array('empty' => '-- select --', 'options' => $dispense_condition_options)
                        ); ?>
                    </td>
                    <td class="js-input-wrapper js-pgd-only">
                        <?= CHtml::dropDownList(
                            "{$prefix}[meds][$key][dispense_location_id]",
                            $med->dispense_location_id,
                            $dispense_location_options,
                        ); ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="js-delete-meds">
                            <i class="oe-i trash"></i>
                        </a>
                        <a href="javascript:void(0);" class="js-copy-meds js-psd-only">
                            <i class="oe-i duplicate"></i>
                        </a>
                        <a data-comment-row="#comment-row-<?=$key?>" href="javascript:void(0);" class="js-add-pgd-comments js-pgd-only <?=$comment_cls?>" <?=$comment_btn_style?>>
                            <i class="oe-i comments"></i>
                        </a>
                    </td>
                </tr>
                <tr id="comment-row-<?=$key?>" class="no-line col-gap js-comment-container js-pgd-only <?=$comment_cls?>" <?=$comment_input_ctn_style?>>
                    <td colspan="8">
                        <div class="flex-layout flex-left">
                            <input class="cols-full" placeholder="Comments" type="text" name="<?=$prefix?>[meds][<?=$key?>][comments]" value="<?=$med->comments?>">
                            <i class="oe-i remove-circle pad-left js-remove-pgd-comments"></i>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="10">
                        <div class="flex-layout flex-right">
                            <button id="js-add-meds" class="button hint green" type="button">
                                <i class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="x-tmpl-mustache" id="meds_row_template" style="display:none">
    <tr 
        data-key="{{key}}" 
        data-preferred_term="{{preferred_term}}" 
        data-medication_id="{{medication_id}}" 
        data-dose="{{dose}}"
        data-dose_unit_term="{{dose_unit_term}}"
        class="meds-entry"
    >
        <input type="hidden" value="{{key}}">
        <td>
            {{preferred_term}}
            <input class="js-input" name="{{prefix}}[meds][{{key}}][medication_id]" type="hidden" value="{{medication_id}}">
        </td>
        <td class="js-input-wrapper">
            <input class="js-input cols-full" name="{{prefix}}[meds][{{key}}][dose]" type="text" value="{{dose}}">
        </td>
        <td class="js-input-wrapper">
            <span data-type="dose_unit_term">
                <?=\CHtml::dropDownList('{{prefix}}[meds][{{key}}][dose_unit_term]', null, $unit_options, ['empty' => '-Unit-', 'class' => 'js-unit-dropdown',])?>
            </span>
        </td>
        <td class="js-input-wrapper">
            <?=\CHtml::dropDownList(
                '{{prefix}}[meds][{{key}}][route_id]',
                null,
                $route_options,
                [
                    'id' => null,
                    'class' => 'js-input cols-full',
                    'empty' => '-- select --',
                ]
            );?>
        </td>
        <td class="js-input-wrapper js-pgd-only">
            <?=\CHtml::dropDownList(
                '{{prefix}}[meds][{{key}}][frequency_id]',
                null,
                $frequency_options,
                [
                    'id' => null,
                    'class' => 'js-input cols-full',
                    'empty' => '-- select --',
                ]
            );?>
        </td>
        <td class="js-input-wrapper js-pgd-only">
            <?=\CHtml::dropDownList(
                '{{prefix}}[meds][{{key}}][duration_id]',
                null,
                $duration_options,
                [
                    'id' => null,
                    'class' => 'js-input cols-full',
                    'empty' => '-- select --',
                ]
            );?>
        </td>
        <td class="js-input-wrapper js-pgd-only">
            <?= CHtml::dropDownList(
                "{{prefix}}[meds][{{key}}][dispense_condition_id]",
                null,
                CHtml::listData(
                    $dispense_conditions,
                    'id',
                    'name'
                ),
                [
                    'id' => null,
                    'class' => 'js-input cols-full',
                    'empty' => '-- select --',
                    'options' => $dispense_condition_options,
                ]
            ); ?>
        </td>
        <td class="js-input-wrapper js-pgd-only">
            <?= CHtml::dropDownList(
                "{{prefix}}[meds][{{key}}][dispense_location_id]",
                null,
                array(),
                [
                    'id' => null,
                    'class' => 'js-input cols-full',
                    'empty' => '-- select --',
                ]
            ); ?>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-meds">
                <i class="oe-i trash"></i>
            </a>
            <a href="javascript:void(0);" class="js-copy-meds js-psd-only">
                <i class="oe-i duplicate"></i>
            </a>
            <a href="javascript:void(0);" class="js-add-pgd-comments js-pgd-only no-comment" data-comment-row="#comment-row-{{key}}">
                <i class="oe-i comments"></i>
            </a>
        </td>
    </tr>
    <tr id="comment-row-{{key}}" class="no-line col-gap js-comment-container js-pgd-only no-comment" style="display:none;">
        <td colspan="8">
            <div class="flex-layout flex-left">
                <input class="cols-full" placeholder="Comments" type="text" name="{{prefix}}[meds][{{key}}][comments]">
                <i class="oe-i remove-circle pad-left js-remove-pgd-comments"></i>
            </div>
        </td>
    </tr>
</script>
<script>
    $(function(){
        const prefix = '<?=$prefix;?>';
        const $meds_selections_tbl = $('#pgdpsd-meds-selections');
        const $meds_template = $('#meds_row_template');
        const medications = <?=json_encode($medications);?>;
        const medications_itemSet = new OpenEyes.UI.AdderDialog.ItemSet(medications, {
            multiSelect: true,
        });
        const category = [
            {
                id: 1,
                label: 'Ophthalmic',
                type: 'ophthalmic',
            },
            {
                id: 2,
                label: 'Systemic',
                type: 'systemic',
            },
            {
                id: 3,
                label: 'Drops',
                type: 'drops',
            },
            {
                id: 4,
                label: 'Oral',
                type: 'oral',
            }
        ];
        
        const category_itemSet = new OpenEyes.UI.AdderDialog.ItemSet(category, {
            multiSelect: false,
        });

        function getDispenseLocation($dispense_condition, dispense_location)
        {
            $.get("/OphDrPrescription/PrescriptionCommon/GetDispenseLocation", {
                condition_id: $dispense_condition.val(),
            }, function (data) {
                let $dispense_location = $dispense_condition.closest('tr').find('select[name$="[dispense_location_id]"]');
                $dispense_location.find('option').remove();
                $dispense_location.append(data);
                $dispense_location.show();
                if (dispense_location) {
                    $dispense_location.val(dispense_location);
                }
            });
        };

        new OpenEyes.UI.AdderDialog.MedSearch({
            openButton: $('#js-add-meds'),
            itemSets: [category_itemSet, medications_itemSet],
            onReturn: function (adderDialog, selectedItems) {
                const selected_type = $('input[name$="[type]"]:checked').val();
                selectedItems.forEach(item => {
                    if(item.type){
                        return;
                    }
                    //how nice that filter is coming back as a selected item
                    if (item.label && item.label === 'Include brand names') {
                        return;
                    }

                    const medication_id = item.id;

                    let data = item;
                    data.key = OpenEyes.Util.getNextDataKey($meds_selections_tbl.find('tr.meds-entry'), 'key');
                    data.id = '';
                    data.prefix = prefix;
                    data.medication_id = medication_id;
                    data.dose = item.dose || item.default_dose;
                    data.dose_unit_term = item.dose_unit_term || item.default_dose_unit_term;
                    data.route_id = item.route_id || item.default_route_id;
                    data.frequency_id = item.frequency_id || item.default_frequency_id;
                    data.duration_id = item.duration_id || item.default_duration_id;
                    data.preferred_term = data.name || data.short_term || data.preferred_term;
                    data.preferred_term += (data.amp_term && data.vtm_term) ? ' (' + data.vtm_term + ')' : '';
                    const $tr_html = Mustache.render($meds_template.html(), data);
                    $meds_selections_tbl.append($tr_html)
                    switchType(selected_type);
                    $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[route_id]"]').val(data.route_id);
                    $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[dose_unit_term]"]').val(data.dose_unit_term);
                    $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[frequency_id]"]').val(data.frequency_id);
                    $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[duration_id]"]').val(data.duration_id);
                    $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[dispense_condition_id]"]').val(data.dispense_condition_id);
                    $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[dispense_condition_id]"]').trigger('change');
                });
            },
        });
        $(document).off('change', 'tbody select[name$="[dispense_condition_id]"]').on('change', 'tbody select[name$="[dispense_condition_id]"]', function(){
            getDispenseLocation($(this), null)
        });
        $(document).off('click', '.js-add-pgd-comments').on('click', '.js-add-pgd-comments', function(){
            $(this).hide();
            const key = $(this).closest('tr').data('key');
            const comment_row = $(this).data(`comment-row`);
            $(comment_row).show();
            $(comment_row).find('input').focus();
        });
        $(document).off('click', '.js-remove-pgd-comments').on('click', '.js-remove-pgd-comments', function(){
            const $tr = $(this).closest('tr');
            $tr.hide();
            $(this).siblings('input').val(null);
            $(this).closest('tbody').find(`.js-add-pgd-comments[data-comment-row$="${$tr.attr('id')}"]`).show();
        });
        $(document).off('keyup', 'input[name$="[comments]"]').on('keyup', 'input[name$="[comments]"]', function(){
            const $tr = $(this).closest('tr')
            if(this.value){
                $tr.removeClass('no-comment').addClass('has-comment');
                $(this).closest('tbody').find(`.js-add-pgd-comments[data-comment-row$="${$tr.attr('id')}"]`).removeClass('no-comment').addClass('has-comment');
            }else{
                $tr.removeClass('has-comment').addClass('no-comment');
                $(this).closest('tbody').find(`.js-add-pgd-comments[data-comment-row$="${$tr.attr('id')}"]`).removeClass('has-comment').addClass('no-comment');
            }
        });
        $meds_selections_tbl.off('click', '.js-copy-meds').on('click', '.js-copy-meds', function(){
            const selected_type = $('input[name$="[type]"]:checked').val();
            const $this_tr = $(this).closest('tr.meds-entry');
            const data = JSON.parse(JSON.stringify($this_tr.data()));
            const route = $this_tr.find('select[name$="[route_id]"]').val();
            const dose_unit_term = $this_tr.find('select[name$="[dose_unit_term]"]').val();
            const dose = $this_tr.find('input[name$="[dose]"]').val();
            data.dose = dose;
            data.prefix = prefix;
            data.key = OpenEyes.Util.getNextDataKey($meds_selections_tbl.find('tr.meds-entry'), 'key');
            const $tr_html = Mustache.render($meds_template.html(), data);
            $meds_selections_tbl.append($tr_html);
            $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[route_id]"]').val(route);
            $meds_selections_tbl.find(`tr[data-key=${data.key}]`).find('select[name$="[dose_unit_term]"]').val(dose_unit_term);
            switchType(selected_type);
        });
        $meds_selections_tbl.off('click', '.js-delete-meds').on('click', '.js-delete-meds', function(e){
            const $tr = $(this).closest("tr.meds-entry");
            const $comment_tr = $tr.siblings(`#comment-row-${$tr.data('key')}`);
            $comment_tr.remove();
            $tr.remove();
        });
    });
</script>