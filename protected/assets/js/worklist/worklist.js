$(function () {
    let timer_interval = {};
    let completeHoldStep = function (ele, step_data) {
        cancelTimer(ele, step_data);
        $(ele).find('svg').remove();
        // send post request to update the hold step to complete
        let data = {
            step_id: step_data.id,
            direction: 'next',
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
        };
        $.ajax({
            url: '/worklist/changeStepStatus',
            type: 'POST',
            data: data,
            success: function (response) {
                handleRunActionResponse($(ele), response);
            }
        });
    };
    
    let cancelTimer = function (ele, step_data) {
        if (timer_interval[step_data.id]) {
            clearInterval(timer_interval[step_data.id]);
        }
    };

    let startTimer = function (ele, step_data) {
        // clear active interval if exists
        if (timer_interval[step_data.id]) {
            clearInterval(timer_interval);
        }
        // total duration
        const total = Number(step_data.short_name) * 60;
        // calculate how much time has elapsed
        let elapsed = (step_data.now_timestamp - step_data.start_timestamp);
        // circle specs
        const radius = 15;
        const circumference = radius * 2 * Math.PI;

        timer_interval[step_data.id] = setInterval(() => {
            let percent = (elapsed / total) * 100;
            if (percent >= 100) {
                // clear interval and complete the hold step when time is up
                completeHoldStep(ele, step_data);
                return;
            }
            // from blueJS, make sure displaying something after start the timer
            if (percent < 2)
                percent = 2;
            // offset acts as an indicator of the progress
            let offset = circumference - percent / 100 * circumference;
            $(ele).find('circle').css({strokeDashoffset: offset});
            elapsed += 1;
        }, 1000);
    };


    let loadTimer = function() {
        $('.oe-pathstep-btn.hold.active').each(function (i, ele) {
            startTimer(ele, $(ele).data('step-data'));
        });
    };

    // After the page reload, start the timer for all active hold steps
    loadTimer();

    // dictionary for executing the special actions from the buttons
    const special_actions = {
        'startTimer': startTimer,
        'cancelTimer': cancelTimer,
    };

    function getSelectedPatients()
    {
        let patient_list = {};
        const cps = $('.js-select-patient-for-psd');
        cps.each(function (i, item) {
            if ($(item).is(':checked')) {
                patient_list[$(item).val()] = $(item).data();
            }
        });
        return patient_list;
    }

    // checkbox all
    $(document).off('click', '.work-ls-patient-all').on('click', '.work-ls-patient-all', function () {
        const tb = $(this).parents('table');
        let patient_record = tb.find('.js-select-patient-for-psd');
        if ($(this).is(':checked')) {
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

    $(document).off('click', '.js-pathway-finish').on('click', '.js-pathway-finish', function () {
        // Display a dialog with options to specify how to alter step states.
        const id = $(this).data('pathway-id');
        const $pathway = $(this);
        let dialog = new OpenEyes.UI.Dialog.Confirm({
            title: 'Quick complete pathway',
            content: '<h3>How should the pathway be completed?</h3>' +
                '<div class="block js-complete-option-list">' +
                '<fieldset class="btn-list">' +
                '<label><input type="radio" name="complete-option" value="none" checked/><div class="li">Leave incomplete steps</div></label>' +
                '<label><input type="radio" name="complete-option" value="remove"/><div class="li">Remove incomplete steps</div></label>' +
                '<label><input type="radio" name="complete-option" value="mark_done"/><div class="li">Mark all steps as done</div></label>' +
                '</fieldset>' +
                '</div>',
            okButton: 'Complete pathway',
            okButtonClassList: 'green hint ok',
            cancelButtonClassList: 'red hint cancel',
        });
        dialog.on('ok', function () {
            const $selected_complete_option = $('#selected-complete-option')
            const selected_option = $selected_complete_option.val();
            $selected_complete_option.val('none');
            $.ajax({
                url: '/worklist/changePathwayStatus',
                data: {
                    pathway_id: id,
                    new_status: 'done',
                    step_action: selected_option,
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                },
                type: 'POST',
                success: function (response) {
                    updatePathwayTr($pathway.closest('tr'), response, 'done');
                }
            });
        }.bind(this));
        dialog.open();
    });
    $(document).off('click', '.js-pathway-complete').on('click', '.js-pathway-complete', function () {
        const id = $(this).data('pathway-id');
        let $this = $(this);
        $.ajax({
            url: '/worklist/changePathwayStatus',
            data: {
                pathway_id: id,
                new_status: 'done',
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            type: 'POST',
            success: function (response) {
                updatePathwayTr($this.closest('tr'), response, 'done');
            }
        });
    });
    $(document).off('click', '.js-pathway-reactivate').on('click', '.js-pathway-reactivate', function () {
        const id = $(this).data('pathway-id');
        let $this = $(this);
        $.ajax({
            url: '/worklist/changePathwayStatus',
            data: {
                pathway_id: id,
                new_status: 'discharged',
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            type: 'POST',
            success: function (response) {
                updatePathwayTr($this.closest('tr'), response);
            }
        });
    });

    // checkbox for the row
    $(document).off('change', '.js-select-patient-for-psd').on('change', '.js-select-patient-for-psd', function () {
        if (!$('.js-select-patient-for-psd:checked').length) {
            $('#js-worklist-psd-add').attr('disabled', true);
        } else {
            $('#js-worklist-psd-add').attr('disabled', false);
        }
    });

    // "Assign Preset Order to selected patients" button, click to show popup
    $(document).off('click', '#js-worklist-psd-add').on('click', '#js-worklist-psd-add', function () {
        const patient_list = getSelectedPatients();
        // only when at least one patient is selected,
        // display the psd popup
        if (Object.keys(patient_list).length > 0) {
            let content = '';
            Object.keys(patient_list).forEach(function (key, i) {
                content += `<li>${patient_list[key]['name']}<input type="hidden" name="PresetAssignment[patients][${i}][patient_id]" value="${key}"></li><input type="hidden" name="PresetAssignment[patients][${i}][visit_id]" value="${patient_list[key]['visit_id']}"></li>`;
            });
            $('.psd-selected-patients').find('ul').html(content);
            $('.js-add-psd-popup').addClass('js-psdpopup-opened').show();
            $('.js-add-psd-popup li[data-laterality="both"]').trigger('click');
        }
    });
    let checking_pincode = false;
    let handleCheckPincode = function (ps, resp) {
        if (resp['success']) {
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
    };
    let checkPincode = function (event, context, ps) {
        if (context.value.length < context.maxLength) {
            return;
        }
        if (checking_pincode) {
            return;
        }
        if (!ps.pathstepId) {
            return;
        }
        let assignment_id = $('input[name="Assignment[assignment_id]"]').val();
        checking_pincode = true;
        $(context).prop('disabled', true);
        ps.requestDetails(
            {
                pincode: context.value,
                assignment_id: assignment_id,
                YII_CSRF_TOKEN,
            },
            '/OphDrPGDPSD/PSD/checkPincode',
            'POST',
            handleCheckPincode.bind(context)
        );
    };
    let updatePathstepIcon = function (ps, resp) {
        ps.renderPopupContent(resp);
        let status_dict = ps.currentPopup.find('.slide-open').data('status-dict');
        let current_status_class = ps.currentPopup.find('.js-current-icon-class').val();
        let status_class_list = status_dict.map(function (status) {
            return status['css'];
        });
        $(ps.pathstepIcon).removeClass(status_class_list.join(' ')).addClass(current_status_class);
    };

    let serializeForm = function (selector) {
        let o = {};
        const a = $('form' + selector).serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    let unlockAdministration = function (event, context, ps) {
        event.preventDefault();

        let form_data = {
            pincode: $('input[name="pincode"]').val(),
        };
        ps.administer_ready = true;
        runAction(event, context, ps, form_data);
    };
    let administerMeds = function (event, context, ps) {
        let $admin_icon = $(context).closest('tr').find('.js-administer-icon');
        let $admin_tr_cbks = $(context).closest('tr').find('.js-administer-cbk');
        let $end_date_field = $(context).siblings('.js-administer-end');
        let $start_date_field = $(context).siblings('.js-administer-start');
        let $administer_user_field = $(context).siblings('.js-administer-user');

        if ($(context).attr('checked')) {
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
        if ($admin_tr_cbks.length === $checked_cbk.length) {
            $admin_icon.addClass('tick').removeClass('waiting');
        } else {
            $admin_icon.removeClass('tick').addClass('waiting');
        }
    };

    let confirmAdministration = function (event, context, ps) {
        event.preventDefault();
        let form_data = serializeForm('#worklist-administration-form');
        runAction(event, context, ps, form_data);
    };

    let changeVisualFields = function (event, context, ps) {
        event.preventDefault();
        let action = $(event.target).data('action');

        if (action === 'next') {
            let form_data = serializeForm('#visual-fields-form');
            // Combine the laterality into a single value.
            if (form_data.eyelat_select_R && form_data.eyelat_select_L) {
                form_data.laterality = 3;
                delete form_data.eyelat_select_R;
                delete form_data.eyelat_select_L;
            } else {
                form_data.laterality = form_data.eyelat_select_R ? form_data.eyelat_select_R : form_data.eyelat_select_L;
                if (form_data.eyelat_select_R) {
                    delete form_data.eyelat_select_R;
                }
                if (form_data.eyelat_select_L) {
                    delete form_data.eyelat_select_L;
                }
            }
            runAction(event, context, ps, form_data);
        } else {
            runAction(event, context, ps);
        }
    };

    let cancelAdmin = function (event, context, ps) {
        event.preventDefault();
        ps.resetPopup();
        ps.administer_ready = false;
        ps.requestDetails({
            partial: 0,
            pathstep_id: ps.pathstepId,
            patient_id: ps.patientID
        }, null, null, updatePathstepIcon);
    };
    // use timeout to handle the popup position and display
    // to avoid the popup position updated frame by frame
    let delay_handle_popup = null;
    // scroll event callback will be fired every 300ms at the end of the event
    let pageScroll = _.throttle(function (event, context, ps) {
        if (!ps.checkPopupExistence() || !ps.pathstepIcon) {
            return;
        }
        // hide the popup to avoid the popup position updated frame by frame
        ps.currentPopup.hide();
        // clear the timeout to avoid stacked timeout
        if (delay_handle_popup) {
            clearTimeout(delay_handle_popup);
        }
        delay_handle_popup = setTimeout(
            pageScrollHandlePopup.bind(this, event, context, ps),
            300
        );
    }, 300, {'trailing': true});

    let pageScrollHandlePopup = function (event, context, ps) {
        const popup_pos = ps.getPopupPosition(ps.pathstepIcon, ps.currentPopup);
        ps.currentPopup.css(popup_pos);

        if (isInViewport(ps.pathstepIcon, context)) {
            ps.currentPopup.show();
        } else {
            ps.currentPopup.hide();
        }
    };

    let updateLetterCount = function (event, context, ps) {
        let textString = $(context).val();
        let textLength = textString.length / 0.8;
        let $progressBar = $(context).next().find('.percent-bar');
        $progressBar.css('width', textLength + '%');
    };

    let addComment = function (event, context, ps) {
        let comment = $(event.target).closest('.js-comments-edit').find('.js-step-comments').val();
        $(context).removeClass('save-plus');
        $(context).addClass('spinner');
        $(context).addClass('as-icon');

        $.ajax({
            url: '/worklist/addComment',
            type: 'POST',
            data: {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                pathway_id: ps.pathwayId,
                patient_id: ps.patientID,
                pathstep_id: ps.pathstepId,
                user_id: user_id,
                comment: comment,
            },
            success: function (response) {
                let $eventSelector = $(event.target);
                let $editSelector = $eventSelector.closest('.js-comments-edit');
                let $viewSelector = $editSelector.next('.js-comments-view');
                let $commentButton = $('.comments[data-pathway-id="' + ps.pathwayId + '"]');
                $eventSelector.removeClass('spinner');
                $eventSelector.removeClass('as-icon');
                $eventSelector.addClass('save-plus');
                $editSelector.css('display', 'none');
                $viewSelector.find('.comment').text(response);
                $viewSelector.show();
                if (!$commentButton.hasClass('comments-added')) {
                    $commentButton.addClass('comments-added');
                }
            }
        });
    };

    let updatePathwayTr = function($tr, response, static_status = null, ps = null){
        const pathway_status = response.status || response.pathway_status;
        let status = static_status ? static_status : pathway_status;
        $tr.attr('class', '');
        $tr.addClass(status);
        $tr.find('.js-pathway-container').html(response.step_html);
        $tr.find('.js-pathway-status').html(response.status_html);

        if(response.waiting_time_html){
            $tr.find('.wait-duration').removeClass('stopped');
            $tr.find('.wait-duration').html();
            $tr.find('.wait-duration').html(response.waiting_time_html);
            $tr.removeClass('done')
        }
        if($tr.hasClass('done')){
            $tr.find('.patient-checkbox').hide();
        } else {
            $tr.find('.patient-checkbox').show();
        }
        if(ps){
            ps.closePopup();
        }
    };

    // update pathstep icon status and position
    let handleRunActionResponse = function ($thisStep, response) {
        const $lastActiveStep = $thisStep.closest('.pathway').find('.active').not($thisStep).last();
        const $lastCompleteStep = $thisStep.closest('.pathway').find('.done').not($thisStep).last();
        const $lastRequestedStep = $thisStep.closest('.pathway').find('.todo').not($thisStep).last();
        if (response.step.status === 'active') {
            $thisStep.removeClass('done');
            $thisStep.removeClass('todo');
            $thisStep.addClass('active');
            $thisStep.find('.info').text(response.step.start_time);
            $thisStep.find('.info').show();
            if ($lastActiveStep.length > 0) {
                $lastActiveStep.after($thisStep);
            } else {
                $lastCompleteStep.after($thisStep);
            }
            $thisStep.closest('td').find('.wait, .delayed-wait').remove();
        } else if (response.step.status === 'done') {
            $thisStep.addClass('done');
            $thisStep.removeClass('todo');
            $thisStep.removeClass('active');
            if ($thisStep.hasClass('break')) {
                $thisStep.removeClass('break');
                $thisStep.addClass('break-back');
            }
            $thisStep.find('.info').hide();
            $lastCompleteStep.after($thisStep);
            // if the hold step is after a finishing step
            // start the timer automatically
            const $nextHoldStep = $thisStep.next('.hold.todo');
            if ($nextHoldStep.length) {
                $nextHoldStep.find('.js-ps-popup-btn').trigger('click');
            } else {
                const wait_time_details = JSON.parse(response.wait_time_details);
                let step_html = Mustache.render($('#js-step-template').html(), {
                    status: wait_time_details.status_class,
                    type: 'buff',
                    id: 'wait',
                    patient_id: $thisStep.data('patient-id'),
                    icon: wait_time_details.icon_class,
                    display_info: wait_time_details.wait_time.toString(),
                });
                $thisStep.after(step_html);
            }
        } else {
            $thisStep.removeClass('done');
            $thisStep.addClass(response.step.status);
            $thisStep.removeClass('active');
            $thisStep.find('.info').hide();
            if ($lastRequestedStep.length > 0) {
                $lastRequestedStep.after($thisStep);
            } else if ($lastActiveStep.length > 0) {
                $lastActiveStep.after($thisStep);
            } else {
                $lastCompleteStep.after($thisStep);
            }

        }
        updatePathwayTr($thisStep.closest('tr'), response);
    }

    let runAction = function (event, context, ps, form_data) {
        event.preventDefault();
        let action = $(event.target).data('action');
        // the runAction may not be manually triggered, so try to grab the step id from the event target first
        let pathstep_id = $(event.target).data('pathstep-id') || ps.pathstepId;
        let data = {
            step_id: pathstep_id,
            direction: action,
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
        };
        if (form_data) {
            data.extra_form_data = form_data;
        }

        switch (action) {
            case 'next':
            case 'prev':
                $.ajax({
                    url: '/worklist/changeStepStatus',
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        // Move the step to the correct position.
                        // query the step icon again to make sure the consistency with the server
                        const $thisStep = $('.oe-pathstep-btn[data-pathstep-id="' + response.step.id + '"]');
                        // get the special action from the button data attribute
                        // current format: step_status: callback
                        let special_action = $(event.target).data('special-action');

                        // Move the step to the correct position.
                        handleRunActionResponse($thisStep, response);
                        
                        const state_data = JSON.parse(response.step.state_data);
                        // run the special action if it matches the current status
                        if (special_action && special_action[response.step.status]) {
                            const callback = special_action[response.step.status];
                            /* 
                            * no existence check, as if the special action is required, it must be included
                            * in special_actions dictionary
                            */
                            special_actions[callback].call(null, $thisStep, response.step);
                        }
                        if (state_data.hasOwnProperty('event_create_url') && state_data.event_create_url) {
                            // Redirect to the target URL.
                            window.location = state_data.event_create_url;
                        } else {
                            ps.resetPopup();
                            ps.requestDetails({
                                partial: 0,
                                pathstep_id: ps.pathstepId,
                                patient_id: ps.patientID
                            });
                        }
                    }
                });
                break;
            case 'left':
            case 'right':
                // Reorder the step.
                $.ajax({
                    url: '/worklist/reorderStep',
                    type: 'POST',
                    data: {
                        step_id: ps.pathstepId,
                        direction: action,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    success: function () {
                        // Swap the step with the step either in front or behind it (depending on the action).
                        let $current_step = $('.oe-pathstep-btn[data-pathstep-id="' + ps.pathstepId + '"]');
                        if (action === 'left') {
                            if ($current_step.prev()) {
                                $current_step.prev().before($current_step);
                            }
                        } else {
                            if ($current_step.next()) {
                                $current_step.before($current_step.next());
                            }
                        }
                        ps.resetPopup();
                        ps.requestDetails({
                            partial: 0,
                            pathstep_id: ps.pathstepId,
                            patient_id: ps.patientID
                        });
                    }
                });
                break;
            case 'done':
                $.ajax({
                    url: '/worklist/checkIn',
                    type: 'POST',
                    data: {
                        visit_id: ps.visitID,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    success: function (response) {
                        // This is a check-in step so it is already in the correct position.
                        const $thisStep = $('.oe-pathstep-btn[data-pathstep-id="checkin"][data-visit-id="' + ps.visitID + '"]');
                        $thisStep.addClass('done');
                        $thisStep.removeClass('todo');
                        $thisStep.removeClass('active');
                        $thisStep.find('.info').text(response.end_time);
                        $thisStep.find('.info').show();
                        // Change the icon for the pathway's completion status.
                        $thisStep.closest('tr').find('td:last').html(response.pathway_status_html);

                        // Add a wait step.
                        let step_html = Mustache.render($('#js-step-template').html(), {
                            status: 'wait',
                            type: 'buff',
                            id: 'wait',
                            patient_id: ps.patientID,
                            icon: 'i-wait',
                            display_info: '0'
                        });
                        // As the first completed element will always be the check-in element (and no other steps should have been started by this point),
                        // place the wait timer after the first done step (the check-in step).
                        $thisStep.closest('.js-pathway-container').find('.oe-pathstep-btn.done:first').after(step_html);
                        ps.resetPopup();
                        ps.requestDetails({
                            partial: 0,
                            pathstep_id: ps.pathstepId,
                            patient_id: ps.patientID
                        });
                    }
                });
                break;
            case 'DNA':
                // Patient did not attend.
                $.ajax({
                    url: '/worklist/didNotAttend',
                    type: 'POST',
                    data: {
                        visit_id: ps.visitID,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    success: function (response) {
                        // This is a check-in step so it is already in the correct position.
                        const $thisStep = $('.oe-pathstep-btn[data-pathstep-id="checkin"][data-visit-id="' + ps.visitID + '"]');
                        $thisStep.addClass('done');
                        $thisStep.removeClass('todo');
                        $thisStep.removeClass('active');
                        // Change the icon for the pathway's completion status.
                        $thisStep.closest('tr').find('td:last').html(response.pathway_status_html);

                        ps.resetPopup();
                        ps.requestDetails({
                            partial: 0,
                            pathstep_id: ps.pathstepId,
                            patient_id: ps.patientID
                        });
                    }
                });
                break;
            case 'remove':
                $.ajax({
                    url: '/worklist/deleteStep',
                    type: 'POST',
                    data: data,
                    success: function () {
                        // This is a requested step so as long as it is deleted the order of other requested steps should be correct.
                        const $thisStep = $('.oe-pathstep-btn[data-pathstep-id="' + ps.pathstepId + '"]');
                        $thisStep.remove();
                        ps.closePopup(true);
                    }
                });
                break;
            case 'goto':
                window.location = $(event.target).data('url');
                break;
            case 'addNotes':
                const comment = $(event.target).closest('.slide-open').find('textarea').val();
                const btn_text = $(event.target).text();
                const btn_action = $(event.target).data('action');
                $.ajax({
                    url: '/worklist/addComment',
                    type: 'POST',
                    data: {
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                        pathway_id: ps.pathwayId,
                        patient_id: ps.patientID,
                        pathstep_id: ps.pathstepId,
                        user_id: user_id,
                        comment: comment,
                    },
                    success: function () {
                        $(event.target).removeAttr('data-action');
                        $(event.target).addClass('green');
                        $(event.target).html('<i class="oe-i medium tick"></i>')
                        setTimeout(function () {
                            $(event.target).removeClass('green');
                            $(event.target).attr('data-action', btn_action);
                            $(event.target).html(btn_text);
                        }, 1500);
                    }
                });
                break;
            case 'checkout':
                $.ajax({
                    url: '/worklist/checkout',
                    type: 'POST',
                    data: {
                        step_id: pathstep_id,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    success: function (response) {
                        const $tr = $(`tr#js-pathway-${response.pathway_id}`);
                        const time = $tr.find('.wait-duration small').text();
                        updatePathwayTr($tr, response, 'done', ps);
                        $tr.find('.wait-duration').addClass('stopped');
                        $tr.find('.wait-duration').html(time);
                    }
                });
                break;
            case 'undo_finish':
                $.ajax({
                    url: '/worklist/revertCheckout',
                    type: 'POST',
                    data: {
                        step_id: pathstep_id,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    success: function (response) {
                        const $tr = $(`tr#js-pathway-${response.pathway_id}`);
                        updatePathwayTr($tr, response, null, ps);
                    }
                });
                
                break;
            default:
                new OpenEyes.UI.Dialog.Alert({
                    content: "This is an invalid action for this step. No actions have been performed on this step.",
                }).open();
                break;
        }
    };

    function isInViewport(el, ctn) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (ctn.offsetHeight || document.documentElement.clientHeight) &&
            rect.right <= (ctn.offsetWidth || document.documentElement.clientWidth)

        );
    }

    new OpenEyes.UI.PathStep({
        domRequestURL: '/worklist/getPathStep',
        extraActions: [
            {
                'target': '.step-actions .js-ps-popup-btn',
                'event': 'click',
                'callback': runAction,
            },
            {
                'target': '.oe-pathstep-btn.hold.todo .js-ps-popup-btn',
                'event': 'click',
                'callback': runAction,
            },
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
                'target': '.step-actions .js-change-visual-fields',
                'event': 'click',
                'callback': changeVisualFields,
            },
            {
                'target': '.step-actions .user-pin-entry',
                'event': 'keyup',
                'callback': checkPincode,
            },
            {
                'target': '.step-actions .js-remove-assignment',
                'event': 'click',
                'callback': runAction,
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
            },
            {
                'target': '.js-comments-edit .js-save',
                'event': 'click',
                'callback': addComment,
            },
            {
                'target': '.js-comments-edit .js-step-comments',
                'event': 'keyup',
                'callback': updateLetterCount,
            }
        ]
    });
});