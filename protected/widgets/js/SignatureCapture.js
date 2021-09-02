this.OpenEyes = this.OpenEyes || {};
this.OpenEyes.UI = this.OpenEyes.UI || {};

(function(exports) {
    const BACKGROUND = 'rgb(255, 255, 255)';
    const FOREGROUND = 'rgb(22, 38, 76)';
    const FIXED_WIDTH = 900;
    const FIXED_HEIGHT = 300;

    function SignatureCapture(options) {
        this.dialog = null;
        this.signaturePad = null;
        this.signatureImage = null;
        this.$canvas = null;
        this.$buttonContainer = null;
        this.isFullScreen = false;
        this.options = $.extend(true, {}, SignatureCapture._defaultOptions, options);
        this.create();
    }

    SignatureCapture._defaultOptions = {
        submitURL: "",
        csrf: {
            name: "",
            token: ""
        },
        // The default afterSubmit callback
        afterSubmit: function(response, widget) {
            if(!response.success) {
                new OpenEyes.UI.Dialog.Alert({
                    content: response.message
                }).open();
            }
        },
        openButtonSelector: "",
        widgetId: "",
        canvasSelector: "",
        eraseButtonSelector: "",
        saveButtonSelector: "",
        toggleFullScreenButtonSelector: ""
    };

    SignatureCapture.prototype.create = function ()
    {
        this.$canvas = $(this.options.canvasSelector);
        if(this.$canvas.length === 0) {
            console.error("Canvas with id " + this.options.canvasSelector + "not found");
        }
        // Adjust canvas to its container
        let width = this.$canvas.closest("div").innerWidth() - 40;
        this.$canvas.attr("width", width);
        this.$canvas.attr("height", width / 3);

        this.$buttonContainer = $(this.options.eraseButtonSelector).closest(".js-signature-buttons-container");
        // Launch signaturePad widget
        this.signaturePad = new SignaturePad(this.$canvas[0], {
            backgroundColor: BACKGROUND,
            penColor: FOREGROUND
        });
        this.addEventHandlers();
    };

    SignatureCapture.prototype.getNewCanvasByCoords = function(canvas, x, y, width, height) {
        // create a temp canvas
        const newCanvas = document.createElement('canvas');
        let dx, dy, dWidth, dHeight;
        // Must be exactly 900x300 px
        newCanvas.width = FIXED_WIDTH;
        newCanvas.height = FIXED_HEIGHT;
        // Crop image
        if(width < height * 3) {
            // Too narrow
            dHeight = FIXED_HEIGHT;
            dWidth = width * (FIXED_HEIGHT / height);
            dx = (FIXED_WIDTH - dWidth) / 2;
            dy = 0;
        }
        else if(width > height * 3) {
            // Too wide
            dWidth = FIXED_WIDTH;
            dHeight = height * (FIXED_WIDTH / width);
            dx = 0;
            dy = (FIXED_HEIGHT - dHeight) / 2;
        }
        else {
            // Exactly 3:1
            dWidth = FIXED_WIDTH;
            dHeight = FIXED_HEIGHT;
            dx = 0;
            dy = 0;
        }
        const ctx = newCanvas.getContext('2d');
        ctx.fillStyle = BACKGROUND;
        ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);
        ctx.drawImage(canvas, x, y, width, height, dx, dy, dWidth, dHeight);
        return newCanvas;
    };

    SignatureCapture.prototype.cropSignature = function(canvas)
    {
        let imgWidth = canvas.width;
        let imgHeight = canvas.height;
        let ctx = canvas.getContext('2d');
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

        return this.getNewCanvasByCoords(canvas, cropLeft, cropTop, signWidth, signHeight);
    };

    SignatureCapture.prototype.addEventHandlers = function()
    {
        let widget = this;
        //Erase button
        $(document).on("click", widget.options.eraseButtonSelector, function() {
            widget.signaturePad.clear();
        });

        //Save button
        $(document).on("click", widget.options.saveButtonSelector, function () {
            const canvas = widget.$canvas[0];
            const newImage = widget.cropSignature(canvas);
            widget.signatureImage = newImage.toDataURL("image/jpeg");
            widget.submitSignature();
        });

        //Toggle full screen button
        $(document).on("click", widget.options.toggleFullScreenButtonSelector, function () {
            if(widget.isFullScreen) {
                widget.exitFullScreen();
            }
            else {
                if(!widget.signaturePad.isEmpty()) {
                    let dlg = new OpenEyes.UI.Dialog.Confirm({
                        content: 'This will erase the current signature. Do you want to proceed?'
                    });
                    dlg.on("ok", function(){
                        widget.goFullScreen();
                    });
                    dlg.open();
                }
                else {
                    widget.goFullScreen();
                }
            }
        });
    };

    SignatureCapture.prototype.submitSignature = function()
    {
        let widget = this;
        let waitDlg = null;
        if(this.isFullScreen) {
            this.exitFullScreen();
        }
        // No UI in print mode
        if(typeof OpenEyes.UI.Dialog !== "undefined") {
            waitDlg = new OpenEyes.UI.Dialog({
                title: "Processing...",
                content: "<p>We're securing and saving your signature. Please wait.</p>",
                width: "auto"
            });
            waitDlg.open();
        }

        let postData = {};
        postData.image = widget.signatureImage;
        postData[widget.options.csrf.name] = widget.options.csrf.token;

        $.post(widget.options.submitURL, postData, function(data) {
            if(waitDlg !== null) {
                waitDlg.close();
            }
            widget.signaturePad.off();
            widget.options.afterSubmit(data, widget);
        });
    };

    SignatureCapture.prototype.swapToggleButtonText = function()
    {
        let $btn = $(this.options.toggleFullScreenButtonSelector);
        let tmpText = $btn.text();
        $btn.text($btn.attr("data-toggle-text"));
        $btn.attr("data-toggle-text", tmpText);
    };

    SignatureCapture.prototype.goFullScreen = function()
    {
        var elem = document.documentElement;

        // Go full screen
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

        // Stretch canvas
        this.$canvas.data("save_width", this.$canvas.attr("width"))
                    .data("save_height", this.$canvas.attr("height"))
                    .data("save_style", this.$canvas.attr("style"))
                    .css({
                        "position" : "fixed",
                        "top": 0,
                        "left" : 0,
                        "z-index" : 1
                    })
                    .attr("width", screen.width - 8)
                    .attr("height", screen.height - 8);

        // Bring buttons to front
        this.$buttonContainer.css({
            "position": "fixed",
            "z-index" : 2,
            "top" : 15,
            "right" : 15
        });

        // Change button text
        this.swapToggleButtonText();

        // Set flag
        this.isFullScreen = true;
    };

    SignatureCapture.prototype.exitFullScreen = function()
    {
        // Save image to tmp canvas
        let tmpCanvas = this.cropSignature(this.$canvas[0]);

        // Change button text
        this.swapToggleButtonText();

        // Restore canvas and buttons
        this.$canvas.attr("width", this.$canvas.data("save_width"));
        this.$canvas.attr("height", this.$canvas.data("save_height"));
        this.$canvas.attr("style", this.$canvas.data("save_style"));
        this.$buttonContainer.css({
            "position" : "",
            "z-index" : "",
            "top" : "",
            "right" : ""
        });

        // Reapply image to the original canvas
        let destCtx = this.$canvas[0].getContext('2d');
        destCtx.drawImage(tmpCanvas, 0, 0);

        // Exit full screen
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }

        // Reset flag
        this.isFullScreen = false;
    };

    exports.SignatureCapture = SignatureCapture;

}(OpenEyes.UI));

