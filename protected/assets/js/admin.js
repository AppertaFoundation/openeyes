$(document).ready(function () {
    var $globalFirmRights = $("input[name='User[global_firm_rights]']");

    $('#selectall').click(function () {
        $('input[type="checkbox"]').attr('checked', this.checked);
    });

    $('table').on('click', 'tr.clickable', function (e) {

        var target = $(e.target);

        // If the user clicked on an input element, or if this cell contains an input
        // element then do nothing.
        if (target.is(':input') || (target.is('td') && target.find('input, button').length)) {
            return;
        }

        var uri = $(this).data('uri');

        if (uri) {
            var url = uri.split('/');
            url.unshift(window.baseUrl);
            window.location.href = url.join('/');
        }
    });

    // Custom episode summaries

    $('#episode-summary #subspecialty_id').change(
        function () {
            window.location.href = baseUrl + '/admin/episodeSummaries?subspecialty_id=' + this.value;
        }
    );

    var showHideEmpty = function (el, min) {
        if (el.find('.draggablelist-item').length > min) {
            el.find('.draggablelist-empty').hide();
        } else {

            el.find('.draggablelist-empty').show();
        }
    };

    //-----------------------------------------------
    // Common Post-Op Complications
    //-----------------------------------------------

    $('#postop-complications #subspecialty_id').change(
        function () {
            window.location.href = baseUrl + '/OphCiExamination/admin/postOpComplications?subspecialty_id=' + this.value;
        }
    );

    var items_enabled = $('#draggablelist-items-enabled');
    var items_available = $('#draggablelist-items-available');

    var extractItemIds = function () {
        $('#draggablelist #item_ids').val(	// remove -items 
            items_enabled.find('.draggablelist-item').map(
                function () {
                    return $(this).data('item-id');
                }
            ).get().join(',')
        );
    };

    showHideEmpty(items_enabled, 0);
    showHideEmpty(items_available, 0);
    extractItemIds();

    var options = {
        containment: '#draggablelist-items',
        items: '.draggablelist-item',
        change: function (e, ui) {
            showHideEmpty($(this), 0);
            if (ui.sender) showHideEmpty(ui.sender, 1);
        }
    };

    items_enabled.sortable($.extend({connectWith: items_available}, options));
    items_available.sortable($.extend({connectWith: items_enabled}, options));

    $('#draggablelist form').submit(extractItemIds);
    $('#draggablelist-cancel').click(function () {
        location.reload();
    });

    $('#admin_settings tr.clickable').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/admin/editSetting?key=' + $(this).data('key');
    });

    $('#settingsform #et_cancel').unbind('click').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/admin/settings';
    });

    // admin menu collapse
    $('.box_admin_header').bind("click", function () {
        $(this).next('.box_admin_elements').toggle();
    });

    // admin menu collapse_all
    $('.box_admin_header_all').bind("click", function () {
        $('.box_admin_elements').toggle();
    });

    // when changing the global rights radiobutton, remove the firms
    $globalFirmRights.on('change', function () {
        $wrapper = $('#User_firms').closest('.multi-select');
        if ($("input:radio[name='User[global_firm_rights]']:checked").val() === '1') {
            $wrapper.hide();
        } else {
            $wrapper.show();
        }
    });

    $globalFirmRights.trigger('change');

    $('#et_delete_disorder').click(function (e) {
        e.preventDefault();

        let $checked = $('input[name="disorders[]"]:checked');
        if ($checked.length === 0) {
            alert('Please select one or more generic procedure data to delete.');
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/Admin/Disorder/delete',
            'data': $checked.serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (response) {
                response = JSON.parse(response);
                if (response['status'] === "1") {
                    window.location.reload();
                } else {
                    $('.js-admin-errors').show();
                    let $errorContainer = $('.js-admin-error-container');
                    $errorContainer.html("");

                    response['errors'].forEach(function (error) {
                        $errorContainer.append('<p class="js-admin-errors">' + error + '</p>');
                    });
                }
            }
        });
    });

});
