$(document).ready(function () {
    var $globalFirmRights = $("input[name='User[global_firm_rights]']");

    $('#selectall').click(function () {
        $('input[type="checkbox"]').attr('checked', this.checked);
    });

    $('table').on('click', 'tr.clickable', function (e) {

        var target = $(e.target);

        // If the user clicked on an input element, or if this cell contains an input
        // element then do nothing.
        if (target.is(':input') || (target.is('td') && target.find('input').length)) {
            return;
        }

        var uri = $(this).data('uri');

        if (uri) {
            var url = uri.split('/');
            url.unshift(baseUrl);
            window.location.href = url.join('/');
        }
    });

    handleButton($('#et_save'), function (e) {
        /*e.preventDefault();

         $('#adminform').submit();*/
    });

    handleButton($('#et_cancel'), function (e) {
        e.preventDefault();
        var hrefArray,
            page;

        if ($(e.target).data('uri')) {
            window.location.href = $(e.target).data('uri');
        } else {
            hrefArray = window.location.href.split('/');
            page = false;

            if (parseInt(hrefArray[hrefArray.length - 1])) {
                page = Math.ceil(parseInt(hrefArray[hrefArray.length - 1]) / items_per_page);
            }

            for (var i = 0; i < hrefArray.length; i++) {
                if (hrefArray[i] === 'admin') {
                    object = ucfirst(hrefArray[parseInt(i) + 1].replace(/ies$/, 'y'));

                    if((object === 'EditUser') || (object === 'AddUser')) {
                        window.location.href = baseUrl + '/admin/users';
                    }else if(object === 'EditFirm') {
                        window.location.href = baseUrl + '/admin/firms';
                    }else {
                        var object = e[parseInt(i) + 1].replace(/^[a-z]+/, '').toLowerCase() + 's';
                        window.location.href = baseUrl + '/admin/' + object + (page ? '/' + page : '');
                    }
                }
            }
        }
    });

    handleButton($('#et_contact_cancel'), function (e) {
        e.preventDefault();
        history.back();
    });

    handleButton($('#et_add'), function (e) {
        e.preventDefault();
        var object,
            hrefArray;

        if ($(e.target).data('uri')) {
            window.location.href = baseUrl + $(e.target).data('uri');
        } else {
            hrefArray = window.location.href.split('?')[0].split('/');

            for (var i = 0; i < hrefArray.length; i++) {
                if (hrefArray[i] === 'admin') {
                    if (hrefArray[parseInt(i) + 1].match(/ies$/)) {
                        object = ucfirst(hrefArray[parseInt(i) + 1].replace(/ies$/, 'y'));
                    } else {
                        object = ucfirst(hrefArray[parseInt(i) + 1].replace(/s$/, ''));
                    }
                    window.location.href = baseUrl + '/admin/add' + object;
                }
            }
        }
    });

    handleButton($('#et_delete'), function (e) {
        e.preventDefault();
        var object,
            hrefArray,
            uri,
            serializedForm,
            $form;

        if ($(e.target).data('object')) {
            object = $(e.target).data('object');
            if (object.charAt(object.length - 1) !== 's') {
                object = object + 's';
            }
        } else {
            hrefArray = window.location.href.split('?')[0].split('/');

            for (var i = 0; i < hrefArray.length; i++) {
                if (hrefArray[i] === 'admin') {
                    object = hrefArray[parseInt(i) + 1].replace(/s$/, '');
                }
            }
        }

        $form = $('#admin_' + object);
        if($('#generic-admin-list, #generic-admin-form').length){
            $form = $('#generic-admin-list, #generic-admin-form');
        }
        serializedForm = $form.serialize();
        if (serializedForm.length === 0) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select one or more items to delete."
            }).open();
            return;
        }

        if ($(e.target).data('uri')) {
            uri = baseUrl + $(e.target).data('uri');
        } else {
            uri = baseUrl + '/admin/delete' + ucfirst(object);
        }

        $.ajax({
            'type': 'POST',
            'url': uri,
            'data': serializedForm + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (html) {
                if (html === '1') {
                    window.location.reload();
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more " + object + " could not be deleted as they are in use."
                    }).open();
                }
            }
        });
    });

    handleButton($('#lookup_user'), function (e) {
        e.preventDefault();

        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/admin/lookupUser?username=' + $('#User_username').val(),
            'success': function (resp) {
                var m = resp.match(/[0-9]+/);
                if (m) {
                    window.location.href = baseUrl + '/admin/editUser/' + m[0];
                } else {
                    enableButtons();
                    new OpenEyes.UI.Dialog.Alert({
                        content: "User not found"
                    }).open();
                }
            }
        });
    });

    handleButton($('#et_add_label'), function (e) {
        e.preventDefault();
        /* TODO */
    });

    handleButton($('#admin_event_deletion_requests #et_approve'), function (e) {
        e.preventDefault();

        var id = $(e.target).parent().parent().data('id');

        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/admin/approveEventDeletionRequest/' + id,
            'success': function (resp) {
                if (resp == "1") {
                    window.location.reload();
                } else {
                    alert("Something went wrong trying to approve the deletion request.  Please try again or contact support for assistance.");
                }
            }
        });
    });

    handleButton($('#admin_event_deletion_requests #et_reject'), function (e) {
        e.preventDefault();

        var id = $(e.target).parent().parent().data('id');

        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/admin/rejectEventDeletionRequest/' + id,
            'success': function (resp) {
                if (resp === "1") {
                    window.location.reload();
                } else {
                    alert("Something went wrong trying to reject the deletion request.  Please try again or contact support for assistance.");
                }
            }
        });
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

    if($("input:radio[name='User[is_doctor]']:checked").val() == 1) {
        $('#div_User_registration_code').show();
        $('#div_User_doctor_grade_id').show();
    }else{
        $('#div_User_registration_code').hide();
        $('#div_User_doctor_grade_id').hide();
    }

    $("input[name='User[is_doctor]']").click(function(){
        if($("input:radio[name='User[is_doctor]']:checked").val() == 1){
            $('#div_User_registration_code').show();
            $('#div_User_doctor_grade_id').show();
        }else{
            $('#div_User_registration_code').hide();
            $('#div_User_doctor_grade_id').hide();
            $("#User_registration_code").val(null);
            $("#User_grade").val(null).change();
        }
    });

    $globalFirmRights.on('change', function(){
        if($("input:radio[name='User[global_firm_rights]']:checked").val() === '1'){
            $('#div_User_Firms').hide();
        } else {
            $('#div_User_Firms').show();
        }
    });

    $globalFirmRights.trigger('change');

});
