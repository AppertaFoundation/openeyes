<div class="order-block" data-key="{{key}}" data-section-name="{{preset_name}}" data-laterality="{{laterality}}">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][pgdpsd_name]"?>" value="{{preset_name}}">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][pgdpsd_id]"?>" value="{{id}}">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][assignment_id]"?>">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][visit_id]"?>">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][confirmed]"?>" value="0">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][is_pgd]"?>" value="{{is_pgd}}">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][create_wp]"?>" value="0">
    <input type="hidden" name="<?=$model_name . "[assignment][{{key}}][active]"?>" value="1">
    <div class="flex row">
        <div class="flex-l">
            <!-- rely on pgdpsd id null or not -->
            <!-- Preset | Custom -->
            <div class="drug-admin-box inline todo">
                {{preset_type}}
            </div>&emsp;
            <div class="large-text">
                <!-- preset name -->
                {{preset_name}}
                <span class="js-appt-details"></span>
            </div>
        </div>
        <div class="flex-r">
            <button class="red hint js-cancel-preset js-after-confirm" style="display:none;">Remove Block</button>
            <!-- rely on worklist -->
            &emsp;<span class="js-validate-date"></span>
        </div>
    </div>
    <div class="flex">
        <div class="cols-11">
            <table class="cols-full js-entry-table">
                <colgroup>
                    <col class="cols-4">
                    <col class="cols-1">
                    <col class="cols-1">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                    <tr>
                        <th>Drug</th>
                        <th>Dose</th>
                        <th>Route</th>
                        <th>Administered by</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th class="js-administer-all" style="cursor:pointer;"></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <!-- user comments -->
            <div class="cols-full comment-row" style="display: none;">
                <!-- textarea & remove icon -->
                <div class="flex-layout flex-left ">
                    <textarea name="<?=$model_name . "[assignment][{{key}}][comment]"?>" placeholder="Comments" autocomplete="off" rows="1" class="js-input-comments cols-full"></textarea>
                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
            </div>
        </div>
        <!-- add comment button -->
        <div class="add-data-actions flex-item-bottom">
            <button class="button js-add-comments">
            <i class="oe-i comments small-icon "></i>
            </button>
        </div>
    </div>
    <?php $this->render(
        'DrugAdministration_event_edit_appointments',
        array(
            'assigned_psd' => null,
            'model_name' => $model_name,
            'available_appointments' => $available_appointments,
            'is_prescriber' => $is_prescriber,
            'is_active' => false,
            'is_new' => true,
            'is_record_admin' => false,
            'btn_text' => null,
            'help_info' => null,
            'is_confirmed' => false,
            'assigned_appt' => 0,
            'key' => '{{key}}'
        ),
    )?>
</div>