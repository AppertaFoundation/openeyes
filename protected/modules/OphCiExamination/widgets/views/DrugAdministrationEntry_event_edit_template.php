<!-- trs can be put into entry file and template -->
<tr 
    data-entry-key="{{entry_key}}" 
    data-entry_key="{{entry_key}}" 
    data-pair-key="{{pair_key}}" 
    data-pair_key="{{pair_key}}" 
    data-is-preset="{{is_preset}}" 
    data-preferred_term="{{preferred_term}}" 
    data-allergy_warning="{{allergy_warning}}" 
    data-prepended_markup="{{prepended_markup}}" 
    data-medication_id="{{medication_id}}" 
    data-dose="{{dose}}"
    data-dose_unit_term="{{dose_unit_term}}"
    data-route_id="{{route_id}}"
    data-laterality="{{laterality}}"
    data-init_lat="{{init_lat}}"
    data-right="{{right}}"
    data-left="{{left}}"
    data-route="{{route}}"
>
    <input type="hidden" name="<?=$field_prefix?>" value="{{entry_key}}">
    <input type="hidden" name="<?=$field_prefix?>[pair_key]" value="{{pair_key}}">
    <input type="hidden" name="<?=$field_prefix?>[medication_id]" value="{{medication_id}}">
    <td>
        {{preferred_term}}
        <span class="js-prepended_markup">
            {{& allergy_warning}}
            {{& prepended_markup}}
        </span>
    </td>
    <td>
        {{#dose}}
            {{dose}}
            <input type="hidden" name="<?=$field_prefix?>[dose]" value="{{dose}}">
        {{/dose}}
        {{^dose}}
            <input class="fixed-width-small js-dose input-validate numbers-only decimal" id="<?=$model_name?>_assignment_{{section_key}}_entries_{{entry_key}}_dose" type="text" name="<?=$field_prefix?>[dose]" value="" placeholder="Dose"/>
        {{/dose}}
        {{#dose_unit_term}}
            {{dose_unit_term}}
            <input type="hidden" name="<?=$field_prefix?>[dose_unit_term]" value="{{dose_unit_term}}">
        {{/dose_unit_term}}
        {{^dose_unit_term}}
            <?php
                echo CHtml::dropDownList(
                    $field_prefix . '[dose_unit_term]',
                    $entry->dose_unit_term,
                    CHtml::listData(MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions, 'description', 'description'),
                    [
                        'empty' => '-Unit-',
                        'class' => 'js-unit-dropdown cols-5',
                    ]
                );
                ?>
        {{/dose_unit_term}}

    </td>
    <td>
        {{#route}}
            <input type="hidden" name="<?=$field_prefix?>[route_id]" value="{{route_id}}">
            <!-- rely on med route -->
            {{#is_eye_route}}
            <span class="oe-eye-lat-icons">    
                <input type="hidden" name="<?=$field_prefix?>[laterality]" value="{{laterality}}">                                
                <i class="oe-i laterality {{right}} small pad"></i>
                <i class="oe-i laterality {{left}} small pad"></i>
            </span>
            {{/is_eye_route}}
            
            {{^is_eye_route}}
            {{route}}
            {{/is_eye_route}}
        {{/route}}

        {{^route}}
            <?php
            $dropdown_list_name = $field_prefix . '[route_id]';
            $dropdown_select = $entry->route_id;
            $dropdown_data = CHtml::listData($element->getRouteOptions(), 'id', 'term');
            $dropdown_htmloptions = array(
                'empty' => '-Route-',
                'class' => 'js-route cols-10',
            );
            echo CHtml::dropDownList($dropdown_list_name, $dropdown_select, $dropdown_data, $dropdown_htmloptions);
            ?>
        {{/route}}
    </td>
    <td class="js-administer-user">
        <!-- rely on administered field -->
        <small class="fade">Waiting to administer</small>
    </td>
    <td class="js-administer-date"></td>
    <td class="js-administer-time"></td>
    <td>
        <label class="toggle-switch">
            <input class="js-administer-med" type="checkbox" name="<?=$field_prefix?>[administered]">
            <div class="toggle-btn"></div>
            <input type="hidden" name="<?=$field_prefix?>[administered_time]" value="">
        </label>
        <input type="hidden" name="<?=$field_prefix?>[administered_by]" value="">
    </td>
    
    <td class="js-entry-action">
        {{#is_preset}}
        <i class="oe-i no-permissions small-icon js-has-tooltip" data-tooltip-content="Drugs within a Preset Order cannot be changed."></i>
        {{/is_preset}}
        {{^is_preset}}
        <i class="oe-i trash js-remove-med"></i>
        {{/is_preset}}
    </td>
</tr>
