/* global fabric */
(function(exports) {
    'use strict';

    /**
     * Represents a ImageAnnotator.
     * @requires fabric
     * @param {object} options
     * @constructor
     */
    function ImageAnnotator(imageUrl, options) {
        this.options = $.extend(true, {}, ImageAnnotator._default_options, options);
        this.imageUrl = imageUrl;

        this.$wrapper = document.querySelector(this.options.annotateSelector);
        this.lineWidth = this.$wrapper.querySelector('.line-width');
        this.drawColor = this.options.drawColor;
        this.$canvas = null;
        this.activeTool = null;

        this.init();

        this.drawCircleControl = this.drawCircle();
        this.drawPointerControl = this.drawPointer();
    }

    ImageAnnotator._default_options = {
        'annotateSelector': '#js-annotate-image',
        'format': 'jpg',
        'activeTool': null,
        'drawColor': '#c00',
        'side': 3,
        'canvasModifiedCallback': () => {},
        'afterInit': () => {},
        'withEventListeners': true
    };

    ImageAnnotator.prototype.getMeta = function getMeta(url) {
        return new Promise((resolve, reject) => {
            let img = new Image();
            img.onload = () => resolve(img);
            img.onerror = () => reject();
            img.src = url;
        });
    };

    ImageAnnotator.prototype.init = async function() {

        // needs to be unique but doesn't matter what is the value
        const canvas_id = 'c' + Date.now();
        const canvasElem = document.createElement('canvas');
        const canvasJsElem = this.$wrapper.querySelector('.canvas-js');
        canvasElem.id = canvas_id;
        canvasElem.textContent = "Image annotation tool";

        const $existing_canvas = canvasJsElem.querySelector('canvas');
        if ($existing_canvas) {
            canvasJsElem.innerHTML = '';
        }

        canvasJsElem.setAttribute('data-side', this.options.side);
        canvasJsElem.append(canvasElem);
        this.$wrapper.style.display = '';

        this.$canvas = new fabric.Canvas(canvas_id);

        // Selecting Object styling
        fabric.Object.prototype.set({
            borderColor: 'rgb(0,255,255)',
            cornerColor: 'rgb(0,255,255)',
            cornerSize: 12,
            transparentCorners: false
        });

        this.$canvas.on('object:added', this.options.canvasModifiedCallback.bind(this));
        this.$canvas.on('object:removed', this.options.canvasModifiedCallback.bind(this));
        this.$canvas.on('object:modified', this.options.canvasModifiedCallback.bind(this));

        // set up the default line (default to middle setting 3)
        this.$canvas.freeDrawingBrush.color = this.options.drawColor;

        this.$wrapper.querySelectorAll('.colors rect').forEach(function (element) {
            element.classList.remove("selected");
        });
        this.$wrapper.querySelector(`.colors [fill="${this.options.drawColor}"]`).classList.add('selected');

        this.$canvas.freeDrawingBrush.width = 2;
        this.lineWidth.querySelector('input').value = 2;
        this.lineWidth.querySelector('small').textContent = `Line width: 2`;

        this.$wrapper.querySelectorAll('.js-tool-btn').forEach(function (element) {
            element.classList.remove("draw");
        });

        // in freehand draw element we need to call init multiple times
        if (this.options.withEventListeners) {
            const colours = this.$wrapper.querySelectorAll('.colors');
            colours.forEach(colour => {
                OpenEyes.UI.DOM.addEventListener(colour, 'click', 'rect', (e) => {
                    this.resetColourSelection(e.target);
                });
            });

            Array.from(this.$wrapper.getElementsByClassName('js-tool-btn')).forEach((element) => {
                element.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (element.getAttribute("name") === "text") {
                        this.addText();
                        return;
                    }
                    this.toolChange(e.target);
                });
            });

            // linewidth input range
            this.lineWidth.querySelector('input').addEventListener('input', (e) => {
                const w = e.target.value;
                this.lineWidth.querySelector('small').textContent = `Line width: ${w}`;
                this.$canvas.freeDrawingBrush.width = parseInt(w, 10) || 1;
            });
        }

        this.resetCanvas(this.imageUrl);
        this.toolChange( document.querySelector('.js-tool-btn[name="freedraw"]'));

        if (typeof this.options.afterInit === 'function') {
            this.options.afterInit.call(this);
        }

        this.options.canvasModifiedCallback.bind(this)();
    };

    ImageAnnotator.prototype.resetColourSelection = function(selected_rect) {
        const colours = this.$wrapper.querySelectorAll('.colors');
        colours.forEach(row => {
            const rects = row.querySelectorAll('rect');
            rects.forEach(rect => { rect.classList.remove("selected"); });
        });
        selected_rect.classList.add("selected");
        this.$canvas.freeDrawingBrush.color = selected_rect.getAttribute("fill");
        this.drawColor = selected_rect.getAttribute("fill");
    };

    /**
     * set up the canvas for an image
     * reset each time...
     * @param {String} imgUrl - jpg
     * @param {Number} w - width
     * @param {Number} h - height
     */
    ImageAnnotator.prototype.resetCanvas = async function(imgUrl) {

        this.$canvas.clear();
        let img = await this.getMeta(this.imageUrl);
        let w = img.naturalWidth;
        let h = img.naturalHeight;

        const canvasMaxWidth = this.$wrapper.offsetWidth - 160; // allow for the toolbox
        const imgScale = canvasMaxWidth / w;
        // multiplier = w / canvasMaxWidth;
        // update canvas size
        this.$canvas.setHeight( h * imgScale );
        this.$canvas.setWidth( canvasMaxWidth );

        // image background
        if (imgUrl) {
            fabric.Image.fromURL(imgUrl, oImg => {
                oImg.scale(imgScale);
                this.$canvas.setBackgroundImage( oImg, this.$canvas.renderAll.bind(this.$canvas));
            });
        }
    };

    /**
     * Circle draw
     */
    ImageAnnotator.prototype.drawCircle = function() {
        let active, adjust;

        /**
         * @callback for mouse:down
         * @param {Event} e
         */
        const addCircle = ( e ) => {
            if( !active || adjust ) {
                return;
            }
            adjust = true;
            // create a new circle
            const circle = new fabric.Circle({
                radius: 30, // a standard size
                left: e.pointer.x - 30,
                top: e.pointer.y - 30,
                fill: false,
                stroke: this.drawColor,
                strokeWidth: parseInt(this.$canvas.freeDrawingBrush.width),
                centeredScaling: true,
                strokeUniform: true,
            });

            this.$canvas.add(circle); // add Circle
            this.$canvas.setActiveObject(circle); // set as active to provide instant control
        };

        // Listeners
        this.$canvas.on('mouse:down', ( e ) => addCircle( e ));
        this.$canvas.on('selection:cleared', () => adjust = false );

        // simple API
        const start = () => {
            active = true;
            adjust = false;
        };

        const stop = () => {
            active = false;
        };

        return { start, stop };
    };

    /**
     * Pointer (Arrow) draw
     */
    ImageAnnotator.prototype.drawPointer = function() {
        let active, adjust;

        const addPointer = ( e ) => {
            if( !active || adjust ) {
                return;
            }
            adjust = true;

            const width = parseInt(this.$canvas.freeDrawingBrush.width);
            const triangle = new fabric.Triangle({ width: 20 + width, height: 20 + width, left: 30, top: 30 });
            const line = new fabric.Line([40,60,40,120], {
                top: triangle.height + triangle.top,
                left: triangle.left + (triangle.width / 2) + 0.5 - (width / 2),
                strokeWidth: width
            });
            let newPointer = new fabric.Group([ triangle, line], { originY:'top', });

            // which quarter is the user adding the arrow in?
            const topHalf = ( this.$canvas.height / 2 ) > e.pointer.y;
            const leftHalf = ( this.$canvas.width / 2 ) > e.pointer.x;
            let r, x, y;

            if( topHalf ){
                if( leftHalf ){
                    r = -45;
                    x = e.pointer.x;
                    y = e.pointer.y + 18;
                } else {
                    r = 45;
                    x = e.pointer.x - 18;
                    y = e.pointer.y;
                }
            } else {
                if( leftHalf ){
                    r = -135;
                    x = e.pointer.x + 20;
                    y = e.pointer.y;
                } else {
                    r = 135;
                    x = e.pointer.x;
                    y = e.pointer.y - 20;
                }
            }

            // adjust and position new Pointer
            newPointer.rotate( r );
            newPointer.set({
                top: y,
                left: x,
            });

            newPointer.item(0).set({
                'fill': this.drawColor,
            });
            newPointer.item(1).set({
                'stroke': this.drawColor,
            });

            this.$canvas.add(newPointer); // add Pointer
            this.$canvas.setActiveObject(newPointer); // set as active to provide instant control
        };

        // Listeners
        this.$canvas.on('mouse:down', ( e ) => addPointer( e ));
        this.$canvas.on('selection:cleared', () => adjust = false );

        // simple API
        const start = () => {
            active = true;
            adjust = false;
        };

        const stop = () => {
            active = false;
        };

        return { start, stop };
    };

    /**
     * Text
     */
    ImageAnnotator.prototype.addText = function() {
        let TextboxWithPadding = fabric.util.createClass(fabric.Textbox, {
            _renderBackground: function(ctx) {
                if (!this.backgroundColor) {
                    return;
                }
                const dim = this._getNonTransformedDimensions();
                ctx.fillStyle = this.backgroundColor;
                ctx.fillRect(
                    -dim.x / 2 - this.padding,
                    -dim.y / 2 - this.padding,
                    dim.x + this.padding * 2,
                    dim.y + this.padding * 2
                );

                ctx.strokeStyle = this.paddingColor;
                ctx.strokeRect(
                    -dim.x / 2 - this.padding,
                    -dim.y / 2 - this.padding,
                    dim.x + this.padding * 2,
                    dim.y + this.padding * 2
                );

                // if there is background color no other shadows
                // should be casted
                this._removeShadow(ctx);
            }
        });

        const labelEl = this.$wrapper.querySelector('.js-label-text');

        // build the text template
        const text = new TextboxWithPadding(labelEl.value, {
            fontFamily: "Arial",
            left: 780,
            top: 100,
            fontSize: 24,
            textAlign: "left",
            fill: this.drawColor,
            backgroundColor: 'rgba(255,255,255,0.7)',
            paddingColor: this.drawColor,
            padding: 5,
        });

        this.$canvas.add(text);
        this.$canvas.setActiveObject( text );
        labelEl.value = '';

        this.$canvas.set('isDrawingMode', false );
        this.$canvas.set('defaultCursor', 'crosshair');
        document.querySelector('.js-tool-btn[name=freedraw]').classList.remove('draw');
    };

    /**
     * Controller for tool button events
     * @param {Element} toolBtn - user requests a tool
     */
    ImageAnnotator.prototype.toolChange = function( toolBtn ) {
        if( toolBtn.name === 'erase'){
            this.$canvas.remove( this.$canvas.getActiveObject());
            return;
        }

        if( toolBtn.name === 'clear-all'){
            this.$canvas.getObjects().forEach((object) => {
                this.$canvas.remove(object);
            });
            return;
        }

        // update the UI
        if( this.activeTool ) {
            this.activeTool.classList.remove('draw');
        }
        this.activeTool = toolBtn;
        toolBtn.classList.add('draw');

        // reset to defaults
        if (this.drawCircleControl) {
            this.drawCircleControl.stop();
            this.drawPointerControl.stop();

        }

        this.$canvas.set('isDrawingMode', false );
        this.$canvas.set('defaultCursor', 'crosshair');

        switch( toolBtn.name ){
            case 'manipulate':
                this.$canvas.set('defaultCursor', 'auto');
                break;
            case 'freedraw':
                this.$canvas.set('isDrawingMode', true );
                break;
            case 'circle':
                this.drawCircleControl.start();
                break;
            case 'pointer':
                this.drawPointerControl.start();
                break;
        }
    };

    ImageAnnotator.prototype.getCanvasDataUrl = async function() {
        try {
            const img = await this.getMeta(this.imageUrl);
            const w = img.naturalWidth;
            const canvasMaxWidth = this.$wrapper.offsetWidth - 160; // allow for the toolbox
            const multiplier = w / canvasMaxWidth;

            return this.$canvas.toDataURL({
                format: this.options.format,
                multiplier: multiplier
            });
        } catch (e) {
            if (e instanceof TypeError) {
                return false;
            }
        }
    };

    ImageAnnotator.prototype.clearCanvas = function() {
        this.$canvas = null;
    };

    exports.ImageAnnotator = ImageAnnotator;

}(OpenEyes.UI));
