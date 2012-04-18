/**
 * @fileOverview Contains doodle subclasses for adnexal
 * @author <a href="mailto:bill.aylward@mac.com">Bill Aylward</a>
 * @version 0.8
 *
 * Modification date: 28th Ootober 2011
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
 * Anterior segment with adjustable sized pupil
 *
 * @class AdnexalEye
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
ED.AdnexalEye = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "AdnexalEye";
}

/**
 * Sets superclass and constructor
 */
ED.AdnexalEye.prototype = new ED.Doodle;
ED.AdnexalEye.prototype.constructor = ED.AdnexalEye;
ED.AdnexalEye.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.AdnexalEye.prototype.setHandles = function()
{
	//this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.AdnexalEye.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
    this.isDeletable = false;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
	this.rangeOfScale = new ED.Range(+1, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-280, -60);
}

/**
 * Sets default parameters
 */
ED.AdnexalEye.prototype.setParameterDefaults = function()
{
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.AdnexalEye.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.AdnexalEye.superclass.draw.call(this, _point);
   
	// Boundary path
	ctx.beginPath();
    
	// Draw outline of eyelids
    var s = 1;
    ctx.moveTo(-400* s,0* s);
    ctx.bezierCurveTo(-400* s,-50* s,-200* s,-250* s,0* s,-250* s);
    ctx.bezierCurveTo(200* s,-250* s,300* s,-100* s,350* s,-50* s);
    ctx.bezierCurveTo(400* s,-60* s,440* s,-10* s,440* s,0* s);
    ctx.bezierCurveTo(440* s,10* s,400* s,50* s,350* s,50* s);
    ctx.bezierCurveTo(300* s,100* s,200* s,250* s,0* s,250* s);
    ctx.bezierCurveTo(-200* s,250* s,-400* s,50* s,-400* s,0* s);
	
	// Set line attributes
	ctx.lineWidth = 4;	
	ctx.fillStyle = "rgba(100, 200, 250, 0)";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Boundary path
        ctx.beginPath();
        
        // Do a 360 arc
        ctx.arc(0, 0, 190 * s, 0, 2 * Math.PI, true);
        
        // Move to inner circle
        ctx.moveTo(120 * s, 0);
        
        // Arc back the other way
        ctx.arc(0, 0, 120 * s,  2 * Math.PI, 0, false);
        
        // Set line attributes
        ctx.lineWidth = 4;	
        ctx.fillStyle = "rgba(100, 200, 250, 0.75)";
        ctx.strokeStyle = "gray";
        
        ctx.fill();
        ctx.stroke();
	}

	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
    
	// Return value indicating successful hit test
	return this.isClicked;
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.AdnexalEye.prototype.description = function()
{
    var returnString = this.getGrade() + " pupil";
    
    if (this.hasPXE) returnString += " with pseudoexfoliation";
	
	return returnString;
}

