$(function () {
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

    let runAction = function (event, context, ps, form_data) {
        event.preventDefault();
        let action = $(event.target).data('action');
        let data = {
            step_id: ps.pathstepId,
            direction: action,
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
        };
        if (form_data) {
            data.extra_form_data = form_data;
        }

        switch (action) {
            case 'left':
            case 'right':
                // Reorder the step.
                $.ajax({
                    url: '/Admin/worklist/reorderStep',
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
            case 'remove':
                $.ajax({
                    url: '/Admin/worklist/deleteStep',
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
            default:
                new OpenEyes.UI.Dialog.Alert({
                    content: "This is an invalid action for this step. No actions have been performed on this step.",
                }).open();
                break;
        }
    };

    function isInViewport(el, ctn)
    {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (ctn.offsetHeight || document.documentElement.clientHeight) &&
            rect.right <= (ctn.offsetWidth || document.documentElement.clientWidth)
        );
    }

    new OpenEyes.UI.PathStep({
        domRequestURL: '/Admin/worklist/getPathStep',
        extraActions: [
            {
                'target': '.step-actions .js-ps-popup-btn',
                'event': 'click',
                'callback': runAction,
            },
            {
                'target': '.step-actions .js-remove-assignment',
                'event': 'click',
                'callback': runAction,
            },
        ]
    });
});