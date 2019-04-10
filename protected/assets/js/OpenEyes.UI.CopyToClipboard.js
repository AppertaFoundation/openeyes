var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    function CopyToClipboardController(options) {
        this.options = $.extend(true, {}, CopyToClipboardController._defaultOptions, options);
        this.selector = this.options.selector;
        this.wrapper = this.options.wrapper;
        this.callback = this.options.callback;

        this.initialiseTriggers();
    };

    CopyToClipboardController._defaultOptions = {
        'selector': '.copy-to-clipboard',
        'wrapper': 'body',
        'callback': function($element){
            $("<span>",{"class":"copy-to-clipboard-copied", "style":"color:lightgreen;margin-left:5px;"}).text("copied").insertAfter($element);
            $(".copy-to-clipboard-copied").fadeOut(2000, function(){ $(".copy-to-clipboard-copied").remove(); });
        },
    };

    CopyToClipboardController.prototype.initialiseTriggers = function(){

        var controller = this;
        var $wrapper = $(controller.wrapper);

        $wrapper.on('click', this.selector, function(e) {
            e.preventDefault();
            var text = ( $(this).val() ? $(this).val() : $(this).text() );

            if (controller.copyToClipboard(text) && typeof controller.callback === "function") {
                controller.callback($(this));
            }
        });

    };

    CopyToClipboardController.prototype.copyToClipboard = function(text, callback){
        var controller = this,
            $input  = $('<input>', {'style':'position:absolute;top:-500px'}).val( text.trim() ),
            result = false;

        $('body').append($input);

        $input.select();
        result = document.execCommand("Copy");

        $input.remove();
        return result;
    }

    exports.CopyToClipboardController = CopyToClipboardController;

}(OpenEyes.UI));

$(document).ready(function(){
    new OpenEyes.UI.CopyToClipboardController();
});