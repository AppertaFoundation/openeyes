this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.UI = this.OpenEyes.UI || {};

(function(exports) {
    /**
     * @param {jQuery} $element
     * @param {Object} options
     * @constructor
     */
    function EsignElementWidget($element, options) {
        this.$element = $element;
        $element.data("widget", this);
        if(typeof options !== "undefined") {
            this.options = $.extend(true, {}, EsignElementWidget._defaultOptions, options);
        }
        this.create();
    }

    EsignElementWidget._defaultOptions = {
        // One of "edit", "view" or "print"
        "mode" : "edit"
    };

    EsignElementWidget.prototype.create = function ()
    {
        this.bindEvents();
        this.renumber();
    };

    EsignElementWidget.prototype.bindEvents = function ()
    {
        let widget = this;
        this.$element.on("signatureAdded", function () {
            if(widget.options.mode === "view") {
                //window.formHasChanged = false;
                //window.location.reload();
            }
        });
    };

    EsignElementWidget.prototype.renumber = function ()
    {
        let num = 1;
        this.$element.find(".js-row-num").each(function(i, e) {
            $(e).text(num++);
        });
    };

    exports.EsignElementWidget = EsignElementWidget;
})(OpenEyes.UI);