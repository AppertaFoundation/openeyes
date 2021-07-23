OpenEyes = OpenEyes || {};
OpenEyes.UI = OpenEyes.UI || {};

/* global baseUrl */
/* global moduleName */
/* global OE_event_id */

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
        this.$userIdInput = this.$element.find(".js-user_id-input");
        this.$signButton = this.$element.find(".js-sign-button");
        this.$popupSignButton = this.$element.find(".js-popup-sign-btn");
        this.$deviceSignButton = this.$element.find(".js-device-sign-btn");
        this.$controlWrapper = this.$element.find(".js-signature-control");
        this.$date = this.$element.find(".js-signature-date");
        this.$time = this.$element.find(".js-signature-time");
        this.$signatoryName = this.$element.find(".js-signatory-name");
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
            const user_id  = widget.$userIdInput.val();

            widget.$pinInput.val("");
            if (pin === "") {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Please enter PIN."
                }).open();
                return false;
            }
            if (widget.options.needUserName && user_id === "") {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Please enter the signatory name."
                }).open();
                return false;
            }

            $.post(
                baseUrl + "/" + moduleName + "/default/" + widget.options.submitAction,
                {
                    "pin": pin,
                    "user_id": user_id,
                    "YII_CSRF_TOKEN": YII_CSRF_TOKEN
                },
                function (response) {
                    if (response.code === 0) {
                        widget.displaySignature(
                            response.singature_image1_base64,
                            response.singature_image2_base64,
                            response.date,
                            response.time,
                            response.signatory_name || null
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
        this.$popupSignButton.click(function () {
            let printUrl =  baseUrl + "/" + moduleName + "/default/print/" + OE_event_id + "?html=1&auto_print=0&sign=1&element_id=";
            let popup = new OpenEyes.UI.Dialog({
                title: "e-Sign",
                iframe: printUrl,
                popupContentClass: "oe-popup-content max max-height",
                width: "100%",
                maxHeight: '90%',
                height: '90%'
            });
            popup.open();
        });
        this.$element.on("signatureDeviceAttached", function(e) {
            widget.$deviceSignButton.prop("disabled", false);
        });
        this.$element.on("signatureDeviceDetached", function(e) {
            widget.$deviceSignButton.prop("disabled", true);
        });
    };

    /**
     * @param {string} signature_file1
     * @param {string} signature_file2
     * @param {string} date
     * @param {string} time
     * @private
     */
    EsignWidget.prototype.displaySignature = function(signature_file1, signature_file2, date, time, signatory_name)
    {
        this.$controlWrapper.hide();
        const $image = $('<div class="esign-check js-has-tooltip" data-tooltip-content="<img src=\''+(signature_file2)+'\'>" style="background-image: url('+signature_file1+');">');
        $image.prependTo(this.$signatureWrapper);
        this.$date.text(date).show();
        this.$time.text(time);
        this.$signatureWrapper.show();

        if(signatory_name){
            this.$signatoryName.html(signatory_name);
        }
    };

    exports.EsignWidget = EsignWidget;
})(OpenEyes.UI);