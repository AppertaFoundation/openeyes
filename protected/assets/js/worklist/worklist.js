$(function () {
    function getSelectedPatients() {
        let patient_list = {};
        const cps = $('.js-select-patient-for-psd');
        cps.each(function(i, item){
            if($(item).is(':checked')){
                patient_list[$(item).val()] = $(item).data();
            }
        });
        return patient_list;
    }

    // checkbox all
    $(document).off('click', '.work-ls-patient-all').on('click', '.work-ls-patient-all', function () {
        const tb = $(this).parents('table');
        let patient_record = tb.find('.js-select-patient-for-psd');
        if ($(this).is(':checked')){
            for (let i = 0; i < patient_record.length; i++) {
                $(patient_record[i]).attr('checked', true);
            }
        } else {
            for (let i = 0; i < patient_record.length; i++) {
                $(patient_record[i]).attr('checked', false);
            }
        }
        patient_record.trigger('change');
    });

    // checkbox for the row
    $(document).off('change', '.js-select-patient-for-psd').on('change', '.js-select-patient-for-psd', function(){
        if(!$('.js-select-patient-for-psd:checked').length){
            $('#js-worklist-psd-add').attr('disabled', true);
        } else {
            $('#js-worklist-psd-add').attr('disabled', false);
        }
    });

    // "Assign Preset Order to selected patients" button, click to show popup
    $(document).off('click', '#js-worklist-psd-add').on('click', '#js-worklist-psd-add', function (e) {
        var patient_list = getSelectedPatients();
        // only when at least one patient is selected,
        // display the psd popup
        if(Object.keys(patient_list).length > 0){
            let content = '';
            Object.keys(patient_list).forEach(function (key, i) {
                content += `<li>${patient_list[key]['name']}<input type="hidden" name="PresetAssignment[patients][${i}][patient_id]" value="${key}"></li><input type="hidden" name="PresetAssignment[patients][${i}][visit_id]" value="${patient_list[key]['visit_id']}"></li>`;
            });
            $('.psd-selected-patients').find('ul').html(content);
            $('.js-add-psd-popup').addClass('js-psdpopup-opened').show();
            $('.js-add-psd-popup li[data-laterality="both"]').trigger('click');
        }
    });

    let handleCheckPincode = function(ps, resp){
        if(resp['success']){
            $(this).closest('div').addClass('accepted-pin').removeClass('wrong-pin');
            $(this).siblings('button.js-unlock').prop('disabled', false);
            ps.currentUserId = resp['payload']['id'];
            ps.currentUserName = resp['payload']['name'];
            
        } else {
            $(this).closest('div').removeClass('accepted-pin').addClass('wrong-pin');
            $(this).siblings('button.js-unlock').prop('disabled', true);
        }
        checking_pincode = false;
        $(this).prop('disabled', false);
    }
    let checking_pincode = false;
    let checkPincode = function(event, context, ps){
        if(context.value.length < context.maxLength){
            return;
        }
        if(checking_pincode){
            return;
        }
        if(!ps.pathstepId){
            return;
        }
        checking_pincode = true;
        $(context).prop('disabled', true);
        ps.requestDetails(
            {
                pincode: context.value,
                assignment_id: ps.pathstepId,
                YII_CSRF_TOKEN,
            },
            '/OphDrPGDPSD/PSD/checkPincode', 
            'POST', 
            handleCheckPincode.bind(context)
        );
    }
    let updatePathstepIcon = function(ps, resp){
        ps.renderPopupContent(resp);
        let status_dict = ps.currentPopup.find('.slide-open').data('status-dict');
        let current_status_class = ps.currentPopup.find('.js-current-icon-class').val();
        let status_class_list = status_dict.map(function(status){
            return status['css'];
        })
        $(ps.pathstepIcon).removeClass(status_class_list.join(' ')).addClass(current_status_class);
    }
    let unlockAdministration = function(event, context, ps){
        event.preventDefault();
        let form = $('form#worklist-administration-form')
        ps.resetPopup();
        ps.administer_ready = true;
        ps.requestDetails(form.serialize(), '/OphDrPGDPSD/PSD/unlockPathStep', 'POST', updatePathstepIcon);
    }
    let administerMeds = function(event, context, ps){
        let $admin_icon = $(context).closest('tr').find('.js-administer-icon');
        let $admin_tr_cbks = $(context).closest('tr').find('.js-administer-cbk');
        let $end_date_field = $(context).siblings('.js-administer-end');
        let $start_date_field = $(context).siblings('.js-administer-start');
        let $administer_user_field = $(context).siblings('.js-administer-user');
        
        if($(context).attr('checked')){
            $end_date_field.val(Date.now());
            $start_date_field.val(Date.now());
            $administer_user_field.val(ps.currentUserId);
            $(context).val('1');
        } else {
            $end_date_field.val(null);
            $start_date_field.val(null);
            $administer_user_field.val(null);
            $(context).val('0');
        }
        let $checked_cbk = $(context).closest('tr').find('.js-administer-cbk[value="1"]');
        if($admin_tr_cbks.length === $checked_cbk.length){
            $admin_icon.addClass('tick').removeClass('waiting');
        } else {
            $admin_icon.removeClass('tick').addClass('waiting');
        }
    }

    let confirmAdministration = function(event, context, ps){
        event.preventDefault();
        let form = $('form#worklist-administration-form');
        ps.resetPopup();
        ps.requestDetails(form.serialize(), '/OphDrPGDPSD/PSD/confirmAdministration', 'POST', updatePathstepIcon, () => (ps.administer_ready = false));
    }
    let postRemoval = function(ps, resp){
        if(resp['success']){
            $(ps.pathstepIcon).remove();
            ps.currentPopup.remove();
            ps.pathStepLocked = false;
        } else {
            ps.requestDetails({partial: 0, pathstep_id: ps.pathstepId, patient_id: ps.patientID}, null, null, updatePathstepIcon);
        }
    }
    let removeAssignment = function(event, context, ps){
        event.preventDefault();
        ps.resetPopup();
        ps.requestDetails({assignment_id: ps.pathstepId, YII_CSRF_TOKEN}, '/OphDrPGDPSD/PSD/removePSD', 'POST', postRemoval);
    }
    let cancelAdmin = function(event, context, ps){
        event.preventDefault();
        ps.resetPopup();
        ps.administer_ready = false;
        ps.requestDetails({partial: 0, pathstep_id: ps.pathstepId, patient_id: ps.patientID}, null, null, updatePathstepIcon);
    }
    // use timeout to handle the popup position and display
    // to avoid the popup position updated frame by frame
    let delay_handle_popup = null;
    // scroll event callback will be fired every 300ms at the end of the event
    let pageScroll = _.throttle(function(event, context, ps){
        if(!ps.checkPopupExistence() || !ps.pathstepIcon){
            return;
        }
        // hide the popup to avoid the popup position updated frame by frame
        ps.currentPopup.hide();
        // clear the timeout to avoid stacked timeout
        if(delay_handle_popup){
            clearTimeout(delay_handle_popup);
        }
        delay_handle_popup = setTimeout(
            pageScrollHandlePopup.bind(this, event, context, ps),
            300
        );
    }, 300, {'trailing': true});

    let pageScrollHandlePopup = function(event, context, ps){
        const popup_pos = ps.getPopupPosition(ps.pathstepIcon, ps.currentPopup);
        ps.currentPopup.css(popup_pos);

        if(isInViewport(ps.pathstepIcon, context)){
            ps.currentPopup.show();
        } else {
            ps.currentPopup.hide();
        }
    }
    
    function isInViewport(el, ctn){
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (ctn.offsetHeight || document.documentElement.clientHeight) &&
            rect.right <= (ctn.offsetWidth || document.documentElement.clientWidth)
    
        );
    }
    new OpenEyes.UI.PathStep({
        extraActions:[
            {
                'target': '.step-actions .js-unlock',
                'event': 'click',
                'callback': unlockAdministration,
            },
            {
                'target': '.step-content .js-administer-cbk',
                'event': 'change',
                'callback': administerMeds,
            },
            {
                'target': '.step-actions .js-confirm-admin',
                'event': 'click',
                'callback': confirmAdministration,
            },
            {
                'target': '.step-actions .user-pin-entry',
                'event': 'keyup',
                'callback': checkPincode,
            },
            {
                'target': '.step-actions .js-remove-assignment',
                'event': 'click',
                'callback': removeAssignment,
            },
            {
                'target': '.step-actions .js-cancel-admin',
                'event': 'click',
                'callback': cancelAdmin,
            },
            {
                'target': '.oe-worklists .oe-full-main',
                'event': 'scroll',
                'callback': pageScroll,
            }
        ]
    });
});