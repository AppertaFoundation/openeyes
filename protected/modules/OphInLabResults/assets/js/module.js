$(document).ready(function () {
    if (OpenEyes.Lab === Object(OpenEyes.Lab) && OpenEyes.Lab.Form === Object(OpenEyes.Lab.Form)) {
        OpenEyes.Lab.Form.init($('#Element_OphInLabResults_Details_result_type_id'));
    }

    handleButton($('#et_print'), function (e) {
        printIFrameUrl(OE_print_url, null);
        enableButtons();
        e.preventDefault();
    });

});