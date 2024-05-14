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
        let snapshot = [];
        let $rows = $(controller.options.tableSelector).find('tr[id*="medication"]');

        $rows.each(function (i, row) {
            const $row = $(row);
            snapshot[$row.attr('id')] = $row.clone().wrap('<div>').parent().html(); //take initial snapshot of the table incase user cancels their action
        });


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

                controller.showEditFields($tr, $tapers);

            } else if (action === 'cancel') {

                const trs = $(controller.options.tableSelector).find(`.js-row-of-${$tr.data('med_id')}`);
                $tapers.each(function (i, taper) {
                    trs.push(taper);
                });

                $.each(trs, function(i, tr) {
                    let $tr = $(tr);
                    $tr.replaceWith(snapshot[$tr.attr('id')]); //replace table row with snapshot of latest edit
                });

            }
        });

        $('#meds-list').bind('taperAdded', function () { //add taper HTML to snapshot when adding it
            let $taper = $(this).find('tr.prescription-taper.new').clone();
            $taper.find('.js-text').show();
            $taper.find('.js-input').hide();
            $taper.find('.js-remove-taper').hide();
            snapshot[$taper.attr('id')] = $taper.wrap('<div>').parent().html();
            $(this).find('tr.prescription-taper.new').removeClass('new');
        });

        $('#meds-list').bind('medicationAdded', function () { //add medication row HTML to snapshot when adding it via AdderDialog
            let $rows = $(this).find('tr.new[class*="js-row-of"]').clone();
            $rows.find('.js-text').show();
            $rows.find('.js-input').hide();
            $rows.each(function (i, row) {
                let $row = $(row);
                snapshot[$row.attr('id')] = $row.wrap('<div>').parent().html();
            });
            $(this).find('tr.new[class*="js-row-of"]').removeClass('new');
        });

        $(this.options.tableSelector).on('click', 'td a[data-action_type="save"]', function() {
            const $tr = $(this).closest('tr');
            controller.updateRow($tr);
            snapshot = controller.updateSnapshot($tr, snapshot);
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

    //update the snapshot for the row when the user saves
    TableInlineEdit.prototype.updateSnapshot = function($row, snapshot) {
        let controller = this;
        let tr_id = $row.data('med_id');
        let $trs = $(controller.options.tableSelector).find(`.js-row-of-${tr_id}`);
        let $tapers = $('#meds-list tr[data-parent-med-id="' + tr_id + '"]');
        $tapers.each(function (i, taper) {
            $trs.push(taper);
        });

        $trs.each(function (i, tr) {
            const $tr = $(tr);
            snapshot[$tr.attr('id')] = $tr.clone().wrap('<div>').parent().html();
        });

        return snapshot;
    };

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
        $tapers.find('.js-remove-taper').hide();
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
        const value = $input.val();

        $text.toggle(state === 'show');
        $input.toggle(state === 'edit');
        $wrapper.find('div').show();
        if ($wrapper.find('div').hasClass('js-hide-field')) {
            $wrapper.find('div').hide();
            $text.attr('data-id', '');
        }

        if (showEditValue === true) { // set correct HTML and values for snapshot and for saving
            if ($input.prop('tagName') === 'SELECT') {
                const $selectedOption = $input.find('option:selected');
                if(typeof $selectedOption.attr('label') != "undefined") {
                    selectedText = $selectedOption.attr('label');
                } else if (value !== ''){
                    selectedText = $selectedOption.text();
                }
                if ($input.find('option[selected="selected"]').val() !== $selectedOption.val()) {
                    $input.find('option[selected="selected"]').removeAttr('selected');
                    if (value !== '') {
                        $selectedOption.attr('selected', 'selected');
                    }
                }
            } else if ($input.prop('tagName') === 'LABEL') {
                const $first = $input.find('[type="checkbox"]');
                if ($first.is(':checked')) {
                    selectedText = 'yes';
                    $first.attr('checked', 'checked');
                } else {
                    selectedText = 'no';
                    $first.removeAttr('checked');
                }
            } else if (value !== ''){
                selectedText = value;
                $input.attr('value', value);
            } else {
                $input.removeAttr('value');
            }

            const label = $text.data('display-label') !== undefined ? $text.data('display-label') : '';
            $text.text(label + selectedText);
            $text.data('id', value);
        }
    };

    TableInlineEdit.prototype.deleteRow = function($tr, $tapers)
    {
        const $actionsTd = $tr.find('td.actions');
        const controller = this;

        $.ajax({
            'type': 'POST',
            'url': controller.options.deleteUrl,
            'data': {
                id: $tr.data('id'),
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            'success': function() {
                controller.hideEditControls($tr, $tapers);
                controller.hideGeneralControls($tr);
                $actionsTd.append("<small style='color:red'>Deleted.</small>");
                $tr.fadeOut(1000, function(){ $(this).remove(); });
                $tapers.fadeOut(1000, function () { $(this).remove();});
            },
            'error': function () {
                new OpenEyes.UI.Dialog.Alert({
                    content: "This medication cannot be deleted from the current set."
                }).open();
            }
        });
    };

    exports.TableInlineEdit = TableInlineEdit;

}(OpenEyes));