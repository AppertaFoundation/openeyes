var OpenEyes = OpenEyes || {};

OpenEyes.OphDrPrescriptionAdmin = OpenEyes.OphDrPrescriptionAdmin || {};

(function (exports) {
    function DrugSetController(options) {
        this.options = $.extend(true, {}, DrugSetController._defaultOptions, options);
        
        this.loadingOverlay = new OpenEyes.UI.LoadingOverlay();
        this.initFilters();
        this.initTable();
        this.bindFilterButtons();
    }

    DrugSetController._defaultOptions = {
        tableSelector: '#drugset-list',
        searchUrl: '/OphDrPrescription/admin/DrugSet/search',
        deleteUrl: '/OphDrPrescription/admin/drugSet/delete',
        templateSelector: '#medication_set_template',
        deleteButtonSelector: '#delete_sets',
        rowLink: 'med_id'
    };

    DrugSetController.prototype.initTable = function () {
        let controller = this;
        $(this.options.tableSelector).on('mouseenter', 'tbody tr', function(){ $(this).css({'cursor':'default'}); });

        $(this.options.deleteButtonSelector).on('click', function(event) {
            event.preventDefault();
            controller.deleteSets();
        });

        $(this.options.tableSelector).on('click', '.js-add-taper', function () {

            const $tr = $(this).closest('tr');
            const id = $tr.data(controller.options.rowLink);
            controller.addTaper($tr);
            let $taper = $(controller.options.tableSelector).find('tr[data-parent-med-id="' + $tr.attr('data-med_id') + '"]');
            if ($(`.js-row-of-${id}.js-addition-line`).find('.js-tick-set-medication').is(':hidden')) { //don't trigger if edit fields already shown
                $(`.js-row-of-${id}`).find('.js-edit-set-medication').trigger('click');
            } else {
                $taper.find('.js-remove-taper').show();
                $taper.find('.js-input').show();
                $taper.find('.js-text').hide();
            }
            return false;
        });

        if ($(this.options.tableSelector).find('tbody tr').length > 0) {
            $(this.options.tableSelector).show();
            $('.empty-set').hide();
        }
    };

    DrugSetController.prototype.bindFilterButtons = function () {
        // Set value on load
        this.selected_code_filter = $('.js-set-select.green').data('usage_code_id');

        $('#et_add_drugset').click( e => {
            e.preventDefault();
            window.location.href = baseUrl + $(e.target).data('uri') + '?usage_code=' + this.selected_code_filter;
        });
    };

    DrugSetController.prototype.addTaper = function($row) {
        let data_med_id = $row.data('med_id');
        let data_parent_key = $row.data('key');
        let next_taper_count = 0;
        let last_taper_count;

        let $tapers = $('#meds-list tr[data-parent-med-id="' + data_med_id + '"]');
        if($tapers.length > 0) {
            last_taper_count = parseInt($tapers.last().attr("data-taper"));
            next_taper_count = last_taper_count + 1;
        }

        var markup = Mustache.render(
            $('#medication_item_taper_template').html(),
            {
                'data_parent_key' : data_parent_key,
                'data_med_id' : data_med_id,
                'taper_count' : next_taper_count
            });

        let $lastrow = $('#meds-list tr.js-addition-line[data-med_id="' + data_med_id + '"]');

        if($tapers.length>0) {
            $lastrow = $tapers.last();
        }

        $(markup).insertAfter($lastrow);
        $('#meds-list').trigger('taperAdded');

        return false;

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

            controller.selected_code_filter = $(this).data('usage_code_id');
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
            beforeSend: () => controller.loadingOverlay.open(),
            success: function(data) {
                let rows = [];

                if (data.items && data.items.length) {
                    rows = $.map(data.items, function(item, index) {
                        let data = {};
                        Object.keys(item).forEach(function(key) {
                            data[key] = item[key];
                        });
                        data.key = index;

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
                controller.loadingOverlay.close();

                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    };

    DrugSetController.prototype.deleteSets = function () {
        let controller = this;
        let data = {};
        data['delete-ids'] = $.map($(controller.options.tableSelector + ' tbody tr'), function(tr) {
            return $(tr).find('input[type=checkbox]:checked').val();
        });

        data['usage-code'] = $('#set-filters button.green.hint').data('usage_code_id');

        $.ajax({
            url: controller.options.deleteUrl,
            dataType: "json",
            data: data,
            beforeSend: () => controller.loadingOverlay.open(),
            success: function(data) {
                if (data.message && data.message.length) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: data.message,
                    }).open();
                }
            },
            complete: function() {
                controller.refreshResult();
            }
        });

    };

    exports.DrugSetController = DrugSetController;

})(OpenEyes.OphDrPrescriptionAdmin);
