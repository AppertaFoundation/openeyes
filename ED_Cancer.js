/**
 * @fileOverview Contains doodle subclasses for breast cancer
 * @author <a href="mailto:bill.aylward@mac.com">Bill Aylward</a>
 * @version 0.9
 *
 * Modification date: 20th May 2011
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
 * Breast diagram
 *
 * @class Breasts
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
ED.Breasts = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Breasts";
}

/**
 * Sets superclass and constructor
 */
ED.Breasts.prototype = new ED.Doodle;
ED.Breasts.prototype.constructor = ED.Breasts;
ED.Breasts.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Breasts.prototype.setHandles = function()
{
    this.handleArray[1] = new ED.Handle(null, true, ED.Mode.Scale, false);
	this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
}

/**
 * Sets default dragging attributes
 */
ED.Breasts.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.5, +1.5);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-40, +30);
}

/**
 * Sets default parameters
 */
ED.Breasts.prototype.setParameterDefaults = function()
{
    this.originY = -100;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Breasts.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Breasts.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();

    ctx.moveTo(-400,-200);
    ctx.bezierCurveTo(-400,-160,-300,-150,-300,-100);
    ctx.bezierCurveTo(-300,-50,-450,20,-450,150);
    ctx.bezierCurveTo(-450,200,-400,300,-300,300);
    ctx.bezierCurveTo(-200,300,-100,200,0,200);
    ctx.bezierCurveTo(100,200,200,300,300,300);
    ctx.bezierCurveTo(400,300,450,200,450,150);
    ctx.bezierCurveTo(450,20,300,-50,300,-100);
    ctx.bezierCurveTo(300,-150,400,-160,400,-200);
    
	// Close path
	//ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 8;
	ctx.fillStyle = "rgba(0, 0, 0, 0)";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Areola
        ctx.beginPath();
        ctx.arc(300,150,40,0,Math.PI*2,true);
        ctx.arc(-300,150,40,0,Math.PI*2,true);
        ctx.fillStyle = "pink";
        ctx.fill();
        // Nipple
        ctx.beginPath();
        ctx.arc(300,150,20,0,Math.PI*2,true);
        ctx.arc(-300,150,20,0,Math.PI*2,true);
        ctx.fillStyle = "brown";
        ctx.fill();
	}
	
	// Coordinates of handles (in canvas plane)
    this.handleArray[1].location = this.transform.transformPoint(new ED.Point(-400, -200));
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(400, -200));
	
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
ED.Breasts.prototype.description = function()
{
    var returnString = "Breast examination";
	
	return returnString;
}

/**
 * Returns the SnoMed code of the doodle
 *
 * @returns {Int} SnoMed code of entity representated by doodle
 */
ED.Breasts.prototype.snomedCode = function()
{
	return 232006002;
}

/**
 * Returns a number indicating position in a hierarchy of diagnoses from 0 to 9 (highest)
 *
 * @returns {Int} Position in diagnostic hierarchy
 */
ED.Breasts.prototype.diagnosticHierarchy = function()
{
	return 2;
}

/**
 * Scar on skin of breast
 *
 * @class Scar
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
ED.Scar = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Scar";
}

/**
 * Sets superclass and constructor
 */
ED.Scar.prototype = new ED.Doodle;
ED.Scar.prototype.constructor = ED.Scar;
ED.Scar.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Scar.prototype.setHandles = function()
{
	this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, true);
}

/**
 * Sets default dragging attributes
 */
ED.Scar.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = true;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.5, +1.5);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-40, +30);
}

/**
 * Sets default parameters
 */
ED.Scar.prototype.setParameterDefaults = function()
{
    this.originY = 46;
    this.originX = 292;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Scar.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Scar.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    ctx.moveTo(-100,-50);
    ctx.lineTo(100,-50);
    ctx.lineTo(100, 50);
    ctx.lineTo(-100, 50);            
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 0;
	ctx.fillStyle = "rgba(0, 0, 0, 0)";
	ctx.strokeStyle = "white";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        ctx.beginPath();
        ctx.lineWidth = 8;
        ctx.strokeStyle = "brown";
        ctx.moveTo(-100,0);
        ctx.lineTo(100,0);
        ctx.moveTo(-50,-50);
        ctx.lineTo(-50,50);        
        ctx.stroke();
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(100, -50));
	
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
ED.Scar.prototype.description = function()
{
    var returnString = "Surgical scar";
	
	return returnString;
}

/**
 * Returns the SnoMed code of the doodle
 *
 * @returns {Int} SnoMed code of entity representated by doodle
 */
ED.Scar.prototype.snomedCode = function()
{
	return 232006002;
}

/**
 * Returns a number indicating position in a hierarchy of diagnoses from 0 to 9 (highest)
 *
 * @returns {Int} Position in diagnostic hierarchy
 */
ED.Scar.prototype.diagnosticHierarchy = function()
{
	return 2;
}

/**
 * Breast lump
 *
 * @class Lump
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
ED.Lump = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Lump";
}

/**
 * Sets superclass and constructor
 */
ED.Lump.prototype = new ED.Doodle;
ED.Lump.prototype.constructor = ED.Lump;
ED.Lump.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Lump.prototype.setHandles = function()
{
	this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
}

/**
 * Sets default dragging attributes
 */
ED.Lump.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
	this.rangeOfScale = new ED.Range(+0.5, +2);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-40, +30);
}

/**
 * Sets default parameters
 */
ED.Lump.prototype.setParameterDefaults = function()
{
    this.originX = 258;
	this.originY = 114;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Lump.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Lump.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
	
	// Circle
	ctx.arc(0,0,30,0,Math.PI*2,true);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;
    ctx.fillStyle = "White";
	ctx.strokeStyle = "rgba(80, 40, 0, 1)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        ctx.beginPath();
        ctx.arc(0,0,4,0,Math.PI*2,true);
        ctx.fillStyle = "gray";
        ctx.fill();
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(30, -30));
	
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
ED.Lump.prototype.description = function()
{
    var returnString = "";
    
    // Description
	returnString += "Lump scar at ";
	
    // Location (clockhours)
	returnString += this.clockHour() + " o'clock";
	
	return returnString;
}

