$(document).ready(function () {
    handleButton($('#et_print'), function (e) {
        printIFrameUrl(OE_print_url, null);
        enableButtons();
        e.preventDefault();
    });
});

