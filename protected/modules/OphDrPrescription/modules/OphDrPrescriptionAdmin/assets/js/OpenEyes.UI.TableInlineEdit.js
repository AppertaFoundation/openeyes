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
                $.each(trs, function(i, tr) {
                    const $tr = $(tr);
                    $tr.find('.js-text').hide();
                });

                $tapers.find('.js-text').hide();
                controller.showEditFields($tr, $tapers);

            } else if (action === 'cancel') {
                controller.hideEditControls($tr, $tapers);
                controller.showGeneralControls($tr);

                const trs = $(controller.options.tableSelector).find(`.js-row-of-${$tr.data('med_id')}`);
                $.each(trs, function(i, tr) {
                    const $tr = $(tr);
                    $tr.find('.js-text').show();
                    $tr.find('.js-input').hide();
                });

                $tapers.find('.js-text').show();
                $tapers.find('.js-input').hide();
            }
        });

        $(this.options.tableSelector).on('click', 'td a[data-action_type="save"]', function() {
            const $tr = $(this).closest('tr');
            controller.saveRow($tr);
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

    TableInlineEdit.prototype.saveRow = function($tr)
    {
        let controller = this;
        let data = {};
        let json_tapers = {};
const $actionsTd = $tr.find('td:last-child');

        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            $.each( $tr.find('.js-input'), function(i, input) {
                const $input = $(input);
                let name, value;
                if ($input.prop('tagName') === 'LABEL') {
                    $checkbox = $input.find('[type="checkbox"]');
                    name = $checkbox.attr('name');
                    value = $checkbox.is(':checked') ? 1 : 0;
                } else {
                    name = $input.attr('name');
                    value = $input.val();
                }

                data[name] = value;

            });
        });

        data.YII_CSRF_TOKEN = YII_CSRF_TOKEN;

        $.each($('.js-update-row-data'), function(i, input) {
            const name = $(input).data('name');
            const value = $(input).val();
            data[name] = value;
        });

        const $tapers = $('#meds-list tr[data-parent-med-id="' + data['Medication[id]'] + '"]');

        $.each($tapers, function (taperIndex, taper) {
            let taper_data = {};
            $.each( $(taper).find('.js-input'), function(inputIndex, input) {
                taper_data[$(input).attr('name')] = $(input).val();
            });
            json_tapers[taperIndex] = JSON.stringify(taper_data);
        });

        data['tapers'] = JSON.stringify(json_tapers);

        $.ajax({
            'type': 'POST',
            'data': data,
            'url': controller.options.updateUrl,
            'dataType': 'json',
            'beforeSend': function() {
                controller.hideEditControls($tr, $tapers);

                const $spinner = '<div class="js-spinner-as-icon"><i class="spinner as-icon"></i></div>';
                $actionsTd.append($spinner);
            },
            'success': function (resp) {
                if (resp.success === true) {
                    $actionsTd.append("<small style='color:red'>Saved.</small>");
                    controller.updateRowValuesAfterSave($tr);
                    if ($tapers !== undefined) {
                        controller.updateRowValuesAfterSave($tapers);
                    }
                    setTimeout(() => {
                        $actionsTd.find('small').remove();
                        controller.showGeneralControls($tr);
                    }, 2000);
                }
            },
            'error': function(resp){
                alert('Saving medication defaults FAILED. Please try again.');
                console.error(resp);
                controller.showGeneralControls($tr);
                if (typeof controller.options.onAjaxError === 'function') {
                    controller.options.onAjaxError();
                }
            },
            'complete': function(resp) {

                const result = JSON.parse(resp.responseText);

                $actionsTd.find('.js-spinner-as-icon').remove();

                if (result.success && result.success === true) {

                    const trs = $(controller.options.tableSelector).find(`.js-row-of-${tr_id}`);
                    $.each(trs, function(i, tr) {
                        const $tr = $(tr);
                        $tr.find('.js-text').show();
                        $tr.find('.js-input').hide();
                    });

                    $tapers.find('.js-text').show();
                    $tapers.find('.js-input').hide();
                    if (typeof controller.options.onAjaxComplete === 'function') {
                        controller.options.onAjaxComplete();
                    }
                } else if(result.errors) {
                    const trs = $(controller.options.tableSelector).find(`.js-row-of-${tr_id}`);
                    $.each(trs, function(i, tr) {
                        const $tr = $(tr);
                        $tr.find('.js-text').hide();
                        $tr.find('.js-input').show();
                    });

                    $tapers.find('.js-text').hide();
                    $tapers.find('.js-input').show();
                    controller.showEditControls($tr, $tapers);
                    let content = '';
                    Object.keys(result.errors).forEach(function(key) {
                        $input = $tr.find('input[name*="' + key + '"]').addClass('error');
                        if (content.length) {
                            content += '<br>';
                        }

                        content +=  `${result.errors[key]}`;
                    });

                    new OpenEyes.UI.Dialog.Alert({
                        content: content
                    }).open();

                }
            }
        });
    };

    TableInlineEdit.prototype.updateRowValuesAfterSave = function($tr) {
        const controller = this;
        const tr_id = $tr.data('med_id');
        const trs = $(this.options.tableSelector).find(`.js-row-of-${tr_id}`);
        $.each(trs, function(i, tr) {
            const $tr = $(tr);
            controller.updateIndividualRowValuesAfterSave($tr);
        });
    };

    TableInlineEdit.prototype.updateIndividualRowValuesAfterSave = function($tr) {
        const controller = this;
        $.each($tr.find('.js-input-wrapper'), function(i, wrapper) {
            console.log($(wrapper));
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
                selectedText = $input.find('option:selected').text();
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
        let data = {};

        data.YII_CSRF_TOKEN = YII_CSRF_TOKEN;

        $.each( $tr.find('.js-input'), function(i, input) {
            const $input = $(input);
            data[$input.attr('name')] = $input.val();
        });

        $.ajax({
            'type': 'POST',
            'data': data,
            'url': controller.options.deleteUrl,
            'dataType': 'json',
            'beforeSend': function() {
                controller.hideEditControls($tr, $tapers);
                controller.hideGeneralControls($tr);

                const $spinner = '<div class="js-spinner-as-icon"><i class="spinner as-icon"></i></div>';
                $actionsTd.append($spinner);
            },
            'success': function (resp) {
                if (resp.success === true) {
                    $actionsTd.append("<small style='color:red'>Deleted.</small>");
                    $tr.fadeOut(1000, function(){ $(this).remove(); });
                    $tapers.fadeOut(1000, function () { $(this).remove();});
                }
            },
            'error': function(resp){
                alert('Remove medication from set FAILED. Please try again.');
                console.error(resp);
                if (typeof controller.onAjaxError === 'function') {
                    controller.onAjaxError();
                }
            },
            'complete': function(){
                $actionsTd.find('.js-spinner-as-icon').remove();
                if (typeof controller.onAjaxComplete === 'function') {
                    controller.onAjaxComplete();
                }
            }
        });
    };

    exports.TableInlineEdit = TableInlineEdit;

}(OpenEyes));