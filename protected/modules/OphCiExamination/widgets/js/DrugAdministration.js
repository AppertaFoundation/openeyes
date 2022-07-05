var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports, Util) {
    const HistoryMedicationsController = exports.HistoryMedicationsController;

    function DrugAdministrationController(options) {
        this.options = $.extend(true, {}, DrugAdministrationController._defaultOptions, HistoryMedicationsController._defaultOptions, options);
        this.temp_meds = {};
        this.record_admin_btn_text = 'Record order As Administered';
        this.assign_preset_btn_text = 'Assign this Preset Order';
        this.assign_custom_btn_text = 'Assign Custom Order';
        this.custom_help_info = 'Building custom order';
        this.preset_help_info = 'Adding new Preset<br/><small>Preset orders can not be modified</small>';
        this.init();
    }

    DrugAdministrationController._defaultOptions = {
        modelName: '',
        element: undefined,
        elementContainerClass: 'element-fields',
        parentClass: 'order-block',
        cancelBtnClass: 'js-cancel-preset',
        confirmBtnClass: 'js-confirm-preset',
        apptCtnClass: 'assign-for',
        addMedsBtnCtnClass: 'js-add-meds-ctn',
        rmMedBtnClass: 'js-remove-med',
        delPresetBtnClass: 'js-delete-preset',
        revertDelPresetBtnClass: 'js-revert-preset-del',
        delPresetBtnHtml: `<button class="red hint js-delete-preset js-after-confirm">Cancel remaining items</button>`,
        revertDelPresetBtnHtml: `<button class="blue hint js-revert-preset-del">Revert</button>`,
        commentBtnClass: 'js-add-comments',
        commentCtnClass: 'comment-row',
        rmCommentBtnClass: 'js-remove-add-comments',
        assignOrderCtnClass: 'js-assign-order-ctn',
        assignOrderBtnClass: 'js-assign-order',
        recordAdminRadioClas: 'js-record-admin',
        assignmentErrorCtnClass: 'js-assignment-errors',
        appointmentDetailsCtnClass: 'js-appt-details',
        validDateCtn: 'js-validate-date',
        entryActionClass: 'js-entry-action',
        administerAllBtnClass: 'js-administer-all',
        administerBtnClass: 'js-administer-med',
        administerDateTdClass: 'js-administer-date',
        administerUserTdClass: 'js-administer-user',
        administerTimeTdClass: 'js-administer-time',
        sectionTemplateClass: 'section-template',
        sectionEntryTemplateClass: 'section-entry-template',
        templateTargetSelector: '.element-fields > .flex-r',
        medsSearchSource: '/medicationManagement/findRefMedications',
        fields:[
            'preferred_name',
            'medication_id',
            'dose',
            'dose_unit_term',
            'route_id',
            'laterality',
            'start_date',
            'end_date',
            'usage_type',
            'prepended_markup',
        ]
    };
    Util.inherits(HistoryMedicationsController, DrugAdministrationController);

    DrugAdministrationController.prototype.init = function(){
        const controller = this;
        $(`.${this.options.parentClass}`).each(function(i, block){
            controller.orderActionControll($(block));
        });
        this.addMedsBtnsControl();
        this.bindCancelPresetBtn();
        this.bindDeletePresetBtn();
        this.bindRevertPresetDeleteBtn();
        this.bindAdministerBtn();
        this.bindRemoveMedBtn();
        this.bindAddComment();
        this.bindRmComment();
        this.bindRouteOpt();
        this.initAdderPopups();
        this.bindAdministerAllBtn();
        this.bindTimeChange();
        this.bindAppointmentSelection();
        this.bindConfirmBlockClick();
    }

    DrugAdministrationController.prototype.buildApptDetailsDisplay = function(date, time, clinic){
        return `<i class='oe-i small start pad'></i><span>${date}<span class='fade'><small> at </small>${time} </span>${clinic}</span>`;
    }

    DrugAdministrationController.prototype.buildReadOnlyAdministerCell = function($parent){
        const $administered_entries = $parent.find('input[name$="[administered]"][value="1"]')
        $parent.find('input[name$="[administered]"][value="1"]').closest('td').each(function(i, td){
            if($(td).find('i.tick').length){
                return
            }
            $('<i class="oe-i tick medium pad selected"></i>').insertBefore($(td).find('.toggle-switch'));
        });
        $administered_entries.closest('.toggle-switch').addClass('disabled').hide();
    }

    // click event on buttons 'Assign Custom Order', 'Assign this Preset Order' and 'Record order As Administered'
    // these buttons are actually the same, but with different texts
    DrugAdministrationController.prototype.bindConfirmBlockClick = function(){
        const controller = this;
        const confirm_btn_selector = `.${controller.options.confirmBtnClass}`;
        const parent_selector = `.${controller.options.parentClass}`;
        const appt_ctn_selector = `.${this.options.apptCtnClass}`;
        controller.options.element.off('click', confirm_btn_selector).on('click', confirm_btn_selector, function(e){
            e.preventDefault();
            const $parent = $(this).closest(parent_selector);
            const $appt_selection = $parent.find(`${appt_ctn_selector} input:checked:not(:disabled)`);
            const $disabled_appt_selection = $parent.find(`${appt_ctn_selector} input:disabled`);
            // if no appointment selected and meds are not recorded as administered, display popup
            if(!$appt_selection.length && !$disabled_appt_selection.length){
                new OpenEyes.UI.Dialog.Alert({
                    content: 'Please select an appointment date or administer all of the listed medications'
                }).open();
                return false;
            }
            $parent.find('input[name$="[confirmed]"]').val(1);
            // if the block is assigned to a date
            if($appt_selection.length){
                const appt_date = $appt_selection.data('appt-date');
                const appt_valid_date = $appt_selection.data('appt-valid-date');
                const appt_time = $appt_selection.data('appt-time');
                const appt_clinic = $appt_selection.data('appt-clinic');
                const is_for_today = $appt_selection.data('for-today');
                if(!is_for_today){
                    $parent.addClass('fade');
                }
                if(!$appt_selection.hasClass('unbooked')){
                    $parent.find('input[name$="[visit_id]"]').val($appt_selection.val());
                    $parent.find('input[name$="[create_wp]"]').val(0);
                } else {
                    $parent.find('input[name$="[create_wp]"]').val(1);
                    $parent.find('input[name$="[visit_id]"]').val(null);
                }
                $parent.find('.js-appt-details').html(controller.buildApptDetailsDisplay(appt_date, appt_time, appt_clinic));
                $parent.find('.js-validate-date').addClass('highlighter').text(`Assigned for: ${appt_valid_date}`);
            } else {
                // if the block is for record as administrated, lock the meds
                controller.buildReadOnlyAdministerCell($parent);
            }
            // remove the appointment section
            $parent.find('.da-order-options').remove();
            // re-enable 'Add preset' and/or '+' buttons
            controller.addMedsBtnsControl();
        });
    }

    DrugAdministrationController.prototype.addMedsBtnsControl = function(){
        let add_meds_btn_disabled = false;
        let add_preset_btn_disabled = false;
        const parent_selector = `.${this.options.parentClass}`;
        const $non_confirmed_block = $(parent_selector).find('input[name$="[confirmed]"][value="0"]');
        const add_meds_btn_ctn_selector = `.${this.options.addMedsBtnCtnClass}`;
        const assign_order_ctn_selector = `.${this.options.assignOrderCtnClass}`;
        // if there is non confirmed block and the user is a prescriber
        if($non_confirmed_block.length && this.options.is_prescriber){
            add_meds_btn_disabled = $non_confirmed_block.closest(parent_selector).find('input[name$="[pgdpsd_id]"]').val() ? true : false;
            add_preset_btn_disabled = true;
        }
        if(!add_meds_btn_disabled && !add_preset_btn_disabled){
            $(parent_selector).find('.js-after-confirm').show();
            $(parent_selector).each(function(i, section){
                if(!$(section).find('hr.divider').length && !$(section).find(assign_order_ctn_selector).length){
                    $(section).append('<hr class="divider">');
                }
            })
        }
        $(add_meds_btn_ctn_selector).find('button#js-add-preset-order').prop('disabled', add_preset_btn_disabled);
        $(add_meds_btn_ctn_selector).find('button#js-add-medications').prop('disabled', add_meds_btn_disabled);
    }

    DrugAdministrationController.prototype.initAdderPopups = function(){
        if(!this.options.adderPopupSetups){
            return;
        }
        const controller = this;
        const adderPopupSetups = controller.options.adderPopupSetups;
        adderPopupSetups.forEach(function(item){
            new item['adderPopup']({
                openButton: item['openButton'],
                multiSelect: item['multiSelect'],
                itemSets: item['itemSetDataSource'].map(function(data){
                    return new OpenEyes.UI.AdderDialog.ItemSet(data['source'], data['options']);
                }),
                onReturn: item['onReturn'].bind(controller),
            })
        })
    }

    DrugAdministrationController.prototype.bindAppointmentSelection = function(){
        const controller = this;
        const parent_selector = `.${controller.options.parentClass}`;
        const assign_order_ctn_selector = `.${controller.options.assignOrderCtnClass}`;
        const appoint_radio_btns_selector = `${assign_order_ctn_selector} input[type="radio"]`;
        controller.options.element.off('change', appoint_radio_btns_selector).on('change', appoint_radio_btns_selector, function(){
            const $this_parent = $(this).closest(parent_selector);
            // remove other radio buttons checked attribute
            $this_parent
            .find(appoint_radio_btns_selector)
            .removeAttr('checked')
            .removeProp('checked');

            // add checked attribute to current selected
            $(this).attr('checked', true).prop('checked', true);
            $this_parent
            .find('input.js-assignment-options')
            .siblings('span')
            .removeClass('highlighter good');

            $this_parent
            .find('input.js-assignment-options:checked')
            .siblings('span')
            .addClass('highlighter good');
            if($(this).data('for-today')){
                $this_parent.find('.js-administer-med').closest('.toggle-switch').removeClass('disabled');
            } else {
                $this_parent.find('.js-administer-med')
                .prop('checked', false)
                .attr('checked', false)
                .trigger('change');
                $this_parent.find('.js-administer-med').closest('.toggle-switch').addClass('disabled');
            }
        });
    }

    // bind click event on add comment button
    DrugAdministrationController.prototype.bindAddComment = function(){
        const controller = this;
        const parent_selector = `.${controller.options.parentClass}`;
        const add_comment_btn_selector = `.${controller.options.commentBtnClass}`;
        const comment_ctn_selector = `.${controller.options.commentCtnClass}`;
        controller.options.element.off('click', add_comment_btn_selector).on('click', add_comment_btn_selector, function(e){
            e.preventDefault();
            $(this).closest(parent_selector).find(comment_ctn_selector).show();
            $(this).closest('div').hide();
        });
    }
    // bind click event on remove comment button
    DrugAdministrationController.prototype.bindRmComment = function(){
        const controller = this;
        const parent_selector = `.${controller.options.parentClass}`;
        const add_comment_btn_selector = `.${controller.options.commentBtnClass}`;
        const rm_comment_btn_selector = `.${controller.options.rmCommentBtnClass}`;
        const comment_ctn_selector = `.${controller.options.commentCtnClass}`;
        controller.options.element.off('click', rm_comment_btn_selector).on('click', rm_comment_btn_selector, function(e){
            e.preventDefault();
            $(this).closest(parent_selector).find(add_comment_btn_selector).show();
            $(this).closest(parent_selector).find(add_comment_btn_selector).closest('div').show();
            $(this).closest(parent_selector).find(comment_ctn_selector).hide();
            $(this).siblings('textarea').val(null);
        });
    }
    // bind click event on remove preset button
    DrugAdministrationController.prototype.bindCancelPresetBtn = function(){
        const controller = this;
        const cancel_btn_selector = `.${controller.options.cancelBtnClass}`;
        const parent_selector = `.${controller.options.parentClass}`;
        controller.options.element.off('click', cancel_btn_selector).on('click', cancel_btn_selector, function(e){
            e.preventDefault();
            $(this).parents(parent_selector).remove();
            controller.addMedsBtnsControl();
        })
    }

    /**
     * Delete preset button displays in edit mode for saved assignments
     */
    DrugAdministrationController.prototype.bindDeletePresetBtn = function(){
        const controller = this;
        const del_preset_btn_selector = `.${controller.options.delPresetBtnClass}`;
        controller.options.element.off('click', del_preset_btn_selector).on('click', del_preset_btn_selector, function(e){
            e.preventDefault();
            controller.toggleDeleted(this);
        })
    }
    /**
     * Revert Delete button displays after the Delete preset button is clicked
     */
    DrugAdministrationController.prototype.bindRevertPresetDeleteBtn = function(){
        const controller = this;
        const revert_del_preset_btn_selector = `.${controller.options.revertDelPresetBtnClass}`;
        controller.options.element.off('click', revert_del_preset_btn_selector).on('click', revert_del_preset_btn_selector, function(e){
            e.preventDefault();
            controller.toggleDeleted(this, false);
        })
    }
    /**
     * Toggle the section classes, activate and deactivate the button actions
     * @param {object} btn button object
     * @param {boolean} for_delete indicate if the action is an delete action. Default to true, revert delete if false
     */
    DrugAdministrationController.prototype.toggleDeleted = function(btn, for_delete = true){
        const btn_to_replace = for_delete ? `${this.options.revertDelPresetBtnHtml}` : `${this.options.delPresetBtnHtml}`;
        const val = for_delete ? 0 : 1;
        const parent_selector = `.${this.options.parentClass}`;
        const administer_btn_selector = `.${this.options.administerBtnClass}`;
        const del_med_btn_selector = `.${this.options.rmMedBtnClass}`;
        const $parent = $(btn).parents(parent_selector);
        const $admin_btns = $parent.find(administer_btn_selector);
        const $trash_btns = $parent.find(del_med_btn_selector);
        let block_classes = ['status-box', 'red', 'fade'];
        let switch_classes = 'disabled';
        $parent.find('input[name$="[active]"]').val(val);
        const is_relevant = $parent.find('input[name$="[is_relevant]"]').val();
        if (parseInt(is_relevant) === 0) {
            block_classes = block_classes.filter(sc => sc !== 'fade');
            switch_classes = '';
        }
        $parent.toggleClass(block_classes.join(' '));
        $admin_btns.closest('.toggle-switch').toggleClass(switch_classes);
        $trash_btns.toggleClass(switch_classes);
        $(btn).replaceWith(btn_to_replace);
    }
    // bind click event on administer all button
    DrugAdministrationController.prototype.bindAdministerAllBtn = function(){
        const controller = this;
        const administer_all_btn_selector = `.${controller.options.administerAllBtnClass}`;
        const administer_btn_selector = `.${controller.options.administerBtnClass}`;
        const parent_selector = `.${controller.options.parentClass}`;
        controller.options.element.off('click', administer_all_btn_selector).on('click', administer_all_btn_selector, function(){
            const $admin_btns = $(this).closest(parent_selector).find(administer_btn_selector);
            if($admin_btns.closest('.toggle-switch').hasClass('disabled')){
                return;
            }
            $(this).closest(parent_selector).find(administer_btn_selector).trigger('click');
        })
    }

    DrugAdministrationController.prototype.switchEntryActionIcon = function($current_tr, checked){
        const tr_data = $current_tr.data();
        if(!this.options.noPermissionIcon || !this.options.trashIcon || tr_data['isPreset']){
            return;
        }
        const action_td_selector = `.${this.options.entryActionClass}`;
        const $action_td = $current_tr.find(action_td_selector);
        const icon = checked ? this.options.noPermissionIcon : this.options.trashIcon;
        $action_td.html(icon);
    }
    // bind change event on administer button
    DrugAdministrationController.prototype.bindAdministerBtn = function(){
        const controller = this;
        const administer_btn_selector = `.${controller.options.administerBtnClass}`;
        const administer_date_ctn_selector = `.${controller.options.administerDateTdClass}`;
        const administer_time_ctn_selector = `.${controller.options.administerTimeTdClass}`;
        const administer_user_ctn_selector = `.${controller.options.administerUserTdClass}`;
        const parent_selector = `.${controller.options.parentClass}`;
        controller.options.element.off('change', administer_btn_selector).on('change', administer_btn_selector, function(e){
            const $current_tr = $(this).closest('tr');
            const $parent_td = $(this).closest('td');
            const parent_block = this.closest(parent_selector);
            // the following if statement limits the user to administer all the meds for a confirmed block which is assigned to a date
            // if the user wants to administer all of the meds, they should do it in "Record order as administered" mode
            if(parseInt(parent_block.querySelector('input[name$="[confirmed]"]').value)
            && !parseInt(parent_block.querySelector('input[name$="[assignment_id]"]').value)
            && (parent_block.querySelector('input[name$="[visit_id]"]').value || parent_block.querySelector('input[name$="[create_wp]"]').value)
            && (parent_block.querySelectorAll('tbody tr').length === 1
            || parent_block.querySelectorAll(`tbody tr ${administer_btn_selector}:checked`).length === parent_block.querySelectorAll('tbody tr').length
            )){
                $(this).attr('checked', false).prop('checked', false);
                return;
            }
            if($(this).attr('checked')){
                $(this).val('1');
                const today = new Date();
                const today_date = Util.formatDateToDisplayString(today);
                let time_val = `${('0' + today.getHours()).slice(-2)}:${('0' + today.getMinutes()).slice(-2)}`;
                $current_tr.find(administer_date_ctn_selector).html(today_date);
                $current_tr.find(administer_user_ctn_selector).html(controller.options.currentUser['name']);
                $current_tr.find(administer_time_ctn_selector).html(`<input type="time" value="${time_val}"/>`);
                $parent_td.find('input[name$="[administered_by]"]').val(controller.options.currentUser['id']);
                $(this).siblings('input').val(Date.now());
            } else {
                $(this).val('0');
                $current_tr.find(administer_date_ctn_selector).text('');
                $current_tr.find(administer_user_ctn_selector).html(controller.options.nonAdministeredText);
                $current_tr.find(administer_time_ctn_selector).html('');
                $parent_td.find('input').val('');
            }
            controller.orderActionControll($(parent_block));
        });
    }

    // before a block is confirmed, administer all meds will hide the appointment dates and disable the selections
    DrugAdministrationController.prototype.orderActionControll = function($parent_block){
        const confirm_btn_selector = `.${this.options.confirmBtnClass}`;
        const appt_ctn_selector = `.${this.options.apptCtnClass}`;
        const administer_btn_selector = `.${this.options.administerBtnClass}`;
        if($parent_block.find('tbody tr').length === $parent_block.find(`tbody tr ${administer_btn_selector}:checked`).length){
            $parent_block.find(confirm_btn_selector).text(this.record_admin_btn_text);
            $parent_block.find(`${appt_ctn_selector} input`).prop('disabled', true);
            $parent_block.find(appt_ctn_selector).hide();
        } else {
            if($parent_block.find('input[name$="[pgdpsd_id]"]').val()){
                $parent_block.find(confirm_btn_selector).text(this.assign_preset_btn_text);
            } else {
                $parent_block.find(confirm_btn_selector).text(this.assign_custom_btn_text);
            }
            $parent_block.find(`${appt_ctn_selector} input`).prop('disabled', false);
            $parent_block.find(appt_ctn_selector).show();
        }
    }
    // bind change event on time input
    DrugAdministrationController.prototype.bindTimeChange = function(){
        const controller = this;
        const administer_btn_selector = `.${controller.options.administerBtnClass}`;
        const timeInputSelector = `.${controller.options.administerTimeTdClass} input`
        controller.options.element.off('change', timeInputSelector).on('change', timeInputSelector, function(e){
            const $parent_tr = $(this).closest('tr');
            const today = new Date().toDateString()
            const timestamp = new Date(`${today} ${$(this).val()}`).getTime();
            const $admin_btn = $parent_tr.find(administer_btn_selector);
            $admin_btn.siblings('input').val(timestamp);
        });
    }

    DrugAdministrationController.prototype.buildEntryTemplate = function($container ,data, laterality){
        const controller = this;
        const administer_btn_selector = `.${controller.options.administerBtnClass}`;

        data['allergy_warning'] = controller.getAllergyWarning(data);
        const section_entry_template_class = `.${controller.options.sectionEntryTemplateClass}`;
        let section_entry_template = $(section_entry_template_class).html();
        Mustache.parse(section_entry_template);
        let section_entry_template_render = Mustache.render(section_entry_template, data);
        $container.find('tbody').append(section_entry_template_render);
        if(!controller.options.is_prescriber){
            $container.find(`tr[data-entry-key="${data['entry_key']}"] ${administer_btn_selector}`)
            .prop('checked', true)
            .attr('checked', true)
            .trigger('change').closest('label').addClass('disabled');
            controller.buildReadOnlyAdministerCell($container);
        }
        controller.orderActionControll($container);
    }

    // bind change event on route option dropdown
    DrugAdministrationController.prototype.bindRouteOpt = function(){
        const controller = this;
        const parent_selector = `.${controller.options.parentClass}`;
        const eye_route_ids = controller.options.eyeRouteIds;
        const assign_order_ctn_selector = `.${controller.options.assignOrderCtnClass}`;
        const appoint_radio_btns_selector = `${assign_order_ctn_selector} input[type="radio"]`;
        controller.options.element.off('change', controller.options.routeFieldSelector).on('change', controller.options.routeFieldSelector, function(){
            const $current_row = $(this).closest('tr');
            let data = $current_row.data();

            const $current_container = $(this).closest(parent_selector);
            const lat_id = $current_row.data('init_lat');
            let laterality = {name: ''};
            let tr_key = Util.getNextDataKey($(`${parent_selector} tbody tr`), 'entry-key');
            if(eye_route_ids.includes(this.value)){
                data['is_eye_route'] = true;
            }
            Object.keys(controller.options.laterality).forEach(function(item, id){
                if(controller.options.laterality[item] === lat_id){
                    laterality['name'] = item;
                }
            });
            data['route'] = this.options[this.selectedIndex].text;
            data['route_id'] = this.value;
            data['section_key'] = $current_container.data('key');
            if(!data['allergy_warning']){
                const allergy_warning = $current_row.find('td .js-prepended_markup .warning');
                data['allergy_warning'] = allergy_warning.length > 0 ? allergy_warning[0].outerHTML : null;
            }
            if(!data['prepended_markup']){
                const prepended_markup = $current_row.find('.js-prepended_markup .formulary,.info');
                data['prepended_markup'] = prepended_markup.length > 0 ? prepended_markup[0].outerHTML : null;
            }
            controller.processEntry(tr_key, $current_container, data, laterality);
            $current_row.remove();
            $current_container.find(`${appoint_radio_btns_selector}:checked`).trigger('change');
        });
    }
    DrugAdministrationController.prototype.buildTemplate = function(preset, laterality, meds, is_custom = false){
        const controller = this;
        const parent_selector = `.${controller.options.parentClass}`;
        const section_template_class = `.${controller.options.sectionTemplateClass}`;
        preset['valid_date'] = Util.formatDateToDisplayString(new Date());
        let section_template = $(section_template_class).html();
        let existing_sections = document.querySelectorAll(parent_selector);
        let open_section = null;
        existing_sections.forEach(function(section){
            if(section.querySelector('input[name$="[confirmed]"][value="0"]') && !section.querySelector('input[name$="[pgdpsd_id]"]').value){
                open_section = section;
                return;
            }
        });
        let $open_section = $(open_section);
        let key = $open_section.data('key');
        const add_meds_btns_ctn_selector = `.${controller.options.addMedsBtnCtnClass}`;
        preset['key'] = key;
        if(preset['is_preset']){
            preset['btn_text'] = controller.assign_preset_btn_text;
            preset['help_info'] = controller.preset_help_info;
            $(add_meds_btns_ctn_selector).find('button').prop('disabled', true);
        } else {
            preset['btn_text'] = controller.assign_custom_btn_text;
            preset['help_info'] = controller.custom_help_info;
            $(add_meds_btns_ctn_selector).find('button#js-add-preset-order').prop('disabled', true);
        }
        if(!$open_section.length || !is_custom){
            key = (Util.getNextDataKey($(`${parent_selector}`), 'key'));
            preset['key'] = key;
            Mustache.parse(section_template);
            let section_template_render = Mustache.render(section_template, preset);
            $(section_template_render).insertBefore($(controller.options.templateTargetSelector));
            $open_section = $(`${parent_selector}[data-key=${preset['key']}]`);
        }
        meds.sort((a, b) => (a.is_eye_route > b.is_eye_route) ? (a.preferred_term < b.preferred_term) ? 1 : -1 : 0);
        meds.forEach(function(item, i){
            let tr_key = Util.getNextDataKey($(`${$open_section.selector} tbody tr`), 'entry-key');
            if(laterality){
                item['left'] = laterality.left;
                item['right'] = laterality.right;
                item['init_lat'] = laterality.id;
            }
            item['entry_key'] = tr_key;
            item['section_key'] = key;
            item['is_preset'] = preset['is_preset'];
            controller.processEntry(tr_key, $open_section, item, laterality)
        });
        controller.addMedsBtnsControl();
    }
    DrugAdministrationController.prototype.processEntry = function(tr_key, $existing_section, data, laterality){
        const pair_key = tr_key + 1;
        const controller = this;
        if(data['is_eye_route'] && laterality['name'] === 'both'){
            [{'side': 'right', 'css': 'R', 'opp': 'left'}, {'side': 'left', 'css': 'L', 'opp': 'right'}].forEach(function(lat, i){
                data[lat['side']] = lat['css'];
                data[lat['opp']] = 'NA';
                data['laterality'] = controller.options.laterality[lat['side']];
                data['pair_key'] = pair_key;
                controller.buildEntryTemplate($existing_section, data, laterality);
                data['entry_key']++;
            });
        } else {
            data['laterality'] = data['is_eye_route'] ? controller.options.laterality[laterality['name']] : null;
            controller.buildEntryTemplate($existing_section, data, laterality);
        }
    }
    // bind click event on remove medication button
    DrugAdministrationController.prototype.bindRemoveMedBtn = function(){
        const controller = this;
        const rm_med_btn_selector = `.${controller.options.rmMedBtnClass}`;
        const cancel_btn_selector = `.${controller.options.cancelBtnClass}`;
        const parent_selector = `.${controller.options.parentClass}`;
        controller.options.element.off('click', rm_med_btn_selector).on('click', rm_med_btn_selector, function(){
            const $current_tr = $(this).closest('tr');
            const $current_tbody = $(this).closest('tbody');
            $current_tr.remove();
            if(!$current_tbody.children().length){
                $current_tbody.closest(parent_selector).find(cancel_btn_selector).trigger('click');
            }
        });
    }
    exports.DrugAdministrationController = DrugAdministrationController;
})(OpenEyes.OphCiExamination, OpenEyes.Util);