var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    function TableInlineEdit(options) {
        this.options = $.extend(true, {}, TableInlineEdit._defaultOptions, options);
        this.initTriggers();
    }

    TableInlineEdit._defaultOptions = {
        tableSelector: '.js-inline-edit',
        updateUrl: '/OphDrPrescription/admin/DrugSet/updateMedicationDefaults',
        deleteUrl: '/OphDrPrescription/admin/DrugSet/removeMedicationFromSet',
        onAjaxError: function() {}
    };

    TableInlineEdit.prototype.initTriggers = function () {
        const controller = this;
        $(this.options.tableSelector + ' td.actions').on('click', 'a', function() {
            const $tr = $(this).closest('tr');
            const action = $(this).data('action_type');

            if (action === 'edit') {
                controller.showEditControls($tr);
                controller.hideGeneralControls($tr);
                $tr.find('.js-text, .js-input').toggle();
            } else if (action === 'cancel') {
                controller.hideEditControls($tr);
                controller.showGeneralControls($tr);
                $tr.find('.js-text, .js-input').toggle();
            }
        });

        $(this.options.tableSelector + ' td.actions').on('click', 'a[data-action_type="save"]', function(){
            const $tr = $(this).closest('tr');
            controller.saveRow($tr);
        });

        $(this.options.tableSelector + ' td.actions').on('click', 'a[data-action_type="delete"]', function(){
            const $tr = $(this).closest('tr');
            controller.deleteRow($tr);
        });
    };

    TableInlineEdit.prototype.showEditControls = function($tr)
    {
        $tr.find('td.actions').find('a[data-action_type="save"], a[data-action_type="cancel"]').show();
    };

    TableInlineEdit.prototype.hideEditControls = function($tr)
    {
        const $actionTd = $tr.find('td.actions');

       // $actionTd.find('a[data-action_type="edit"], a[data-action_type="delete"]').show();
        $actionTd.find('a[data-action_type="save"], a[data-action_type="cancel"]').hide();
    };

    TableInlineEdit.prototype.hideGeneralControls = function($tr)
    {
        $tr.find('td.actions').find('a[data-action_type="edit"], a[data-action_type="delete"]').hide();
    };

    TableInlineEdit.prototype.showGeneralControls = function($tr)
    {
        $tr.find('td.actions').find('a[data-action_type="edit"], a[data-action_type="delete"]').show();
    };

    TableInlineEdit.prototype.saveRow = function($tr)
    {
        let controller = this;
        let data = {};
        const $actionsTd = $tr.find('td.actions');
        $.each( $tr.find('.js-input'), function(i, input) {
            const $input = $(input);
            data[$input.attr('name')] = $input.val();
        });

        data.YII_CSRF_TOKEN = YII_CSRF_TOKEN;

        $.each($('.js-update-row-data'), function(i, input) {
            const name = $(input).data('name');
            const value = $(input).val();
            data[name] = value;
        });

        $.ajax({
            'type': 'POST',
            'data': data,
            'url': controller.options.updateUrl,
            'dataType': 'json',
            'beforeSend': function() {
                controller.hideEditControls($tr);

                const $spinner = '<div class="js-spinner-as-icon"><i class="spinner as-icon"></i></div>';
                $actionsTd.append($spinner);
            },
            'success': function (resp) {
                if (resp.success === true) {
                    $actionsTd.append("<small style='color:red'>Saved.</small>");
                    setTimeout(() => {
                        $actionsTd.find('small').remove();
                        controller.showGeneralControls($tr);
                    }, 2000);
                }
            },
            'error': function(resp){
                alert('Saving medication defaults FAILED. Please try again.');
                console.error(resp);
                if (typeof onAjaxError === 'function') {
                    onAjaxError();
                }
            },
            'complete': function(){
                $actionsTd.find('.js-spinner-as-icon').remove();
            }
        });
    };

    TableInlineEdit.prototype.deleteRow = function($tr)
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
                controller.hideEditControls($tr);
                controller.hideGeneralControls($tr);

                const $spinner = '<div class="js-spinner-as-icon"><i class="spinner as-icon"></i></div>';
                $actionsTd.append($spinner);
            },
            'success': function (resp) {
                if (resp.success === true) {
                    $actionsTd.append("<small style='color:red'>Deleted.</small>");
                    $tr.fadeOut(1000, function(){ $(this).remove(); });
                }
            },
            'error': function(resp){
                alert('Remove medication from set FAILED. Please try again.');
                console.error(resp);
                if (typeof onAjaxError === 'function') {
                    onAjaxError();
                }
            },
            'complete': function(){
                $actionsTd.find('.js-spinner-as-icon').remove();
            }
        });
    };
    exports.TableInlineEdit = TableInlineEdit;

}(OpenEyes));