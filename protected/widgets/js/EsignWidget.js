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
        "submitAction" : "",
        "signature_type" : null,
        "element_id" : null
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
        this.$signatoryName = this.$element.find(".js-signatory_name-field");
        this.$signatoryRole = this.$element.find(".js-signatory_role-field");
        this.$signatureWrapper = this.$element.find(".js-signature-wrapper");
        this.$signature = this.$element.find(".js-signature");
        this.events = [];

        this.createEvents();
        this.bindEvents();

        let widget = this;

        if(this.options.needUserName) {
            OpenEyes.UI.AutoCompleteSearch.init({
                input: widget.$element.find(".autocompletesearch"),
                url: '/user/autoComplete?consultant_only=1',
                onSelect: function() {
                    let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                    $("#signatory_name_" + widget.$element.attr("id")).val(response.label);
                    widget.$userIdInput.val(response.id);
                    widget.$signatoryName.val(response.label);
                }
            });
        }
    };

    /**
     * @private
     */
    EsignWidget.prototype.createEvents = function()
    {
        this.events.onSignatureAdded = document.createEvent('Event');
        this.events.onSignatureAdded.initEvent('signatureAdded', true, true);
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
                    content: "Please enter the signatory's name."
                }).open();
                return false;
            }
            disableButtons();
            $.post(
                baseUrl + "/" + moduleName + "/default/" + widget.options.submitAction,
                {
                    "pin": pin,
                    "user_id": user_id,
                    "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                    "signature_type" : widget.options.signature_type,
                    "element_id" : widget.options.element_id,
                    "element_type_id" : widget.$element.closest("section.element").attr("data-element-type-id"),
                    "event_id" : OE_event_id,
                    "signatory_role" : widget.$signatoryRole.val(),
                    "signatory_name" : widget.$signatoryName.val()
                },
                function (response) {
                    if (response.code === 0) {
                        widget.displaySignature(
                            response.singature_image1_base64,
                            response.singature_image2_base64,
                            response.date,
                            response.time,
                            response.signatory_name || null,
                            !!response.signed_by_secretary
                        );
                        if(typeof response.signed_by_secretary !== "undefined") {
                            widget.$element.find(".js-secretary-field").val(response.signed_by_secretary ? "1" :"0");
                        }
                        widget.setDataInput(response.signature_proof);
                        widget.$element.closest("section.element").trigger(widget.events.onSignatureAdded);
                    } else {
                        let dlg = new OpenEyes.UI.Dialog.Alert({
                            content: response.error
                        });
                        dlg.open();
                    }
                    enableButtons();
                }
            );
        });
        this.$popupSignButton.click(function () {
            let printUrl =  baseUrl + "/" + moduleName +
                "/default/print/" + OE_event_id +
                "?html=1&auto_print=0&sign=1&element_id=" + widget.options.element_id +
                "&element_type_id=" + widget.$element.closest("section.element").attr("data-element-type-id") +
                "&signature_type=" + widget.options.signature_type +
                "&signatory_role=" + widget.$signatoryRole.val() +
                "&signatory_name=" + widget.$signatoryName.val();
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
        this.$deviceSignButton.click(function () {
            const element_type_id = widget.$element.closest(".element").attr("data-element-type-id");
            const signature_type = widget.$element.find(".js-type-field").val();
            let signatory_role =  widget.$signatoryRole.val();
            let signatory_name =  widget.$signatoryName.val();
            const confirm_dlg = new OpenEyes.UI.Dialog.Confirm({
                content: "Once you capture the signature, signatory role and name can't be changed. Are you sure you want to proceed?"
            });
            confirm_dlg.on("ok", function () {
                $.post("/" + moduleName + "/default/postSignRequest",
                    {
                        "YII_CSRF_TOKEN" : YII_CSRF_TOKEN,
                        "event_id" : OE_event_id,
                        "element_type_id" : element_type_id,
                        "signature_type" : signature_type,
                        "signatory_role" : signatory_role,
                        "signatory_name" : signatory_name,
                    },
                    function (response) {
                        if(response.success) {
                            const waitingDlg = new OpenEyes.UI.Dialog({
                                title: "Signature request sent",
                                content: "Waiting for signature to be captured on linked device..."
                            });
                            waitingDlg.open();
                            widget.startPolling(element_type_id, signature_type, waitingDlg);
                        }
                    }
                );
            });
            confirm_dlg.open();
        });
    };

    /**
     * @param {string} signature_file1
     * @param {string} signature_file2
     * @param {string} date
     * @param {string} time
     * @param {string} signatory_name
     * @param {bool} is_secretary
     * @private
     */
    EsignWidget.prototype.displaySignature = function(signature_file1, signature_file2, date, time, signatory_name, is_secretary)
    {
        this.$controlWrapper.hide();
        if(typeof signature_file1 !== "undefined") {
            const $image = $('<div class="esign-check js-has-tooltip" data-tooltip-content="<img src=\''+(signature_file2)+'\'>" style="background-image: url('+signature_file1+');">');
            $image.prependTo(this.$signatureWrapper);
        }
        else if(is_secretary) {
            const $txt = $("<span>ELECTRONIC VERIFIED, NOT SIGNED TO AVOID DELAYS</span>");
            $txt.prependTo(this.$signatureWrapper);
        }
        this.$date.text(date).show();
        this.$time.text(time);
        this.$signatureWrapper.show();

        if(signatory_name) {
            this.$signatoryName.html(signatory_name);
        }

        this.$element.find(".autocompletesearch").prop("readonly", true);
    };

    /**
     * @param {string} proof
     * @private
     */
    EsignWidget.prototype.setDataInput = function(proof)
    {
        this.$element.find(".js-proof-field").val(proof);
    };

    /**
     * @param {int} element_type_id
     * @param {int} signature_type
     * @param {OpenEyes.UI.Dialog} waitingDlg
     * @private
     */
    EsignWidget.prototype.startPolling = function (element_type_id, signature_type, waitingDlg)
    {
        let widget = this;
        async function subscribe() {
            let response = await fetch(
                "/site/pollCompletedSignature?event_id=" + OE_event_id +
                "&element_type_id=" + element_type_id +
                "&signature_type=" + signature_type
            );
            if (response.status === 502) {
                // Connection timeout error, reconnect
                await subscribe();
            }
            else if (response.status === 200) {
                waitingDlg.close();
                let message = await response.text();
                let data = JSON.parse(message);
                widget.displaySignature(
                    data.signature_image1_base64,
                    data.signature_image2_base64,
                    data.date,
                    data.time,
                    "",
                    false
                );
            }
        }
        subscribe();
    };

    exports.EsignWidget = EsignWidget;
})(OpenEyes.UI);