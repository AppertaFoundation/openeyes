/**
 * @fileOverview Contains doodle subclasses for general use
 * @author <a href="mailto:bill.aylward@mac.com">Bill Aylward</a>
 * @version 0.91
 *
 * Modification date: 28th March 2012
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
if (ED == null || typeof(ED) != "object") { var ED = new Object();}

/**
 * An example to be used as a template
 *
 * @class Template
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Template = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Template";
}

/**
 * Sets superclass and constructor
 */
ED.Template.prototype = new ED.Doodle;
ED.Template.prototype.constructor = ED.Template;
ED.Template.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Template.prototype.setHandles = function()
{
}

/**
 * Sets default dragging attributes
 */
ED.Template.prototype.setPropertyDefaults = function()
{
}

/**
 * Sets default parameters
 */
ED.Template.prototype.setParameterDefaults = function()
{
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Template.prototype.draw = function(_point)
{	
	// Call draw method in superclass
	ED.Template.superclass.draw.call(this, _point);

    // Get context
	var ctx = this.drawing.context;
    
	// Boundary path
	ctx.beginPath();
	
	// Template
	ctx.rect(-50, -50, 100, 100);
	
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
	ctx.fillStyle = "yellow";
	ctx.strokeStyle = "blue";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Put other drawing paths in this if block
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	//this.handleArray[0].location = this.transform.transformPoint(new ED.Point(-50, 50));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Freehand drawing
 *
 * @class Freehand
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Freehand = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Freehand";
}

/**
 * Sets superclass and constructor
 */
ED.Freehand.prototype = new ED.Doodle;
ED.Freehand.prototype.constructor = ED.Freehand;
ED.Freehand.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Freehand.prototype.setHandles = function()
{
	this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, true);
}

/**
 * Sets default dragging attributes
 */
ED.Freehand.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
    this.isDrawable = true; 
	this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-400, +100);
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Freehand.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Freehand.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
	
	// Freehand
	ctx.rect(-150, -150, 300, 300);
	
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
    this.isFilled = false;
    ctx.strokeStyle = "rgba(0, 0, 0, 0)";
    if (this.isSelected) ctx.strokeStyle = "gray";
    if (this.isForDrawing) ctx.strokeStyle = "blue";

	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Iterate through squiggles, drawing them
        for (var i = 0; i < this.squiggleArray.length; i++)
        {
            var squiggle = this.squiggleArray[i];
            
            ctx.beginPath();
            
            // Squiggle attributes
            ctx.lineWidth = squiggle.thickness;
            ctx.strokeStyle = squiggle.colour.rgba();
            ctx.fillStyle = squiggle.colour.rgba();
            
            // Iterate through squiggle points
            for (var j = 0; j < squiggle.pointsArray.length; j++)
            {
                ctx.lineTo(squiggle.pointsArray[j].x, squiggle.pointsArray[j].y);
            }
            
            // Draw squiggle
            ctx.stroke();
            
            // Optionally fill if squiggle is complete (stops filling while drawing)
            if (squiggle.filled && squiggle.complete) ctx.fill();
        }
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(150, -150));
	
	// Draw handles if selected but not if for drawing
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Lable
 *
 * @class Lable
 * @property {String} className Name of doodle subclass
 * @property {String} lableText Text of lable
 * @property {Int} lableWidth Width of lable in pixels in doodle plane
 * @property {Int} lableHeight Height of lable in pixels in doodle plane
 * @property {String} labelFont Font settings of lable in CSS format eg "50px sans-serif"
 * @property {Int} padding Horizontal padding between lable and boundary path
 * @property {Int} maximumLength Maximum number of characters in lable
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Lable = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Lable";
    
    // Lable text
    this.lableText = "";
    
    // Lable width and height
    this.lableWidth = 0;
    this.lableHeight = 80;
    
    // Lable font
    this.lableFont = "50px sans-serif";
    
    // Horizontal padding between lable and boundary path
    this.padding = 10;
    
    // Maximum length
    this.maximumLength = 20;
}

/**
 * Sets superclass and constructor
 */
ED.Lable.prototype = new ED.Doodle;
ED.Lable.prototype.constructor = ED.Lable;
ED.Lable.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Lable.prototype.setHandles = function()
{
	this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, true);
}

/**
 * Sets default dragging attributes
 */
ED.Lable.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
    this.isDrawable = false; 
	this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-400, +100);
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Lable.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Lable.superclass.draw.call(this, _point);
    
    // Set font
    ctx.font = this.lableFont;
    
    // Calculate pixel width of text with padding
    this.lableWidth = ctx.measureText(this.lableText).width + this.padding * 2;
	
	// Boundary path
	ctx.beginPath();
	
	// Lable boundary
	ctx.rect(-this.lableWidth/2, -this.lableHeight/2, this.lableWidth, this.lableHeight);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
    this.isFilled = false;
    ctx.strokeStyle = "rgba(0, 0, 0, 0)";
    if (this.isSelected) ctx.strokeStyle = "gray";
    
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{    
        // Draw boundary
        ctx.stroke();
        
        // Draw text
        ctx.fillText(this.lableText, -this.lableWidth/2 + this.padding, this.lableHeight/6);
    }
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(this.lableWidth/2, -this.lableHeight/2));
	
	// Draw handles if selected but not if for drawing
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Adds a letter to the lable text
 *
 * @param {Int} _keyCode Keycode of pressed key
 */
ED.Lable.prototype.addLetter = function(_keyCode)
{
    // need code here to convert to character
    var character = String.fromCharCode(_keyCode);
    
    if (this.lableText.length < this.maximumLength) this.lableText += character;
}


/**
 * Arrow to be used as a pointer
 *
 * @class Arrow
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Arrow = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Arrow";
}

/**
 * Sets superclass and constructor
 */
ED.Arrow.prototype = new ED.Doodle;
ED.Arrow.prototype.constructor = ED.Arrow;
ED.Arrow.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Arrow.prototype.setHandles = function()
{
	this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, true);
}

/**
 * Sets default dragging attributes
 */
ED.Arrow.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-40, +30);
}

/**
 * Sets default parameters
 */
ED.Arrow.prototype.setParameterDefaults = function()
{
    this.rotation = Math.PI/3;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Arrow.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Arrow.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    // Draw a left pointing arrow shape
    ctx.moveTo(-100,0);
    ctx.lineTo(-40,-40);
    ctx.lineTo(-40, -15);
    ctx.lineTo(100, -15);
    ctx.lineTo(100, 15);
    ctx.lineTo(-40, 15);
    ctx.lineTo(-40, 40);    
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 3;
	ctx.fillStyle = "yellow";
	ctx.strokeStyle = "blue";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(100, -15));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Slider
 *
 * @class Slider
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Slider = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
    // Slider Properties (Establish before superclass call so values are available for setPropertyDefaults)
    this.halfHeight =  6;
    this.halfLength = 200;
    
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Slider";

}

/**
 * Sets superclass and constructor
 */
ED.Slider.prototype = new ED.Doodle;
ED.Slider.prototype.constructor = ED.Slider;
ED.Slider.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Slider.prototype.setHandles = function()
{
    this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Slider.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfApexX = new ED.Range(-this.halfLength, this.halfLength);
	this.rangeOfApexY = new ED.Range(-0, +0);
}

/**
 * Sets default parameters
 */
ED.Slider.prototype.setParameterDefaults = function()
{
    this.defaultRadius = 374;
    
    // The radius property is changed by movement in rotatable doodles
    this.radius = this.defaultRadius;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Slider.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Slider.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    // Outline of slider
    ctx.moveTo(-this.halfLength, -this.halfHeight);
    ctx.lineTo(this.halfLength, -this.halfHeight);
    ctx.arc(this.halfLength, 0, this.halfHeight, -Math.PI/2, Math.PI/2, false);
    ctx.lineTo(-this.halfLength, this.halfHeight);    
    ctx.arc(-this.halfLength, 0, this.halfHeight, Math.PI/2, -Math.PI/2, false);
    ctx.closePath();
    
    // Colour of fill
    
    // Colors for gradient
    var topColour = "rgba(160, 160, 160, 1)";
    var bottomColour = "rgba(240, 240, 240, 1)";
    
    // Vertical gradient
    var gradient = ctx.createLinearGradient(0, -this.halfHeight, 0, this.halfHeight)
    gradient.addColorStop(0, topColour);
    gradient.addColorStop(1, bottomColour);
    ctx.fillStyle = gradient;
    
	// Set line attributes
	ctx.lineWidth = 2;
    
    // Colour of outer line is dark gray
    ctx.strokeStyle = "rgba(120,120,120,0.8)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
    // Coordinates of handles (in canvas plane)
    this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
    
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.Slider.prototype.description = function()
{
    var returnString = "Mattress suture at ";
    
    returnString += this.clockHour() + " o'clock";
    
	return returnString;
}

/**
 * Point in a line
 *
 * @class PointInLine
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.PointInLine = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "PointInLine";
}

/**
 * Sets superclass and constructor
 */
ED.PointInLine.prototype = new ED.Doodle;
ED.PointInLine.prototype.constructor = ED.PointInLine;
ED.PointInLine.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.PointInLine.prototype.setHandles = function()
{
}

/**
 * Sets default dragging attributes
 */
ED.PointInLine.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
    this.isPointInLine = true;
}

/**
 * Sets default parameters
 */
ED.PointInLine.prototype.setParameterDefaults = function()
{
	this.originY = -300;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.PointInLine.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.PointInLine.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
	
	// Point
	ctx.arc(0,0,8,0,Math.PI*2,true);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;
	ctx.fillStyle =  "rgba(20,20,20,1)";
	ctx.strokeStyle = "rgba(20,20,20,1)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Return value indicating successful hittest
	return this.isClicked;
}


/**
 * Surgeon tube
 *
 * @class Surgeon
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.Surgeon = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Surgeon";
}

/**
 * Sets superclass and constructor
 */
ED.Surgeon.prototype = new ED.Doodle;
ED.Surgeon.prototype.constructor = ED.Surgeon;
ED.Surgeon.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Surgeon.prototype.setHandles = function()
{
}

/**
 * Sets default dragging attributes
 */
ED.Surgeon.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
    this.isShowHighlight = false;
	this.isOrientated = true;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
    this.snapToPoints = true;
	this.rangeOfApexY = new ED.Range(+100, +500);
	this.rangeOfApexX = new ED.Range(-0, +0);
    
    // Array of points to snap to
    this.pointsArray = new Array();
    var point = new ED.Point(0, -300);
    this.pointsArray.push(point);
    var point = new ED.Point(212, -212);
    this.pointsArray.push(point);
    var point = new ED.Point(300, 0);
    this.pointsArray.push(point);
    var point = new ED.Point(212, 212);
    this.pointsArray.push(point);
    var point = new ED.Point(0,300);
    this.pointsArray.push(point);
    var point = new ED.Point(-212, 212);
    this.pointsArray.push(point);
    var point = new ED.Point(-300,0);
    this.pointsArray.push(point);
    var point = new ED.Point(-212, -212);
    this.pointsArray.push(point);
}

/**
 * Sets default parameters
 */
ED.Surgeon.prototype.setParameterDefaults = function()
{
    this.originY = -300;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Surgeon.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Surgeon.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    // Scaling factor
    var s = 0.2;
    
    // Surgeon
    ctx.moveTo(0 * s, -200 * s);
    ctx.bezierCurveTo( -100 * s, -250 * s, -145 * s, -190 * s, -200 * s, -180 * s);
    ctx.bezierCurveTo( -310 * s, -160 * s, -498 * s, -75 * s, -500 * s, 0 * s);
    ctx.bezierCurveTo( -500 * s, 50 * s, -500 * s, 460 * s, -470 * s, 700 * s);
    ctx.bezierCurveTo( -470 * s, 710 * s, -500 * s, 770 * s, -500 * s, 810 * s);
    ctx.bezierCurveTo( -500 * s, 840 * s, -440 * s, 850 * s, -420 * s, 840 * s);
    ctx.bezierCurveTo( -390 * s, 830 * s, -380 * s, 710 * s, -380 * s, 700 * s);
    ctx.bezierCurveTo( -370 * s, 700 * s, -360 * s, 780 * s, -350 * s, 780 * s);
    ctx.bezierCurveTo( -330 * s, 780 * s, -340 * s, 730 * s, -340 * s, 700 * s);
    ctx.bezierCurveTo( -340 * s, 690 * s, -350 * s, 680 * s, -350 * s, 670 * s);
    ctx.bezierCurveTo( -350 * s, 590 * s, -385 * s, 185 * s, -300 * s, 100 * s);

    ctx.bezierCurveTo( -150 * s, 140 * s, -250 * s, 200 * s, 0 * s, 300 * s);

    ctx.bezierCurveTo( 250 * s, 200 * s, 150 * s, 140 * s, 300 * s, 100 * s);
    ctx.bezierCurveTo( 380 * s, 180 * s, 350 * s, 590 * s, 350 * s, 670 * s);
    ctx.bezierCurveTo( 350 * s, 680 * s, 340 * s, 690 * s, 340 * s, 700 * s);
    ctx.bezierCurveTo( 340 * s, 730 * s, 330 * s, 780 * s, 350 * s, 780 * s);
    ctx.bezierCurveTo( 360 * s, 780 * s, 370 * s, 700 * s, 380 * s, 700 * s);
    ctx.bezierCurveTo( 380 * s, 710 * s, 390 * s, 830 * s, 420 * s, 840 * s);
    ctx.bezierCurveTo( 430 * s, 845 * s, 505 * s, 840 * s, 505 * s, 810 * s);
    ctx.bezierCurveTo( 505 * s, 760 * s, 470 * s, 710 * s, 470 * s, 700 * s);
    ctx.bezierCurveTo( 500 * s, 460 * s, 499 * s, 45 * s, 500 * s, 0 * s);
    ctx.bezierCurveTo( 498 * s, -78 * s, 308 * s, -164 * s, 200 * s, -182 * s);
    ctx.bezierCurveTo( 145 * s, -190 * s, 100 * s, -250 * s, 0 * s, -200 * s);
    
    // Set Attributes
    ctx.lineWidth = 4;
    ctx.strokeStyle = "rgba(120,120,120,1)";
    
    // Set light blue for surgeon's gown
    var colour = new ED.Colour(0,0,0,1);
    colour.setWithHexString('3AFEFA');
    ctx.fillStyle = colour.rgba();
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        ctx.beginPath();

        ctx.moveTo(0 * s, -250 * s);
        ctx.bezierCurveTo( -100 * s, -250 * s, -180 * s, -200 * s, -200 * s, -170 * s);
        ctx.bezierCurveTo( -209 * s, -157 * s, -220 * s, -100 * s, -230 * s, -50 * s);
        ctx.bezierCurveTo( -260 * s, -70 * s, -260 * s, -20 * s, -260 * s, 0 * s);
        ctx.bezierCurveTo( -260 * s, 20 * s, -260 * s, 80 * s, -230 * s, 60 * s);
        ctx.bezierCurveTo( -230 * s, 90 * s, -220 * s, 141 * s, -210 * s, 160 * s);
        ctx.bezierCurveTo( -190 * s, 200 * s, -100 * s, 280 * s, -40 * s, 300 * s);
        ctx.bezierCurveTo( -34 * s, 303 * s, -20 * s, 350 * s, 0 * s, 350 * s);
        ctx.bezierCurveTo( 20 * s, 350 * s, 34 * s, 300 * s, 40 * s, 300 * s);
        ctx.bezierCurveTo( 100 * s, 280 * s, 190 * s, 200 * s, 210 * s, 160 * s);
        ctx.bezierCurveTo( 218 * s, 143 * s, 230 * s, 90 * s, 230 * s, 60 * s);
        ctx.bezierCurveTo( 260 * s, 80 * s, 260 * s, 20 * s, 260 * s, 0 * s);
        ctx.bezierCurveTo( 260 * s, -20 * s, 260 * s, -70 * s, 230 * s, -50 * s);
        ctx.bezierCurveTo( 220 * s, -100 * s, 208 * s, -158 * s, 200 * s, -170 * s);
        ctx.bezierCurveTo( 180 * s, -200 * s, 100 * s, -250 * s, 0 * s, -250 * s);
        
        ctx.fill();
        ctx.stroke();
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
    
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Returns parameters
 *
 * @returns {String} value of parameter
 */
ED.Surgeon.prototype.getParameter = function(_parameter)
{
    var returnValue;
    var isRE = (this.drawing.eye == ED.eye.Right);
    
    var dial = 2 * Math.PI;
    
    switch (_parameter)
    {
        // Surgeon position
        case 'surgeonPosition':
            if (isRE)
            {
                if (this.rotation < dial/16 ) returnValue = 'Superior';
                else if (this.rotation < 3 * dial/16 ) returnValue = 'Supero-nasal';
                else if (this.rotation < 5 * dial/16 ) returnValue = 'Nasal';
                else if (this.rotation < 7 * dial/16 ) returnValue = 'Infero-nasal';
                else if (this.rotation < 9 * dial/16 ) returnValue = 'Inferior';
                else if (this.rotation < 11 * dial/16) returnValue = 'Infero-temporal';
                else if (this.rotation < 13 * dial/16) returnValue = 'Temporal';
                else if (this.rotation < 15 * dial/16) returnValue = 'Supero-temporal';
                else returnValue = 'Superior';                
            }
            else
            {
                if (this.rotation < dial/16 ) returnValue = 'Superior';
                else if (this.rotation < 3 * dial/16 ) returnValue = 'Supero-temporal';
                else if (this.rotation < 5 * dial/16) returnValue = 'Temporal';
                else if (this.rotation < 7 * dial/16) returnValue = 'Infero-temporal';
                else if (this.rotation < 9 * dial/16) returnValue = 'Inferior';
                else if (this.rotation < 11 * dial/16) returnValue = 'Infero-nasal';
                else if (this.rotation < 13 * dial/16) returnValue = 'Nasal';
                else if (this.rotation < 15 * dial/16) returnValue = 'Supero-nasal';
                else returnValue = 'Superior';
            }
            break;
            
        default:
            returnValue = "";
            break;
    }
    
    return returnValue;
}

/**
 * Sets derived parameters for this doodle
 *
 * @param {String} _parameter Name of parameter
 * @param {String} _value New value of parameter
 */
ED.Surgeon.prototype.setParameter = function(_parameter, _value)
{
    var isRE = (this.drawing.eye == ED.eye.Right);
    switch (_parameter)
    {
            // Surgeon position
        case 'surgeonPosition':
            switch (_value)
        {
            case 'Superior':
                if (isRE)
                {
                    this.originX = 0;
                    this.originY = -300;
                    this.rotation = 0;
                }
                else
                {
                    this.originX = 0;
                    this.originY = -300;
                    this.rotation = 0;
                }
                break;
            case 'Supero-temporal':
                if (isRE)
                {
                    this.originX = -212;
                    this.originY = -212;
                    this.rotation = 7 * Math.PI/4;
                }
                else
                {
                    this.originX = 212;
                    this.originY = -212;
                    this.rotation = Math.PI/4;
                }
                break;
            case 'Temporal':
                if (isRE)
                {
                    this.originX = -300;
                    this.originY = 0;
                    this.rotation =  6 * Math.PI/4;
                }
                else
                {
                    this.originX = 300;
                    this.originY = 0;
                    this.rotation = Math.PI/2;
                }
                break;
            case 'Infero-temporal':
                if (isRE)
                {
                    this.originX = -212;
                    this.originY = 212;
                    this.rotation = 5 * Math.PI/4;
                }
                else
                {
                    this.originX = 212;
                    this.originY = 212;
                    this.rotation = 3 * Math.PI/4;
                }
                break;
            case 'Inferior':
                if (isRE)
                {
                    this.originX = 0;
                    this.originY = 300;
                    this.rotation = Math.PI;
                }
                else
                {
                    this.originX = 0;
                    this.originY = 300;
                    this.rotation = Math.PI;
                }
                break;
            case 'Infero-nasal':
                if (isRE)
                {
                    this.originX = 212;
                    this.originY = 212;
                    this.rotation = 3 * Math.PI/4;
                }
                else
                {
                    this.originX = -212;
                    this.originY = 212;
                    this.rotation = 5 * Math.PI/4;
                }
                break;
            case 'Nasal':
                if (isRE)
                {
                    this.originX = 300;
                    this.originY = 0;
                    this.rotation = 2 * Math.PI/4;
                }
                else
                {
                    this.originX = -300;
                    this.originY = 0;
                    this.rotation = 6 * Math.PI/4;
                }
                break;
            case 'Supero-nasal':
                if (isRE)
                {
                    this.originX = 212;
                    this.originY = -212;
                    this.rotation = 1 * Math.PI/4;
                }
                else
                {
                    this.originX = -212;
                    this.originY = -212;
                    this.rotation = 7 * Math.PI/4;
                }
                break;
            default:
                break;
        }
            break;
            
        default:
            break
    }
}

ED.Surgeon.prototype.nearestPointTo = function(_point)
{
    var min = 10000000; // Greater than square of maximum separation in doodle plane
    var index = 0;
    
    // Iterate through points array to find nearest point
    for (var i = 0; i < this.pointsArray.length; i++)
    {
        var p = this.pointsArray[i];
        var d = (_point.x - p.x) * (_point.x - p.x) + (_point.y - p.y) * (_point.y - p.y);
        
        if (d < min)
        {
            min = d;
            index = i;
        }
    }
    
    return this.pointsArray[index];
}

/**
 *  OperatingTable
 *
 * @class  OperatingTable
 * @property {String} className Name of doodle subclass
 * @param {Drawing} _drawing
 * @param {Int} _originX
 * @param {Int} _originY
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED. OperatingTable = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = " OperatingTable";
}

/**
 * Sets superclass and constructor
 */
ED. OperatingTable.prototype = new ED.Doodle;
ED. OperatingTable.prototype.constructor = ED. OperatingTable;
ED. OperatingTable.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED. OperatingTable.prototype.setHandles = function()
{
}

/**
 * Sets default dragging attributes
 */
ED. OperatingTable.prototype.setPropertyDefaults = function()
{
	this.isSelectable = false;
}

/**
 * Sets default parameters
 */
ED. OperatingTable.prototype.setParameterDefaults = function()
{
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED. OperatingTable.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED. OperatingTable.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
	// Round hole
	ctx.arc(0,-0,60,0,Math.PI*2,true);
    
    // Set Attributes
    ctx.lineWidth = 30;
    ctx.strokeStyle = "rgba(120,120,120,1)";
    ctx.fillStyle = "rgba(220,220,220,1)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Non boundary paths here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        ctx.beginPath();
        
        // Round hole
        ctx.rect(-100, 20, 200, 400);
        
        // Set Attributes
        ctx.lineWidth = 8;
        ctx.strokeStyle = "rgba(120,120,120,1)";
        ctx.fillStyle = "rgba(220,220,220,1)";
        
        ctx.fill();
        ctx.stroke();
	}

	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
    
	// Return value indicating successful hittest
	return this.isClicked;
}


