var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    function CopyToClipboardController(options) {
        this.options = $.extend(true, {}, CopyToClipboardController._defaultOptions, options);
        this.selector = this.options.selector;
        this.wrapper = this.options.wrapper;
        this.callback = this.options.callback;

        this.initialiseTriggers();
    }

    CopyToClipboardController._defaultOptions = {
        'selector': '.js-copy-to-clipboard',
        'wrapper': '.patient-details',
        'callback': function ($element) {
            $("<div>", { "class": "js-copy-to-clipboard-copied oe-tooltip fade-out copied" }).text("copied").insertAfter($element);
        },
        'copyContentSelector': 'copy-content-selector',
    };

    CopyToClipboardController.prototype.initialiseTriggers = function(){
        let controller = this;
        let $wrapper = $(controller.wrapper);

        $wrapper.on('click', this.selector, function(e) {
            e.preventDefault();
            let contentToCopySelector = $(this).data(controller.options.copyContentSelector);
            let contentToCopy = (contentToCopySelector !== undefined ? $(contentToCopySelector) : $(this));
            let text = ( contentToCopy.val() ? contentToCopy.val() : contentToCopy.text() );

            if (controller.copyToClipboard(text) && typeof controller.callback === "function") {
                controller.callback($(this));
            }
        });
    };

    CopyToClipboardController.prototype.copyToClipboard = function(text){
        let $input  = $('<textarea>', {'style':'position:absolute;top:-500px'}).val( text.trim() ),
            result;

        $('body').append($input);

        $input.select();
        result = document.execCommand("Copy");

        $input.remove();
        return result;
    };

    exports.CopyToClipboardController = CopyToClipboardController;

}(OpenEyes.UI));

$(document).ready(function(){
    new OpenEyes.UI.CopyToClipboardController();
});