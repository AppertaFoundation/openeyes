/* global zkSignature */

this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.UI = this.OpenEyes.UI || {};

(function(exports) {

    var BACKGROUND = 'rgb(255, 255, 255)';
    var FOREGROUND = 'rgb(22, 38, 76)';

    function SignatureCapture(options) {

        this.dialog = null;
        this.signaturePad = null;
        this.signatureImage = null;
        this.pin = "";
        this.$canvas = null;

        this.pinDialog = null;

        this.options = $.extend(true, {}, SignatureCapture._defaultOptions, options);

        this.create();
    }

    SignatureCapture._defaultOptions = {
        requirePIN: false,
        openButtonSelector: "#signature_open",
        dialogTemplate:'<div class="text-center canvas-footer">\n' +
                '        <button id="SignatureCapture_Cancel" class="button small warning" type="button">Cancel</button>\n' +
                '        <button id="SignatureCapture_Redo" class="button small warning" type="button">Erase</button>\n' +
                '        <button id="SignatureCapture_Submit" class="button small" type="button">Submit</button>\n' +
                '    </div>',
        dialogTitle: 'Please sign below and click "Submit" when ready',
        unique_identifier: null,
        cryptKey: null,
        submitURL: null,
        messageContainer: null,
        csrf: {name: "", token: ""},
        onSubmit: function(data){},
        widgetid: "uidnotset",
        embedded: false,
        embedded_canvas_selector: ""
    };

    SignatureCapture.prototype.create = function ()
    {
        let widget = this;

        if(widget.options.unique_identifier === null || widget.options.cryptKey === null) {
            console.error("SignatureCapture error: 'unique_identifier' and 'cryptKey' must be set");
            return false;
        }

        if(!widget.options.embedded) {
            widget.createDialog();
        }
        else {
            var $canvas = $(widget.options.embedded_canvas_selector);
     
            if($canvas.length > 0) {
                widget.attachCanvas($canvas);
            }
            else {
                console.error("SignatureCapture error: canvas '"+widget.options.embedded_canvas_selector+"' cannot be found");
                return false;
            }
        }
        widget.addEventHandlers();

    };

    SignatureCapture.prototype.createDialog = function()
    {
        let widget = this;
        zkSignature.widgetID = 'js-id-'+widget.options.widgetid+'';
        zkSignature.widgetid = widget.options.widgetid;
        zkSignature.canvasCopyID = "canvasCopy_" + widget.options.widgetid;
      
        widget.transparent = {
            popup : '<div class="modal-transparent js-id-'+widget.options.widgetid+'">'+
                    '<div class="modal-transparent-content">'+
                    '<div id="canvas">Canvas is not supported.</div>'+
                    '<script>zkSignature.capture();</script>'+
                    widget.options.dialogTemplate +
                    '</div>'+
                    '</div>',
            'containerClass' : 'modal-transparent',
        };

        $('body').append(widget.transparent.popup);
    };

    SignatureCapture.prototype.attachCanvas = function($canvas)
    {
        var widget = this;

        $("body").append('' +
                '<div class="js-id-'+widget.options.widgetid+'" style="position: fixed; left: 0; right: 0; bottom: 0; height: 40px; padding-top: 5px; border-top: 1px solid #ccc; background-color: white; box-shadow: 0 -5px 15px 5px #efefef">' +
                '    <div class="text-center">\n' +
                '        <button id="SignatureCapture_Redo" class="button small warning" type="button">Erase</button>\n' +
                '        <button id="SignatureCapture_Submit" class="button small" type="button">Submit</button>\n' +
                '    </div>'+
                '</div>');

        widget.signaturePad = new SignaturePad($canvas[0], {
            backgroundColor: BACKGROUND,
            penColor: FOREGROUND
        });

        widget.$canvas = $canvas;
    };

    SignatureCapture.prototype.getNewCanvasByCoords = function(canvas, x, y, width, height) {
        // create a temp canvas
        const newCanvas = document.createElement('canvas');
        // set its dimensions
        newCanvas.width = width;
        newCanvas.height = height;
        // draw the canvas in the new resized temp canvas
        newCanvas.getContext('2d').drawImage(canvas, x, y, width, height, 0, 0, width, height);
        return newCanvas;
    };

    SignatureCapture.prototype.removeBlanks = function ($canvas) {
        let imgWidth = $canvas.width;
        let imgHeight = $canvas.height;
        let ctx = $canvas.getContext('2d');
        let imageData = ctx.getImageData(0, 0, imgWidth, imgHeight),
        data = imageData.data,
        checkColor = function(x, y) {
            for (let i = 0; i < 3; i++) {
                if (data[(imgWidth*y + x) * 4 + i] !== 255) {
                    return true;
                }
            }

            return false;
        },
        scanY = function (fromTop) {
            let offset = fromTop ? 1 : -1;

            // loop through each row
            for(let y = fromTop ? 0 : imgHeight - 1; fromTop ? (y < imgHeight) : (y > -1); y += offset) {

                // loop through each column
                for(let x = 0; x < imgWidth; x++) {
                    if (checkColor(x, y)) {
                        return y;
                    }
                }
            }
            return null; // all image is white
        },
        scanX = function (fromLeft) {
            let offset = fromLeft? 1 : -1;

            // loop through each column
            for(let x = fromLeft ? 0 : imgWidth - 1; fromLeft ? (x < imgWidth) : (x > -1); x += offset) {

                // loop through each row
                for(let y = 0; y < imgHeight; y++) {
                    if (checkColor(x, y)) {
                        return x;
                    }
                }
            }
            return null; // all image is white
        };

        let cropTop = scanY(true),
        cropBottom = scanY(false),
        cropLeft = scanX(true),
        cropRight = scanX(false);

        // leave 10px around the image
        cropLeft = (cropLeft - 10) < 0 ? 0 : (cropLeft - 10);
        cropTop = (cropTop - 10) < 0 ? 0 : (cropTop - 10);

        const signWidth = (cropRight + 10) > imgWidth ? (cropRight - cropLeft) : ((cropRight - cropLeft) + 10);
        const signHeight = (cropBottom + 10) > imgHeight ? (cropBottom - cropTop) : ((cropBottom - cropTop) + 10);

        return this.getNewCanvasByCoords($canvas, cropLeft, cropTop, signWidth, signHeight);
    };

    SignatureCapture.prototype.addEventHandlers = function()
    {
        let widget = this;
        // dialog opener
        $(document).on("click", widget.options.openButtonSelector, function(e) {
        
            if(widget.transparent.popup === null) {
                $('body').append(widget.transparent.popup);
            }
            
            // widget.dialog.open();
            $('body').css({'overflow' : 'hidden'});
            $('.' + widget.transparent.containerClass + '.js-id-'+widget.options.widgetid+'').show();
            $('#canvasCopy_'+ widget.options.widgetid).css({'display' : 'block'});

            let $canvas = $("canvas#canvasCopy_"+widget.options.widgetid);
            widget.$canvas = $canvas;
            
            widget.setFullScreen();

            $('#canvasCopy_'+ widget.options.widgetid).parent().parent().parent().find('img[id*="_signature_image"]').hide();
            // the path in CVI is different
            $('#canvasCopy_'+ widget.options.widgetid).parent().parent().parent().parent().find('img[id*="_signature_image"]').hide();
            $(widget.options.openButtonSelector).hide();
        });
        //Cancel button
        $(document).on("click", ".js-id-"+widget.options.widgetid+" #SignatureCapture_Cancel", function(e) {
            $('body').css({'overflow' : ''});
            $('.'+widget.transparent.containerClass).hide();
            $('#canvasCopy_'+ widget.options.widgetid).css({'display' : 'none'});
            zkSignature.widgetID = 'js-id-'+widget.options.widgetid+'';
            zkSignature.widgetid = widget.options.widgetid;
            zkSignature.canvasCopyID = "canvasCopy_" + widget.options.widgetid;
            zkSignature.clear();
            widget.exitFullScreen();
            var signature_image = $('#canvasCopy_'+ widget.options.widgetid).parent().parent().parent().find('img[id*="_signature_image"]');

            if (signature_image.attr('src') != '' && signature_image.attr('src') != '//:0') {
                signature_image.show();
            }

            $(widget.options.openButtonSelector).show();
        });
    
        // redo button
        $(document).on("click", ".js-id-"+widget.options.widgetid+" #SignatureCapture_Redo", function(e) {
            zkSignature.widgetID = 'js-id-'+widget.options.widgetid+'';
            zkSignature.widgetid = widget.options.widgetid;
            zkSignature.canvasCopyID = "canvasCopy_" + widget.options.widgetid;
            zkSignature.clear();
        });

        // done button (PIN)
        $(document).on("click", ".js-id-"+widget.options.widgetid+" #SignatureCapture_pinDone", function(e) {
            let $errorMsg = $("#SignatureCapture_pin_warning");
            let $pinInput = $("#SignatureCapture_pin");
            $errorMsg.hide();
            let pin = $pinInput.val();
            if (pin.length !== 4 || isNaN(pin)) {
                $errorMsg.show();
                $pinInput.val("").focus();
            }
            else {
                widget.pin = pin;
                widget.pinDialog.close();
                widget.submitSignature();
            }
        });

        // submit button
        $(document).on("click", ".js-id-" + widget.options.widgetid + " #SignatureCapture_Submit", function (e) {
         
            const $canvas = widget.$canvas[0];
            const ctx = $canvas.getContext('2d');
            const newImage = widget.removeBlanks($canvas);
            widget.signatureImage = newImage.toDataURL("image/jpeg");
            $(widget.options.openButtonSelector).show();

            if (widget.options.requirePIN) {
                widget.pinDialog = new OpenEyes.UI.Dialog({
                    title: "Please enter PIN",
                    content: "<div class='row'>" +
                            "<div class='large-8 column'>" +
                            " <label>Please enter a four-digit PIN to secure your signature:</label>" +
                            " <p id='SignatureCapture_pin_warning' style='color: red; display: none;'>Invalid PIN. Please enter 4 digits.</p>" +
                            "</div>" +
                            "<div class='large-4 column'>" +
                            " <input type='text' maxlength='4' pattern='[0-9]*' id='SignatureCapture_pin' class='dummy-password' style='max-width: 60px; display: inline-block' />" +
                            " <button class='small button' type='button' id='SignatureCapture_pinDone'>Done</button> " +
                            "</div></div>",
                    width: "auto",
                    dialogClass: 'dialog js-id-' + widget.options.widgetid
                });
                widget.pinDialog.open();
            } else {
                widget.submitSignature();
            }
        });
    };

    SignatureCapture.prototype.submitSignature = function()
    {
        let widget = this;
        let waitDlg = new OpenEyes.UI.Dialog({
            title: "Processing...",
            content: "<p>We're securing and uploading your signature. Please wait. <img alt='Processing...' src=\"data:image/gif;base64,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\"></p>",
            width: "auto"
        });
        waitDlg.open();
        let postData = {};
        if(widget.options.requirePIN) {
            postData.image = widget.encodeSignature();
        }
        else {
            postData.image = widget.signatureImage;
        }
        postData[widget.options.csrf.name] = widget.options.csrf.token;
        $.post(widget.options.submitURL, postData, function(data) {

            if(!widget.options.embedded) {
                //widget.dialog.close();
                $('.'+widget.transparent.containerClass).hide();
                $('body').css({'overflow' : ''});

                $('#canvasCopy_'+ widget.options.widgetid).css({'display' : 'none'});
                zkSignature.widgetID = 'js-id-'+widget.options.widgetid+'';
                zkSignature.widgetid = widget.options.widgetid;
                zkSignature.canvasCopyID = "canvasCopy_" + widget.options.widgetid;
                zkSignature.clear();
                
                widget.exitFullScreen();
            } else {
                widget.signaturePad.off();
                $(".js-id-"+widget.options.widgetid).remove();
            }

            waitDlg.close();
            if(widget.options.messageContainer !== null) {
                widget.options.messageContainer.text(data);
            }
            widget.options.onSubmit(data);
        });
    };

    SignatureCapture.prototype.encodeSignature = function()
    {
        let widget = this;
        imgData = widget.signatureImage.replace('data:image/jpeg;base64,','');
        str = unescape(encodeURIComponent(imgData));
        //Encrypt - new
        /*
            1. generate the base 64 of the original image
            2. generate md5 of base 64
            3. append  md5 to the base64
            4. encrypt
            5. base64 of encrypted data
         */
        md5OfBase64 = CryptoJS.MD5(str);
        appendMd5ToBase64 = str+md5OfBase64;
        encryptAppendedMd5ToBase64 = mcrypt.Encrypt(
                appendMd5ToBase64,
        '',
        CryptoJS.MD5(widget.options.cryptKey + widget.options.unique_identifier + widget.pin).toString(),
        'rijndael-256',
        'ecb');

        return btoa(encryptAppendedMd5ToBase64);
    };
  
    SignatureCapture.prototype.setFullScreen = function()
    {
        var elem = document.documentElement;

        if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            }
        }
    };
    
    SignatureCapture.prototype.exitFullScreen = function()
    {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    };

    exports.SignatureCapture = SignatureCapture;

}(OpenEyes.UI));
