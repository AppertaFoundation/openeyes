$(document).ready(function () {

    $('#et_admin-save').live('click', function (e) {
        var check = true;
        /**
         * Comments label validation if comments allowed in Clinical disorder section
         */
        $("input[name^='comments_allowed']").each(function (key, value) {
            if (key < ($("input[name^='comments_allowed']").length - 1) && $(this).is(':checked')) {
                var textbox_value = $("input[name^='comments_label']").eq(key).val();
                var text_length = (textbox_value).length;
                if (text_length == 0) {
                    alert("Please enter comments label");
                    check = false;
                }
            }
        });

        /**
         * Maximum one child as to select for employment status
         */
        var chk = 0;
        $("input[name^='child_default']").each(function () {
            if ($(this).is(':visible') && $(this).is(':checked')) {
                chk++;
            }
        });

        if (chk > 1) {
            alert('Must be one child as default');
            check = false;
        }

        if (!check) {
            event.preventDefault();
        }
        return check;

    });

    $(document).on('click', '#js-clear-disorder',function(e) {
        e.preventDefault();
        $('#autocomplete_disorder_id').val('');
        $('#disorder_id').val('');
        $('#disorder_code_box').hide();
    });
});
