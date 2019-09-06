var OpenEyes = OpenEyes || {};

OpenEyes.OphDrPrescriptionAdminMedication = OpenEyes.OphDrPrescriptionAdminMedication || {};

(function (exports) {
    function MedicationController(options) {
        this.options = $.extend(true, {}, MedicationController._defaultOptions, options);

        this.initSearch();
        this.initTable();
    }

    MedicationController.prototype.addOption = function (extraOptions) {
        let optionsToAdd = JSON.parse(extraOptions);
        this.options = $.extend(true, {}, this.options, optionsToAdd);
    };

    MedicationController._defaultOptions = {
        tableSelector: '#medication-list',
        searchUrl: '/OphDrPrescription/admin/Medication/search',
        deleteUrl: '/OphDrPrescription/admin/Medication/delete',
        templateSelector: '#medication_template',
        deleteButtonSelector: '#delete_medication'
    };

    MedicationController.prototype.initTable = function () {
        $(this.options.tableSelector).on('mouseenter', 'tbody tr', function () {
            $(this).css({ 'background-color' : 'lightblue' });
        });
        $(this.options.tableSelector).on('mouseleave', 'tbody tr', function () {
            $(this).css({ 'background-color' : 'unset' });
        });

        $(this.options.deleteButtonSelector).on('click', event => {
            event.preventDefault();
            this.deleteMedications();
        });
    };

    MedicationController.prototype.addRow = function (data) {
        return Mustache.render($(this.options.templateSelector).html(), data);
    };

    MedicationController.prototype.refreshResult = function (page = 1, callback = null) {
        let data = {};

        data.page = page;
        data.search = {};

        this.options.searchFields.map( x => {
            if ($('#search_'+x).length)
                data.search[x] = $('#search_'+x).val().trim();
            });

        $.ajax({
            url: this.options.searchUrl,
            dataType: 'json',
            data: data,
            beforeSend: this.showOverlay,
            success: data => {
                let rows = [];

                if (data.items && data.items.length) {
                    rows = $.map(data.items, item => {
                        let data = {};
                        Object.keys(item).forEach(key => {
                            data[key] = item[key];
                        });

                        return Mustache.render($(this.options.templateSelector).html(), data);
                    });
                    $(this.options.tableSelector + ' tbody').html(rows.join(''));
                    $('.empty-set').hide();
                    $(this.options.tableSelector + ', #search_query').show();
                } else {
                    $(this.options.tableSelector + ' tbody').html('<tr class="no-result"><td colspan="3"><small>No results found</small>');
                }

                $('.pagination-container').find('.pagination').replaceWith(data.pagination);
            },
            complete: () => {
                $('.oe-popup-wrap').remove();
                if (typeof callback === 'function')
                    callback();
            }
        });
    };

    MedicationController.prototype.initSearch = function () {
        $('#et_search').on('click', () => {
            console.log(this);
            this.refreshResult();
        });
    };

    MedicationController.prototype.showOverlay = function () {
        if (!$('.oe-popup-wrap').length) {
            let $overlay = $('<div>', {class: 'oe-popup-wrap'});
            let $spinner = $('<div>', {class: 'spinner'});
            $overlay.append($spinner);
            $('body').prepend($overlay);
        }
    };

    MedicationController.prototype.deleteMedications = function () {
        let data = {};
        data['delete-ids'] = $.map($(this.options.tableSelector + ' tbody tr'), function(tr) {
            return $(tr).find('input[type=checkbox]:checked').val();
        });

        $.ajax({
            url: this.options.deleteUrl,
            dataType: "json",
            data: data,
            beforeSend: this.showOverlay,
            success: data => {
                if (data.message && data.message.length) {
                    // because $('.oe-popup-wrap').remove(); will remove alert as well
                    setTimeout(() => {
                        new OpenEyes.UI.Dialog.Alert({
                            content: data.message
                        }).open();
                    }, 500);
                }
            },
            complete: () => {
                this.refreshResult();
            }
        });
    };

    exports.MedicationController = MedicationController;

})(OpenEyes.OphDrPrescriptionAdminMedication);
