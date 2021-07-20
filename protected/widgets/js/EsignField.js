OpenEyes = OpenEyes || {};
OpenEyes.UI = OpenEyes.UI || {};

(function(exports) {
    /**
     * @param {jQuery} $element
     * @param {Object} options
     * @constructor
     */
    function EsignWidget($element, options) {
        this.$element = $element;
        this.options = $.extend(true, {}, EsignWidget._defaultOptions, options);
        this.create();
    }

    EsignWidget._defaultOptions = {
        "submitAction" : ""
    };

    /**
     * @private
     */
    EsignWidget.prototype.create = function()
    {
        this.$pinInput = this.$element.find(".js-pin-input");
        this.$signButton = this.$element.find(".js-sign-button");
        this.$controlWrapper = this.$element.find(".js-signature-control");
        this.$date = this.$element.find(".js-signature-date");
        this.$time = this.$element.find(".js-signature-time");
        this.$signatureWrapper = this.$element.find(".js-signature-wrapper");
        this.$signature = this.$element.find(".js-signature");
        this.bindEvents();
    };

    /**
     * @private
     */
    EsignWidget.prototype.bindEvents = function()
    {
        let widget = this;
        this.$signButton.click(function () {
            const pin = widget.$pinInput.val();
            widget.$pinInput.val("");
            if(pin === "") {
                let dlg = OpenEyes.UI.Dialog.Alert({
                    content: "Please enter PIN"
                });
                dlg.open();
                return false;
            }
            $.post(
                baseUrl + "/" + moduleName + "/default/" + widget.options.submitAction,
                {
                    "pin": pin,
                    "YII_CSRF_TOKEN": YII_CSRF_TOKEN
                },
                function (response) {
                    if (response.code === 0) {
                        widget.displaySignature(
                            response.signature_file_id,
                            response.date,
                            response.time
                        );
                    } else {
                        let dlg = new OpenEyes.UI.Dialog.Alert({
                            content: "There has been an error while signing: " + response.error
                        });
                        dlg.open();
                    }
                }
            );
        });
    };

    /**
     * @param {int} signature_file_id
     * @param {string} date
     * @param {string} time
     * @private
     */
    EsignWidget.prototype.displaySignature = function(signature_file_id, date, time)
    {
        this.$controlWrapper.hide();
        const $image = $('<div class="esign-check js-has-tooltip" data-tip="{&quot;type&quot;:&quot;esign&quot;,&quot;png&quot;:&quot;/idg-php/imgDemo/esign/esign2.png&quot;}" style="background-image: url(\'/idg-php/imgDemo/esign/esign2.png\')"></div>');
        $image.appendTo(this.$signature);
        this.$date.text(date).show();
        this.$time.text(time);
        this.$signatureWrapper.show();
    };

    exports.EsignWidget = EsignWidget;
})(OpenEyes.UI);