<div class="oe-popup-wrap">
    <div class="oe-popup ">
        <div class="title">Preset Orders</div>
        <div class="close-icon-btn">
            <i class="oe-i remove-circle pro-theme" id="js-close-worklist-popup"></i>
        </div>
        <?php
            $form = $this->beginWidget(
                'BaseEventTypeCActiveForm',
                [
                    'id' => 'savePreset',
                    'enableAjaxValidation' => false,
                ]
            )
            ?>
        <div class="oe-popup-content build-psd">
            <!-- show selected patients -->
            <div class="psd-selected-patients">
                <h3>Patient(s)</h3>
                <ul>    
                </ul>
            </div>
            <!-- options for PSD -->
            <div class="psd-options">
                <!-- use flex to create sub columns -->
                <h3>Assign Preset Order</h3>
                <ul class="btn-list">
                    <?php foreach ($preset_orders as $preset_order) { ?>
                        <li
                            data-preset-name="<?=$preset_order->name?>"
                            data-preset-id="<?=$preset_order->id?>"
                            data-preset-meds='<?=$preset_order->getAssignedMedsInJSON(false)?>'
                        ><?=$preset_order->name?></li>
                    <?php }?>
                </ul>           
            </div>
            <!-- laterality for PSD (if required) -->
            <div class="psd-laterality">
                <h3>Preset Order: Laterality</h3>
                <ul class="btn-list">
                    <li data-laterality-id="<?=MedicationLaterality::RIGHT?>" data-laterality="right">Right Eye</li>
                    <li data-laterality-id="<?=MedicationLaterality::LEFT?>" data-laterality="left">Left Eye</li>
                    <li data-laterality-id="<?=MedicationLaterality::BOTH?>" data-laterality="both">Right &amp; Left Eye</li>       
                </ul>
            </div>
            
            
            <div class="psd-build">
                    <div id="js-validation-errors" class="alert-box warning with-icon hidden">
                        <p>Please fix the following input errors:</p>
                        <ul></ul>
                    </div>
                <!-- Selected Preset Details -->
            </div>
            
        </div><!-- .oe-popup-content -->
        <?php $this->endWidget() ?>
        <div class="popup-actions flex-right">
            <button id="js-save-preset" class="green hint">Assign Preset Order to patients</button>
        </div>
    </div>
</div>
<!-- selected preset -->
<script type="x-tmpl-mustache" id="psd_row_template" style="display:none">
    <div data-section-key="{{key}}" data-lat="{{laterality}}" class="js-order-section">
        <input type="hidden" name="PresetAssignment[presets][{{key}}][pgdpsd_id]" value="{{presetId}}">
        <h3>
            {{presetName}}
            <i class="oe-i remove-circle small-icon pad-left js-remove-preset"></i>
        </h3>
        <table>
            <tbody class="preset-meds-{{key}}">
            </tbody>
        </table>
    </div>
</script>
<!-- selected preset meds -->
<script type="x-tmpl-mustache" id="psd_med_row_template" style="display:none">
    <tr data-tr-key="{{tr_key}}">
        <input type="hidden" name="PresetAssignment[presets][{{key}}][entries][{{tr_key}}][laterality]" value="{{lateralityId}}">
        <input type="hidden" name="PresetAssignment[presets][{{key}}][entries][{{tr_key}}][medication_id]" value="{{medication_id}}">
        <input type="hidden" name="PresetAssignment[presets][{{key}}][entries][{{tr_key}}][dose]" value="{{dose}}">
        <input type="hidden" name="PresetAssignment[presets][{{key}}][entries][{{tr_key}}][dose_unit_term]" value="{{dose_unit_term}}">
        <input type="hidden" name="PresetAssignment[presets][{{key}}][entries][{{tr_key}}][route_id]" value="{{route_id}}">
        <td>
            <div class="drug">{{preferred_term}}</div>
        </td>
        <td>{{dose}} {{dose_unit_term}}</td>
        <td>
        {{^is_eye_route}}
            {{route}}
        {{/is_eye_route}}

        {{#is_eye_route}}
            <span class="oe-eye-lat-icons">
                <i class="oe-i laterality {{right}} small pad"></i>
                <i class="oe-i laterality {{left}} small pad"></i>
            </span>
        {{/is_eye_route}}
        </td>
    </tr>
</script>
<script>
    $(function () {
        let $preset_setup_ctn = $('.oe-popup-wrap .build-psd');
        let close_popup_btn_query = '#js-close-worklist-popup';

        let preset_lat_lis_query = '.psd-laterality li';
        let $preset_lat_lis = $preset_setup_ctn.find(preset_lat_lis_query);
        
        let preset_lis_query = '.psd-options li';
        let $preset_lis = $preset_setup_ctn.find(preset_lis_query);
        
        let selected_preset_ctn_query = '.psd-build';
        let $selected_preset_ctn = $preset_setup_ctn.find(selected_preset_ctn_query);
        
        let preset_delete_btn_query = '.js-remove-preset';
        let preset_save_btn_query = '#js-save-preset';

        // templates
        let $preset_row_template_ctn = $('#psd_row_template');
        let $preset_med_row_template_ctn = $('#psd_med_row_template');

        // error msg container
        let error_ctn = $('#js-validation-errors');
        let error_list_ctn = error_ctn.find('ul');
        let hidden_class = 'hidden';


        let selected_preset_obj = null;
        let selected_lat = null;

        function resetSelected(){
            // reset selected
            selected_preset_obj = null;
            selected_lat = null;
            $preset_lat_lis.removeClass('selected');
            $preset_lis.removeClass('selected');
        }
        function buildTemplate(selected_preset_param, selected_lat_param){
            if(!selected_preset_param || !selected_lat_param){
                return;
            }
            let selected_preset_id = selected_preset_param.presetId;
            selected_preset_param.key = (OpenEyes.Util.getNextDataKey($selected_preset_ctn.find('div.js-order-section'), 'section-key'));
            selected_preset_param.laterality = selected_lat_param.lateralityId;
            let preset_row_template = $preset_row_template_ctn.html();
            Mustache.parse(preset_row_template);
            let preset_row_render = Mustache.render(preset_row_template, selected_preset_param);
            $selected_preset_ctn.append(preset_row_render);

            let preset_med_row_template = $preset_med_row_template_ctn.html();
            Mustache.parse(preset_med_row_template);
            selected_preset_param.presetMeds.forEach(function(item, i){
                item.key = selected_preset_param.key;
                item.tr_key = (OpenEyes.Util.getNextDataKey($selected_preset_ctn.find('div.js-order-section table tr'), 'tr-key'));
                item.lateralityId = null;
                if(item.is_eye_route){
                    item.right = selected_lat.laterality === 'right' || selected_lat.laterality === 'both' ? 'R' : 'NA';
                    item.left = selected_lat.laterality === 'left' || selected_lat.laterality === 'both' ? 'L' : 'NA';
                    item.lateralityId = selected_lat.lateralityId;
                }
                let preset_med_row_render = Mustache.render(preset_med_row_template, item);
                $selected_preset_ctn.find(`.preset-meds-${selected_preset_param.key}`).append(preset_med_row_render);
            });
            resetSelected();
        }

        $(document).off('click', preset_lis_query).on('click', preset_lis_query, function(){
            $preset_lis.removeClass('selected');
            $(this).addClass('selected');
            selected_preset_obj = $(this).data();
            buildTemplate(selected_preset_obj, selected_lat);
        });
        $(document).off('click', preset_lat_lis_query).on('click', preset_lat_lis_query, function(){
            $preset_lat_lis.removeClass('selected');
            $(this).addClass('selected');
            selected_lat = $(this).data();
            buildTemplate(selected_preset_obj, selected_lat);
        });

        $(document).off('click', preset_delete_btn_query).on('click', preset_delete_btn_query, function(){
            $(this).closest('div').remove();
        });

        // click on popup close button
        // clear entries
        $(document).off('click', close_popup_btn_query).on('click', close_popup_btn_query, function (e) {
            $selected_preset_ctn.html("");

            $('.js-add-psd-popup').removeClass('js-psdpopup-opened').hide();
        });

        $(document).off('click', preset_save_btn_query).on('click', preset_save_btn_query, function(e){
            e.preventDefault();
            const context = $(this);
            context.attr('disabled', true).prop('disabled', true);
            error_list_ctn.html('');
            if(!$selected_preset_ctn.find('div[data-section-key]').length){
                error_list_ctn.html('');
                error_ctn.removeClass(hidden_class);
                error_list_ctn.append(
                    `<li>Please Assign at least 1 preset</li>`
                )
                context.attr('disabled', false).prop('disabled', false);
                return;
            }
            let form = $('form#savePreset');
            $.ajax({
                type: "POST",
                url: '/OphDrPGDPSD/PSD/createPSD',
                data: form.serialize(),
                success: function (data) {
                    if(!data['success'] && Object.keys(data['msg']).length > 0){
                        error_ctn.removeClass(hidden_class);
                        for(const error in data['msg']){
                            error_list_ctn.append(
                                `<li>${data['msg'][error]}</li>`
                            )
                        }
                    } else {
                        error_ctn.removeClass(hidden_class).removeClass('warning').addClass('success');
                        error_ctn.find('p').text(data['msg']);
                        window.location.reload();
                    }
                },
                complete: function(){
                    context.attr('disabled', false).prop('disabled', false);
                }
            });
        });
    });
</script>