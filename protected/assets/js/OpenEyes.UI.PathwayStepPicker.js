(function (exports) {
    'use strict';

    function PathwayStepPicker(options) {
        this.delaySearch = 0;
        this.options = $.extend(true, {}, PathwayStepPicker._defaultOptions, options);
    }

    PathwayStepPicker._defaultOptions = {
        pathways: [],
        psd_step_type_id: null,
        exam_step_type_id: null,
        letter_step_type_id: null,
        generic_step_type_id: null,
        vf_step_type_id: null,
        userContext: 'undefined',
        pathway_checkboxes: '.js-check-patient',
        base_url: '/',
    };

    const selectors = {
        mainSelector: '.oec-adder',
        assigneeSearchField: '.assign-to',
        assigneeList: '#js-assignee-list',
        selectAssignee: '#js-assignee-list li',
        commonPathwayDialog: '#js-common-pathway-dialog',
        drugAdminDialog: '#js-drug-admin-dialog',
        closeButton: '.close-btn',
        step: '.js-step',
        pathwayAssignee: '.js-pathway-assignee',
        addPresetPathway: '#js-clinic-manager #add-preset-pathway',
        stepTemplate: '#js-step-template',
        undoTodoStepButton: '#undo-add-step',
    };

    PathwayStepPicker.prototype.selected_patients = 0;

    PathwayStepPicker.prototype.init = function () {
        const self = this;

        $(selectors.mainSelector).on('click', selectors.step, this.addStep.bind(this));
        $(this.options.pathway_checkboxes).change(this.onAddButtonClicked.bind(this));
        $(selectors.addPresetPathway).click(this.addPresetPathway.bind(this));
        $(selectors.mainSelector).on('click', selectors.selectAssignee, this.assignToUser.bind(this));
        $(selectors.mainSelector + ' ' + selectors.closeButton).click(function () {
            $(selectors.mainSelector).removeClass('fadein');
            $(self.options.pathway_checkboxes + ':checked').prop('checked', false);
        });
        $(selectors.assigneeSearchField).keyup(this.onSearchAssignee.bind(this));
        $(selectors.undoTodoStepButton).click(this.undoTodoStep.bind(this));
    };

    PathwayStepPicker.prototype.assignToUser = function (e) {
        const self = this;
        let pathway_ids = $(this.options.pathway_checkboxes + ':checked').map(
            function () {
                return $(this).val();
            }
        ).get();
        for (let pathway_id of pathway_ids) {
            $.ajax({
                url: this.options.base_url + 'worklist/assignUserToPathway',
                data: {
                    user_id: $(e.target).data('id'),
                    target_pathway_id: pathway_id,
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                },
                type: 'POST',
                success: function (response) {
                    // Add the assignee's initials to the pathway.
                    let $affectedRow = $(self.options.pathway_checkboxes + '[value="' + pathway_id + '"]').closest('tr');
                    const oldId = $affectedRow.find('td' + selectors.pathwayAssignee).attr('data-id');

                    $affectedRow.find('td' + selectors.pathwayAssignee).attr('data-id', response.id);
                    $affectedRow.find('td' + selectors.pathwayAssignee).text(response.initials);

                    if (self.options.onChangePatientRow) {
                        self.options.onChangePatientRow('change-assigned-to', { pathwayId: pathway_id, oldId: oldId, newId: response.id });
                    }
                }
            });
        }
    };

    PathwayStepPicker.prototype.onAddButtonClicked = function (e) {
        if ($(e.target).val() === 'all') {
            if ($(e.target).is(':checked')) {
                $(e.target).closest('table').find(this.options.pathway_checkboxes).prop('checked', true);
            } else {
                $(e.target).closest('table').find(selectors.pathway_checkboxes).prop('checked', false);
            }
        }
        this.selected_patients = $(this.options.pathway_checkboxes + '[value!="all"]:checked').length;

        if (this.selected_patients === 0) {
            $(e.target).closest('table').find(this.options.pathway_checkboxes + '[value="all"]').prop('checked', false);
        }
        if (!$(selectors.mainSelector).hasClass('fadein')) {
            $(selectors.mainSelector).addClass('fadein');
        }
        if (this.selected_patients === 0) {
            $(selectors.mainSelector + ' .add-to').text('No patients selected');
        } else if ($(selectors.mainSelector + ' .add-to .num').length === 0) {
            $(selectors.mainSelector + ' .add-to').html('<span class="num">' + this.selected_patients + '</span> selected');
        } else {
            $(selectors.mainSelector + ' .add-to .num').text(this.selected_patients);
        }
    };

    PathwayStepPicker.prototype.addPresetPathway = function () {
        const self = this;
        let pathway_ids = $(this.options.pathway_checkboxes + ':checked').map(
            function () {
                return $(this).val();
            }
        ).get();
        new OpenEyes.UI.Dialog.PathwayStepOptions({
            title: 'Add common pathway',
            itemSets: [{
                is_form: true,
                title: 'Pathways',
                id: 'pathway',
                display_options: 'wide',
                items: this.options.pathways
            },
                {
                    is_form: true,
                    title: 'Insert position',
                    id: 'position',
                    items: [{
                        id: 0,
                        name: 'path-position',
                        label: 'End of pathway',
                    },
                        {
                            id: 1,
                            name: 'path-position',
                            label: 'Todo: Next',
                        },
                        {
                            id: 2,
                            name: 'path-position',
                            label: 'Todo: 2nd',
                        },
                        {
                            id: 3,
                            name: 'path-position',
                            label: 'Todo: 3rd',
                        },
                    ]
                }
            ],
            onReturn: function (dialog, selectedValues) {
                for (let pathway_id of pathway_ids) {
                    const into_pathway_id = pathway_id;

                    $.ajax({
                        url: self.options.base_url + 'worklist/addPathwayStepsToPathway',
                        data: {
                            selected_values: selectedValues,
                            target_pathway_id: pathway_id,
                            YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                        },
                        type: 'POST',
                        success: function (response) {
                            // Add all steps for the chosen pathway to the selected pathways.
                            const pathway = $(`${self.options.pathway_checkboxes}:checked[value="${into_pathway_id}"]`).closest('tr').find('td.js-pathway-container');

                            const oldSteps = pathway.find('.active, .todo').map(function() { return $(this).data('long-name') }).get();

                            pathway.html(response.step_html);

                            const newSteps = pathway.find('.active, .todo').map(function() { return $(this).data('long-name') }).get();

                            if (self.options.onChangePatientRow) {
                                self.options.onChangePatientRow('change-waiting-for', { pathwayId: into_pathway_id, oldSteps: oldSteps, newSteps: newSteps });
                            }
                        },
                    });
                }
                dialog.close();
            }
        }).open();
    };

    PathwayStepPicker.prototype.newStep = function (step_type_id, pathway_id, step_data = null, position = 0) {
        const self = this;
        $.ajax({
            url: this.options.base_url + 'worklist/addStepToPathway',
            data: {
                id: step_type_id,
                pathway_id: pathway_id,
                position: position,
                step_data: step_data,
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
            },
            type: 'POST',
            success: function (response) {
                let $pathway = $(`.pathway[data-visit-id="${pathway_id}"]`).closest('td.js-pathway-container');
                if(!$pathway.length){
                    $pathway = $(`tr[data-pathway-type-id="${pathway_id}"]`).find('td.js-pathway-container');
                }

                const oldSteps = $pathway.find('.active, .todo').map(function() { return $(this).data('long-name') }).get();

                $pathway.html(response.step_html);

                const newSteps = $pathway.find('.active, .todo').map(function() { return $(this).data('long-name') }).get();

                if (
                    $pathway.find('.oe-pathstep-btn.wait, .oe-pathstep-btn.delayed-wait').length === 0 &&
                    $pathway.find('.oe-pathstep-btn.done').length > 0 &&
                    $pathway.find('.oe-pathstep-btn.active').length === 0) {
                    // Add a wait step after this step
                    if (!response.no_wait_timer) {
                        let step_html = Mustache.render($(selectors.stepTemplate).html(), {
                            status: 'wait',
                            type: 'buff',
                            id: 'wait',
                            patient_id: response.patient_id,
                            icon: 'i-wait',
                            display_info: '0'
                        });
                        $pathway.find('.oe-pathstep-btn.todo:first').before(step_html);
                    }
                }

                if (self.options.onChangePatientRow) {
                    self.options.onChangePatientRow('change-waiting-for', { pathwayId: pathway_id, oldSteps: oldSteps, newSteps: newSteps });
                }
            }
        });
    };

    PathwayStepPicker.prototype.getPSDDrugs = function () {
        const self = this;
        $.ajax({
            url: this.options.base_url + 'worklist/getPresetDrugs/' + self.preset_id,
            data: {
                laterality: self.laterality,
            },
            success: function (response) {
                $('.popup-path-step-options .js-itemset[data-itemset-id="drug-list"] tbody').empty();
                for (let drug_item of response) {
                    let newRow = Mustache.render($('#psd-drug-list-item').html(), drug_item);
                    $('.popup-path-step-options .js-itemset[data-itemset-id="drug-list"] tbody').append(newRow);
                }
            }
        });
    };

    PathwayStepPicker.prototype.addStep = function (e) {
        let pathway_ids = $(this.options.pathway_checkboxes + '[value!="all"]' + ':checked').map(
            function () {
                return $(this).val();
            }
        ).get();

        const self = this;

        let step_type_id = $(e.target).data('id');

        switch (step_type_id) {
            case this.options.psd_step_type_id:
                new OpenEyes.UI.Dialog.PathwayStepOptions({
                    title: 'Drug Administration Preset Orders',
                    itemSets: [{
                        is_form: true,
                        title: 'Presets',
                        id: 'preset',
                        items: this.options.preset_orders,
                        onSelectValue: function (dialog, itemSet, selected_value) {
                            self.preset_id = selected_value;
                            let preset_name = itemSet.items.find(element => element.id === selected_value).label;
                            $('.popup-path-step-options .js-itemset[data-itemset-id="drug-list"] tbody').empty();
                            $('.popup-path-step-options .js-itemset[data-itemset-id="drug-list"] h3').text(preset_name);
                            if (self.preset_id && self.laterality) {
                                self.getPSDDrugs();
                            }
                        }
                    },
                        {
                            is_form: true,
                            title: 'Laterality',
                            id: 'laterality',
                            items: [{
                                id: 3,
                                name: 'laterality',
                                label: 'Right & Left Eye'
                            },
                                {
                                    id: 2,
                                    name: 'laterality',
                                    label: 'Right only'
                                },
                                {
                                    id: 1,
                                    name: 'laterality',
                                    label: 'Left only'
                                },
                            ],
                            onSelectValue: function (dialog, itemSet, selected_value) {
                                self.laterality = selected_value;
                                $('.popup-path-step-options .js-itemset[data-itemset-id="drug-list"] tbody').empty();

                                if (self.laterality && self.preset_id) {
                                    self.getPSDDrugs();
                                }
                            }
                        },
                        {
                            is_form: false,
                            title: '',
                            display_options: 'wide',
                            id: 'drug-list',
                        },
                        {
                            is_form: true,
                            title: 'Insert position',
                            id: 'position',
                            items: [{
                                id: 0,
                                name: 'path-position',
                                label: 'End of pathway',
                            },
                                {
                                    id: 1,
                                    name: 'path-position',
                                    label: 'Todo: Next',
                                },
                                {
                                    id: 2,
                                    name: 'path-position',
                                    label: 'Todo: 2nd',
                                },
                                {
                                    id: 3,
                                    name: 'path-position',
                                    label: 'Todo: 3rd',
                                },
                            ]
                        }
                    ],
                    onReturn: function (dialog, selectedValues) {
                        for (let pathway_id of pathway_ids) {
                            self.newStep(step_type_id, pathway_id, {
                                preset_id: selectedValues[0].value,
                                laterality: selectedValues[1].value,
                            }, selectedValues[2].value);
                        }
                        dialog.close();
                    }
                }).open();
                break;
            case this.options.vf_step_type_id:
                new OpenEyes.UI.Dialog.PathwayStepOptions({
                    title: 'Visual Fields',
                    itemSets: [
                        {
                            is_form: true,
                            title: 'Preset tests',
                            id: 'preset',
                            items: this.options.vf_test_presets,
                            onSelectValue: function (dialog, itemSet, selected_value) {
                                self.test_preset_id = selected_value;

                                if (self.test_preset_id) {
                                    $.get(
                                        '/worklist/getVfPresetData/' + self.test_preset_id,
                                        null,
                                        function (response) {
                                            self.test_type_id = response.test_type_id;
                                            self.test_option_id = response.test_option_id;
                                            $('.popup-path-step-options .js-itemset[data-itemset-id="settings-list"] tbody tr#test-type td').text(response.test_type_name);
                                            $('.popup-path-step-options .js-itemset[data-itemset-id="settings-list"] tbody tr#option td').text(response.option_name);
                                            $('.popup-path-step-options .js-itemset[data-itemset-id="type"]').hide();
                                            $('.popup-path-step-options .js-itemset[data-itemset-id="option"]').hide();
                                            $('.popup-path-step-options .js-itemset[data-itemset-id="type"] input[value="' + self.test_type_id + '"]').prop('checked', true);
                                            $('.popup-path-step-options .js-itemset[data-itemset-id="option"] input[value="' + self.test_option_id + '"]').prop('checked', true);
                                        }
                                    );
                                } else {
                                    $('.popup-path-step-options .js-itemset[data-itemset-id="type"]').show();
                                    $('.popup-path-step-options .js-itemset[data-itemset-id="option"]').show();
                                }
                            }
                        },
                        {
                            is_form: true,
                            title: 'Test types',
                            id: 'type',
                            items: this.options.vf_test_types,
                            onSelectValue: function (dialog, itemSet, selected_value, label) {
                                self.test_type_id = selected_value;
                                const $typeField = $('.popup-path-step-options .js-itemset[data-itemset-id="settings-list"] tbody tr#test-type td');
                                $typeField.text(label);
                            }
                        },
                        {
                            is_form: true,
                            title: 'SITA Algorithm',
                            id: 'option',
                            items: this.options.vf_test_options,
                            onSelectValue: function (dialog, itemSet, selected_value, label) {
                                self.option_id = selected_value;
                                const $optionField = $('.popup-path-step-options .js-itemset[data-itemset-id="settings-list"] tbody tr#option td');
                                $optionField.text(label);
                            }
                        },
                        {
                            is_form: true,
                            title: 'Laterality',
                            id: 'laterality',
                            items: [
                                {
                                    id: 3,
                                    name: 'laterality',
                                    label: 'Right & Left Eye'
                                },
                                {
                                    id: 2,
                                    name: 'laterality',
                                    label: 'Right only'
                                },
                                {
                                    id: 1,
                                    name: 'laterality',
                                    label: 'Left only'
                                },
                            ],
                            onSelectValue: function (dialog, itemSet, selected_value) {
                                self.laterality = selected_value;
                                if (self.laterality) {
                                    const $latField = $('.popup-path-step-options .js-itemset[data-itemset-id="settings-list"] tbody tr#laterality td');
                                    if (self.laterality === '3') {
                                        $latField.html('<span class="oe-eye-lat-icons"><i class="oe-i laterality R small pad"></i>' +
                                            '<i class="oe-i laterality L small pad"></i></span>');
                                    } else if (self.laterality === '2') {
                                        $latField.html('<span class="oe-eye-lat-icons"><i class="oe-i laterality R small pad"></i></span>');
                                    } else {
                                        $latField.html('<span class="oe-eye-lat-icons"><i class="oe-i laterality L small pad"></i></span>');
                                    }
                                }
                            }
                        },
                        {
                            is_form: false,
                            title: 'Test settings',
                            id: 'settings-list',
                            items: [
                                {
                                    id: 'laterality',
                                    label: 'Laterality',
                                    value: null
                                },
                                {
                                    id: 'test-type',
                                    label: 'Test type',
                                    value: null
                                },
                                {
                                    id: 'option',
                                    label: 'SITA Algorithm',
                                    value: null
                                },
                            ]
                        },
                        {
                            is_form: true,
                            title: 'Insert position',
                            id: 'position',
                            items: [
                                {
                                    id: 0,
                                    name: 'path-position',
                                    label: 'End of pathway',
                                },
                                {
                                    id: 1,
                                    name: 'path-position',
                                    label: 'Todo: Next',
                                },
                                {
                                    id: 2,
                                    name: 'path-position',
                                    label: 'Todo: 2nd',
                                },
                                {
                                    id: 3,
                                    name: 'path-position',
                                    label: 'Todo: 3rd',
                                },
                            ]
                        }
                    ],
                    onReturn: function (dialog, selectedValues) {
                        for (let pathway_id of pathway_ids) {
                            self.newStep(step_type_id, pathway_id, {
                                preset_id: selectedValues[0].value,
                                test_type_id: selectedValues[1].value,
                                laterality: selectedValues[2].value,
                            }, selectedValues[3].value);
                        }
                        dialog.close();
                    }
                }).open();
                break;
            case this.options.exam_step_type_id:
                new OpenEyes.UI.Dialog.NewPathwayStep({
                    workflow_steps: this.options.workflows,
                    current_subspecialty: this.options.current_subspecialty_id,
                    current_firm: this.options.current_firm_id,
                    custom_options: [{
                        id: 'subspecialty',
                        name: 'Subspecialty',
                        option_values: this.options.subspecialties
                    },
                        {
                            id: 'context',
                            name: 'Context',
                            option_values: []
                        },
                        {
                            id: 'workflow_step',
                            name: 'Workflow Step',
                            option_values: []
                        }
                    ],
                    title: 'Add examination task',
                    onReturn: function (dialog, long_name, short_name, selected_custom_options) {
                        for (let pathway_id of pathway_ids) {
                            self.newStep(step_type_id, pathway_id, {
                                long_name,
                                short_name,
                                subspecialty_id: selected_custom_options[0],
                                firm_id: selected_custom_options[1],
                                workflow_step_id: selected_custom_options[2]
                            });
                            dialog.close();
                        }
                    }
                }).open();
                break;
            case this.options.letter_step_type_id:
                new OpenEyes.UI.Dialog.NewPathwayStep({
                    custom_options: [{
                        id: 'macro',
                        name: 'Macro',
                        option_values: this.options.macros,
                    }],
                    title: 'Add letter task',
                    onReturn: function (dialog, long_name, short_name, selected_custom_option) {
                        for (let pathway_id of pathway_ids) {
                            self.newStep(step_type_id, pathway_id, {
                                long_name,
                                short_name,
                                macro_id: selected_custom_option[0]
                            });
                            dialog.close();
                        }
                    }
                }).open();
                break;
            case this.options.generic_step_type_id:
                // Display dialog for entering step name, selecting insert position and selecting workflow.
                new OpenEyes.UI.Dialog.NewPathwayStep({
                    title: 'Add custom general task',
                    onReturn: function (dialog, long_name, short_name) {
                        for (let pathway_id of pathway_ids) {
                            self.newStep(step_type_id, pathway_id, {
                                long_name,
                                short_name,
                            });
                            dialog.close();
                        }
                    }
                }).open();
                break;
            case this.options.onhold_step_type_id:
                new OpenEyes.UI.Dialog.PathwayStepOptions({
                    title: 'Add pathway hold timer',
                    itemSets: [
                        {
                            is_form: true,
                            title: 'Timer',
                            id: 'timer',
                            items: [
                                {id: 1, label: 'Add 1 minute timer', name: 'duration'},
                                {id: 2, label: 'Add 2 minute timer', name: 'duration'},
                                {id: 5, label: 'Add 5 minute timer', name: 'duration'},
                                {id: 10, label: 'Add 10 minute timer', name: 'duration'},
                                {id: 15, label: 'Add 15 minute timer', name: 'duration'},
                                {id: 20, label: 'Add 20 minute timer', name: 'duration'},
                                {id: 30, label: 'Add 30 minute timer', name: 'duration'},
                            ],
                        }
                    ],
                    onReturn: function (dialog, selectedValues) {
                        let duration = selectedValues[0].value
                        for (let pathway_id of pathway_ids) {
                            self.newStep(
                                step_type_id,
                                pathway_id,
                                {
                                    short_name: duration,
                                    duration: duration,
                                }
                            );
                        }
                        dialog.close();
                    }
                }).open();
                break;
            default:
                for (let pathway_id of pathway_ids) {
                    this.newStep(step_type_id, pathway_id);
                }
                break;
        }
    };

    PathwayStepPicker.prototype.onSearchAssignee = function (e) {
        let self = this;
        clearTimeout(this.delaySearch);
        this.delaySearch = setTimeout(function () {
            let search_term = $(e.target).val();
            $(e.target).parent().find('.spinner-loader').show();
            $.get(
                self.options.base_url + 'worklist/getAssignees',
                { term: search_term },
                function (response) {
                    $(e.target).parent().find('.spinner-loader').hide();
                    $.each(response, function (id, item) {
                        $(selectors.assigneeList).empty();
                        $(selectors.assigneeList).append('<li data-id="' + item.id + '">' + item.label + '</li>');
                    });
                }
            );
        }, 500);
    };

    PathwayStepPicker.prototype.undoTodoStep = function () {
        const self = this;
        const selected = $(`${this.options.pathway_checkboxes}:checked`);
        const rows = selected.closest('tr');

        const result = {remaining: rows.length, hadError: false};
        const onAjaxResult = function (isError) {
            result.remaining -= 1;
            result.hadError |= isError;

            if (result.remaining <= 0) {
                $('.spinner').hide();

                if (result.hadError) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Unable to remove pathway steps from selected patients.\n\nPlease try again or contact support."
                    }).open();
                }
            }
        };

        $('.spinner').show();

        rows.each(function () {
            const row = $(this);
            const lastStep = row.find('.pathway .oe-pathstep-btn.todo:last-child');

            if (lastStep.length > 0) {
                const data = {
                    step_id: lastStep.data('pathstep-id'),
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                };

                $.ajax({
                    url: self.options.base_url + 'worklist/deleteStep',
                    type: 'POST',
                    data: data,
                    success: function () {
                        const pathwayId = row.children('.pathway').data('visit-id') || row.data('pathway-type-id');
                        const oldSteps = row.find('.active, .todo').map(function() { return $(this).data('long-name') }).get();

                        // This is a requested step so as long as it is deleted the order of other requested steps should be correct.
                        lastStep.remove();

                        const newSteps = oldSteps.slice(0, -1);

                        if (self.options.onChangePatientRow) {
                            self.options.onChangePatientRow('change-waiting-for', { pathwayId: pathwayId, oldSteps: oldSteps, newSteps: newSteps });
                        }

                        onAjaxResult(false);
                    },
                    error: function () {
                        onAjaxResult(true);
                    }
                });
            }
        });
    };

    PathwayStepPicker.prototype.reattachCheckboxHandlers = function () {
        $(this.options.pathway_checkboxes).off('change').on('change', this.onAddButtonClicked.bind(this));
    };

    exports.PathwayStepPicker = PathwayStepPicker;
}(OpenEyes.UI));
