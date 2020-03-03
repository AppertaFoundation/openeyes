var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    function TableInlineEdit(options) {
        this.options = $.extend(true, {}, TableInlineEdit._defaultOptions, options);
        this.initTriggers();
    }

    TableInlineEdit._defaultOptions = {
        tableSelector: '.js-inline-edit',
        updateUrl: '/OphDrPrescription/admin/autoSetRule/updateMedicationDefaults',
        deleteUrl: '/OphDrPrescription/admin/autoSetRule/removeMedicationFromSet',
        onAjaxError: function() {},
        onAjaxComplete: function() {}
    };

    TableInlineEdit.prototype.initTriggers = function () {
        const controller = this;
        $(this.options.tableSelector).on('click', 'td a', function() {
            const $tr = $(this).closest('tr');
            const $tapers = $(controller.options.tableSelector).find('tr[data-parent-med-id="' + $tr.attr('data-med_id') + '"]');

            const action = $(this).data('action_type');

            if (action === 'edit') {
                controller.hideGeneralControls($tr);
                controller.showEditControls($tr, $tapers);

                const trs = $(controller.options.tableSelector).find(`.js-row-of-${$tr.data('med_id')}`);

                $tapers.each(function (i, taper) {
                    trs.push(taper);
                });

                $.each(trs, function(i, tr) {
                    const $tr = $(tr);
                    $tr.find('.js-text').hide();
                    controller.setInputValues($tr);
                });

                $tapers.find('.js-text').hide();
                controller.showEditFields($tr, $tapers);

            } else if (action === 'cancel') {
                controller.hideEditControls($tr, $tapers);
                controller.showGeneralControls($tr);

                const trs = $(controller.options.tableSelector).find(`.js-row-of-${$tr.data('med_id')}`);
                $tapers.each(function (i, taper) {
                    trs.push(taper);
                });

                $.each(trs, function(i, tr) {
                    const $tr = $(tr);
                    controller.setInputValues($tr);
                    let $dispense_condition = $tr.find('.js-prescription-dispense-condition');
                    if ($dispense_condition) {
                        let $dispense_location = $dispense_condition.closest('tr').find('.js-prescription-dispense-location');
                        let $dispense_condition_text = $dispense_condition.find('.js-text');
                        $dispense_location.toggle($dispense_condition_text.data('id') !== '');
                    }
                    $tr.find('.js-text').show();
                    $tr.find('.js-input').hide();
                });

            }
        });

        $(this.options.tableSelector).on('click', 'td a[data-action_type="save"]', function() {
            const $tr = $(this).closest('tr');
            controller.updateRow($tr);
        });

        $(this.options.tableSelector).on('click', 'td a[data-action_type="remove"]', function () {
            let $tr = $(this).closest('tr');
            $tr.remove();
        });

        $(this.options.tableSelector).on('click', 'td a[data-action_type="delete"]', function() {
            const $tr = $(this).closest('tr');
            const text = '<button class="red hint js-delete-row" data-row_med_id="' + $tr.data('med_id') + '">Delete</button> <button class="green js-delete-row-cancel">Cancel</button>';
            let leftPos, toolCSS;

            // get icon DOM position
            let iconPos = $(this)[ 0 ].getBoundingClientRect();
            let iconCenter = iconPos.width / 2;

            // check for the available space for tooltip:
            if ( ( $( window ).width() - iconPos.left) < 100 ){
                leftPos = (iconPos.left - 188) + iconPos.width; // tooltip is 200px (left offset on the icon)
                toolCSS = "oe-tooltip offset-left oe-tooltip-confirm";
            } else {
                leftPos = (iconPos.left - 100) + iconCenter - 0.5; 	// tooltip is 200px (center on the icon)
                toolCSS = "oe-tooltip oe-tooltip-confirm";
            }

            // add, calculate height then show (remove 'hidden')
            var tip = $( "<div></div>", {
                "class": toolCSS,
                "style":"left:"+leftPos+"px; top:0;"
            });
            // add the tip (HTML as <br> could be in the string)
            tip.html(text);

            $('body').append(tip);
            // calc height:
            var h = $(".oe-tooltip").height();
            // update position and show
            var top = iconPos.y - h - 25;

            $(".oe-tooltip").css({"top":top+"px", width:'unset'});

        });

        $('body').on('click', '.oe-tooltip .js-delete-row', function() {
            const id = $(this).data('row_med_id');

            const $tr = $(controller.options.tableSelector).find('tr[data-med_id="' + id + '"]');
            const $tapers = $(controller.options.tableSelector).find('tr[data-parent-med-id="' + id + '"]');

            controller.deleteRow($tr, $tapers);
            $('.oe-tooltip-confirm').remove();
        });

        $('body').on('click', '.oe-tooltip .js-delete-row-cancel', function() {
            $('.oe-tooltip-confirm').remove();
        });
    };

    TableInlineEdit.prototype.showEditFields = function($tr, $tapers)
    {
        const controller = this;
        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            $.each($tr.find('.js-input-wrapper'), function(i, wrapper) {
                controller.setInputState($(wrapper), 'edit', false);
            });
        });

        if ($tapers !== undefined) {
            $tapers.find('.js-input').show();
        }
    };

    TableInlineEdit.prototype.setInputValues = function($tr) {
        let $tds = $tr.find('.js-input-wrapper');
        $tds.each(function (j, td) {
            let text = $(td).find('.js-text').text().trim();
            let $input = $(td).find('.js-input');
            if (text === '-') { //handles if text is empty, matches to input empty text value
                text = $input.attr('type') === 'text' ? '' : '-- select --';
            } else if (text.includes('Print to')) {
                text = 'Print to {form_type}';
            }
            let $option = $input.find('option:contains("' + text + '")');
            let value = $option.val();
            if ($option.length === 0) { // if input is not a select tag
                if ($input.prop('tagName') === 'LABEL') { //for includes parent and includes child checkboxes
                    $input = $(td).find('input[type="checkbox"]');
                    value = text.includes('yes') ? 1 : 0;
                } else {
                    value = $input.attr('type') === 'text' ? text : [];  // handles input is just a text field or dispense location
                }
            }
            $input.val(value);
        });
    }

    TableInlineEdit.prototype.showEditControls = function($tr, $tapers)
    {
        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            $tr.find('td').find('a[data-action_type="save"], a[data-action_type="cancel"]').show();
        });

        if ($tapers !== undefined) {
            $tapers.find('td').find('a[data-action_type="remove"]').show();
        }
    };

    TableInlineEdit.prototype.hideEditControls = function($tr, $tapers)
    {
        const trs = $(this.options.tableSelector).find(`.js-row-of-${$tr.data('med_id')}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            $tr.find('td').find('a[data-action_type="save"], a[data-action_type="cancel"]').hide();
        });
    };

    TableInlineEdit.prototype.hideGeneralControls = function($tr)
    {
        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            $tr.find('td').find('a[data-action_type="edit"], a[data-action_type="delete"], button[data-action="add-taper"]').hide();
        });
    };

    TableInlineEdit.prototype.showGeneralControls = function($tr)
    {
        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            $tr.find('td').find('a[data-action_type="edit"], a[data-action_type="delete"], button[data-action="add-taper"]').show();
        });
    };

    TableInlineEdit.prototype.updateRow = function($tr)
    {
        let controller = this;

        let tr_id = $tr.data('med_id');
        let $trs = $(controller.options.tableSelector).find(`.js-row-of-${tr_id}`);
        let $tapers = $('#meds-list tr[data-parent-med-id="' + tr_id + '"]');

        controller.updateRowValues($tr);
        if ($tapers !== undefined) {
            $.each($tapers, function (taperIndex, taper) {
                controller.updateIndividualRowValues($(taper));
            });
        }

        $.each($trs, function(i, tr) {
            const $tr = $(tr);
            $tr.find('.js-text').show();
            $tr.find('.js-input').hide();
        });

        $tapers.find('.js-text').show();
        $tapers.find('.js-input').hide();
        controller.showGeneralControls($tr);
        controller.hideEditControls($tr);
    };

    TableInlineEdit.prototype.updateRowValues = function($tr) {
        const controller = this;
        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            controller.updateIndividualRowValues($tr);
        });
    };

    TableInlineEdit.prototype.updateIndividualRowValues= function($tr) {
        const controller = this;
        $.each($tr.find('.js-input-wrapper'), function(i, wrapper) {
            controller.setInputState($(wrapper), 'show', true);
        });
    };

    TableInlineEdit.prototype.setInputState = function($wrapper, state, showEditValue) {
        const $input = $wrapper.find('.js-input');
        const $text = $wrapper.find('.js-text');
        let selectedText = '-';

        $text.toggle(state === 'show');
        $input.toggle(state === 'edit');

        if (showEditValue === true) {

            if ($input.prop('tagName') === 'SELECT' && $input.val() && $input.val() !== '') {
                let $selectedOption = $input.find('option:selected');
                if(typeof $selectedOption.attr('label') != "undefined") {
                    selectedText = $selectedOption.attr('label');
                } else {
                    selectedText = $input.find('option:selected').text();
                }
            } else if ($input.prop('tagName') === 'LABEL') {
                const $first = $input.find('[type="checkbox"]');
                selectedText = $first.is(':checked') ? 'yes' : 'no';
            } else {
                selectedText = $input.val();
            }

            const label = $text.data('display-label') !== undefined ? $text.data('display-label') : '';
            $text.text(label + selectedText);
            $text.data('id', $input.val());
        }
    };

    TableInlineEdit.prototype.deleteRow = function($tr, $tapers)
    {
        const $actionsTd = $tr.find('td.actions');
        const controller = this;

        controller.hideEditControls($tr, $tapers);
        controller.hideGeneralControls($tr);
        $actionsTd.append("<small style='color:red'>Deleted.</small>");
        $tr.fadeOut(1000, function(){ $(this).remove(); });
        $tapers.fadeOut(1000, function () { $(this).remove();});
    };

    exports.TableInlineEdit = TableInlineEdit;

}(OpenEyes));