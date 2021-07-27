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

    };

    EsignElementWidget.prototype.create = function ()
    {
        this.renumber();
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