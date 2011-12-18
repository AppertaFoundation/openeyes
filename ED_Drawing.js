/**
 * @fileOverview Contains the core classes for EyeDraw
 * @author <a href="mailto:bill.aylward@mac.com">Bill Aylward</a>
 * @version 0.93
 *
 * Modification date: 23th October 2011
 * Copyright 2011 OpenEyes
 * 
 * This file is part of OpenEyes.
 * 
 * OpenEyes is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * OpenEyes is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with OpenEyes.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Defines the EyeDraw namespace
 * @namespace Namespace for all EyeDraw classes
 */
var ED = new Object();
 
/**
 * Radius of inner handle displayed with selected doodle
 * @constant
 */
ED.handleRadius = 15;

/**
 * Distance in doodle plane moved by pressing an arrow key
 * @constant
 */
ED.arrowDelta = 4;

/**
 * SquiggleWidth
 */
ED.squiggleWidth = 
{
    Thin:4,
    Medium:12,
    Thick:20
}

/**
 * Flag to detect double clicks
 */
ED.recentClick = false;

/**
 * Eye (Some doodles behave differently according to side)
 */
ED.eye = 
{
	Right:0,
	Left:1
}

/**
 * Draw function mode (Canvas pointInPath function requires a path)
 */
ED.drawFunctionMode = 
{
	Draw:0,
	HitTest:1
}

/**
 * Mouse dragging mode
 */
ED.Mode = 
{
	None:0,
	Move:1,
	Scale:2,
	Arc:3,
	Rotate:4,
	Apex:5,
    Draw:6
}

/**
 * Handle ring
 */
ED.handleRing =
{
	Inner:0,
	Outer:1
}

/*
 * Chris Raettig's function for getting accurate mouse position in all browsers
 *
 * @param {Object} obj Object to get offset for, usually canvas object
 * @returns {Object} x and y values of offset
 */
ED.findOffset = function(obj)
{
    var curleft = curtop = 0;
    if (obj.offsetParent) {
        do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
        } while (obj = obj.offsetParent);
        return { x: curleft, y: curtop };
    }
}

/*
 * Returns true if browser is firefox
 *
 * @returns {Bool} True is browser is firefox
 */
ED.isFirefox = function()
{
    var index = 0;
    var ua = window.navigator.userAgent;
    index = ua.indexOf("Firefox");
    
    if (index > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * A Drawing consists of one canvas element displaying one or more doodles;
 * Doodles are drawn in the 'doodle plane' consisting of a 1001 pixel square grid -500 to 500) with central origin, and negative Y upwards;
 * Affine transforms are used to convert points in the doodle plane to the canvas plane, the plane of the canvas element;
 * Each doodle contains additional transforms to handle individual position, rotation, and scale.
 * 
 * @class Drawing
 * @property {Canvas} canvas A canvas element used to edit and display the drawing
 * @property {Eye} eye Right or left eye (some doodles display differently according to side)
 * @property {Context} context The 2d context of the canvas element
 * @property {Array} doodleArray Array of doodles in the drawing
 * @property {AffineTransform} transform Transform converts doodle plane -> canvas plane
 * @property {AffineTransform} inverseTransform Inverse transform converts canvas plane -> doodle plane
 * @property {Doodle} selectedDoodle The currently selected doodle, null if no selection
 * @property {Bool} mouseDown Flag indicating whether mouse is down in canvas
 * @property {Mode} mode The current mouse dragging mode
 * @property {Point} lastMousePosition Last position of mouse in canvas coordinates
 * @property {Image} image Optional background image
 * @property {Int} doubleClickMilliSeconds Duration of double click
 * @property {Bool} newPointOnClick Flag indicating whether a mouse click will create a new PointInLine doodle
 * @property {Bool} completeLine Flag indicating whether to draw an additional line to the first PointInLine doodle
 * @property {Float} scale Scaling of transformation from canvas to doodle planes, preserving aspect ratio and maximising doodle plnae
 * @property {Float} globalScaleFactor Factor used to scale all added doodles to this drawing, defaults to 1
 * @param {Canvas} _canvas Canvas element 
 * @param {Eye} _eye Right or left eye
 * @param {String} _IDSuffix String suffix to identify HTML elements related to this drawing
 */
ED.Drawing = function(_canvas, _eye, _IDSuffix)
{
	// Properties
	this.canvas = _canvas;
	this.eye = _eye;
	this.IDSuffix = _IDSuffix;
	
	this.context = this.canvas.getContext('2d');
	this.doodleArray = new Array();
	this.transform = new ED.AffineTransform();
	this.inverseTransform = new ED.AffineTransform();
	this.selectedDoodle = null;
	this.mouseDown = false;
    this.doubleClick = false;
	this.mode = ED.Mode.Move;
	this.lastMousePosition = new ED.Point(0, 0);
    this.doubleClickMilliSeconds = 250;
    this.onLoadedHasRun = false;
    this.newPointOnClick = false;
    this.completeLine = false;
    this.globalScaleFactor = 1;
    
    // Fit canvas making maximum use of doodle plane
    if (this.canvas.width >= this.canvas.height)
    {
        this.scale = this.canvas.width/1001;
    }
    else
    {
        this.scale = this.canvas.height/1001;
    }
    
    // Array of images to be preloaded (Add new images here)
    this.imageArray = new Array();
    this.imageArray['latticePattern'] = new Image();
    this.imageArray['cryoPattern'] = new Image();
    this.imageArray['antPVRPattern'] = new Image();
    this.imageArray['laserPattern'] = new Image();
    this.imageArray['fuchsPattern'] = new Image();
    this.imageArray['pscPattern'] = new Image();
    this.imageArray['meshworkPattern'] = new Image();
    this.imageArray['newVesselPattern'] = new Image();
    
	// Set transform to map from doodle to canvas plane
	this.transform.translate(this.canvas.width/2, this.canvas.height/2);
	this.transform.scale(this.scale, this.scale);
	
	// Set inverse transform to map the other way
	this.inverseTransform = this.transform.createInverse();
	
	// Initialise canvas context transform by calling clear() method	
	this.clear();
	
	// Get reference to button elements
	this.moveToFrontButton = document.getElementById('moveToFront' + this.IDSuffix);
	this.moveToBackButton = document.getElementById('moveToBack' + this.IDSuffix);
	this.deleteButton = document.getElementById('delete' + this.IDSuffix);
	this.lockButton = document.getElementById('lock' + this.IDSuffix);
	this.unlockButton = document.getElementById('unlock' + this.IDSuffix);
    this.squiggleSpan = document.getElementById('squiggleSpan' + this.IDSuffix);
    this.colourPreview = document.getElementById('colourPreview' + this.IDSuffix);
    this.fillRadio = document.getElementById('fillRadio' + this.IDSuffix);
    this.thickness = document.getElementById('thicknessSelect' + this.IDSuffix);
    
    // Add event listeners (NB within the event listener 'this' refers to the canvas, NOT the drawing instance)
    var drawing = this;
    
    // Mouse listeners
    this.canvas.addEventListener('mousedown', function(e) {
                            var offset = ED.findOffset(this);
                            var point = new ED.Point(e.pageX-offset.x,e.pageY-offset.y);
                            drawing.mousedown(point);
                             }, false);
    
    this.canvas.addEventListener('mouseup', function(e) { 
                            var offset = ED.findOffset(this);
                            var point = new ED.Point(e.pageX-offset.x,e.pageY-offset.y);
                            drawing.mouseup(point); 
                            }, false);
    
    this.canvas.addEventListener('mousemove', function(e) { 
                            var offset = ED.findOffset(this);
                            var point = new ED.Point(e.pageX-offset.x,e.pageY-offset.y);
                            drawing.mousemove(point); 
                            }, false);
    
    this.canvas.addEventListener('mouseout', function(e) { 
                            var offset = ED.findOffset(this);
                            var point = new ED.Point(e.pageX-offset.x,e.pageY-offset.y);
                            drawing.mouseout(point); 
                            }, false);
    
    // iOS listeners
    this.canvas.addEventListener('touchstart', function(e) { 
                            var point = new ED.Point(e.targetTouches[0].pageX - this.offsetLeft,e.targetTouches[0].pageY - this.offsetTop);
                            e.preventDefault();
                            drawing.mousedown(point); 
                            }, false);
    
    this.canvas.addEventListener('touchend', function(e) { 
                            var point = new ED.Point(e.targetTouches[0].pageX - this.offsetLeft,e.targetTouches[0].pageY - this.offsetTop);
                            drawing.mouseup(point); 
                            }, false);
    
    this.canvas.addEventListener('touchmove', function(e) { 
                            var point = new ED.Point(e.targetTouches[0].pageX - this.offsetLeft,e.targetTouches[0].pageY - this.offsetTop);
                            drawing.mousemove(point); 
                            }, false);
    
    // Keyboard listener
    window.addEventListener('keydown',function(e) {
                            if (document.activeElement && document.activeElement.tagName == 'CANVAS') drawing.keydown(e);
                            }, true);
    
    
    // Stop browser stealing double click to select text
    this.canvas.onselectstart = function () { return false; }
}


/**
 * Preloads image files
 *
 * @param {String} Relative path to directory where images are stored
 */
ED.Drawing.prototype.preLoadImagesFrom = function(_path)
{
    var drawing = this;
    var loaded = false;
    
    // Iterate through array loading each image, calling checking function from onload event
    for (var key in this.imageArray)
    {
        this.imageArray[key].onload = function()
        {
            drawing.checkAllLoaded();
        }
        this.imageArray[key].src = _path + key + '.gif';
    }
}

/**
 * Checks all images are loaded then calls onLoaded function to proceed with other processing
 */
ED.Drawing.prototype.checkAllLoaded = function()
{
    // Set flag to check loading
    var allLoaded = true;
    
    // Iterate through array loading each image, checking all are loaded
    for (var key in this.imageArray)
    {
        var imageLoaded = false;
        if (this.imageArray[key].width > 0) imageLoaded = true;
        
        // Check all are loaded
        allLoaded = allLoaded && imageLoaded;
    }
    
    // If all are loaded, proceed with onLoaded event (if defined)
    if (allLoaded && typeof(this.onLoaded) == 'function')
    {
        if (!this.onLoadedHasRun)
        {
            this.onLoaded();
            this.onLoadedHasRun = true;
        }
    }
}

/**
 * Loads doodles from passed set into doodleArray
 *
 * @param {Set} _doodleSet Set of doodles from server
 */
ED.Drawing.prototype.load = function(_doodleSet)
{
	// Iterate through set of doodles and load into doodle array
	for (var i = 0; i < _doodleSet.length; i++)
	{
		// Instantiate a new doodle object with parameters from doodle set
		this.doodleArray[i] = new ED[_doodleSet[i].subclass]
		(
			this,
			_doodleSet[i].originX,
			_doodleSet[i].originY,
			_doodleSet[i].apexX,
			_doodleSet[i].apexY,
			_doodleSet[i].scaleX,
			_doodleSet[i].scaleY,
			_doodleSet[i].arc,
			_doodleSet[i].rotation,
			_doodleSet[i].order
		);
        
        // Squiggle array
        if (typeof(_doodleSet[i].squiggleArray) != 'undefined')
        {
            for (var j = 0; j < _doodleSet[i].squiggleArray.length; j++)
            {
                // Get paramters and create squiggle
                var c = _doodleSet[i].squiggleArray[j].colour;
                var colour = new ED.Colour(c.red, c.green, c.blue, c.alpha);
                var thickness = _doodleSet[i].squiggleArray[j].thickness;
                var filled = _doodleSet[i].squiggleArray[j].filled;
                var squiggle = new ED.Squiggle(this.doodleArray[i], colour, thickness, filled);
                
                // Add points to squiggle and complete it
                var pointsArray = _doodleSet[i].squiggleArray[j].pointsArray;
                for (var k = 0; k < pointsArray.length; k++)
                {
                    var point = new ED.Point(pointsArray[k].x, pointsArray[k].y);
                    squiggle.addPoint(point);
                }
                squiggle.complete = true;
                
                // Add squiggle to doodle's squiggle array
                this.doodleArray[i].squiggleArray.push(squiggle);
            }
        }
	}
	
	// Sort array by order (puts back doodle first)
	this.doodleArray.sort(function(a,b){return a.order - b.order});
}

/**
 * Creates string containing drawing data in JSON format with surrounding square brackets
 *
 * @returns {String} Serialized data in JSON format with surrounding square brackets
 */
ED.Drawing.prototype.save = function()
{    
    // Store current data in textArea
    return '[' + this.json() + ']';
}

/**
 * Creates string containing drawing data in JSON format
 *
 * @returns {String} Serialized data in JSON format
 */
ED.Drawing.prototype.json = function()
{
    var s = "";
	for (var i = 0; i < this.doodleArray.length; i++)
	{
        s = s + this.doodleArray[i].json() + ", ";
    }
    
    return s;
}

/**
 * Draws all doodles for this drawing
 */ 
ED.Drawing.prototype.drawAllDoodles = function()
{
    // Draw any connecting lines

    var ctx = this.context;
    ctx.beginPath();
    var started = false;
    var startPoint;
    
    for (var i = 0; i < this.doodleArray.length; i++)
    {
        if (this.doodleArray[i].isPointInLine)
        {
            // Start or draw line
            if (!started)
            {
                ctx.moveTo(this.doodleArray[i].originX, this.doodleArray[i].originY);
                started = true;
                startPoint = new ED.Point(this.doodleArray[i].originX, this.doodleArray[i].originY);
            }
            else
            {
                ctx.lineTo(this.doodleArray[i].originX, this.doodleArray[i].originY);
            }
        }
    }
    
    // Optionally add line to start
    if (this.completeLine && typeof(startPoint) != 'undefined')
    {
        ctx.lineTo(startPoint.x, startPoint.y);
    }
    
    // Draw lines
    if (started)
    {
        ctx.lineWidth = 4;
        ctx.strokeStyle = "rgba(20,20,20,1)";
        ctx.stroke();
    }

    
	// Draw doodles
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		// Save context (draw method of each doodle may alter it)
		this.context.save();
		
		// Draw doodle
		this.doodleArray[i].draw();
		
		// Restore context
		this.context.restore();
	}
}


/**
 * Responds to mouse down event in canvas, cycles through doodles from front to back.
 * Selected doodle is first selectable doodle to have click within boundary path.
 * Double clicking on a selected doodle promotes it to drawing mode (if is drawable)
 *
 * @event
 * @param {Point} _point Coordinates of mouse in canvas plane
 */  
ED.Drawing.prototype.mousedown = function(_point)
{
	// Set flag to indicate dragging can now take place
	this.mouseDown = true;
    
    // Detect double click
    if (ED.recentClick) this.doubleClick = true;
    ED.recentClick = true;
    var t = setTimeout("ED.recentClick = false;",this.doubleClickMilliSeconds);
    
	// Set flag to indicate success
	var found = false;
	this.selectedDoodle = null;

	// Cycle through doodles from front to back doing hit test
	for (var i = this.doodleArray.length - 1; i > -1; i--)
	{
		if (!found)
		{
			// Save context (draw method of each doodle may alter it)
			this.context.save();
		
			// Successful hit test?
			if (this.doodleArray[i].draw(_point))
			{
				if (this.doodleArray[i].isSelectable)
				{
                    // If double clicked, go into drawing mode
                    if (this.doubleClick && this.doodleArray[i].isSelected && this.doodleArray[i].isDrawable)
                    {
                        this.doodleArray[i].isForDrawing = true;
                    }
                    
					this.doodleArray[i].isSelected = true;
					this.selectedDoodle = this.doodleArray[i];
					found = true;
                    
                    // If for drawing, mouse down starts a new squiggle
                    if (!this.doubleClick && this.doodleArray[i].isForDrawing)
                    {
                        // Add new squiggle
                        this.doodleArray[i].addSquiggle();
                    }
				}
			}
			// Ensure that unselected doodles are marked as such
			else
			{
				this.doodleArray[i].isSelected = false;
                this.doodleArray[i].isForDrawing = false;
			}
			
			// Restore context
			this.context.restore();
		}
		else
		{
			this.doodleArray[i].isSelected = false;
            this.doodleArray[i].isForDrawing = false;
		}
		
		// Ensure drag flagged is off for each doodle
		this.doodleArray[i].isBeingDragged = false;
	}
    
    
    if (this.newPointOnClick && !found)
    {
        var mousePosDoodlePlane = this.inverseTransform.transformPoint(_point);
        
        var newPointInLine = this.addDoodle('PointInLine');
        newPointInLine.originX = mousePosDoodlePlane.x;
        newPointInLine.originY = mousePosDoodlePlane.y;
    }
	
	// Repaint
	this.repaint();
}

/**
 * Responds to mouse move event in canvas according to the drawing mode
 *
 * @event
 * @param {Point} _point coordinates of mouse in canvas plane
 */
ED.Drawing.prototype.mousemove = function(_point)
{
	// Only drag if mouse already down and a doodle selected
	if (this.mouseDown && this.selectedDoodle != null)
	{
		// Dragging not started
		if (!this.selectedDoodle.isBeingDragged)
		{
			// Flag start of dragging manoeuvre
			this.selectedDoodle.isBeingDragged = true;
		}
		// Dragging in progress
		else
		{
			// Get mouse position in doodle plane
			var mousePosDoodlePlane = this.inverseTransform.transformPoint(_point);
			var lastMousePosDoodlePlane = this.inverseTransform.transformPoint(this.lastMousePosition);
			
			// Get mouse positions in selected doodle's plane
			var mousePosSelectedDoodlePlane = this.selectedDoodle.inverseTransform.transformPoint(_point);
			var lastMousePosSelectedDoodlePlane = this.selectedDoodle.inverseTransform.transformPoint(this.lastMousePosition);
			
			// Get mouse positions in canvas plane relative to centre
			var mousePosRelCanvasCentre = new ED.Point(_point.x - this.canvas.width/2, _point.y - this.canvas.height/2);
			var lastMousePosRelCanvasCentre = new ED.Point(this.lastMousePosition.x - this.canvas.width/2, this.lastMousePosition.y - this.canvas.height/2);
				
			// Get position of centre of display (canvas plane relative to centre) and of an arbitrary point vertically above
			var canvasCentre = new ED.Point(0, 0);
			var canvasTop = new ED.Point(0, -100);
			
			// Get coordinates of origin of doodle in doodle plane
			var doodleOrigin = new ED.Point(this.selectedDoodle.originX, this.selectedDoodle.originY);

			// Get position of point vertically above doodle origin in doodle plane
			var doodleTop = new ED.Point(this.selectedDoodle.originX, this.selectedDoodle.originY - 100);
			
			// Effect of dragging depends on mode
			switch (this.mode)
			{
				case ED.Mode.None:
					break;
				case ED.Mode.Move:
					// If isMoveable is true, move doodle
					if (this.selectedDoodle.isMoveable)
					{
                        this.selectedDoodle.move(mousePosDoodlePlane.x - lastMousePosDoodlePlane.x, mousePosDoodlePlane.y - lastMousePosDoodlePlane.y);
					}
					// Otherwise rotate it (if isRotatable)
					else 
					{
						if (this.selectedDoodle.isRotatable)
						{
							// Calculate angles from centre to mouse positions relative to north
							var oldAngle = this.innerAngle(canvasTop, canvasCentre, lastMousePosRelCanvasCentre);
							var newAngle = this.innerAngle(canvasTop, canvasCentre, mousePosRelCanvasCentre);
							
							// Work out difference, and change doodle's angle of rotation by this amount
							var deltaAngle = newAngle - oldAngle;
							this.selectedDoodle.rotation += deltaAngle;
                            
                            // Adjust radius property
                            var oldRadius = Math.sqrt(lastMousePosDoodlePlane.x * lastMousePosDoodlePlane.x + lastMousePosDoodlePlane.y * lastMousePosDoodlePlane.y);
                            var newRadius = Math.sqrt(mousePosDoodlePlane.x * mousePosDoodlePlane.x + mousePosDoodlePlane.y * mousePosDoodlePlane.y);
                            this.selectedDoodle.radius += (newRadius - oldRadius);
                            
                            // Keep within bounds
                            this.selectedDoodle.radius = this.selectedDoodle.rangeOfRadius.constrain(this.selectedDoodle.radius);
 						}
					}
					break;
				case ED.Mode.Scale:
					if (this.selectedDoodle.isScaleable)
					{
						// Get sign of scale (negative scales create horizontal and vertical flips)
						var signX = this.selectedDoodle.scaleX/Math.abs(this.selectedDoodle.scaleX);
						var signY = this.selectedDoodle.scaleY/Math.abs(this.selectedDoodle.scaleY);

						// Calculate change in scale (sign change indicates mouse has moved across central axis)
						var changeX = mousePosSelectedDoodlePlane.x/lastMousePosSelectedDoodlePlane.x;
						var changeY = mousePosSelectedDoodlePlane.y/lastMousePosSelectedDoodlePlane.y;
						
						// Ensure scale change is same if not squeezable
						if (!this.selectedDoodle.isSqueezable)
						{
							if (changeX > changeY) changeY = changeX;
							else changeY = changeX;
						}
						
						// Check that mouse has not moved from one quadrant to another 
						if (changeX > 0 && changeY > 0)
						{
							// Now do scaling
							this.selectedDoodle.scaleX = this.selectedDoodle.scaleX * changeX;
							this.selectedDoodle.scaleY = this.selectedDoodle.scaleY * changeY;
							
							// Constrain scale
							this.selectedDoodle.scaleX = this.selectedDoodle.rangeOfScale.constrain(Math.abs(this.selectedDoodle.scaleX)) * signX;
							this.selectedDoodle.scaleY = this.selectedDoodle.rangeOfScale.constrain(Math.abs(this.selectedDoodle.scaleY)) * signY;
						}
						else
						{
							this.mode = ED.Mode.None;
						}
					}
					break;
				case ED.Mode.Arc:

                    // Calculate angles from centre to mouse positions relative to north
                    var newAngle = this.innerAngle(doodleTop, doodleOrigin, mousePosSelectedDoodlePlane);
                    var oldAngle = this.innerAngle(doodleTop, doodleOrigin, lastMousePosSelectedDoodlePlane);
                    
                    // Work out difference, and sign of rotation correction
                    var deltaAngle = newAngle - oldAngle;
                    if (this.selectedDoodle.isArcSymmetrical) deltaAngle = 2 * deltaAngle;
                    rotationCorrection = 1;

                    // Arc left or right depending on which handle is dragging
                    if (this.selectedDoodle.draggingHandleIndex < 2)
                    {
                        deltaAngle =  -deltaAngle;
                        rotationCorrection = -1;
                    }
                    
                    // Clamp to permitted range and stop dragging if exceeded
                    if (this.selectedDoodle.rangeOfArc.isBelow(this.selectedDoodle.arc + deltaAngle))
                    {
                        deltaAngle = this.selectedDoodle.rangeOfArc.min - this.selectedDoodle.arc;
                        this.selectedDoodle.arc = this.selectedDoodle.rangeOfArc.min;
                        this.mode = ED.Mode.None;
                    }
                    else if (this.selectedDoodle.rangeOfArc.isAbove(this.selectedDoodle.arc + deltaAngle))
                    {
                        deltaAngle = this.selectedDoodle.rangeOfArc.max - this.selectedDoodle.arc;
                        this.selectedDoodle.arc = this.selectedDoodle.rangeOfArc.max;
                        this.mode = ED.Mode.None;
                    }
                    else
                    {
                        this.selectedDoodle.arc += deltaAngle;
                    }
                    
                    // Correct rotation with counter-rotation
                    if (!this.selectedDoodle.isArcSymmetrical)
                    {
                        rotationCorrection = rotationCorrection * deltaAngle/2;
                        this.selectedDoodle.rotation += rotationCorrection;
                    }

					break;
				case ED.Mode.Rotate:
					if (this.selectedDoodle.isRotatable)
					{
						// Calculate angles from centre to mouse positions relative to north
						var oldAngle = this.innerAngle(doodleTop, doodleOrigin, lastMousePosDoodlePlane);
						var newAngle = this.innerAngle(doodleTop, doodleOrigin, mousePosDoodlePlane);
						
						// Work out difference, and change doodle's angle of rotation by this amount
						var deltaAngle = newAngle - oldAngle;
						this.selectedDoodle.rotation = this.selectedDoodle.rotation + deltaAngle;
					}
					break;
				case ED.Mode.Apex:
					// Move apex to new position
					this.selectedDoodle.apexX += (mousePosSelectedDoodlePlane.x - lastMousePosSelectedDoodlePlane.x);
					this.selectedDoodle.apexY += (mousePosSelectedDoodlePlane.y - lastMousePosSelectedDoodlePlane.y);
					
					// Enforce bounds
					this.selectedDoodle.apexX = this.selectedDoodle.rangeOfApexX.constrain(this.selectedDoodle.apexX);
					this.selectedDoodle.apexY = this.selectedDoodle.rangeOfApexY.constrain(this.selectedDoodle.apexY);
					break;
                case ED.Mode.Draw:
                    var p = new ED.Point(mousePosSelectedDoodlePlane.x,mousePosSelectedDoodlePlane.y);
                    this.selectedDoodle.addPointToSquiggle(p);
                    break;
				default:
					break;		
			}
			            
			// Refresh drawing
			this.repaint();				
		}
		
		// Store mouse position
		this.lastMousePosition = _point;
	}
}

/**
 * Responds to mouse up event in canvas
 *
 * @event
 * @param {Point} _point coordinates of mouse in canvas plane
 */  
ED.Drawing.prototype.mouseup = function(_point)
{
	// Reset flags
	this.mouseDown = false;
    this.doubleClick = false;
	
	// Reset selected doodle's dragging flag
	if (this.selectedDoodle != null)
	{
		this.selectedDoodle.isBeingDragged = false;
        
        // Optionally complete squiggle
        if (this.selectedDoodle.isDrawable)
        {
            this.selectedDoodle.completeSquiggle();
            this.drawAllDoodles();
        }
	}
}

/**
 * Responds to mouse out event in canvas, stopping dragging operation
 *
 * @event
 * @param {Point} _point coordinates of mouse in canvas plane
 */  
ED.Drawing.prototype.mouseout = function(_point)
{
	// Reset flag
	this.mouseDown = false;
	
	// Reset selected doodle's dragging flag
	if (this.selectedDoodle != null)
	{
		this.selectedDoodle.isBeingDragged = false;
        
        // Optionally complete squiggle
        if (this.selectedDoodle.isDrawable)
        {
            this.selectedDoodle.completeSquiggle();
            this.drawAllDoodles();
        }
	}
}

/**
 * Responds to key down event in canvas
 *
 * @event
 * @param {event} e Keyboard event
 */  
ED.Drawing.prototype.keydown = function(e)
{
	// Keyboard action works on selected doodle
	if (this.selectedDoodle != null)
	{
        // Delete or move doodle
        switch (e.keyCode) {
            case 8:     // Backspace
                this.deleteDoodle();
                break;
            case 37:    // Left arrow
                this.selectedDoodle.move(-ED.arrowDelta,0);
                break;
            case 38:    // Up arrow
                this.selectedDoodle.move(0,-ED.arrowDelta);
                break;
            case 39:    // Right arrow
                this.selectedDoodle.move(ED.arrowDelta,0);
                break;
            case 40:    // Down arrow
                this.selectedDoodle.move(0,ED.arrowDelta);
                break;
            default:
                break;
        }
        
        // If alphanumeric, send to Lable doodle
        var code = 0;
        
        // Shift key has code 16
        if (e.keyCode != 16)
        {
            // Alphabetic
            if (e.keyCode >= 65 && e.keyCode <= 90)
            {
                if (e.shiftKey)
                {
                    code = e.keyCode;
                }
                else
                {
                    code = e.keyCode + 32;
                }
            }
            // Space or numeric
            else if (e.keyCode == 32 || (e.keyCode > 47 && e.keyCode < 58))
            {
                code = e.keyCode;
            }
        }

        // Currently only doodles of Lable class accept alphanumeric input
        if (code > 0 && this.selectedDoodle.className == "Lable")
        {
            this.selectedDoodle.addLetter(code);
        }
        
        // Redraw doodle
        this.repaint();
        
        // Prevent key stroke bubbling up (***TODO*** may need cross browser handling)
        e.stopPropagation();
        e.preventDefault();
	}
}

/**
 * Moves selected doodle to front
 */
ED.Drawing.prototype.moveToFront = function()
{
	// Should only be called if a doodle is selected, but check anyway
	if (this.selectedDoodle != null)
	{
		// Assign large number to selected doodle
		this.selectedDoodle.order = 1000;
		
		// Sort array by order (puts back doodle first)
		this.doodleArray.sort(function(a,b){return a.order - b.order});
		
		// Re-assign ordinal numbers to array
		for (var i = 0; i < this.doodleArray.length; i++)
		{
			this.doodleArray[i].order = i;
		}
		
		// Refresh canvas
		this.repaint();
	}
}

/**
 * Moves selected doodle to back
 */
ED.Drawing.prototype.moveToBack = function()
{
	// Should only be called if a doodle is selected, but check anyway
	if (this.selectedDoodle != null)
	{
		// Assign negative order to selected doodle
		this.selectedDoodle.order = -1;
		
		// Sort array by order (puts back doodle first)
		this.doodleArray.sort(function(a,b){return a.order - b.order});
		
		// Re-assign ordinal numbers to array
		for (var i = 0; i < this.doodleArray.length; i++)
		{
			this.doodleArray[i].order = i;
		}
		
		// Refresh canvas
		this.repaint();
	}
}

/**
 * Deletes selected doodle
 */
ED.Drawing.prototype.deleteDoodle = function()
{
	// Should only be called if a doodle is selected, but check anyway
	if (this.selectedDoodle != null)
	{
		// Go through doodles removing any that are selected (should be just one)
		for (var i = 0; i < this.doodleArray.length; i++)
		{
			if (this.doodleArray[i].isSelected && this.doodleArray[i].isDeletable)
			{
                // Deselect doodle
                this.selectedDoodle = null;
                
                // Reset array
				this.doodleArray.splice(i,1);
			}
		}
		
		// Re-assign ordinal numbers to array
		for (var i = 0; i < this.doodleArray.length; i++)
		{
			this.doodleArray[i].order = i;
		}
        
		// Refresh canvas
		this.repaint();
	}
}

/**
 * Locks selected doodle
 */
ED.Drawing.prototype.lock = function()
{
	// Should only be called if a doodle is selected, but check anyway
	if (this.selectedDoodle != null)
	{
		// Go through doodles locking any that are selected
		for (var i = 0; i < this.doodleArray.length; i++)
		{
			if (this.doodleArray[i].isSelected)
			{
				this.doodleArray[i].isSelectable = false;
				this.doodleArray[i].isSelected = false;
				this.selectedDoodle = null;
			}
		}
		
		// Refresh canvas
		this.repaint();
	}
}

/**
 * Unlocks all doodles
 */
ED.Drawing.prototype.unlock = function()
{
	// Go through doodles unlocking all
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		this.doodleArray[i].isSelectable = true;
	}
	
	// Refresh canvas
	this.repaint();
}

/**
 * Adds a doodle to the drawing
 *
 * @param {String} _parameter Name of parameter
 * @param {String} _className Classname of doodle
 * @param {String} _value New value of parameter
 */
ED.Drawing.prototype.setParameterValueForClass= function(_parameter, _value, _className)
{
    // Go through doodle array (backwards because of splice function) looking for doodles of passed className
	for (var i = this.doodleArray.length - 1; i >= 0; i--)
	{
        // Find doodles of given class name
        if (this.doodleArray[i].className == _className)
        {
            var doodle = this.doodleArray[i];
            
            // Objects are also associative arrays!
            doodle[_parameter] =  _value;
        }
	}
    
    // Refresh drawing
    this.repaint();
}

/**
 * Deselect any selected doodles
 *
 */
ED.Drawing.prototype.deselectDoodles = function()
{
    // Deselect all doodles
    for (var i = 0; i < this.doodleArray.length; i++)
    {
        this.doodleArray[i].isSelected = false;
    }
    
    this.selectedDoodle = null;
    
    // Refresh drawing
    this.repaint();
}

/**
 * Adds a doodle to the drawing
 *
 * @param {String} _className Classname of doodle
 * @returns {Doodle} The newly added doodle
 */
ED.Drawing.prototype.addDoodle = function(_className)
{
    // Create a new doodle of the specified class
	var newDoodle = new ED[_className](this);
    
    // Check if one is already there if unique)
    if (!(newDoodle.isUnique && this.hasDoodleOfClass(_className)))
    {
        // Ensure no other doodles are selected
        for (var i = 0; i < this.doodleArray.length; i++)
        {
            this.doodleArray[i].isSelected = false;
        }

        // Set default parameters
        newDoodle.setParameterDefaults();
        
        // New doodles are selected by default
        this.selectedDoodle = newDoodle;
        
        // Apply global scale factor
        newDoodle.scaleX = newDoodle.scaleX * this.globalScaleFactor;
        newDoodle.scaleY = newDoodle.scaleY * this.globalScaleFactor;
        
        // If drawable, also go into drawing mode
        if (newDoodle.isDrawable)
        {
            newDoodle.isForDrawing = true;
        }
        
        // Add to array
        this.doodleArray[this.doodleArray.length] = newDoodle;
        
        if (newDoodle.addAtBack)
        {
            this.moveToBack();
        }
        else
        {
            this.repaint();
        }

        // Return doodle
        return newDoodle;
    }
    else
    {
        return null;
    }
}

/**
 * Test if doodle of a class exists in drawing
 *
 * @param {String} _className Classname of doodle
 * @returns {Bool} True is a doodle of the class exists, otherwise false 
 */
ED.Drawing.prototype.hasDoodleOfClass = function(_className)
{
    var returnValue = false;
    
	// Go through doodle array looking for doodles of passed className
	for (var i = 0; i < this.doodleArray.length; i++)
	{
        if (this.doodleArray[i].className == _className)
        {
            returnValue = true;
        }
	}

    return returnValue;
}

/**
 * Returns first doodle of the passed className, or false if does not exist
 *
 * @param {String} _className Classname of doodle
 * @returns {Doodle} The first doodle of the passed className
 */
ED.Drawing.prototype.firstDoodleOfClass = function(_className)
{
    var returnValue = false;
    
	// Go through doodle array looking for doodles of passed className
	for (var i = 0; i < this.doodleArray.length; i++)
	{
        if (this.doodleArray[i].className == _className)
        {
            returnValue = this.doodleArray[i];
            break;
        }
	}
    
    return returnValue;
}


/**
 * Returns last doodle of the passed className, or false if does not exist
 *
 * @param {String} _className Classname of doodle
 * @returns {Doodle} The last doodle of the passed className
 */
ED.Drawing.prototype.lastDoodleOfClass = function(_className)
{
    var returnValue = false;
    
	// Go through doodle array backwards looking for doodles of passed className
	for (var i = this.doodleArray.length - 1; i >= 0; i--)
	{
        if (this.doodleArray[i].className == _className)
        {
            returnValue = this.doodleArray[i];
            break;
        }
	}
    
    return returnValue;
}

/**
 * Deletes all doodles that are deletable
 */
ED.Drawing.prototype.deleteAllDoodles = function()
{
	// Go through doodle array (backwards because of splice function)
	for (var i = this.doodleArray.length - 1; i >= 0; i--)
	{
        // Find doodles of given class name
        if (this.doodleArray[i].isDeletable)
        {
            // If it happens to be selected, deselect it
            if (this.doodleArray[i].isSelected)
            {
                // Deselect doodle
                this.selectedDoodle = null;
            }
            
            // Remove item for removal
            this.doodleArray.splice(i,1);
        }
	}
    
    // Re-assign ordinal numbers to array
    for (var i = 0; i < this.doodleArray.length; i++)
    {
        this.doodleArray[i].order = i;
    }
    
	// Refresh canvas
	this.repaint();
}

/**
 * Deletes doodles of one class from the drawing
 *
 * @param {String} _className Classname of doodle
 */
ED.Drawing.prototype.deleteDoodlesOfClass = function(_className)
{
	// Go through doodle array (backwards because of splice function) looking for doodles of passed className
	for (var i = this.doodleArray.length - 1; i >= 0; i--)
	{
        // Find doodles of given class name
        if (this.doodleArray[i].className == _className)
        {
            // If it happens to be selected, deselect it
            if (this.doodleArray[i].isSelected && this.doodleArray[i].isDeletable)
            {
                // Deselect doodle
                this.selectedDoodle = null;
            }
            
            // Remove item for removal
            this.doodleArray.splice(i,1);
        }
	}
    
    // Re-assign ordinal numbers to array
    for (var i = 0; i < this.doodleArray.length; i++)
    {
        this.doodleArray[i].order = i;
    }
    
	// Refresh canvas
	this.repaint();
}


/**
 * Updates a doodle with a vew value of a parameter
 *
 * @param {Doodle} _doodle The doodle to be updated
 * @param {String} _parameter Name of the parameter
 * @param {Any} _value New value of the parameter
 */
ED.Drawing.prototype.setParameterForDoodle = function(_doodle, _parameter, _value)
{
    // Get pointer to doodle
    //var doodle = this.firstDoodleOfClass(_class);
    
    // Determine whether doodle exists
    if (typeof(_doodle[_parameter]) != 'undefined')
    {
        _doodle[_parameter] = +_value;
    }
    else
    {
        _doodle.setParameter(_parameter, _value);
    }
    
    // Refresh drawing
    this.repaint();               
}

/**
 * Updates a doodle of class with a vew value of a parameter. Use if one one member of class exists
 *
 * @param {String} _className The name of the doodle class to be updated
 * @param {String} _parameter Name of the parameter
 * @param {Any} _value New value of the parameter
 */
ED.Drawing.prototype.setParameterForDoodleOfClass = function(_className, _parameter, _value)
{
    // Get pointer to doodle
    var doodle = this.firstDoodleOfClass(_className);
    
    // Determine whether doodle exists
    if (typeof(doodle[_parameter]) != 'undefined')
    {
        doodle[_parameter] = +_value;
    }
    else
    {
        doodle.setParameter(_parameter, _value);
    }
    
    // Refresh drawing
    this.repaint();               
}

/**
 * Returns the total extent in degrees covered by doodles of the passed class
 *
 * @param {String} _class Class of the doodle to be updated
 * @returns {Int} Total extent in degrees, with maximum of 360
 */
ED.Drawing.prototype.totalDegreesExtent = function(_class)
{
    var degrees = 0;

    // Calculate total for all doodles of this class
    for (var i = 0; i < this.doodleArray.length; i++)
    {
        // Find doodles of given class name
        if (this.doodleArray[i].className == _class)
        {
            degrees += this.doodleArray[i].degreesExtent();
        }
    }

    // Overlapping doodles do not increase total beyond 360 degrees
    if (degrees > 360) degrees = 360;

    return degrees;
}

/**
 * Returns a string containing a description of the drawing
 *
 * @returns {String} Description of the drawing
 */
ED.Drawing.prototype.report = function()
{
	var returnString = "";
    var groupArray = new Array();
	
	// Go through every doodle
	for (var i = 0; i < this.doodleArray.length; i++)
	{
        var doodle = this.doodleArray[i];
        
        // Check for a group description
        if (doodle.groupDescription().length > 0)
        {
            // Create an array entry for it or add to existing
            if (typeof(groupArray[doodle.className]) == 'undefined')
            {
                groupArray[doodle.className] = doodle.groupDescription();
                groupArray[doodle.className] += doodle.description();
            }
            else
            {
                // Only add additional detail if supplied by descripton method
                if (doodle.description().length > 0)
                {
                    groupArray[doodle.className] += ", ";
                    groupArray[doodle.className] += doodle.description();
                }
            }
        }
        else
        {
            if (doodle.willReport)
            {
                // Get description
                var description = doodle.description();
                
                // If its not an empty string, add to the return
                if (description.length > 0)
                {
                    // If text there already, make it lower case and add a comma before
                    if (returnString.length == 0)
                    {
                        returnString += description;
                    }
                    else
                    {
                        returnString = returnString + ", " + description.firstLetterToLowerCase();
                    }
                }
            }
        }
	}
    
    // Go through group array adding descriptions
    for (className in groupArray)
    {
        // Get description
        var description = groupArray[className];
        
        // Replace last comma with a comma and 'and'
        description = description.addAndAfterLastComma();
        
        // If its not an empty string, add to the return
        if (description.length > 0)
        {
            // If text there already, make it lower case and add a comma before
            if (returnString.length == 0)
            {
                returnString += description;
            }
            else
            {
                returnString = returnString + ", " + description.firstLetterToLowerCase();
            }
        }        
    }
	
    // Return result
	return returnString;
}


/**
 * Returns a SNOMED diagnostic code derived from the drawing, returns zero if no code
 *
 * @returns {Int} SnoMed code of doodle with highest postion in hierarchy
 */
ED.Drawing.prototype.diagnosis = function()
{
    var positionInHierarchy = 0;
    var returnCode = 0;
    
    // Loop through doodles with diagnoses, taking one highest in hierarchy
	for (var i = 0; i < this.doodleArray.length; i++)
    {
        var doodle = this.doodleArray[i];
        var code = doodle.snomedCode();
        if (code > 0)
        {
            var codePosition = doodle.diagnosticHierarchy();
            if (codePosition > positionInHierarchy)
            {
                positionInHierarchy = codePosition;
                returnCode = code;
            }
        }
    }
    
    return returnCode;
}

/**
 * Changes value of eye
 *
 * @param {String} _eye Eye to change to
 */
ED.Drawing.prototype.setEye = function(_eye)
{
    // Change eye
    if (_eye == "Right") this.eye = ED.eye.Right;
    if (_eye == "Left") this.eye = ED.eye.Left;
    
    // Refresh drawing
    this.repaint();
}

/**
 * Clears canvas and sets context
 */
ED.Drawing.prototype.clear = function()
{
	// Resetting a dimension attribute clears the canvas and resets the context
	this.canvas.width = this.canvas.width;
	
	// But, might not clear canvas, so do it explicitly
	this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
	
	// Set context transform to map from doodle plane to canvas plane	
	this.context.translate(this.canvas.width/2, this.canvas.height/2);
	this.context.scale(this.scale, this.scale);	
}

/**
 * Clears canvas and draws all doodles
 */
ED.Drawing.prototype.repaint = function()
{
	// Clear canvas
	this.clear();
    
    // Draw background image (In doodle space because of transform)
    if (typeof(this.image) != 'undefined')
    {
        if (this.image.width >= this.image.height)
        {
            var height = 1000 * this.image.height/this.image.width;
            this.context.drawImage(this.image, -500, -height/2, 1000, height);
        }
        else
        {
            var width = 1000 * this.image.width/this.image.height;
            this.context.drawImage(this.image, -width/2, -500, width, 1000);
        }
    }
	
	// Redraw all doodles
	this.drawAllDoodles();
	
	// Enable or disable buttons which work on selected doodle
	if (this.selectedDoodle != null)
	{
		if (this.moveToFrontButton !== null) this.moveToFrontButton.disabled = false;
		if (this.moveToBackButton !== null) this.moveToBackButton.disabled = false;
		if (this.deleteButton !== null) this.deleteButton.disabled = false;
		if (this.lockButton !== null) this.lockButton.disabled = false;
        if (this.squiggleSpan !== null && this.selectedDoodle.isDrawable) this.squiggleSpan.style.display = "inline-block";
	}
	else
	{
		if (this.moveToFrontButton !== null) this.moveToFrontButton.disabled = true;
		if (this.moveToBackButton !== null) this.moveToBackButton.disabled = true;
		if (this.deleteButton !== null) this.deleteButton.disabled = true;
		if (this.lockButton !== null) this.lockButton.disabled = true;
        if (this.squiggleSpan !== null) this.squiggleSpan.style.display = "none";
	}
	
	// Go through doodles looking for any that are locked and enable/disable unlock button
    if (this.unlockButton != null)
    {
        this.unlockButton.disabled = true;
        for (var i = 0; i < this.doodleArray.length; i++)
        {
            if (!this.doodleArray[i].isSelectable)
            {
                this.unlockButton.disabled = false;
            }
        }
    }
    
    // Call to optional method to notify changes in doodle parametes
    if (typeof(this.parameterListener) != 'undefined') this.parameterListener();
}

/**
 * Calculates angle between three points (clockwise from _pointA to _pointB in radians)
 *
 * @param {Point} _pointA First point
 * @param {Point} _pointM Mid point
 * @param {Point} _pointB Last point
 * @returns {Float} Angle between three points in radians (clockwise)
 */
ED.Drawing.prototype.innerAngle = function(_pointA, _pointM, _pointB)
{
	// Get vectors from midpoint to A and B
	var a = new ED.Point(_pointA.x - _pointM.x, _pointA.y - _pointM.y);
	var b = new ED.Point(_pointB.x - _pointM.x, _pointB.y - _pointM.y);
	
	return a.clockwiseAngleTo(b);
}

/**
 * Toggles drawing state for drawing points in line
 */
ED.Drawing.prototype.togglePointInLine = function()
{
    if (this.newPointOnClick)
    {
        this.newPointOnClick = false;
        this.completeLine = true;
        this.deselectDoodles();
        this.repaint();
    }
    else
    {
        this.newPointOnClick = true;
        this.completeLine = false;
    }
}



/**
 * An object of the Report class is used to extract data for the Royal College of Ophthalmologists retinal detachment dataset.
 * The object analyses an EyeDraw drawing, and sets the value of HTML elements on the page accordingly.
 *
 * @class Report
 * @property {Canvas} canvas A canvas element used to edit and display the drawing
 * @property {Int} breaksInAttached The number of retinal breaks in attached retina
 * @property {Int} breaksInDetached The number of retinal breaks in detached retina
 * @property {String} largestBreakType The type of the largest retinal break
 * @property {Int} largestBreakSize The size in clock hours of the largest retinal break
 * @property {Int} lowestBreakPosition The lowest position of any break in clock hours
 * @property {String} pvrType The type of PVR
 * @property {Int} pvrCClockHours The number of clock hours of posterior PVR type C
 * @property {Int} antPvrClockHours The number of clock hours of anterior PVR
 * @param Drawing _drawing The drawing object to be analysed
 */
ED.Report = function(_drawing)
{
   	// Properties
	this.drawing = _drawing;
    this.breaksInAttached = 0;
    this.breaksInDetached = 0;
    this.largestBreakType = 'Not found';
    this.largestBreakSize = 0;
    this.lowestBreakPosition = 12;
    this.pvrType = 'None';
    this.pvrCClockHours = 0;
    this.antPvrClockHours = 0;
    
    // Variables
    var pvrCDegrees = 0;
    var AntPvrDegrees = 0;
    var minDegreesFromSix = 180;
    
    // Create array of doodle classes which are retinal breaks
    var breakClassArray = new Array();
    breakClassArray["UTear"] = "U tear";
    breakClassArray["RoundHole"] = "Round hole";
    breakClassArray["Dialysis"] = "Dialysis";
    breakClassArray["GRT"] = "GRT";
    breakClassArray["MacularHole"] = "Macular hole";
    
    // Array of RRD doodles
    this.rrdArray = new Array();
    
    // First iteration to create array of retinal detachments
    var i, doodle;
	for (i = 0; i < this.drawing.doodleArray.length; i++)
	{
        doodle = this.drawing.doodleArray[i];
        
        // If its a RRD, add to RRD array
        if(doodle.className == "RRD")
        {
            this.rrdArray.push(doodle);
        }
    }

    // Second iteration for other doodles
	for (i = 0; i < this.drawing.doodleArray.length; i++)
	{
        doodle = this.drawing.doodleArray[i];
        
        // Star fold - PVR C
        if (doodle.className == "StarFold")
        {
            this.pvrType = 'C';
            pvrCDegrees += doodle.arc * 180/Math.PI;
        }
        // Anterior PVR
        else if (doodle.className == "AntPVR")
        {
            this.pvrType = 'C';
            AntPvrDegrees += doodle.arc * 180/Math.PI;
        }
        // Retinal breaks
        else if (doodle.className in breakClassArray)
        {
            // Bearing of break is calculated in two different ways
            var breakBearing = 0;
            if( doodle.className == "UTear" || doodle.className == "RoundHole")
            {
                breakBearing = (Math.round(Math.atan2(doodle.originX, -doodle.originY) * 180/Math.PI) + 360) % 360;
            }
            else
            {
                breakBearing = (Math.round(doodle.rotation * 180/Math.PI + 360)) % 360;
            }
            
            // Bool if break is in detached retina
            var inDetached = this.inDetachment(breakBearing);
            
            // Increment totals
            if(inDetached)
            {
                this.breaksInDetached++;
            }
            else
            {
                this.breaksInAttached++;
            }
            
            // Get largest break in radians
            if (inDetached && doodle.arc > this.largestBreakSize)
            {
                this.largestBreakSize = doodle.arc;
                this.largestBreakType = breakClassArray[doodle.className];
            }
            
            // Get lowest break
            var degreesFromSix = Math.abs(breakBearing - 180);
            
            if (inDetached && degreesFromSix < minDegreesFromSix)
            {
                minDegreesFromSix = degreesFromSix;
                
                // convert to clock hours
                var bearing = breakBearing + 15;
                remainder = bearing % 30;
                this.lowestBreakPosition = Math.floor((bearing - remainder) / 30);
                if (this.lowestBreakPosition == 0) this.lowestBreakPosition = 12;
            }
        }
    }
    
    // Star folds integer result (round up to one clock hour)
    pvrCDegrees += 25;
    var remainder = pvrCDegrees % 30;
    this.pvrCClockHours = Math.floor((pvrCDegrees - remainder) / 30);
    
    // Anterior PVR clock hours
    AntPvrDegrees += 25;
    remainder = AntPvrDegrees % 30;
    this.antPvrClockHours = Math.floor((AntPvrDegrees - remainder) / 30);
    
    // Convert largest break size to clockhours
    var size = this.largestBreakSize * 180/Math.PI + 25;
    var remainder = size % 30;
    this.largestBreakSize = Math.floor((size - remainder) / 30);
}

/**
 * Accepts a bearing in degrees (0 is at 12 o'clock) and returns true if it is in an area of detachment
 *
 * @param {Float} _angle Bearing in degrees
 * @returns {Bool} True is the bearing intersects with an area of retinal deatchment
 */
ED.Report.prototype.inDetachment = function(_angle)
{
    var returnValue = false;
    
    // Iterate through retinal detachments
    for (key in this.rrdArray)
    {
        var rrd = this.rrdArray[key];
        
        // Get start and finish bearings of detachment in degrees
        var min = (rrd.rotation - rrd.arc/2) * 180/Math.PI;
        var max = (rrd.rotation + rrd.arc/2) * 180/Math.PI;
        
        // Convert to positive numbers
        var min = (min + 360)%360;
        var max = (max + 360)%360;
        
        // Handle according to whether RRD straddles 12 o'clock
        if (max < min)
        {
            if ((0 <= _angle && _angle <= max) || (min <= _angle && _angle <= 360))
            {
                returnValue = true;
            }
        }
        else if (max == min) // Case if detachment is total
        {
            return true;
        }
        else
        {
            if (min <= _angle && _angle <= max)
            {
                returnValue = true;
            }
        }
    }
    
    return returnValue;
}

/**
 * Extent of RRD in clock hours
 *
 * @returns {Array} An array of extents (1 to 3 clock hours) for each quadrant
 */
ED.Report.prototype.extent = function()
{
    // Array of extents by quadrant
    var extentArray = new Array();
    if (this.drawing.eye == ED.eye.Right)
    {
        extentArray["SN"] = 0;
        extentArray["IN"] = 0;
        extentArray["IT"] = 0;
        extentArray["ST"] = 0;
    }
    else
    {
        extentArray["ST"] = 0;
        extentArray["IT"] = 0;
        extentArray["IN"] = 0;
        extentArray["SN"] = 0;
    }
    
    // get middle of first hour in degrees
    var midHour = 15;
    
    // Go through each quadrant counting extent of detachment
    for (quadrant in extentArray)
    {
        for (var i = 0; i < 3; i++)
        {
            var addition = this.inDetachment(midHour)?1:0;
            extentArray[quadrant] = extentArray[quadrant] + addition;
            midHour = midHour + 30;
        }
    }
    
    return extentArray;
}

/**
 * Returns true if the macular is off
 *
 * @returns {Bool} True if the macula is off
 */
ED.Report.prototype.isMacOff = function()
{
    var result = false;
    
    // Iterate through each detachment, one macoff is enough
    for (key in this.rrdArray)
    {
        var rrd = this.rrdArray[key];
        if (rrd.isMacOff()) result = true;
    }
    
    return result;
}
    
/**
 * Doodles are components of drawings which have built in knowledge of what they represent, and how to behave when manipulated;
 * Doodles are drawn in the 'doodle plane' consisting of 1001 pixel square grid with central origin (ie -500 to 500) and
 * are rendered in a canvas element using a combination of the affine transform of the host drawing, and the doodle's own transform. 
 *
 * @class Doodle
 * @property {Drawing} drawing Drawing to which this doodle belongs
 * @property {Int} originX X coordinate of origin in doodle plane
 * @property {Int} originY Y coordinate of origin in doodle plane
 * @property {Int} apexX X coordinate of apex in doodle plane
 * @property {Int} apexY Y coordinate of apex in doodle plane
 * @property {Float} scaleX Scale of doodle along X axis
 * @property {Float} scaleY Scale of doodle along Y axis
 * @property {Float} arc Angle of arc for doodles that extend in a circular fashion
 * @property {Float} rotation Angle of rotation from 12 o'clock
 * @property {Int} order Order in which doodle is drawn (0 first ie backmost layer)
 * @property {Array} squiggleArray Array containing squiggles (freehand drawings)
 * @property {AffineTransform} transform Affine transform which handles the doodle's position, scale and rotation
 * @property {AffineTransform} inverseTransform The inverse of transform
 * @property {Bool} isSelectable True if doodle is locked (ie non-selectable)
 * @property {Bool} isDeletable True if doodle can be deleted 
 * @property {Bool} isOrientated True if doodle should always point to the centre (default = false)
 * @property {Bool} isScaleable True if doodle can be scaled. If false, doodle increases its arc angle
 * @property {Bool} isSqueezable True if scaleX and scaleY can be independently modifed (ie no fixed aspect ratio)
 * @property {Bool} isMoveable True if doodle can be moved. When combined with isOrientated allows automatic rotation.
 * @property {Bool} isRotatable True if doodle can be rotated
 * @property {Bool} isDrawable True if doodle accepts freehand drawings
 * @property {Bool} isUnique True if only one doodle of this class allowed in a drawing
 * @property {Bool} isArcSymmetrical True if changing arc does not change rotation
 * @property {Bool} addAtBack True if new doodles are added to the back of the drawing (ie first in array)
 * @property {Bool} isPointInLine True if centre of all doodles with this property should be connected by a line segment
 * @property {Bool} snapToGrid True if doodle should snap to a grid in doodle plane
 * @property {Bool} snapToQuadrant True if doodle should snap to a specific position in quadrant (defined in subclass)
 * @property {Bool} willReport True if doodle responds to a report request (can be used to suppress reports when not needed)
 * @property {Float} radius Distance from centre of doodle space, calculated for doodles with isRotable true
 * @property {Range} rangeOfScale Range of allowable scales
 * @property {Range} rangeOfArc Range of allowable Arcs
 * @property {Range} rangeOfApexX Range of allowable values of apexX
 * @property {Range} rangeOfApexY Range of allowable values of apexY
 * @property {Bool} isSelected True if doodle is currently selected
 * @property {Bool} isBeingDragged Flag indicating doodle is being dragged
 * @property {Int} draggingHandleIndex index of handle being dragged
 * @property {Range} draggingHandleRing Inner or outer ring of dragging handle
 * @property {Bool} isClicked Hit test flag
 * @property {Enum} drawFunctionMode Mode for boundary path
 * @property {Bool} isFilled True if boundary path is filled as well as stroked
 * @property {Array} handleArray Array containing handles to be rendered
 * @property {Point} leftExtremity Point at left most extremity of doodle (used to calculate arc)
 * @property {Point} rightExtremity Point at right most extremity of doodle (used to calculate arc)
 * @property {Int} gridSpacing Separation of grid elements
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Doodle = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Function called as part of prototype assignment has no parameters passed
	if (typeof(_drawing) != 'undefined')
	{
		// Drawing containing this doodle
		this.drawing = _drawing;
		
		// New doodle (constructor called with _drawing parameter only)
		if (typeof(_originX) == 'undefined')
		{
			// Default set of parameters (Note use of unary + operator to type convert to numbers)
			this.originX = +0;
			this.originY = +0;
			this.apexX = +0;
			this.apexY = +0;
			this.scaleX = +1;
			this.scaleY = +1;
			this.arc = Math.PI;
			this.rotation = 0;
			this.order = this.drawing.doodleArray.length;
			
			this.setParameterDefaults();
			
			// Selected
			this.isSelected = true;
		}
		// Doodle with passed parameters
		else
		{
			// Parameters
			this.originX = +_originX;
			this.originY = +_originY;
			this.apexX = +_apexX;
			this.apexY = +_apexY;
			this.scaleX = +_scaleX;
			this.scaleY = +_scaleY;
			this.arc = _arc * Math.PI/180;
			this.rotation = _rotation * Math.PI/180;
			this.order = +_order;

			// Not selected
			this.isSelected = false;
            this.isForDrawing = false;
		}
        
        // Optional rray of squiggles
        this.squiggleArray = new Array();
		
		// Transform used to draw doodle (includes additional transforms specific to the doodle)
		this.transform = new ED.AffineTransform();
		this.inverseTransform = new ED.AffineTransform();
		
		// Dragging defaults - set individual values in subclasses
		this.isSelectable = true;
		this.isDeletable = true;
		this.isOrientated = false;
		this.isScaleable = true;
		this.isSqueezable = false;
		this.isMoveable = true;
		this.isRotatable = true;
        this.isDrawable = false;
        this.isUnique = false;
        this.isArcSymmetrical = false;
        this.addAtBack = false;
        this.isPointInLine = false;
        this.snapToGrid = false;
        this.snapToQuadrant = false;
        this.willReport = true;
        this.radius = 0;
		this.rangeOfScale = new ED.Range(+0.5, +4.0);
		this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
		this.rangeOfApexX = new ED.Range(-500, +500);
		this.rangeOfApexY = new ED.Range(-500, +500);
        this.rangeOfRadius = new ED.Range(100, 450);
        this.gridSpacing = 100;
		
		// Flags and other properties
		this.isBeingDragged = false;
		this.draggingHandleIndex = null;
		this.draggingHandleRing = null;
		this.isClicked = false;
		this.drawFunctionMode = ED.drawFunctionMode.Draw;
        this.isFilled = true;

		// Array of 5 handles
		this.handleArray = new Array();
		this.handleArray[0] = new ED.Handle(new ED.Point(-50, 50), false, ED.Mode.Scale, false);
		this.handleArray[1] = new ED.Handle(new ED.Point(-50, -50), false, ED.Mode.Scale, false);
		this.handleArray[2] = new ED.Handle(new ED.Point(50, -50), false, ED.Mode.Scale, false);
		this.handleArray[3] = new ED.Handle(new ED.Point(50, 50), false, ED.Mode.Scale, false);
		this.handleArray[4] = new ED.Handle(new ED.Point(this.apexX, this.apexY), false, ED.Mode.Apex, false);
		this.setHandles();
        
        // Extremities
        this.leftExtremity = new ED.Point(-100,-100);
        this.rightExtremity = new ED.Point(0,-100);
        
		// Set dragging default settings
		this.setPropertyDefaults();
	}
}

/**
 * Sets default handle attributes (overridden by subclasses)
 */
ED.Doodle.prototype.setHandles = function()
{
}

/**
 * Sets default properties (overridden by subclasses)
 */
ED.Doodle.prototype.setPropertyDefaults = function()
{
}

/**
 * Sets default parameters (overridden by subclasses)
 */
ED.Doodle.prototype.setParameterDefaults = function()
{
}

/**
 * Moves doodle and adjusts rotation
 *
 * @param {Float} _x Distance to move along x axis in doodle plane
 * @param {Float} _y Distance to move along y axis in doodle plane
 */
 ED.Doodle.prototype.move = function(_x, _y)
{
    // Get position of centre of display (canvas plane relative to centre) and of an arbitrary point vertically above
    var canvasCentre = new ED.Point(0, 0);
    var canvasTop = new ED.Point(0, -100);
        
    if (this.isMoveable)
    {        
        // Move doodle to new position
        this.originX += _x;
        this.originY += _y;

        // If doodle isOriented is true, rotate doodle around centre of canvas (eg makes 'U' tears point to centre)
        if (this.isOrientated)
        {
            // New position of doodle
            var newDoodleOrigin = new ED.Point(this.originX, this.originY);
            
            // Calculate angle to current position from centre relative to north
            var angle = this.drawing.innerAngle(canvasTop, canvasCentre, newDoodleOrigin);
            
            // Alter orientation of doodle
            this.rotation = angle;
        }
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Doodle.prototype.draw = function(_point)
{
	// Determine function mode
	if (typeof(_point) != 'undefined')
	{
		this.drawFunctionMode = ED.drawFunctionMode.HitTest;
	}
	else
	{
		this.drawFunctionMode = ED.drawFunctionMode.Draw;
	}

	// Get context
	var ctx = this.drawing.context;
	
	// Augment transform with properties of this doodle
    if (this.snapToGrid)
    {
        ctx.translate(Math.round(this.originX/this.gridSpacing) * this.gridSpacing, Math.round(this.originY/this.gridSpacing) * this.gridSpacing);
    }
    else if (this.snapToQuadrant)
    {
        //ctx.translate(this.originX, this.originY);
        ctx.translate(this.quadrantPoint.x * this.originX/Math.abs(this.originX), this.quadrantPoint.y * this.originY/Math.abs(this.originY));
    }
    else
    {
        ctx.translate(this.originX, this.originY);
    }
	ctx.rotate(this.rotation);
	ctx.scale(this.scaleX, this.scaleY);
	
	// Mirror with internal transform
	this.transform.setToTransform(this.drawing.transform);
    if (this.snapToGrid)
    {
        this.transform.translate(Math.round(this.originX/this.gridSpacing) * this.gridSpacing, Math.round(this.originY/this.gridSpacing) * this.gridSpacing);
    }
    else
    {
        this.transform.translate(this.originX, this.originY);
    }
	
	this.transform.rotate(this.rotation);
	this.transform.scale(this.scaleX, this.scaleY);
	
	// Update inverse transform
	this.inverseTransform = this.transform.createInverse();
	
	// Reset hit test flag
	this.isClicked = false;
}

/**
 * Draws selection handles and sets dragging mode which is determined by which handle and part of handle is selected
 * Function either performs a hit test or draws the handles depending on whether a valid Point object is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Doodle.prototype.drawHandles = function(_point)
{
	// Reset handle index and selected ring
	if (this.drawFunctionMode == ED.drawFunctionMode.HitTest)
	{
		this.draggingHandleIndex = null;
		this.draggingHandleRing = null;
	}
	
	// Get context
	var ctx = this.drawing.context;
	
	// Save context to stack
	ctx.save();
	
	// Reset context transform to identity matrix
	ctx.setTransform(1, 0, 0, 1, 0, 0);
	
	// Dimensions and colour of handles
	ctx.lineWidth = 1;
	ctx.strokeStyle = "red";
	ctx.fillStyle = "yellow";
	
	// Draw corner handles
	var arc = Math.PI*2;
	
	for (var i = 0; i < 5; i++)
	{
		var handle = this.handleArray[i];
		
		if (handle.isVisible)
		{
			// Path for inner ring
			ctx.beginPath();
			ctx.arc(handle.location.x, handle.location.y, ED.handleRadius/2, 0, arc, true);

			// Hit testing for inner ring
			if (this.drawFunctionMode == ED.drawFunctionMode.HitTest)
			{
				if (ctx.isPointInPath(_point.x, _point.y))
				{
					this.draggingHandleIndex = i;
					this.draggingHandleRing = ED.handleRing.Inner;
					this.drawing.mode = handle.mode;
					this.isClicked = true;
				}
			}
			
			// Path for optional outer ring
			if (this.isRotatable && handle.isRotatable)
			{
				ctx.moveTo(handle.location.x + ED.handleRadius, handle.location.y);
				ctx.arc(handle.location.x, handle.location.y, ED.handleRadius, 0, arc, true);
				
				// Hit testing for outer ring
				if (this.drawFunctionMode == ED.drawFunctionMode.HitTest)
				{
					if (ctx.isPointInPath(_point.x, _point.y))
					{
						this.draggingHandleIndex = i;
						if (this.draggingHandleRing == null)
						{
							this.draggingHandleRing = ED.handleRing.Outer;
							this.drawing.mode = ED.Mode.Rotate;
						}
						this.isClicked = true;
					}
				}
			}
			

			// Draw handles
			if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
			{
				ctx.fill();
				ctx.stroke();
			}
		}
	}
	
	// Restore context
	ctx.restore();
}

/**
 * Draws the boundary path or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Doodle.prototype.drawBoundary = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// HitTest
	if (this.drawFunctionMode == ED.drawFunctionMode.HitTest)
	{
		// Workaround for Mozilla bug 405300 https://bugzilla.mozilla.org/show_bug.cgi?id=405300
		if (ED.isFirefox())
		{
			ctx.save();
			ctx.setTransform( 1, 0, 0, 1, 0, 0 );
			var hitTest = ctx.isPointInPath(_point.x, _point.y);
			ctx.restore();
		}
		else
		{
			var hitTest = ctx.isPointInPath(_point.x, _point.y);
		}
		
		if (hitTest)
		{
			// Set dragging mode
            if (this.isDrawable && this.isForDrawing)
            {
                this.drawing.mode = ED.Mode.Draw;
            }
            else
            {
                this.drawing.mode = ED.Mode.Move;
            }
			
			// Set flag indicating positive hit test
			this.isClicked = true;
		}
	}
	// Drawing
	else
	{
		// Specify highlight attributes
		if (this.isSelected)
		{
			ctx.shadowColor = "gray";
			ctx.shadowOffsetX = 0;
			ctx.shadowOffsetY = 0;
			ctx.shadowBlur = 20;
		}

        // Specify highlight attributes
		if (this.isForDrawing)
		{
			ctx.shadowColor = "blue";
			ctx.shadowOffsetX = 0;
			ctx.shadowOffsetY = 0;
			ctx.shadowBlur = 20;
		}
        
		// Fill path and draw it
		if (this.isFilled)
		{
			ctx.fill();
		}
		ctx.stroke();
	}
}

/**
 * Returns a String which, if not empty, determines the root descriptions of multiple instances of the doodle
 *
 * @returns {String} Group description
 */
ED.Doodle.prototype.groupDescription = function()
{
	return "";
}

/**
 * Returns a string containing a text description of the doodle (overridden by subclasses)
 *
 * @returns {String} Description of doodle
 */
ED.Doodle.prototype.description = function()
{
	return "";
}

/**
 * Returns the SnoMed code of the doodle (overridden by subclasses)
 *
 * @returns {Int} SnoMed code of entity representated by doodle
 */
ED.Doodle.prototype.snomedCode = function()
{
	return 0;
}

/**
 * Returns a number indicating position in a hierarchy of diagnoses from 0 to 9 (highest) (overridden by subclasses)
 *
 * @returns {Int} Position in diagnostic hierarchy
 */
ED.Doodle.prototype.diagnosticHierarchy = function()
{
	return 0;
}

/**
 * Returns the position converted to clock hours
 *
 * @returns {Int} Clock hour from 1 to 12
 */
ED.Doodle.prototype.clockHour = function()
{
    var clockHour;
    
    if (this.isRotatable && !this.isMoveable)
    {
        clockHour = ((this.rotation * 6/Math.PI) + 12) % 12;
    }
    else
    {
        var twelvePoint = new ED.Point(0,-100);
        var thisPoint = new ED.Point(this.originX, this.originY);
        var clockHour = ((twelvePoint.clockwiseAngleTo(thisPoint) * 6/Math.PI) + 12) % 12;
    }
    
    clockHour = clockHour.toFixed(0);
    if (clockHour == 0) clockHour = 12;        
    return clockHour
}

/**
 * Returns the extent converted to clock hours
 *
 * @returns {Int} Clock hour from 1 to 12
 */
ED.Doodle.prototype.clockHourExtent = function()
{
    var clockHourStart;
    var clockHourEnd;
    
    if (this.isRotatable && !this.isMoveable)
    {
        clockHourStart = (((this.rotation - this.arc/2) * 6/Math.PI) + 12) % 12;
        clockHourEnd = (((this.rotation + this.arc/2) * 6/Math.PI) + 12) % 12;
    }
    else
    {
        var twelvePoint = new ED.Point(0,-100);
        var thisPoint = new ED.Point(this.originX, this.originY);
        var clockHour = ((twelvePoint.clockwiseAngleTo(thisPoint) * 6/Math.PI) + 12) % 12;
    }
    
    clockHourStart = clockHourStart.toFixed(0);
    if (clockHourStart == 0) clockHourStart = 12;
    clockHourEnd = clockHourEnd.toFixed(0);
    if (clockHourEnd == 0) clockHourEnd = 12;  
    return "from " + clockHourStart + " to " + clockHourEnd;
}

/**
 * Returns the extent converted to degrees
 *
 * @returns {Int} Extent 0 to 360 degrees
 */
ED.Doodle.prototype.degreesExtent = function()
{
    var degrees = this.arc * 180/Math.PI;
    var intDegrees = Math.round(degrees);
    return intDegrees;
}

/**
 * Adds a new squiggle to the doodle's squiggle array
 */
ED.Doodle.prototype.addSquiggle = function()
{
    // Get preview colour (returned as rgba(r,g,b))
    var colourString = this.drawing.colourPreview.style.backgroundColor;
    
    // Use regular expression to extract rgb values from returned value
    var colourArray = colourString.match(/\d+/g);
    
    // Get solid or clear
    var filled = this.drawing.fillRadio.checked;
    
    // Line thickness
    var thickness = this.drawing.thickness.value;
    var lineThickness;
    switch (thickness)
    {
        case "Thin":
            lineThickness = ED.squiggleWidth.Thin;
            break;
        case "Medium":
            lineThickness = ED.squiggleWidth.Medium;
            break;
        case "Thick":
            lineThickness = ED.squiggleWidth.Thick;
            break;
        default:
            lineThickness = ED.squiggleWidth.Thin;
            break;            
    }

    // Create new squiggle of selected colour
    var colour = new ED.Colour(colourArray[0], colourArray[1], colourArray[2], 1);
    var squiggle = new ED.Squiggle(this, colour, lineThickness, filled);
    
    // Add it to squiggle array
    this.squiggleArray.push(squiggle);
}

/**
 * Adds a point to the active squiggle (the last in the squiggle array)
 *
 * @param {Point} _point The point in the doodle plane to be added
 */
ED.Doodle.prototype.addPointToSquiggle = function(_point)
{
    if(this.squiggleArray.length > 0)
    {
        var index = this.squiggleArray.length - 1;
        var squiggle = this.squiggleArray[index];
        
        squiggle.addPoint(_point);
    }
}

/**
 * Complete the active squiggle (last in the array)
 */
ED.Doodle.prototype.completeSquiggle = function()
{
    if(this.squiggleArray.length > 0)
    {
        var index = this.squiggleArray.length - 1;
        var squiggle = this.squiggleArray[index];
        
        squiggle.complete = true;
    }
}

/**
 * Calculates arc for doodles without a natural arc value
 *
 8 @returns Arc value in radians
 */
ED.Doodle.prototype.calculateArc = function()
{
    // Transform extremity points to origin of 0,0
    var left = new ED.Point(this.leftExtremity.x - this.drawing.canvas.width/2, this.leftExtremity.y - this.drawing.canvas.height/2);
    var right = new ED.Point(this.rightExtremity.x - this.drawing.canvas.width/2, this.rightExtremity.y - this.drawing.canvas.height/2);
    
    // Return angle between them
    return left.clockwiseAngleTo(right);
}

/**
 * Returns a doodle in JSON format
 *
 * @returns {String} A JSON encoded string representing the variable properties of the doodle
 */
ED.Doodle.prototype.json = function()
{
	var s = '{';
    s = s + '"subclass": ' + '"' + this.className + '", '
    s = s + '"originX": ' + this.originX.toFixed(0) + ', '
    s = s + '"originY": ' + this.originY.toFixed(0) + ', '
    s = s + '"apexX": ' + this.apexX.toFixed(0) + ', '
    s = s + '"apexY": ' + this.apexY.toFixed(0) + ', '
    s = s + '"scaleX": ' + this.scaleX.toFixed(2) + ', '
    s = s + '"scaleY": ' + this.scaleY.toFixed(2) + ', '
    s = s + '"arc": ' + (this.arc * 180/Math.PI).toFixed(0)  + ', '
    s = s + '"rotation": ' + (this.rotation * 180/Math.PI).toFixed(0) + ', '
    s = s + '"order": ' + this.order.toFixed(0) + ', '
    
    s = s + '"squiggleArray": ['; 
    for (var j = 0; j < this.squiggleArray.length; j++)
    {
        s = s + this.squiggleArray[j].json() + ', ';
    }
    s = s + ']';
    s = s + '}';
    
    return s;
}

/**
 * Represents a control handle on the doodle
 *
 * @class Handle
 * @property {Point} location Location in doodle plane
 * @property {Bool} isVisible Flag indicating whether handle should be shown
 * @property {Enum} mode The drawing mode that selection of the handle triggers
 * @property {Bool} isRotatable Flag indicating whether the handle shows an outer ring used for rotation
 * @param {Point} _location
 * @param {Bool} _isVisible
 * @param {Enum} _mode
 * @param {Bool} _isRotatable
 */ 
ED.Handle = function(_location, _isVisible, _mode, _isRotatable)
{
	// Properties
	if (_location == null)
	{
		this.location = new ED.Point(0,0);
	}
	else
	{
		this.location = _location;
	}
	this.isVisible = _isVisible;
	this.mode = _mode;
	this.isRotatable = _isRotatable;
}
	

/**
 * Represents a range of numerical values
 *
 * @class Range
 * @property {Float} min Minimum value
 * @property {Float} max Maximum value
 * @param {Float} _min
 * @param {Float} _max
 */
ED.Range = function(_min, _max)
{
	// Properties
	this.min = _min;
	this.max = _max;
}

/**
 * Returns true if the parameter is less than the minimum of the range
 *
 * @param {Float} _num
 * @returns {Bool} True if the parameter is less than the minimum
 */
ED.Range.prototype.isBelow = function(_num)
{
	if (_num < this.min)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Returns true if the parameter is more than the maximum of the range
 *
 * @param {Float} _num
 * @returns {Bool} True if the parameter is more than the maximum
 */
ED.Range.prototype.isAbove = function(_num)
{
	if (_num > this.max)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Returns true if the parameter is inclusively within the range
 *
 * @param {Float} _num
 * @returns {Bool} True if the parameter is within the range
 */
ED.Range.prototype.includes = function(_num)
{
	if (_num < this.min || _num > this.max)
	{
		return false;
	}
	else
	{
		return true;
	}
}
	
/**
 * Constrains a value to the limits of the range
 *
 * @param {Float} _num
 * @returns {Float} The constrained value
 */
ED.Range.prototype.constrain = function(_num)
{
	if (_num < this.min)
	{
		return this.min;
	}
	else if (_num > this.max)
	{
		return this.max;
	}
	else
	{
		return _num;
	}
}

/**
 * Represents a point in two dimensional space
 * @class Point
 * @property {Float} x The x-coordinate of the point
 * @property {Float} y The y-coordinate of the point
 * @property {Array} components Array representing point in matrix notation
 * @param {Float} _x
 * @param {Float} _y
 */ 
ED.Point = function(_x, _y)
{
	// Properties
    this.x = +_x;
    this.y = +_y;
    this.components = [this.x, this.y, 1];
}

/**
 * Sets properties of the point using polar coordinates
 *
 * @param {Float} _r Distance from the origin
 * @param {Float} _p Angle in radians from North going clockwise
 */ 
ED.Point.prototype.setWithPolars = function(_r, _p)
{
    this.x = _r * Math.sin(_p);
    this.y = -_r * Math.cos(_p);
}

/**
 * Calculates the distance between this point and another
 *
 * @param {Point} _point
 * @returns {Float} Distance from the passed point
 */ 
ED.Point.prototype.distanceTo = function(_point)
{
	return Math.sqrt(Math.pow(this.x - _point.x, 2) + Math.pow(this.y - _point.y, 2));
}

/**
 * Calculates the dot product of two points (treating points as 2D vectors)
 *
 * @param {Point} _point
 * @returns {Float} The dot product
 */
ED.Point.prototype.dotProduct = function(_point)
{
	return this.x * _point.x + this.y * _point.y;
}

/**
 * Calculates the cross product of two points (treating points as 2D vectors)
 *
 * @param {Point} _point
 * @returns {Float} The cross product
 */
ED.Point.prototype.crossProduct = function(_point)
{
	return this.x * _point.y - this.y * _point.x;
}

/**
 * Calculates the length of the point treated as a vector
 *
 * @returns {Float} The length
 */
ED.Point.prototype.length = function()
{
	return Math.sqrt(this.x * this.x + this.y * this.y);
}

/**
 * Inner angle to other vector from same origin going round clockwise from vector a to vector b
 *
 * @param {Point} _point
 * @returns {Float} The angle in radians
 */
ED.Point.prototype.clockwiseAngleTo = function(_point)
{
	var angle =  Math.acos(this.dotProduct(_point)/(this.length() * _point.length()));
	if (this.crossProduct(_point) < 0)
	{
		return 2 * Math.PI - angle;
	}
	else
	{
		return angle;
	}
}

/**
 * Returns a point in JSON encoding
 *
 * @returns {String} point in JSON format
 */
ED.Point.prototype.json = function()
{
    return "{\"x\":" + this.x.toFixed(2) + ",\"y\":" + this.y.toFixed(2) + "}";
}


/**
 * Creates a new transformation matrix initialised to the identity matrix
 *
 * @class AffineTransform
 * @property {Array} components Array representing 3x3 matrix
 */
ED.AffineTransform = function()
{
	// Properties - array of arrays of column values one for each row
 	this.components = [[1,0,0],[0,1,0],[0,0,1]];
}

/**
 * Sets matrix to identity matrix
 */
ED.AffineTransform.prototype.setToIdentity = function()
{
	this.components[0][0] = 1;
 	this.components[0][1] = 0;
 	this.components[0][2] = 0;
 	this.components[1][0] = 0;
 	this.components[1][1] = 1;
 	this.components[1][2] = 0;	
 	this.components[2][0] = 0;
 	this.components[2][1] = 0;
 	this.components[2][2] = 1;
}

/**
 * Sets the transform matrix to another
 *
 * @param {AffineTransform} _transform Array An affine transform
 */
ED.AffineTransform.prototype.setToTransform = function(_transform)
{
	this.components[0][0] = _transform.components[0][0];
 	this.components[0][1] = _transform.components[0][1];
 	this.components[0][2] = _transform.components[0][2];
 	this.components[1][0] = _transform.components[1][0];
 	this.components[1][1] = _transform.components[1][1];
 	this.components[1][2] = _transform.components[1][2];
 	this.components[2][0] = _transform.components[2][0];
 	this.components[2][1] = _transform.components[2][1];
 	this.components[2][2] = _transform.components[2][2];
}

/**
 * Adds a translation to the transform matrix
 *
 * @param {float} _x value to translate along x-axis
 * @param {float} _y value to translate along y-axis
 */
ED.AffineTransform.prototype.translate = function(_x, _y)
{
	this.components[0][2] = this.components[0][0] * _x + this.components[0][1] * _y + this.components[0][2];
	this.components[1][2] = this.components[1][0] * _x + this.components[1][1] * _y + this.components[1][2];
	this.components[2][2] = this.components[2][0] * _x + this.components[2][1] * _y + this.components[2][2];
}

/**
 * Adds a scale to the transform matrix
 *
 * @param {float} _sx value to scale along x-axis
 * @param {float} _sy value to scale along y-axis
 */
ED.AffineTransform.prototype.scale = function(_sx, _sy)
{
	this.components[0][0] = this.components[0][0] * _sx;
	this.components[0][1] = this.components[0][1] * _sy;
	this.components[1][0] = this.components[1][0] * _sx;
	this.components[1][1] = this.components[1][1] * _sy;
	this.components[2][0] = this.components[2][0] * _sx;
	this.components[2][1] = this.components[2][1] * _sy;
}

/**
 * Adds a rotation to the transform matrix
 *
 * @param {float} _rad value to rotate by in radians
 */
ED.AffineTransform.prototype.rotate = function(_rad)
{
	// Calulate trigonometry
	var c = Math.cos(_rad);
	var s = Math.sin(_rad);
	
	// Make new matrix for transform
	var matrix = [[0,0,0],[0,0,0],[0,0,0]];
	
	// Apply transform
	matrix[0][0] = this.components[0][0] * c + this.components[0][1] * s;
	matrix[0][1] = this.components[0][1] * c - this.components[0][0] * s;
	matrix[1][0] = this.components[1][0] * c + this.components[1][1] * s;
	matrix[1][1] = this.components[1][1] * c - this.components[1][0] * s;
	matrix[2][0] = this.components[2][0] * c + this.components[2][1] * s;
	matrix[2][1] = this.components[2][1] * c - this.components[2][0] * s;
	
	// Change old matrix
	this.components[0][0] = matrix[0][0];
	this.components[0][1] = matrix[0][1];
	this.components[1][0] = matrix[1][0];
	this.components[1][1] = matrix[1][1];
	this.components[2][0] = matrix[2][0];
	this.components[2][1] = matrix[2][1];
}

/**
 * Applies transform to a point
 *
 * @param {Point} _point a point
 * @returns {Point} a transformed point
 */
ED.AffineTransform.prototype.transformPoint = function(_point)
{
	var newX = _point.x * this.components[0][0] + _point.y * this.components[0][1] + 1 * this.components[0][2];
	var newY = _point.x * this.components[1][0] + _point.y * this.components[1][1] + 1 * this.components[1][2];

	return new ED.Point(newX, newY);
}

/**
 * Calculates determinant of transform matrix
 *
 * @returns {Float} determinant
 */
ED.AffineTransform.prototype.determinant = function()
{
	return  this.components[0][0] * (this.components[1][1] * this.components[2][2] - this.components[1][2] * this.components[2][1]) - 
			this.components[0][1] * (this.components[1][0] * this.components[2][2] - this.components[1][2] * this.components[2][0]) +
			this.components[0][2] * (this.components[1][0] * this.components[2][1] - this.components[1][1] * this.components[2][0]);
}

/**
 * Inverts transform matrix
 *
 * @returns {Array} inverse matrix
 */
ED.AffineTransform.prototype.createInverse = function()
{
	// Create new matrix 
	var inv = new ED.AffineTransform();
	
	var det = this.determinant();
	
	//if (det != 0)
	var invdet = 1/det;
	
	// Calculate components of inverse matrix
	inv.components[0][0] = invdet * (this.components[1][1] * this.components[2][2] - this.components[1][2] * this.components[2][1]);
	inv.components[0][1] = invdet * (this.components[0][2] * this.components[2][1] - this.components[0][1] * this.components[2][2]);
	inv.components[0][2] = invdet * (this.components[0][1] * this.components[1][2] - this.components[0][2] * this.components[1][1]);
		
	inv.components[1][0] = invdet * (this.components[1][2] * this.components[2][0] - this.components[1][0] * this.components[2][2]);
	inv.components[1][1] = invdet * (this.components[0][0] * this.components[2][2] - this.components[0][2] * this.components[2][0]);
	inv.components[1][2] = invdet * (this.components[0][2] * this.components[1][0] - this.components[0][0] * this.components[1][2]);
	
	inv.components[2][0] = invdet * (this.components[1][0] * this.components[2][1] - this.components[1][1] * this.components[2][0]);
	inv.components[2][1] = invdet * (this.components[0][1] * this.components[2][0] - this.components[0][0] * this.components[2][1]);
	inv.components[2][2] = invdet * (this.components[0][0] * this.components[1][1] - this.components[0][1] * this.components[1][0]);
		
	return inv;
}

/**
 * Squiggles are free-hand lines drawn by the mouse;
 * Points are stored in an array and represent points in the doodle plane
 *
 * @class Squiggle
 * @property {Doodle} doodle The doodle to which this squiggle belongs
 * @property {Colour} colour Colour of the squiggle
 * @property {Int} thickness Thickness of the squiggle in pixels
 * @property {Bool} filled True if squiggle is solid (filled)
 * @property {Array} pointsArray Array of points making up the squiggle
 * @property {Bool} complete True if the squiggle is complete (allows a filled squiggle to appear as a line while being created)
 * @param {Doodle} _doodle
 * @param {Colour} _colour
 * @param {Int} _thickness
 * @param {Bool} _filled
 */
ED.Squiggle = function(_doodle, _colour, _thickness, _filled)
{
    this.doodle = _doodle;
    this.colour = _colour;
    this.thickness = _thickness;
    this.filled = _filled;
    
    this.pointsArray = new Array();
    this.complete = false;
}

/**
 * Adds a point to the points array
 *
 * @param {Point} _point
 */
ED.Squiggle.prototype.addPoint = function(_point)
{
    this.pointsArray.push(_point);
}

/**
 * Returns a squiggle in JSON format
 *
 * @returns {String} A JSON encoded string representing the squiggle
 */
ED.Squiggle.prototype.json = function()
{
	var s = '{';
    s = s + '"colour": ' + this.colour.json() + ', ';
    s = s + '"thickness": ' + this.thickness + ', ';
    s = s + '"filled": "' + this.filled + '", ';
    
    s = s + '"pointsArray": [';
    for (var i = 0; i < this.pointsArray.length; i++)
	{
        s = s + this.pointsArray[i].json() + ', ';
    }
    s = s + ']';
    s = s + '}';
    
    return s;
}

/**
 * A colour in the RGB space;
 * Usage: var c = new ED.Colour(0, 0, 255, 0.75); ctx.fillStyle = c.rgba();
 *
 * @property {Int} red The red value
 * @property {Int} green The green value
 * @property {Int} blue The blue value
 * @property {Float} alpha The alpha value
 * @param {Int} _red
 * @param {Int} _green
 * @param {Int} _blue
 * @param {Float} _alpha
 */
ED.Colour = function(_red, _green, _blue, _alpha)
{
    this.red = _red;
    this.green = _green;
    this.blue = _blue;
    this.alpha = _alpha;
}

/**
 * Returns a colour in Javascript rgba format
 *
 * @returns {String} Colour in rgba format
 */
ED.Colour.prototype.rgba = function()
{
    return "rgba(" + this.red + ", " + this.green + ", " + this.blue + ", " + this.alpha + ")";
}

/**
 * Returns a colour in JSON format
 *
 * @returns {String} A JSON encoded string representing the colour
 */
ED.Colour.prototype.json = function()
{
    return "{\"red\":" + this.red + ",\"green\":" + this.green + ",\"blue\":" + this.blue + ",\"alpha\":" + this.alpha + "}";
}

/**
 * Additional function for String object
 *
 * @returns {String} String with first letter made lower case, unless part of an abbreviation
 */
String.prototype.firstLetterToLowerCase = function()
{
    var secondChar = this.charAt(1);
    
    if (secondChar == secondChar.toUpperCase())
    {
        return this;
    }
    else
    {
        return this.charAt(0).toLowerCase() + this.slice(1);
    }
}

/**
 * Additional function for String object
 *
 * @returns {String} String with last ', ' replaced with ', and '
 */
String.prototype.addAndAfterLastComma = function()
{
    // Search backwards from end of string for comma
    var found = false;
    for (var pos = this.length - 1; pos >= 0; pos--)
    {
        if (this.charAt(pos) == ',')
        {
            found = true;
            break;
        }
    }

    if (found) return this.substring(0, pos) + ", and" + this.substring(pos+1, this.length);
    else return this;
}
