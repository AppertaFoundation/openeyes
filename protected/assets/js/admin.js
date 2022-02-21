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
    
    // pincode
    const set_pincode_btn_selector = '.js-set-pincode-btn';
    const pincode_section_selector = '.js-pincode-section';
    const hidden_pincode_input_selector = '.js-user-pincode-val';
    const pincode_display = '.js-pincode-display';

    $(document).off('click', set_pincode_btn_selector).on('click', set_pincode_btn_selector, function(e){
        e.preventDefault();
        const $current_div = $(this).closest('tr').find(pincode_section_selector);
        $(this).remove();
        const $pincode_field = $('<input class="js-pincode-input" type="text" placeholder="6-digit Pincode" maxlength="6" minlength="6" autocomplete="off"/>');
        const $cancel_btn = $('<button class="js-cancel-set-pincode-btn">Cancel Set Pincode</button>');

        $current_div.append($pincode_field).append($cancel_btn);
    });
    $(document).off('click', '.js-cancel-set-pincode-btn').on('click', '.js-cancel-set-pincode-btn', function(e){
        const $current_div = $(this).closest('tr').find(pincode_section_selector);
        const original_pincode = $(this).closest('tr').find(pincode_display).data('origin-pincode');
        const $hidden_input = $(this).closest('tr').find(hidden_pincode_input_selector);
        const $set_pincode_btn = $('<div><button class="js-set-pincode-btn">Set Pincode</button></div>');
        e.preventDefault();
        $current_div.html('');
        $current_div.append($set_pincode_btn);
        $hidden_input.val(original_pincode);
    });
    let requesting = false;
    let renderFlag = function(style_class, flag, target, text = null){
        if(!(flag instanceof jQuery)){
            flag = $(flag);
        }
        if(!(target instanceof jQuery)){
            target = $(target);
        }
        target.closest('div').find('.highlighter').remove();
        flag.addClass(style_class).text(text);
        flag.insertAfter(target);
    }
    $(document).off('keyup', '.js-pincode-input').on('keyup', '.js-pincode-input', function(){
        if(this.value.length !== this.maxLength){
            return;
        }
        if(requesting){
            return;
        }
        const $flag_ele = $('<div class="highlighter"></div>');
        const $pincode_val_input = $(this).closest('td').find(hidden_pincode_input_selector);
        const ins_auth_id = this.closest(pincode_section_selector).dataset.insAuthId;
        const user_id = this.closest(pincode_section_selector).dataset.userId;
        if(!ins_auth_id){
            renderFlag('issue', $flag_ele, $(this), 'Please Select Institution Auth Type First');
            return;
        }
        requesting = true;
        const context = this;
        $.ajax({
            url: '/user/checkPincodeAvailability',
            type: 'GET',
            data: {
                pincode: this.value,
                ins_auth_id: ins_auth_id,
                user_id: user_id,
            },
            success: function(resp){
                let style_class = 'good';
                let text = `${context.value} is available`;
                $(context).closest('div').find('.highlighter').remove();
                if(resp['success']){
                    $pincode_val_input.val(context.value);
                } else {
                    style_class = 'issue';
                    text = `${context.value} is held by ${resp['user']}`;
                }
                renderFlag(style_class, $flag_ele, $(context), text);
                requesting = false;
            },
            complete: function(resp){
                requesting = false;
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
