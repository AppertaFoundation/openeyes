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
         * Comments label validation if comments required in Patient factor
         */
        $("input[name^='require_comments']").each(function (key, value) {
            if (key < ($("input[name^='require_comments']").length - 1) && $(this).is(':checked')) {
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
            if ($(this).is(':checked')) {
                chk++;
            }
        });
        if (chk >= 3) {
            alert('Must be one child as default');
            check = false;
        }

        if (!check) {
            event.preventDefault();
        }
        return check;

    });
});