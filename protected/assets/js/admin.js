$(document).ready(function () {

    autosize($('.autosize'));

    const $globalFirmRights = $("input[name='User[global_firm_rights]']");
    const $ssoGlobalFirmRights = $("input[name='SsoDefaultRights[global_firm_rights]']");
    const $ssoDefaultEnabled = $("input[name='SsoDefaultRights[default_enabled]']");

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

    // when changing the global rights radiobutton in SSO Permissions, remove the firms
    $ssoGlobalFirmRights.on('change', function () {
        $wrapper = $('#SsoDefaultRights_sso_default_firms').closest('.multi-select');
        if ($("input:radio[name='SsoDefaultRights[global_firm_rights]']:checked").val() === '1') {
            $wrapper.hide();
        } else {
            $wrapper.show();
        }
    });

    $ssoDefaultEnabled.on('change', function () {
        $wrapper = $("#SsoDefaultRights_sso_default_roles").closest('.multi-select');
        if ($("input:radio[name='SsoDefaultRights[default_enabled]']:checked").val() === '1') {
            $wrapper.show();
        } else {
            $wrapper.hide();
        }
    });

    $globalFirmRights.trigger('change');
    $ssoGlobalFirmRights.trigger('change');
    $ssoDefaultEnabled.trigger('change');

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
                if (response['status'] === 1) {
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

    $('#et_delete_abnormality').click(function (e) {
        e.preventDefault();

        let $checked = $('input[name="select[]"]:checked');
        if ($checked.length === 0) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select one or more generic procedure data to delete."
            }).open();
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphCiExamination/admin/PupillaryAbnormalities/delete',
            'data': $checked.serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'dataType': 'JSON',
            'success': function (response) {
                if (response['status'] === 1) {
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

function getInstitutionSites(institution_id, $site_dropdown) {
    let empty_text = $site_dropdown.find("option[value='']").text();
    if (institution_id !== '') {
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': baseUrl + '/admin/getInstitutionSites?institution_id=' + institution_id,
            'beforeSend': function(){
                $('#et_save').prop('disabled', true);
            },
            'success': function (sites) {
                let options = '<option value="">' + empty_text + '</option>';
                for (let i in sites) {
                    options += '<option value="' + i + '">' + sites[i] + '</option>';
                }
                $site_dropdown.html(options);
                sort_selectbox($site_dropdown);
            },
            'complete' : function () {
                $('#et_save').prop('disabled', false);
            }
        });
    } else {
        $site_dropdown.html('<option value="">' + empty_text + '</option>');
    }
};

function sort_selectbox(element) {
    rootItem = element.children('option:first').text();
    element.append(element.children('option').sort(selectSort));
};

function selectSort(a, b) {
    if (a.innerHTML == rootItem) {
        return -1;
    } else if (b.innerHTML == rootItem) {
        return 1;
    }
    return (a.innerHTML > b.innerHTML) ? 1 : -1;
};

var rootItem = null;
