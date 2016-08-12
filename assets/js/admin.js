$(document).ready(function () {

    $('#et_admin-save').live('click', function (e) {
        /**
         * Comments label validation if comments allowed in Clinical disorder section
         */
        $.each($("input[name^='comments_allowed']"), function (key, value) {
            if (key < ($("input[name^='comments_allowed']").length - 1) && $(this).is(':checked')) {
                var textbox_value = $("input[name^='comments_label']").eq(key).val();
                var text_length = (textbox_value).length;
                if (text_length == 0) {
                    alert("Please enter comments label");
                    $("input[name^='comments_label']")[key].focus();
                    return;
                }
            }
        });

        /**
         * Comments label validation if comments required in Patient factor
         */
        $.each($("input[name^='require_comments']"), function (key, value) {
            if (key < ($("input[name^='require_comments']").length - 1) && $(this).is(':checked')) {
                var textbox_value = $("input[name^='comments_label']").eq(key).val();
                var text_length = (textbox_value).length;
                if (text_length == 0) {
                    alert("Please enter comments label");
                    $("input[name^='comments_label']")[key].focus();
                    return;
                }
            }
        });

        /**
         * Child default will select only one as default in employement status
         */
        if ($('input[name="child_default"]:checked').length > 1) {
            alert("Maximum one child must be selected");
            return;

        }
    });
});
