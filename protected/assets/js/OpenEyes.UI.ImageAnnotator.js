/* global fabric */
(function(exports) {
    'use strict';

    /**
     * Represents a ImageAnnotator.
     * @requires fabric
     * @param {object} options
     * @constructor
     */
    let canvas, extension, multiplier;
    const init = (imgUrl, width, height, imgWrapper, format, data, side) => {
        // div.oe-annotate-image wrapper for the toolbox and canvas-js for Fabric
        const annotate = document.getElementById('js-annotate-image');
        // line width is using input [range], only need it for freedraw, hide on other tools
        const lineWidth = annotate.querySelector('.line-width');

        let activeTool = null; // store current tool (to handle adding removing "draw" class)
        let drawColor = "#c00"; // user selected colour

        extension = format;
        /*
        Create CANVAS and append to DOM
        */
        const canvasElem = document.createElement('canvas');
        canvasElem.id = 'c1';
        canvasElem.textContent = "Image annotation tool";
        const canvasJsElem = annotate.querySelector('.canvas-js');
        canvasJsElem.setAttribute(data.attribute, data.value);
        canvasJsElem.append( canvasElem );
        annotate.style.display = '';

        canvas = new fabric.Canvas('c1');

        // Selecting Object styling
        fabric.Object.prototype.set({
            borderColor: 'rgb(0,255,255)',
            cornerColor: 'rgb(0,255,255)',
            cornerSize: 12,
            transparentCorners: false
        });

        let canvasModifiedCallback = function() {
            document.getElementById(`${side}_file_canvas_modified`).value = 1;
        };

        canvas.on('object:added', canvasModifiedCallback);
        canvas.on('object:removed', canvasModifiedCallback);
        canvas.on('object:modified', canvasModifiedCallback);

        // set up the default line (default to middle setting 3)
        canvas.freeDrawingBrush.color = drawColor;
        document.querySelectorAll('.colors rect').forEach(function (element) {
            element.classList.remove("selected");
        });
        annotate.querySelector(`.colors [fill="${drawColor}"]`).classList.add('selected');

        canvas.freeDrawingBrush.width = 2;
        lineWidth.querySelector('input').value = 2;
        lineWidth.querySelector('small').textContent = `Line width: 2`;

        document.querySelectorAll('.js-tool-btn').forEach(function (element) {
            element.classList.remove("draw");
        });

        annotate.querySelector('.colors').addEventListener('click', function (e) {
            e.preventDefault();
            const el = e.target;
            document.querySelectorAll('.colors rect').forEach(function (element) {
                element.classList.remove("selected");
            });
            el.classList.add("selected");
            drawColor = el.getAttribute("fill");
            canvas.freeDrawingBrush.color = drawColor;
        });


        /**
         * set up the canvas for an image
         * reset each time...
         * @param {String} imgUrl - jpg
         * @param {Number} w - width
         * @param {Number} h - height
         */
        const resetCanvas = ( imgUrl, w, h ) => {
            canvas.clear();

            const canvasMaxWidth = annotate.offsetWidth - 160; // allow for the toolbox
            const imgScale = canvasMaxWidth / w;
            multiplier = w / canvasMaxWidth;
            // update canvas size
            canvas.setHeight( h * imgScale );
            canvas.setWidth( canvasMaxWidth );

            // image background
            fabric.Image.fromURL(imgUrl, oImg => {
                oImg.scale( imgScale );
                canvas.setBackgroundImage( oImg, canvas.renderAll.bind( canvas ));
            });
        };

        /**
         * Circle draw
         */
        const drawCircle = (() => {
            let active, adjust;

            /**
             * @callback for mouse:down
             * @param {Event} e
             */
            const addCircle = ( e ) => {
                if( !active || adjust ) return; adjust = true;
                // create a new circle
                const circle = new fabric.Circle({
                    radius: 30, // a standard size
                    left: e.pointer.x - 30,
                    top: e.pointer.y - 30,
                    fill: false,
                    stroke: drawColor,
                    strokeWidth: parseInt(canvas.freeDrawingBrush.width),
                    centeredScaling: true,
                    strokeUniform: true,
                });

                canvas.add( circle ); // add Circle
                canvas.setActiveObject( circle ); // set as active to provide instant control
            };

            // Listeners
            canvas.on('mouse:down', ( e ) => addCircle( e ));
            canvas.on('selection:cleared', () => adjust = false );

            // simple API
            const start = () => {
                active = true;
                adjust = false;
            };

            const stop = () => {
                active = false;
            };

            return { start, stop };
        })();

        /**
         * Pointer (Arrow) draw
         */
        const drawPointer = (() => {
            let active, adjust;

            const addPointer = ( e ) => {
                if( !active || adjust ) return; adjust = true;

                const width = parseInt(canvas.freeDrawingBrush.width);
                const triangle = new fabric.Triangle({ width: 20 + width, height: 20 + width, left: 30, top: 30 });
                const line = new fabric.Line([40,60,40,120], {
                    top: triangle.height + triangle.top,
                    left: triangle.left + (triangle.width / 2) + 0.5 - (width / 2),
                    strokeWidth: width
                });
                let newPointer = new fabric.Group([ triangle, line], { originY:'top', });

                // which quarter is the user adding the arrow in?
                const topHalf = ( canvas.height / 2 ) > e.pointer.y;
                const leftHalf = ( canvas.width / 2 ) > e.pointer.x;
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
                    'fill': drawColor,
                });
                newPointer.item(1).set({
                    'stroke': drawColor,
                });

                canvas.add( newPointer ); // add Pointer
                canvas.setActiveObject( newPointer ); // set as active to provide instant control
            };

            // Listeners
            canvas.on('mouse:down', ( e ) => addPointer( e ));
            canvas.on('selection:cleared', () => adjust = false );

            // simple API
            const start = () => {
                active = true;
                adjust = false;
            };

            const stop = () => {
                active = false;
            };

            return { start, stop };
        })();

        /**
         * Text
         */
        const addText = (() => {
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

            const labelEl = document.querySelector('#js-label-text');

            // build the text template
            const text = new TextboxWithPadding(labelEl.value, {
                fontFamily: "Arial",
                left: 780,
                top: 100,
                fontSize: 24,
                textAlign: "left",
                fill: drawColor,
                backgroundColor: 'rgba(255,255,255,0.7)',
                paddingColor: drawColor,
                padding: 5,
            });

            canvas.add(text);
            canvas.setActiveObject( text );
            labelEl.value = '';

            canvas.set('isDrawingMode', false );
            canvas.set('defaultCursor', 'crosshair');
            document.querySelector('.js-tool-btn[name=freedraw]').classList.remove('draw');
        });

        /**
         * Controller for tool button events
         * @param {Element} toolBtn - user requests a tool
         */
        const toolChange = ( toolBtn ) => {
            if( toolBtn.name === 'erase'){
                canvas.remove( canvas.getActiveObject());
                return;
            }

            if( toolBtn.name === 'clear-all'){
                canvas.getObjects().forEach(function(object) {
                    canvas.remove(object);
                });
                return;
            }

            // update the UI
            if( activeTool ) {
                activeTool.classList.remove('draw');
            }
            activeTool = toolBtn;
            toolBtn.classList.add('draw');

            // reset to defaults
            drawCircle.stop();
            drawPointer.stop();
            canvas.set('isDrawingMode', false );
            canvas.set('defaultCursor', 'crosshair');

            switch( toolBtn.name ){
                case 'manipulate':
                    canvas.set('defaultCursor', 'auto');
                    break;
                case 'freedraw':
                    canvas.set('isDrawingMode', true );
                    break;
                case 'circle':
                    drawCircle.start();
                    break;
                case 'pointer':
                    drawPointer.start();
                    break;
            }
        };

        Array.from(document.getElementsByClassName('js-tool-btn')).forEach(function(element) {
            element.addEventListener('click', function (e) {
                e.preventDefault();
                if (element.getAttribute("name") === "text") {
                    addText();
                    return;
                }
                toolChange(e.target);
            });
        });

        // linewidth input range
        lineWidth.querySelector('input').addEventListener('input', (e) => {
            const w = e.target.value;
            lineWidth.querySelector('small').textContent = `Line width: ${w}`;
            canvas.freeDrawingBrush.width = parseInt(w, 10) || 1;
        });

        resetCanvas(imgUrl, width, height);
        toolChange( document.querySelector('.js-tool-btn[name="freedraw"]'));
        // hide the image
        imgWrapper.forEach(function (element) {
            element.style.display = 'none';
        });
    };

    function getCanvasDataUrl() {
        try {
            return canvas.toDataURL({
                format: extension,
                multiplier: multiplier
            });
        } catch (e) {
            if (e instanceof TypeError) {
                return false;
            }
        }
    }

    function clearCanvas() {
        canvas = null;
    }

    exports.ImageAnnotator = {
        'init': init,
        'getCanvasDataUrl' : getCanvasDataUrl,
        'clearCanvas' : clearCanvas
    };
}(OpenEyes.UI));