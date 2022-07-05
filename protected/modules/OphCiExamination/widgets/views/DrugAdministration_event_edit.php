<?php
use OEModule\OphCiExamination\models\OphCiExaminationAllergy;
$right_eye_id = \MedicationLaterality::RIGHT;
$left_eye_id = \MedicationLaterality::LEFT;
$both_eye_id = \MedicationLaterality::BOTH;
$field_prefix = $model_name . '[assignment][{{section_key}}][entries][{{entry_key}}]';
?>
<div class="element-fields full-width" id="<?=$element_id?>">
    <?php
    foreach ($assigned_psds as $key => $assigned_psd) {
        $appointment_details = $assigned_psd->getAppointmentDetails();
        $assignment_type_name = $assigned_psd->getAssignmentTypeAndName();
        $hide_button = $assigned_psd->comment ? 'display:none' : '';
        $hiden_comment = $assigned_psd->comment ? '' : 'display:none';
        $is_preset = $assigned_psd->pgdpsd ? true : false;
        $is_relevant = $assigned_psd->isrelevant;
        $is_active = $assigned_psd->active;
        $is_existing = !$assigned_psd->isNewRecord;
        $is_record_admin = !intval($assigned_psd->visit_id) ? true : false;
        $is_confirmed = $assigned_psd->confirmed ? $assigned_psd->confirmed : 0;

        $grey_out_section = !$is_relevant || !$is_active ? 'fade' : null;

        extract($this->getDeletedUI($is_active));

        $cancel_btn = null;
        if(!$is_existing){
            $cancel_btn = array(
                'class' => 'red js-cancel-preset',
                'text' => 'Remove Block',
                'display' => $is_confirmed ? '' : 'display:none;',
            );
        } else {
            if ($is_prescriber && $is_active) {
                $cancel_btn = array(
                    'class' => 'red js-delete-preset',
                    'text' => 'Cancel remaining items',
                    'display' => ''
                );
            }
        }
        ?>
    <div class="order-block <?=$grey_out_section;?> <?=$deleted_style?>" data-key="<?=$key?>"  data-section-name="<?=$assignment_type_name['name']?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][assignment_id]"?>" value="<?=$assigned_psd->id?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][pgdpsd_name]"?>" value="<?=$assignment_type_name['name']?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][visit_id]"?>" value="<?=$assigned_psd->visit_id;?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][pgdpsd_id]"?>" value="<?=$assigned_psd->pgdpsd ? $assigned_psd->pgdpsd->id : null;?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][confirmed]"?>" value="<?=$is_confirmed;?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][create_wp]"?>" value="<?=$assigned_psd->create_wp;?>">
        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][active]"?>" value="<?=$is_active?>">
        <input type="hidden" name="<?= $model_name . "[assignment][$key][is_relevant]" ?>" value="<?= (int)$is_relevant ?>">
        <div class="flex row">
            <div class="flex-l">
                <!-- rely on pgdpsd id null or not -->
                <!-- Preset | Custom -->
                <div class="drug-admin-box inline <?=$assigned_psd->getStatusDetails()['css']?>">
                    <?=$assignment_type_name['type']?>
                </div>
                <div class="large-text">
                    <!-- preset name -->
                    <?=$assignment_type_name['name']?>
                    <span class="js-appt-details">
                        <?=$appointment_details['appt_details_dom']?>
                    </span>
                    <?=$deleted_tag?>
                </div>
            </div>
            <div class="flex-r">
                <?php if($cancel_btn) {?>
                    <button class="hint <?=$cancel_btn['class']?> js-after-confirm" style="<?=$cancel_btn['display']?>">
                        <?=$cancel_btn['text']?>
                    </button>
                <?php }?>
                <!-- rely on worklist -->
                <?=$appointment_details['valid_date_dom']?>
            </div>
        </div>
        <div class="flex">
            <div class="cols-11">
                <table class="cols-full js-entry-table">
                    <colgroup>
                        <col class="cols-4">
                        <col class="cols-2">
                        <col class="cols-1">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Drug</th>
                            <th>Dose</th>
                            <th>Route</th>
                            <th>Administered by</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th class="js-administer-all" style="cursor:pointer;"><i class="oe-i tick small no-click pad"></i></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $entries = $assigned_psd->assigned_meds;
                        foreach ($entries as $entry_key => $entry) {
                            extract($entry->getAdministerDetails());
                            $row_action = array(
                                'can_remove' => !$is_existing,
                                'msg' => 'No Permission to Remove',
                            );
                            if ($is_prescriber) {
                                if ($entry->administered && $is_existing) {
                                    $row_action['msg'] = 'Can not remove a drug already administered';
                                } else {
                                    $row_action['can_remove'] = true;
                                }
                            }

                            $can_remove = $is_prescriber ? ($entry->administered ? false : true) : false;
                            /**
                             * if is not active, disable the buttons
                             * if is relevant, not prescriber and the med is administered, disabled the button
                             * if not relevant, disable the button
                             */
                            $disable_administer_btn = !$is_active ? 'disabled' : ($is_relevant ? ($is_prescriber ? '' : ($entry->administered ? 'disabled' : '')) : 'disabled');
                            $hide_administer_switch = ($is_confirmed && $is_record_admin && $entry->administered) || ($is_confirmed && !$is_prescriber && $entry->administered) ? true : false;
                            ?>
                        <tr
                            data-entry-key="<?=$entry_key?>"
                            data-entry_key="<?=$entry_key?>"
                            data-id="<?=$entry->id?>"
                            data-is-preset="<?=$is_preset?>"
                            data-preferred_term="<?=$entry->medication->getLabel(true)?>"
                            data-medication_id="<?=$entry->medication_id?>"
                            data-dose="<?=$entry->dose?>"
                            data-dose_unit_term="<?=$entry->dose_unit_term?>"
                            data-route_id="<?=$entry->route_id?>"
                            data-laterality="<?=$entry->laterality?>"
                            data-right="<?=intval($entry->laterality) === MedicationLaterality::RIGHT ? 'R' : 'NA';?>"
                            data-left="<?=intval($entry->laterality) === MedicationLaterality::LEFT ? 'L' : 'NA';?>"
                            data-route="<?=$entry->route;?>"
                        >
                            <input type="hidden" name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][id]"?>" value="<?=$entry->id?>">
                            <input type="hidden" name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][medication_id]"?>" value="<?=$entry->medication_id?>">
                            <td>
                            <?=$entry->medication->getLabel(true)?>
                                <span class="js-prepended_markup">
                                <?php if ($this->patient->hasDrugAllergy($entry->medication_id)) {?>
                                    <i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to <?=implode(',', $this->patient->getPatientDrugAllergy($entry->medication_id))?>"></i>
                                <?php }?>
                                <?=$this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id), true);?>
                                </span>
                            </td>
                            <td>
                            <?php if (!$entry->dose) {?>
                                <input class="fixed-width-small js-dose" type="text" name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][dose]"?>" id="<?=$model_name . "_assignment_{$key}_entries_{$entry_key}_dose"?>" value="" placeholder="Dose" />
                            <?php } else {?>
                                <input type="hidden" name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][dose]"?>" value="<?=$entry->dose?>">
                                <?=$entry->dose?>
                            <?php }?>
                            <?php
                            $dose_field_name = $model_name . "[assignment][{$key}][entries][{$entry_key}][dose_unit_term]";
                            $dose_field_value = $entry->dose_unit_term;
                            if (!$dose_field_value) {
                                $dose_dropdown_data = CHtml::listData(MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions, 'description', 'description');
                                $dose_dropdown_htmloptions = array(
                                    'empty' => '-Unit-',
                                    'class' => 'js-unit-dropdown cols-5',
                                );
                                ?>
                                <?=CHtml::dropDownList($dose_field_name, $dose_field_value, $dose_dropdown_data, $dose_dropdown_htmloptions);?>
                            <?php } else {?>
                                <input type="hidden" name="<?=$dose_field_name?>" value="<?=$dose_field_value?>">
                                <?=$dose_field_value?>
                            <?php } ?>
                            </td>
                            <td>
                                <?php
                                $route_field_name = $model_name . "[assignment][{$key}][entries][{$entry_key}][route_id]";
                                $route_field_value = $entry->route_id;
                                if (!$route_field_value) {
                                    $route_dropdown_data = CHtml::listData($element->getRouteOptions(), 'id', 'term');
                                    $route_dropdown_htmloptions = array(
                                        'empty' => '-Route-',
                                        'class' => 'js-route cols-10'
                                    );
                                    echo CHtml::dropDownList($route_field_name, $route_field_value, $route_dropdown_data, $route_dropdown_htmloptions);
                                    ?>
                                <?php } else {?>
                                    <input type="hidden" name="<?=$route_field_name?>" value="<?=$route_field_value?>">
                                    <?php if ($entry->route->isEyeRoute()) {?>
                                    <!-- rely on med route -->
                                    <span class="oe-eye-lat-icons">
                                        <input type="hidden" name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][laterality]"?>" value="<?=$entry->laterality?>">
                                        <i class="oe-i laterality <?=intval($entry->laterality) === MedicationLaterality::RIGHT ? 'R' : 'NA';?> small pad"></i>
                                        <i class="oe-i laterality <?=intval($entry->laterality) === MedicationLaterality::LEFT ? 'L' : 'NA';?> small pad"></i>
                                    </span>
                                    <?php } else {?>
                                        <?=$entry->route;?>
                                    <?php }?>
                                <?php } ?>
                            </td>
                            <td class="js-administer-user">

                                <?php if ($entry->administered) {?>
                                    <?=$entry->administered_user->getFullName();?>
                                <?php } else { ?>
                                    <!-- rely on administered field -->
                                    <small class="fade">Waiting to administer</small>
                                <?php } ?>
                            </td>
                            <td class="js-administer-date">
                                <?=$administered_nhs;?>
                            </td>
                            <td class="js-administer-time">
                                <?=$administered_time_ui;?>
                            </td>
                            <td>
                                <?php if ($hide_administer_switch) {?>
                                    <i class="oe-i tick medium pad selected"></i>
                                <?php }?>
                                <label
                                    class="toggle-switch <?=$disable_administer_btn?>"
                                    <?=$hide_administer_switch ? 'style="display:none"' : ''?>
                                >
                                    <input
                                        class="js-administer-med"
                                        type="checkbox"
                                        name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][administered]"?>"
                                        value="<?=$entry->administered?>"
                                    <?=$entry->administered ? 'checked' : ''?>
                                    >
                                    <div class="toggle-btn"></div>
                                    <input
                                        type="hidden"
                                        name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][administered_time]"?>"
                                        value="<?=$administered_ts * 1000;?>"
                                    >
                                </label>
                                <input type="hidden" name="<?=$model_name . "[assignment][{$key}][entries][{$entry_key}][administered_by]"?>" value="<?=$entry->administered_by?>">
                            </td>

                            <td class="js-entry-action">
                                <?php if ($is_preset) {?>
                                <i class="oe-i no-permissions small-icon js-has-tooltip" data-tooltip-content="Drugs within a Preset Order not be changed."></i>
                                <?php } else {?>
                                    <?php if ($row_action['can_remove']) {?>
                                        <i class="oe-i trash js-remove-med <?=$disable_administer_btn?>"></i>
                                    <?php } else {?>
                                        <i class="oe-i no-permissions small-icon js-has-tooltip" data-tooltip-content="<?=$row_action['msg']?>"></i>
                                    <?php }?>
                                <?php }?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- user comments -->
                <div class="cols-full comment-row" style="<?=$hiden_comment?>">
                    <!-- textarea & remove icon -->
                    <div class="flex-layout flex-left">
                        <textarea name="<?=$model_name . "[assignment][{$key}][comment]"?>" placeholder="Comments" autocomplete="off" rows="1" class="js-input-comments cols-full"><?=$assigned_psd->comment?></textarea>
                        <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                    </div>
                </div>
            </div>
            <!-- add comment button -->
            <div class="add-data-actions flex-item-bottom" style="<?=$hide_button?>">
                <button class="button js-add-comments">
                    <i class="oe-i comments small-icon "></i>
                </button>
            </div>
        </div>
        <?php
        if ($is_prescriber) {
            $this->render(
                'DrugAdministration_event_edit_appointments',
                array(
                    'assigned_psd' => $assigned_psd,
                    'model_name' => $model_name,
                    'available_appointments' => $available_appointments,
                    'is_prescriber' => $is_prescriber,
                    'is_active' => false,
                    'is_record_admin' => $is_record_admin,
                    'is_new' => $assigned_psd->isNewRecord,
                    'btn_text' => $is_record_admin ? 'Record order As Administered' : ($assigned_psd->pgdpsd_id ? 'Assign this Preset Order' : 'Assign Custom Order'),
                    'help_info' => $assigned_psd->pgdpsd_id ? 'Adding new Preset<br/><small>Preset orders can not be modified</small>' : 'Building custom order',
                    'is_confirmed' => $is_confirmed,
                    'assigned_appt' => $assigned_psd->visit_id,
                    'key' => $key
                ),
            );
        } else { ?>
        <hr class="divider">
        <?php } ?>
    </div>
    <?php } ?>
    <!-- rely on RBAC -->
    <div class="flex-r">
        <div class="add-data-actions flex-item-bottom js-add-meds-ctn">
            <?php if ($is_prescriber) {?>
                <button
                    id="js-add-preset-order"
                    class="green hint js-add-select-btn"
                >Add Preset Order</button>&nbsp;
            <?php } ?>
            <?php if ($is_med_admin || $is_prescriber) {?>
            <button
            id="js-add-medications"
            class="adder js-add-select-btn"
            ></button>
            <?php }?>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('DrugAdministration.js') ?>"></script>
<script type="text/template" class="hidden section-template">
    <?php
        $this->render(
            'DrugAdministration_event_edit_template',
            array(
                'model_name' => $model_name,
                'available_appointments' => $available_appointments,
                'is_prescriber' => $is_prescriber,
            ),
        );
        ?>
</script>
<script type="text/template" class="hidden section-entry-template">
    <?php
        $empty_entry = new \OphDrPGDPSD_AssignmentMeds();
        $this->render(
            'DrugAdministrationEntry_event_edit_template',
            array(
                'entry' => $empty_entry,
                'model_name' => $model_name,
                'field_prefix' => $field_prefix,
                'element' => $element,
            ),
        );
        ?>
</script>

<script>
    const laterality_opts = [
        {id: <?=$right_eye_id;?>, label: 'Right Eye', name: 'right', right: 'R', left: 'NA'},
        {id: <?=$left_eye_id;?>, label: 'Left Eye', name: 'left', right: 'NA', left: 'L'},
        {id: <?=$both_eye_id;?>, label: 'Right & Left Eyes', name: 'both', right: 'R', left: 'L'},
    ];
    const preset_adderpopup_id =  'presets';
    const eye_route_ids = <?=json_encode(MedicationRoute::model()->listEyeRouteIds());?>;
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
    const presetsReturn = function(adderDialog, selectedItems) {
        if(selectedItems.length < 2){
            return;
        }
        let selected_preset = selectedItems[0];
        let preset_meds = selected_preset.meds;
        const laterality = selectedItems[1];
        selected_preset['preset_type'] = 'Preset';
        selected_preset['preset_name'] = selected_preset['label'];
        selected_preset['laterality'] = laterality['id'];
        this.buildTemplate(selected_preset, laterality, preset_meds);
    }
    const medsReturn = function(adderDialog, selectedItems) {
        let meds = [];
        let laterality = null;
        let data = {};
        data['preset_name'] = "<?=$user['name'];?> (Custom)";
        data['preset_type'] = "Custom";
        selectedItems.forEach(function(item){
            if(!item.itemSet){
                item['medication_id'] = item['id'];
                meds.push(item);
                return;
            }
            switch(item.itemSet.options.id){
                case 'meds':
                    item['medication_id'] = item['id'];
                    meds.push(item);
                    break;
                case 'laterality':
                    laterality = item;
                    break;
                default:break;
            }
        });
        // laterality will be mandatory to those meds with eye route or without default route
        let alert = [];
        for(let med of meds){
            if(!med.route || eye_route_ids.includes(med.route_id.toString())){
                if(!laterality){
                    alert.push(med.label);
                } else {
                    data['laterality'] = laterality['id'];
                }
            }
        }
        if(alert.length){
            new OpenEyes.UI.Dialog.Alert({
                content: 'laterality needs to be selected for ' + alert.join(', ')
            }).open();
            return false;
        } else {
            this.buildTemplate(data, laterality, meds, true);
        }
    }
    const da = new OpenEyes.OphCiExamination.DrugAdministrationController({
        element: $("#<?=$element_id?>"),
        noPermissionIcon: '<i class="oe-i no-permissions small-icon js-has-tooltip" data-tooltip-content="Can not remove a drug already administered"></i>',
        trashIcon: '<i class="oe-i trash js-remove-med"></i>',
        is_prescriber: <?=json_encode($is_prescriber);?>,
        patientAllergies: <?= CJSON::encode($this->patient->getAllergiesId()) ?>,
        allAllergies: <?= CJSON::encode(CHtml::listData(OphCiExaminationAllergy::model()->findAll(), 'id', 'name')) ?>,
        laterality: {
            'right': <?=$right_eye_id;?>,
            'left': <?=$left_eye_id;?>,
            'both': <?=$both_eye_id;?>,
        },
        eyeRouteIds: eye_route_ids,
        currentUser: <?=json_encode($user);?>,
        nonAdministeredText: '<small class="fade">Waiting to administer</small>',
        adderPopupSetups: [
            {
                adderPopup: OpenEyes.UI.AdderDialog,
                openButton: $('#js-add-preset-order'),
                multiSelect: false,
                itemSetDataSource: [
                    {
                        source: <?=$presets;?>,
                        options: {
                            id: preset_adderpopup_id,
                            multiSelect: false,
                        }
                    },
                    {
                        source: laterality_opts,
                        options: {
                            id: 'laterality',
                            multiSelect: false,
                        }
                    },
                ],
                onReturn: presetsReturn,
            },
            {
                adderPopup: OpenEyes.UI.AdderDialog.MedSearch,
                openButton: $('#js-add-medications'),
                multiSelect: false,
                itemSetDataSource: [
                    {
                        source: category,
                        options: {
                            id: 'category',
                            multiSelect: false,
                        }
                    },
                    {
                        source: <?=json_encode($medication_options);?>,
                        options: {
                            id: 'meds',
                            multiSelect: true,
                        }
                    },
                    {
                        source: laterality_opts,
                        options: {
                            id: 'laterality',
                            multiSelect: false,
                        }
                    },
                ],
                onReturn: medsReturn,
            }
        ]
    });
</script>