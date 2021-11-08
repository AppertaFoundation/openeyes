this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.UI = this.OpenEyes.UI || {};

/* global moduleName */

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
        "mode" : "edit",
        "element_id" : null
    };

    EsignElementWidget.prototype.create = function ()
    {
        this.$connectDeviceBtn = this.$element.find(".js-connect-device");
        this.$addSecondaryBtn = this.$element.find(".js-add-secondary");
        this.bindEvents();
        this.renumber();
    };

    EsignElementWidget.prototype.bindEvents = function ()
    {
        let widget = this;
        this.$element.on("signatureAdded", function () {
            if(widget.options.mode === "view") {
                window.formHasChanged = false;
                window.location.reload();
            }
        });
        this.$connectDeviceBtn.click(function () {
            const dlg = new OpenEyes.UI.Dialog({
                title: "e-Sign - link to device",
                url: "/" + moduleName + "/default/esignDevicePopup",
            });
            dlg.open();
        });
        if(this.$addSecondaryBtn.length > 0) {
            this.$addSecondaryBtn.click(function () {
                widget.$element.find("tr:hidden").show();
                widget.$addSecondaryBtn.hide();
            });
        }
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