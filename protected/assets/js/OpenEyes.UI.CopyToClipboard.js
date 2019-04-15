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
        'wrapper': 'body',
        'callback': function($element){
            $("<span>",{"class":"js-copy-to-clipboard-copied", "style":"color:lightgreen;margin-left:5px;"}).text("copied").insertAfter($element);
            $(".js-copy-to-clipboard-copied").fadeOut(2000, function(){ $(".js-copy-to-clipboard-copied").remove(); });
        },
    };

    CopyToClipboardController.prototype.initialiseTriggers = function(){
        let controller = this;
        let $wrapper = $(controller.wrapper);

        $wrapper.on('click', this.selector, function(e) {
            e.preventDefault();
            let targetToCopy = ($('.js-to-copy-to-clipboard').length == 1 ? $('.js-to-copy-to-clipboard') : $(this))
            let text = ( targetToCopy.val() ? targetToCopy.val() : targetToCopy.text() );

            if (controller.copyToClipboard(text) && typeof controller.callback === "function") {
                controller.callback($(this));
            }
        });
    };

    CopyToClipboardController.prototype.copyToClipboard = function(text){
        let $input  = $('<input>', {'style':'position:absolute;top:-500px'}).val( text.trim() ),
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