$(document).ready(function () {
    if (OpenEyes.Lab === Object(OpenEyes.Lab) && OpenEyes.Lab.Form === Object(OpenEyes.Lab.Form)) {
        OpenEyes.Lab.Form.init($('#Element_OphInLabResults_Details_result_type_id'));
    }

    handleButton($('#et_print'), function (e) {
        printIFrameUrl(OE_print_url, null);
        enableButtons();
        e.preventDefault();
    });

    $('textarea').autosize();

    // restrict the number of characters to be inserted into the textarea
    $('#Element_OphInLabResults_Inr_comment').live("keypress", function(e) {
        // carriage return /r/n is considered 2 characters; compute the real length
        let countCharacters = $(this).val().replace(/(\r\n|\n|\r)/g, '--').length;
        if (countCharacters >= 254) {
            e.preventDefault();
        }
    });
});