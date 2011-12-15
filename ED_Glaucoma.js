/**
 * @fileOverview Contains doodle Subclasses for the adnexal drawing
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
 * Gonioscopy
 *
 * @class Gonioscopy
 * @property {String} className Name of doodle subclass
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
ED.Gonioscopy = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Gonioscopy";
    
    // Class specific property
    this.hasPXE = false;
}

/**
 * Sets superclass and constructor
 */
ED.Gonioscopy.prototype = new ED.Doodle;
ED.Gonioscopy.prototype.constructor = ED.Gonioscopy;
ED.Gonioscopy.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Gonioscopy.prototype.setHandles = function()
{
}

/**
 * Set default properties
 */
ED.Gonioscopy.prototype.setPropertyDefaults = function()
{
	this.isSelectable = false;
    this.isDeletable = false;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
    this.willReport = false;
}

/**
 * Sets default parameters
 */
ED.Gonioscopy.prototype.setParameterDefaults = function()
{
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Gonioscopy.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Gonioscopy.superclass.draw.call(this, _point);
    
	// Radii from out to in
    var rsc = 460;
    var rtm = 430;
    var rsp = 390;
    var rcb = 380
	var rir = 370;
    var rpu = 100;

	// Calculate parameters for arcs
	var arcStart = 0;
	var arcEnd = 2 * Math.PI;
    
	// Boundary path
	ctx.beginPath();
    
	// Do a 360 arc
	ctx.arc(0, 0, rsc, arcStart, arcEnd, true);
    
    // Move to inner circle
    ctx.moveTo(rpu, 0);
    
	// Arc back the other way
	ctx.arc(0, 0, rpu, arcEnd, arcStart, false);
	
	// Set line attributes
	ctx.lineWidth = 4;	
	ctx.fillStyle = "rgba(255, 255, 255, 0)";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Trabecular meshwork
        ctx.beginPath();
        
        // Arc across, move to inner and arc back
        ctx.arc(0, 0, rtm, arcStart, arcEnd, true);
        //ctx.moveTo(rsp, 0);        
        ctx.arc(0, 0, rsp, arcEnd, arcStart, false);
        
        // Set line attributes
        ctx.lineWidth = 1;
        
        // Create pattern
        var ptrn = ctx.createPattern(this.drawing.imageArray['meshworkPattern'],'repeat');
        ctx.fillStyle = ptrn;
        
        // Draw it
        ctx.strokeStyle = "lightgray";
        ctx.fill();
        ctx.stroke();

        // Ciliary Body
        ctx.beginPath();
        
        // Arc across, move to inner and arc back
        ctx.arc(0, 0, rcb, arcStart, arcEnd, true);       
        ctx.arc(0, 0, rir, arcEnd, arcStart, false);
        
        // Set fill attributes
        ctx.fillStyle = "rgba(200, 150, 100, 0.75)";
        
        // Draw it
        ctx.strokeStyle = "lightgray";
        ctx.fill();
        ctx.stroke();
        
        // Iris
        ctx.beginPath();
        
        // Arc across, move to inner and arc back
        ctx.arc(0, 0, rir, arcStart, arcEnd, true);       
        ctx.arc(0, 0, rpu, arcEnd, arcStart, false);
        
        // Set fill attributes (same colour as AntSeg)
        ctx.fillStyle = "rgba(100, 200, 250, 1.0)";
        
        // Draw it
        ctx.strokeStyle = "lightgray";
        ctx.fill();
	}
        
	// Return value indicating successful hit test
	return this.isClicked;
}

/**
 * Anterior Synechiae
 *
 * @class AntSynech
 * @property {String} className Name of doodle subclass
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
ED.AntSynech = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "AntSynech";
}

/**
 * Sets superclass and constructor
 */
ED.AntSynech.prototype = new ED.Doodle;
ED.AntSynech.prototype.constructor = ED.AntSynech;
ED.AntSynech.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.AntSynech.prototype.setHandles = function()
{
	this.handleArray[0] = new ED.Handle(null, true, ED.Mode.Arc, false);
	this.handleArray[3] = new ED.Handle(null, true, ED.Mode.Arc, false);
}

/**
 * Sets default dragging attributes
 */
ED.AntSynech.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.125, +1.5);
	this.rangeOfArc = new ED.Range(Math.PI/10, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(50, +250);
}

/**
 * Sets default parameters
 */
ED.AntSynech.prototype.setParameterDefaults = function()
{
    this.arc = 30 * Math.PI/180;
    
    // The radius property is changed by movement in rotatable doodles
    this.radius = 350;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.AntSynech.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.AntSynech.superclass.draw.call(this, _point);
    
    // AntSynech is at equator
    var ras = 420;
	var rir = 370;
    var r = rir + (ras - rir)/2;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    var outArcStart = - Math.PI/2 + theta - Math.PI/24;
    var outArcEnd = - Math.PI/2 - theta + Math.PI/24;
    
    // Coordinates of 'corners' of AntSynech
	var topRightX = rir * Math.sin(theta);
	var topRightY = - rir * Math.cos(theta);
	var topLeftX = - rir * Math.sin(theta);
	var topLeftY = topRightY;
    
	// Boundary path
	ctx.beginPath();
    
	// Path
	ctx.arc(0, 0, rir, arcStart, arcEnd, true);
	ctx.arc(0, 0, ras, outArcEnd, outArcStart, false);
    
	// Close path
	ctx.closePath();

    ctx.fillStyle = "rgba(100, 200, 250, 1.0)";
	ctx.strokeStyle = "rgba(255, 255, 255, 0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(new ED.Point(topLeftX, topLeftY));
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(topRightX, topRightY));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Returns a String which, if not empty, determines the root descriptions of multiple instances of the doodle
 *
 * @returns {String} Group description
 */
ED.AntSynech.prototype.groupDescription = function()
{
    // Calculate total extent in degrees
    var degrees = this.drawing.totalDegreesExtent(this.className);
    
    // Return string
    return "Anterior synechiae over " + degrees.toString() + " degrees";
}

/**
 * Angle New Vessels
 *
 * @class AngleNV
 * @property {String} className Name of doodle subclass
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
ED.AngleNV = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "AngleNV";
}

/**
 * Sets superclass and constructor
 */
ED.AngleNV.prototype = new ED.Doodle;
ED.AngleNV.prototype.constructor = ED.AngleNV;
ED.AngleNV.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.AngleNV.prototype.setHandles = function()
{
	this.handleArray[0] = new ED.Handle(null, true, ED.Mode.Arc, false);
	this.handleArray[3] = new ED.Handle(null, true, ED.Mode.Arc, false);
}

/**
 * Sets default dragging attributes
 */
ED.AngleNV.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.125, +1.5);
	this.rangeOfArc = new ED.Range(Math.PI/10, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(50, +250);
}

/**
 * Sets default parameters
 */
ED.AngleNV.prototype.setParameterDefaults = function()
{
    this.arc = 30 * Math.PI/180;
    
    // The radius property is changed by movement in rotatable doodles
    this.radius = 350;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.AngleNV.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.AngleNV.superclass.draw.call(this, _point);
    
    // AngleNV is at equator
    var ras = 420;
	var rir = 370;
    var r = rir + (ras - rir)/2;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    
    // Coordinates of 'corners' of AngleNV
	var topRightX = r * Math.sin(theta);
	var topRightY = - r * Math.cos(theta);
	var topLeftX = - r * Math.sin(theta);
	var topLeftY = topRightY;
    
	// Boundary path
	ctx.beginPath();
    
	// Path
	ctx.arc(0, 0, rir, arcStart, arcEnd, true);
	ctx.arc(0, 0, ras, arcEnd, arcStart, false);
    
	// Close path
	ctx.closePath();
    
    // create pattern
    var ptrn = ctx.createPattern(this.drawing.imageArray['newVesselPattern'],'repeat');
    ctx.fillStyle = ptrn;
	ctx.strokeStyle = "rgba(255, 255, 255, 0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(new ED.Point(topLeftX, topLeftY));
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(topRightX, topRightY));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Returns a String which, if not empty, determines the root descriptions of multiple instances of the doodle
 *
 * @returns {String} Group description
 */
ED.AngleNV.prototype.groupDescription = function()
{
    // Calculate total extent in degrees
    var degrees = this.drawing.totalDegreesExtent(this.className);
    
    // Return string
    return "Angle new vessels over " + degrees.toString() + " degrees";
}

/**
 * Angle Recession
 *
 * @class AngleRecession
 * @property {String} className Name of doodle subclass
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
ED.AngleRecession = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "AngleRecession";
}

/**
 * Sets superclass and constructor
 */
ED.AngleRecession.prototype = new ED.Doodle;
ED.AngleRecession.prototype.constructor = ED.AngleRecession;
ED.AngleRecession.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.AngleRecession.prototype.setHandles = function()
{
	this.handleArray[0] = new ED.Handle(null, true, ED.Mode.Arc, false);
	this.handleArray[3] = new ED.Handle(null, true, ED.Mode.Arc, false);
}

/**
 * Sets default dragging attributes
 */
ED.AngleRecession.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.125, +1.5);
	this.rangeOfArc = new ED.Range(Math.PI/10, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(50, +250);
}

/**
 * Sets default parameters
 */
ED.AngleRecession.prototype.setParameterDefaults = function()
{
    this.arc = 30 * Math.PI/180;
    
    // The radius property is changed by movement in rotatable doodles
    this.radius = 350;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.AngleRecession.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.AngleRecession.superclass.draw.call(this, _point);
    
    // AngleRecession is at equator
    var ras = 350;
	var rir = 370;
    var r = rir + (ras - rir)/2;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    var outArcStart = - Math.PI/2 + theta - Math.PI/24;
    var outArcEnd = - Math.PI/2 - theta + Math.PI/24;
    
    // Coordinates of 'corners' of AngleRecession
	var topRightX = rir * Math.sin(theta);
	var topRightY = - rir * Math.cos(theta);
	var topLeftX = - rir * Math.sin(theta);
	var topLeftY = topRightY;
    
	// Boundary path
	ctx.beginPath();
    
	// Path
	ctx.arc(0, 0, rir, arcStart, arcEnd, true);
	ctx.arc(0, 0, ras, outArcEnd, outArcStart, false);
    
	// Close path
	ctx.closePath();
    
    ctx.fillStyle = "rgba(255, 255, 255, 1.0)";
	ctx.strokeStyle = "rgba(255, 255, 255, 0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(new ED.Point(topLeftX, topLeftY));
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(topRightX, topRightY));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Returns a String which, if not empty, determines the root descriptions of multiple instances of the doodle
 *
 * @returns {String} Group description
 */
ED.AngleRecession.prototype.groupDescription = function()
{
    // Calculate total extent in degrees
    var degrees = this.drawing.totalDegreesExtent(this.className);
    
    // Return string
    return "Angle recession over " + degrees.toString() + " degrees";
}

/**
 * PigmentedMesh
 *
 * @class PigmentedMesh
 * @property {String} className Name of doodle subclass
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
ED.PigmentedMesh = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "PigmentedMesh";
    
    // Class specific property
    this.hasPXE = false;
}

/**
 * Sets superclass and constructor
 */
ED.PigmentedMesh.prototype = new ED.Doodle;
ED.PigmentedMesh.prototype.constructor = ED.PigmentedMesh;
ED.PigmentedMesh.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.PigmentedMesh.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.PigmentedMesh.prototype.setPropertyDefaults = function()
{
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-350, -150);
}

/**
 * Sets default parameters
 */
ED.PigmentedMesh.prototype.setParameterDefaults = function()
{
    this.apexY = -250;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.PigmentedMesh.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.PigmentedMesh.superclass.draw.call(this, _point);
    
	// Radii from out to in
    var rtm = 430;
    var rsp = 390;
    
	// Calculate parameters for arcs
	var arcStart = 0;
	var arcEnd = 2 * Math.PI;
    
	// Boundary path
	ctx.beginPath();

    // Arc across, move to inner and arc back
    ctx.arc(0, 0, rtm, arcStart, arcEnd, true);    
    ctx.arc(0, 0, rsp, arcEnd, arcStart, false);
	
	// Set attributes
	ctx.lineWidth = 1;
    var opacity = 0.1 + (-150 - this.apexY)/400;
	ctx.fillStyle = "rgba(150, 100, 50, " + opacity.toString() + ")";
	ctx.strokeStyle = "rgba(255, 255, 255, 0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}

    // Coordinates of handles (in canvas plane)
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));

	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
    
	// Return value indicating successful hit test
	return this.isClicked;
}

/**
 * Returns a string containing a text description of the doodle (overridden by subclasses)
 *
 * @returns {String} Description of doodle
 */
ED.PigmentedMesh.prototype.description = function()
{
    if (this.apexY > -225) return "Lightly pigmented meshwork";
    if (this.apexY > -300) return "Moderately pigmented meshwork";
    else return "Heavily pigmented meshwork";
}
