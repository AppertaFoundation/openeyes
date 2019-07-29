var OpenEyes = OpenEyes || {};

OpenEyes.OphDrPrescriptionAdmin = OpenEyes.OphDrPrescriptionAdmin || {};

(function (exports) {
    function DrugSetController(options) {
        this.options = $.extend(true, {}, DrugSetController._defaultOptions, options);

        this.initFilters();
        this.initTable();
    }

    DrugSetController._defaultOptions = {
        tableSelector: '#drugset-list',
        searchUrl: '/OphDrPrescription/admin/DrugSet/search',
        deleteUrl: '/OphDrPrescription/admin/drugSet/delete',
        templateSelector: '#medication_set_template',
        deleteButtonSelector: '#delete_sets'
    };

    DrugSetController.prototype.initTable = function () {
        let controller = this;
        $(this.options.tableSelector).on('mouseenter', 'tbody tr', function(){ $(this).css({'background-color':'lightblue'}); });
        $(this.options.tableSelector).on('mouseleave', 'tbody tr', function(){ $(this).css({'background-color':'unset'}); });

        $(this.options.deleteButtonSelector).on('click', function(event) {
            event.preventDefault();
            controller.deleteSets();
        });
    };

    DrugSetController.prototype.initFilters = function () {
        let controller = this;

        $(document).on("keydown", "form", function(event) {

            if (event.key !== "Enter") {
                return true;
            } else {
                controller.refreshResult();
                return false;
            }
        });

        $('#set-filters').on('click', 'button:not(.green.hint)', function () {
            $(this).parent().find('button').removeClass('green hint');
            $(this).addClass('green hint').blur();
            controller.refreshResult();
        });

        $(controller.options.tableSelector).on('click', '.pagination a:not(.green.hint)', function (e) {
            e.preventDefault();
            e.stopPropagation();

            let url = new URL(`${window.location.protocol}//${window.location.host}${$(this).attr('href')}`);
            let search_params = new URLSearchParams(url.search);

            controller.refreshResult(search_params.get('page'));
        });

        $('#et_search').on('click', function() {
            controller.refreshResult();
        });

        if (!$('#set-filters button.green.hint').length) {
            // if nothing is selected for some reason than we selects the first one
            // this should not happen but just in case
            $('#set-filters button:first-child').trigger('click');
        }
    };


    DrugSetController.prototype.addRow = function(data) {
        return Mustache.render($(this.options.templateSelector).html(), data);
    };

    DrugSetController.prototype.refreshResult = function (page = 1, callback = null) {
        let controller = this;
        let data = {};
        let usage_codes = $('#set-filters button.green.hint').map(function (m, button) {
            let usage_code = $(button).data('usage_code_id');
            return usage_code ? usage_code : null;
        }).get();
        const search_term = $('#search_query').length ? $('#search_query').val().trim() : null;
        const subspecialty_id = $('#search_subspecialty_id').length ? $('#search_subspecialty_id').val() : null;
        const site_id = $('#search_site_id').length ? $('#search_site_id').val() : null;

        data.page = page;

        data.search = {};
        if (usage_codes.length) {
            data.search.usage_code_ids = usage_codes;
        } else {
            data.search.usage_code_ids = '';
        }

        if (search_term) {
            data.search.query = search_term;
        }

        if (subspecialty_id) {
            data.search.subspecialty_id = subspecialty_id;
        }

        if (site_id) {
            data.search.site_id = site_id;
        }

        $.each($('.js-search-data'), function(i, input) {
            const name = $(input).data('name');
            const value = $(input).val();
            data.search[name] = value;
        });

        $.ajax({
            url: controller.options.searchUrl,
            dataType: "json",
            data: data,
            beforeSend: controller.showOverlay,
            success: function(data) {
                let rows = [];

                if (data.items && data.items.length) {
                    rows = $.map(data.items, function(item) {
                        let data = {};
                        Object.keys(item).forEach(function(key) {
                            data[key] = item[key];
                        });

                        return Mustache.render($(controller.options.templateSelector).html(), data);
                    });
                    $(controller.options.tableSelector + ' tbody').html(rows.join(''));
                    $('.empty-set').hide();
                    $(controller.options.tableSelector + ', #search_query').show();
                } else {
                    const text = search_term ? `for: <b>${search_term}</b>` : '';
                    $(controller.options.tableSelector + ' tbody').html("<tr class='no-result'><td colspan='3'><small>No result found " +  text + "</small>");
                }

                $('.pagination-container').find('.pagination').replaceWith(data.pagination);

            },
            complete: function() {
                $('.oe-popup-wrap').remove();

                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    };

    DrugSetController.prototype.deleteSets = function () {
        let controller = this;
        let data = {};
        data['delete-ids'] = $.map($(controller.options.tableSelector + ' tbody tr'), function(tr, key) {
            return $(tr).find('input[type=checkbox]:checked').val();
        });

        data['usage-code'] = $('#set-filters button.green.hint').data('usage_code');

        $.ajax({
            url: controller.options.deleteUrl,
            dataType: "json",
            data: data,
            beforeSend: controller.showOverlay,
            success: function(data) {

                if (data.message && data.message.length) {

                    // because $('.oe-popup-wrap').remove(); will remove alert as well
                    setTimeout(() => {
                        new OpenEyes.UI.Dialog.Alert({
                            content: data.message
                        }).open();
                    }, 500);

                }
            },
            complete: function() {
                controller.refreshResult();
            }
        });

    };

    DrugSetController.prototype.showOverlay = function () {
        if (!$('.oe-popup-wrap').length) {
            // load spinner
            let $overlay = $('<div>', {class: 'oe-popup-wrap'});
            let $spinner = $('<div>', {class: 'spinner'});
            $overlay.append($spinner);
            $('body').prepend($overlay);
        }
    };

    exports.DrugSetController = DrugSetController;

})(OpenEyes.OphDrPrescriptionAdmin);