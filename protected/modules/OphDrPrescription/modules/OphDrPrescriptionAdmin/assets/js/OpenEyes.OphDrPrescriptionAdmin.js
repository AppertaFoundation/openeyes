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
        templateSelector: '#medication_set_template'
    };

    DrugSetController.prototype.initTable = function () {
        $(this.options.tableSelector + ' tbody tr').on('mouseenter', function(){ $(this).css({'background-color':'lightblue'}); });
        $(this.options.tableSelector + ' tbody tr').on('mouseleave', function(){ $(this).css({'background-color':'unset'}); });
    };

    DrugSetController.prototype.initFilters = function () {
        var controller = this;

        $(document).on("keydown", "form", function(event) {

            if (event.key !== "Enter") {
                return true;
            } else {
                controller.refreshResult();
                return false;
            }
        });

        $('#set-filters').on('click', 'button', function () {
            $(this).toggleClass('selected green hint').blur();

            if ($(this).hasClass('js-all-sets')) {
                $('#set-filters button:not(.js-all-sets)').removeClass('selected green hint').blur();
            } else {
                $('#set-filters button.js-all-sets').removeClass('selected green hint').blur();
            }

            if (!$('#set-filters button.selected').length) {
                $('#set-filters button.js-all-sets').addClass('selected green hint').blur();
            }

            controller.refreshResult();
        });

        $(controller.options.tableSelector).on('click', '.pagination a:not(.selected)', function (e) {
            e.preventDefault();
            e.stopPropagation();

            let url = new URL(`${window.location.protocol}//${window.location.host}${$(this).attr('href')}`);
            let search_params = new URLSearchParams(url.search);

            controller.refreshResult(search_params.get('page'));
        });

        $('#et_search').on('click', function() {
            controller.refreshResult();
        });
    };

    DrugSetController.prototype.refreshResult = function (page = 1) {
        let controller = this;
        let data = {};
        let usage_codes = $('#set-filters button.selected').map(function (m, button) {
            let usage_code = $(button).data('usage_code');
            return usage_code ? usage_code : null;
        }).get();
        const search_term = $('#search_query').length ? $('#search_query').val().trim() : null;
        const subspecialty_id = $('#search_subspecialty_id').length ? $('#search_subspecialty_id').val() : null;
        const site_id = $('#search_site_id').length ? $('#search_site_id').val() : null;

        data.page = page;

        data.search = {};
        if (usage_codes.length) {
            data.search.usage_codes = usage_codes;
        } else {
            data.search.usage_codes = '';
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
            beforeSend: function() {

                // demo load spinner
                let $overlay = $('<div>', {class: 'oe-popup-wrap'});
                let $spinner = $('<div>', {class: 'spinner'});
                $overlay.append($spinner);
                $('body').prepend($overlay);
            },
            success: function(data) {
                let rows = [];

                if (data.items && data.items.length){
                    rows = $.map(data.items, function(item) {
                        let data = {};
                        Object.keys(item).forEach(function(key) {
                            data[key] = item[key];
                        });

                        return Mustache.render($(controller.options.templateSelector).html(), data);
                    });
                    $(controller.options.tableSelector + ' tbody').html(rows.join(''));
                } else {
                    const text = search_term ? `for: <b>${search_term}</b>` : '';
                    $(controller.options.tableSelector + ' tbody').html("<tr class='no-result'><td colspan='3'><small>No result found " +  text + "</small>");
                }

                $('.pagination-container').find('.pagination').replaceWith(data.pagination);

            },
            complete: function() {
                $('.oe-popup-wrap').remove();
            }
        });
    };

    exports.DrugSetController = DrugSetController;

})(OpenEyes.OphDrPrescriptionAdmin);