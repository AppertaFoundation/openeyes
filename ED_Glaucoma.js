/**
 * @fileOverview Contains doodle subclasses for glaucoma
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
 * Radii from out to in (mainly for gonioscopy)
 * @ignore
 */
var rsl = 480;
var rsli = 470;
var rtmo = 404;
var rtmi = 304;
var rcbo = 270;
var rcbi = 190;
var riro = 190;
var riri = 176;
var rpu = 100;


/**
 * Anterior segment with moveable sized pupil for glaucoma
 *
 * @class Iris
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
ED.Iris = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Iris";
}

/**
 * Sets superclass and constructor
 */
ED.Iris.prototype = new ED.Doodle;
ED.Iris.prototype.constructor = ED.Iris;
ED.Iris.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Iris.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.Iris.prototype.setPropertyDefaults = function()
{
    this.isDeletable = false;
    this.isMoveable = false;
    this.isRotatable = false;
    this.isUnique = true;
    this.rangeOfApexX = new ED.Range(-0, +0);
    this.rangeOfApexY = new ED.Range(-280, -240);
}

/**
 * Sets default parameters
 */
ED.Iris.prototype.setParameterDefaults = function()
{
	this.apexY = -280;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Iris.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Iris.superclass.draw.call(this, _point);
    
	// Radius of limbus
	var ro = 280;

	// Boundary path
	ctx.beginPath();
	ctx.arc(0, 0, ro, 0,  2 * Math.PI, true);
	
	// Set attributes
	ctx.lineWidth = 4;	
	ctx.fillStyle = "rgba(100, 200, 250, 0.75)";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Posterior embryotoxon
        ctx.beginPath();
        ctx.arc(0, 0, -this.apexY, 0,  2 * Math.PI, true);
        
        ctx.lineWidth = 8;	
        ctx.strokeStyle = "rgba(255, 255, 255, 0.75)";
        
        // Only draw it if it is smaller than iris
        if (this.apexY > -ro)
        {
            ctx.stroke();
            
            // Axenfeld's anomaly
            if (this.apexY > -250)
            {
                ctx.beginPath();
                var n = 12;
                for (var i = 0; i < n; i++)
                {
                    var angle = i * 2 * Math.PI/n;
                    var startPoint = new ED.Point(0,0);
                    startPoint.setWithPolars(-this.apexY, angle);
                    var endPoint = new ED.Point(0,0);
                    endPoint.setWithPolars(270, angle);
                    
                    ctx.moveTo(startPoint.x, startPoint.y);
                    ctx.lineTo(endPoint.x, endPoint.y);
                }
                
                ctx.stroke();
            }
            
        }
	}
    
	// Coordinates of handles (in canvas plane)
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
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
ED.Iris.prototype.description = function()
{
    var returnString = "";
    
    // Size description
    if (this.apexY > -280) returnString += "Posterior embryotoxon";
    if (this.apexY > -250) returnString += " with Axenfeld's anomaly ";
	
	return returnString;
}

/**
 * A defomrable and moveable pupil
 *
 * @class Pupil
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
ED.Pupil = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Pupil";
}

/**
 * Sets superclass and constructor
 */
ED.Pupil.prototype = new ED.Doodle;
ED.Pupil.prototype.constructor = ED.Pupil;
ED.Pupil.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Pupil.prototype.setHandles = function()
{
    this.handleArray[3] = new ED.Handle(null, true, ED.Mode.Scale, true);
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Pupil.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
    this.isDeletable = false;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfOriginX = new ED.Range(-90, +90);
	this.rangeOfOriginY = new ED.Range(-90, +90);
	this.rangeOfScale = new ED.Range(+0.5, +1.5);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-240, -150);
}

/**
 * Sets default parameters
 */
ED.Pupil.prototype.setParameterDefaults = function()
{
	this.apexY = -150;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Pupil.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Pupil.superclass.draw.call(this, _point);

    // Radius of 'circle'
    var r = 150;
    var rs = - this.apexY;
    var f = 0.55;   // Gives a circular bezier curve
    
	// Boundary path
	ctx.beginPath(); 
	
	// Pupil
    ctx.moveTo(0, -rs);
    ctx.bezierCurveTo(r * f, -rs, r, -r * f, r, 0);
    ctx.bezierCurveTo(r, r * f, r * f, r, 0, r); 
    ctx.bezierCurveTo(-r * f, r, -r, r * f, -r, 0);
    ctx.bezierCurveTo(-r, -rs * f, -r * f, -rs, 0, -rs);
	
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
    ctx.fillStyle = "white";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(r, 0));
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
ED.Pupil.prototype.description = function()
{
    var returnString = "";
    
    // Size description
    if (this.originX * this.originX > 0 || this.originY * this.originY > 0) returnString = "Correctopia ";
	
	return returnString;
}

/**
 * Gonioscopy
 *
 * @class Gonioscopy
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
ED.Gonioscopy = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
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
    this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.Gonioscopy.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
    this.isDeletable = false;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
    this.rangeOfApexX = new ED.Range(-460, -420);
    this.rangeOfApexY = new ED.Range(-460, -400);
}

/**
 * Sets default parameters
 */
ED.Gonioscopy.prototype.setParameterDefaults = function()
{
    this.apexX = -460;
    this.apexY = -460;
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

	// Calculate parameters for arcs
	var arcStart = 0;
	var arcEnd = 2 * Math.PI;
    
	// Boundary path
	ctx.beginPath();
    
	// Do a 360 arc
	ctx.arc(0, 0, rsl, arcStart, arcEnd, true);
    
    // Move to inner circle
    //ctx.moveTo(rpu, 0);
    
	// Arc back the other way
	//ctx.arc(0, 0, rpu, arcEnd, arcStart, false);
	
	// Set line attributes
	ctx.lineWidth = 15;	
	ctx.fillStyle = "rgba(255, 255, 255, 0)";
	ctx.strokeStyle = "rgba(200, 200, 200, 1)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Trabecular meshwork
        ctx.beginPath();
        
        // Arc across, move to inner and arc back
        ctx.arc(0, 0, rtmo, arcStart, arcEnd, true);
        ctx.moveTo(rtmi, 0);
        ctx.arc(0, 0, rtmi, arcEnd, arcStart, false);
        
        // Set line attributes
        ctx.lineWidth = 1;
        
        // Fill style
        var ptrn;
        
        // Pattern
        if (this.apexX < -440)
        {
            if (this.apexY < -440) ptrn = ctx.createPattern(this.drawing.imageArray['MeshworkPatternLight'],'repeat');
            else if (this.apexY < -420) ptrn = ctx.createPattern(this.drawing.imageArray['MeshworkPatternMedium'],'repeat');
            else ptrn = ctx.createPattern(this.drawing.imageArray['MeshworkPatternHeavy'],'repeat');
            ctx.fillStyle = ptrn;
        }
        // Uniform
        else
        {
            if (this.apexY < -440) ctx.fillStyle = "rgba(250, 200, 0, 1)";
            else if (this.apexY < -420) ctx.fillStyle = "rgba(200, 150, 0, 1)";
            else ctx.fillStyle = "rgba(150, 100, 0, 1)";            
        }
        
        // Stroke style
        ctx.strokeStyle = "rgba(200, 200, 200, 1)";
        
        // Draw it
        ctx.fill();
        ctx.stroke();

        // Ciliary Body
        ctx.beginPath();
        
        // Arc across, move to inner and arc back
        ctx.arc(0, 0, rcbo, arcStart, arcEnd, true);       
        ctx.arc(0, 0, rcbi, arcEnd, arcStart, false);

        // Draw it
        ctx.fillStyle = "rgba(200, 200, 200, 1)";
        ctx.fill();

        // Draw radial lines
        var firstAngle = 15;
        var innerPoint = new ED.Point(0,0);
        var outerPoint = new ED.Point(0,0);
        var i = 0;
        
        // Loop through clock face
        for (i = 0; i < 12; i++)
        {
            // Get angle
            var angleInRadians = (firstAngle + i * 30) * Math.PI/180;
            innerPoint.setWithPolars(rcbi, angleInRadians);
            
            // Set new line
            ctx.beginPath();
            ctx.moveTo(innerPoint.x, innerPoint.y);
            
            // Some lines are longer, wider and darker
            if (i == 1 || i == 4 || i == 7 || i == 10)
            {  
                outerPoint.setWithPolars(rsl + 80, angleInRadians);
                ctx.lineWidth = 6;
                ctx.strokeStyle = "rgba(20, 20, 20, 1)";
            }
            else
            {
                outerPoint.setWithPolars(rsl, angleInRadians);
                ctx.lineWidth = 2;
                ctx.strokeStyle = "rgba(137, 137, 137, 1)";
            }

            // Draw line
            ctx.lineTo(outerPoint.x, outerPoint.y);
            ctx.closePath();
            ctx.stroke();
        }
        
        // Iris
        ctx.beginPath();
        
        // Arc across, move to inner and arc back
        ctx.arc(0, 0, riro, arcStart, arcEnd, true);       
        ctx.arc(0, 0, riri, arcEnd, arcStart, false);
        
        // Set attributes
        ctx.lineWidth = 2;
        ctx.strokeStyle = "rgba(180, 180, 180, 1)";
        ctx.fillStyle = "rgba(200, 200, 200, 1)";
        
        // Draw it
        ctx.fill();
        ctx.stroke();
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
ED.Gonioscopy.prototype.description = function()
{
    var returnValue = "";
    if (this.apexX < -440)
    {
        if (this.apexY < -440) returnValue = "Light patchy pigment";
        else if (this.apexY < -420) returnValue = "Medium patchy pigment";
        else returnValue = "Heavy patchy pigment";
    }
    // Uniform
    else
    {
        if (this.apexY < -440) returnValue = "Light homogenous pigment";
        else if (this.apexY < -420) returnValue = "Medium homogenous pigment";
        else returnValue = "Heavy homogenous pigment";           
    }

    
    return returnValue;
}

/**
 * Anterior Synechiae
 *
 * @class AntSynech
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
ED.AntSynech = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
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
    this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
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
	this.rangeOfArc = new ED.Range(30 * Math.PI/180, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-rsli, -rcbo);
}

/**
 * Sets default parameters
 */
ED.AntSynech.prototype.setParameterDefaults = function()
{
    this.arc = 30 * Math.PI/180;
    this.apexY = -rtmi;
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
    var ras = -this.apexY;
	var rir = riri;
    
    var r = rir + (ras - rir)/2;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    var outArcStart = - Math.PI/2 + theta - Math.PI/14;
    var outArcEnd = - Math.PI/2 - theta + Math.PI/14;
    
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
    this.handleArray[4].location = this.transform.transformPoint(new ED.Point(0, this.apexY));
	
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
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.AngleNV = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
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
    var ras = rtmo;
	var rir = rtmi;
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
    var ptrn = ctx.createPattern(this.drawing.imageArray['NewVesselPattern'],'repeat');
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
 * @param {Float} _radius
 * @param {Int} _apexX
 * @param {Int} _apexY
 * @param {Float} _scaleX
 * @param {Float} _scaleY
 * @param {Float} _arc
 * @param {Float} _rotation
 * @param {Int} _order
 */
ED.AngleRecession = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
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
    var ras = riri - 30;
	var rir = riri;
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
    
    ctx.fillStyle = "rgba(255, 255, 200, 1.0)";
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
 * AngleGrade
 *
 * @class AngleGrade
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
ED.AngleGrade = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "AngleGrade";
}

/**
 * Sets superclass and constructor
 */
ED.AngleGrade.prototype = new ED.Doodle;
ED.AngleGrade.prototype.constructor = ED.AngleGrade;
ED.AngleGrade.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.AngleGrade.prototype.setHandles = function()
{   
    this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.AngleGrade.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
    this.isDeletable = false;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
	this.rangeOfScale = new ED.Range(+0.125, +1.5);
	this.rangeOfArc = new ED.Range(Math.PI/12, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-rsli, -riri);
}

/**
 * Sets default parameters
 */
ED.AngleGrade.prototype.setParameterDefaults = function()
{
    this.rotation = 0 * Math.PI/180;
    this.arc = 90 * Math.PI/180;
    this.apexY = -riri;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.AngleGrade.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.AngleGrade.superclass.draw.call(this, _point);
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    
	// Boundary path
	ctx.beginPath();
    
    // Arc across, move to inner and arc back
	ctx.arc(0, 0, -this.apexY, arcStart, arcEnd, true);
	ctx.arc(0, 0, rpu, arcEnd, arcStart, false);
    ctx.closePath();
    
    // Set fill attributes (same colour as Iris)
    ctx.fillStyle = "rgba(100, 200, 250, 1.0)";
	ctx.strokeStyle = "rgba(100, 100, 100, 1.0)";
    ctx.lineWidth = 4;
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
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
 * Returns parameters
 *
 * @returns {String} value of parameter
 */
ED.AngleGrade.prototype.getParameter = function(_parameter)
{
    var returnValue;
    
    switch (_parameter)
    {
        case 'grade':
            // Return value uses SCHEIE classificaton
            returnValue = "O";
            if (-this.apexY > riro) returnValue = "I";
            if (-this.apexY > rcbo) returnValue = "II";
            if (-this.apexY > rtmo) returnValue = "III";
            if (-this.apexY >= rsli) returnValue = "IV";
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
ED.AngleGrade.prototype.setParameter = function(_parameter, _value)
{
    switch (_parameter)
    {
        case 'grade':
            switch (_value)
            {
                case 'IV':
                    this.apexY = -rsli;
                    break;
                case 'III':
                    this.apexY = -rtmo;
                    break;
                case 'II':
                    this.apexY = -rcbo;
                    break;
                case 'I':
                    this.apexY = -riro;
                    break;
                case 'O':
                    this.apexY = -riri;
                    break;
                default:
                    this.apexY = -riri;
                    break;
            }
            break;
            
        default:
            break
    }
}

/**
 * The optic disk (used in conjunection with the optic cup)
 *
 * @class OpticDisk
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
ED.OpticDisk = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "OpticDisk";
    
    // Create a squiggle to store the four corner points
    var squiggle = new ED.Squiggle(this, new ED.Colour(100, 100, 100, 1), 4, true);
    
    // Add it to squiggle array
    this.squiggleArray.push(squiggle);
    
    // Add four points to the squiggle
    this.addPointToSquiggle(new ED.Point(-this.radius, 0));
    this.addPointToSquiggle(new ED.Point(0, -this.radius));
    this.addPointToSquiggle(new ED.Point(this.radius, 0));
    this.addPointToSquiggle(new ED.Point(0, this.radius));
}

/**
 * Sets superclass and constructor
 */
ED.OpticDisk.prototype = new ED.Doodle;
ED.OpticDisk.prototype.constructor = ED.OpticDisk;
ED.OpticDisk.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.OpticDisk.prototype.setHandles = function()
{
    this.handleArray[0] = new ED.Handle(null, true, ED.Mode.Handles, false);
    this.handleArray[1] = new ED.Handle(null, true, ED.Mode.Handles, false);
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Handles, false);
    this.handleArray[3] = new ED.Handle(null, true, ED.Mode.Handles, false);
}

/**
 * Sets default dragging attributes
 */
ED.OpticDisk.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
    this.isDeletable = false;
    
    var max = this.radius * 1.5;
    var min = this.radius;
    this.rangeOfHandlesXArray[0] = new ED.Range(-max, -min);
    this.rangeOfHandlesYArray[0] = new ED.Range(-0, +0);
    this.rangeOfHandlesXArray[1] = new ED.Range(-0, +0);
    this.rangeOfHandlesYArray[1] = new ED.Range(-max, -min);
    this.rangeOfHandlesXArray[2] = new ED.Range(min, max);
    this.rangeOfHandlesYArray[2] = new ED.Range(-0, +0);
    this.rangeOfHandlesXArray[3] = new ED.Range(-0, +0);
    this.rangeOfHandlesYArray[3] = new ED.Range(min, max);
}

/**
 * Sets default parameters
 */
ED.OpticDisk.prototype.setParameterDefaults = function()
{    
    this.radius = 300;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.OpticDisk.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.OpticDisk.superclass.draw.call(this, _point);
    
	// Boundary path
	ctx.beginPath();
	
	// OpticDisk
    var f = 0.55;   // Gives a circular bezier curve
    var fromX;
    var fromY;
    var toX;
    var toY;
    
    // Top left curve
    fromX = this.squiggleArray[0].pointsArray[0].x;
    fromY = this.squiggleArray[0].pointsArray[0].y;
    toX = this.squiggleArray[0].pointsArray[1].x;
    toY = this.squiggleArray[0].pointsArray[1].y;
    ctx.moveTo(fromX, fromY);
    ctx.bezierCurveTo(fromX, fromX * f, toY * f, toY, toX, toY);
    
    // Top right curve
    fromX = toX;
    fromY = toY;
    toX = this.squiggleArray[0].pointsArray[2].x;
    toY = this.squiggleArray[0].pointsArray[2].y;
    ctx.bezierCurveTo(-fromY * f, fromY, toX, -toX * f, toX, toY);    
    
    // Bottom right curve
    fromX = toX;
    fromY = toY;
    toX = this.squiggleArray[0].pointsArray[3].x;
    toY = this.squiggleArray[0].pointsArray[3].y;
    ctx.bezierCurveTo(fromX, fromX * f, toY * f, toY, toX, toY);
    
    // Bottom left curve
    fromX = toX;
    fromY = toY;
    toX = this.squiggleArray[0].pointsArray[0].x;
    toY = this.squiggleArray[0].pointsArray[0].y;
    ctx.bezierCurveTo(-fromY * f, fromY, toX, -toX * f, toX, toY);

    // Only fill to margin, to allow cup to sit behind giving disk margin
    ctx.moveTo(280, 00);
    ctx.arc(0, 0, 280, 0, Math.PI*2, true);
    
	// Close path
	ctx.closePath();
	
	// Set attributes
	ctx.lineWidth = 2;
    var colour = new ED.Colour(0,0,0,1);
    colour.setWithHexString('DFD989');
    ctx.fillStyle = colour.rgba();
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Disc vessels
        ctx.beginPath();
        
        // Vessels start on nasal side of disk
        var sign;
        if (this.drawing.eye == ED.eye.Right)
        {
            sign = -1;
        }
        else
        {
            sign = 1;
        }
        
        // Superotemporal vessel
        var startPoint = new ED.Point(0,0);
        startPoint.setWithPolars(150, - sign * Math.PI/2);
        
        var controlPoint1 = new ED.Point(0,0);
        controlPoint1.setWithPolars(400, - sign * Math.PI/8);
        var controlPoint2 = new ED.Point(0,0);
        controlPoint2.setWithPolars(450, sign * Math.PI/8);
        
        var endPoint = new ED.Point(0,0);
        endPoint.setWithPolars(500, sign * Math.PI/4);
        
        ctx.moveTo(startPoint.x, startPoint.y);
        ctx.bezierCurveTo(controlPoint1.x, controlPoint1.y, controlPoint2.x, controlPoint2.y, endPoint.x, endPoint.y);
        
        // Inferotemporal vessel
        var startPoint = new ED.Point(0,0);
        startPoint.setWithPolars(150, - sign * Math.PI/2);
        
        var controlPoint1 = new ED.Point(0,0);
        controlPoint1.setWithPolars(400, - sign * 7 * Math.PI/8);
        var controlPoint2 = new ED.Point(0,0);
        controlPoint2.setWithPolars(450, sign * 7 * Math.PI/8);
        
        var endPoint = new ED.Point(0,0);
        endPoint.setWithPolars(500, sign * 3 * Math.PI/4);
        
        ctx.moveTo(startPoint.x, startPoint.y);
        ctx.bezierCurveTo(controlPoint1.x, controlPoint1.y, controlPoint2.x, controlPoint2.y, endPoint.x, endPoint.y);
        
        // Superonasal vessel
        var startPoint = new ED.Point(0,0);
        startPoint.setWithPolars(150, - sign * Math.PI/2);
        
        var controlPoint1 = new ED.Point(0,0);
        controlPoint1.setWithPolars(300, - sign * 2 *  Math.PI/8);
        var controlPoint2 = new ED.Point(0,0);
        controlPoint2.setWithPolars(350, -sign * 5 * Math.PI/16);
        
        var endPoint = new ED.Point(0,0);
        endPoint.setWithPolars(450, - sign * 3 * Math.PI/8);
        
        ctx.moveTo(startPoint.x, startPoint.y);
        ctx.bezierCurveTo(controlPoint1.x, controlPoint1.y, controlPoint2.x, controlPoint2.y, endPoint.x, endPoint.y);
        
        // Inferonasal vessel
        var startPoint = new ED.Point(0,0);
        startPoint.setWithPolars(150, - sign * Math.PI/2);
        
        var controlPoint1 = new ED.Point(0,0);
        controlPoint1.setWithPolars(300, - sign * 6 *  Math.PI/8);
        var controlPoint2 = new ED.Point(0,0);
        controlPoint2.setWithPolars(350, -sign * 11 * Math.PI/16);
        
        var endPoint = new ED.Point(0,0);
        endPoint.setWithPolars(450, - sign * 5 * Math.PI/8);
        
        ctx.moveTo(startPoint.x, startPoint.y);
        ctx.bezierCurveTo(controlPoint1.x, controlPoint1.y, controlPoint2.x, controlPoint2.y, endPoint.x, endPoint.y);
        
        // Line attributes
        ctx.lineWidth = 48;
        ctx.lineCap = "round";
        ctx.strokeStyle = "rgba(255, 0, 0, 0.5)";
        
        // Draw line
        ctx.stroke();
	}
    
	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(this.squiggleArray[0].pointsArray[0]);
	this.handleArray[1].location = this.transform.transformPoint(this.squiggleArray[0].pointsArray[1]);
	this.handleArray[2].location = this.transform.transformPoint(this.squiggleArray[0].pointsArray[2]);
	this.handleArray[3].location = this.transform.transformPoint(this.squiggleArray[0].pointsArray[3]);
    
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
ED.OpticDisk.prototype.description = function()
{
    var returnString = "";
	
    // Get distances of control points from centre
    var left = - this.squiggleArray[0].pointsArray[0].x;
    var top = - this.squiggleArray[0].pointsArray[1].y;
    var right = this.squiggleArray[0].pointsArray[2].x;
    var bottom = this.squiggleArray[0].pointsArray[3].y;
    
    // Get maximum control point, and its sector
    var sector = "";
    var max = this.radius;
    if (left > max)
    {
        max = left;
        sector = (this.drawing.eye == ED.eye.Right)?"temporally":"nasally";
    }
    if (top > max)
    {
        max = top;
        sector = "superiorly";
    }
    if (right > max)
    {
        max = right;
        sector = (this.drawing.eye == ED.eye.Right)?"nasally":"temporally";
    }
    if (bottom > max)
    {
        max = bottom;
        sector = "inferiorly";
    }  
    
    // Grade degree of atrophy
    if (max > this.radius)
    {
        var degree = "Mild";
        if (max > 350) degree = "Moderate";
        if (max > 400) degree = "Signficant";
        returnString += degree;
        returnString += " peri-papillary atrophy, maximum ";
        returnString += sector;
    }
	
	return returnString;
}

/**
 * The optic cup (used in conjunection with the optic disk
 *
 * @class OpticCup
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
ED.OpticCup = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
    // Number of handles (set before superclass call because superclass calles setHandles())
    this.numberOfHandles = 8;
    
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "OpticCup";
    
    // Create a squiggle to store the handles points
    var squiggle = new ED.Squiggle(this, new ED.Colour(100, 100, 100, 1), 4, true);
    
    // Add it to squiggle array
    this.squiggleArray.push(squiggle);

    // Flag to simplify sizing of cup
    this.isBasic = true;
    
    // Toggle function loads points if required
    this.setHandleProperties();
}

/**
 * Sets superclass and constructor
 */
ED.OpticCup.prototype = new ED.Doodle;
ED.OpticCup.prototype.constructor = ED.OpticCup;
ED.OpticCup.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.OpticCup.prototype.setHandles = function()
{
    // Array of handles for expert mode
    for (var i = 0; i < this.numberOfHandles; i++)
    {
        this.handleArray[i] = new ED.Handle(null, true, ED.Mode.Handles, false);
    }
    
    // Apex handle for basic mode
    this.handleArray[this.numberOfHandles] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.OpticCup.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isDeletable = false;

    // Create ranges for handle array
    for (var i = 0; i < this.numberOfHandles; i++)
    {
        this.rangeOfHandlesXArray[i] = new ED.Range(-500, +500);
        this.rangeOfHandlesYArray[i] = new ED.Range(-500, +500);
    }
}

/**
 * Sets default parameters
 */
ED.OpticCup.prototype.setParameterDefaults = function()
{    
    this.radius = 200;
    this.apexY = -150;
    this.rangeOfRadius = new ED.Range(50, 280);
    this.rangeOfApexY = new ED.Range(-280, -20);
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.OpticCup.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.OpticCup.superclass.draw.call(this, _point);

    // Draw background first
    if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Disk margin
        ctx.beginPath();
        ctx.arc(0, 0, 300, 0, Math.PI*2, true);
        ctx.closePath();
        
        // Set attributes
        ctx.lineWidth = 2;
        var colour = new ED.Colour(0,0,0,1);
        colour.setWithHexString('FFA83C');  // Taken from disk margin of a fundus photo
        ctx.fillStyle = colour.rgba();
        ctx.strokeStyle = "gray";
        
        // Draw disk margin
        ctx.fill();
        ctx.stroke();
    }

	// Boundary path
	ctx.beginPath();
	
    if (this.isBasic)
    {
        // Round cup
        ctx.arc(0, 0, -this.apexY, 0, Math.PI*2, true);        
    }
    else
    {
        // Bezier points
        var fp;
        var tp;
        var cp1;
        var cp2;

        // Angle of control point from radius line to point (this value makes path a circle Math.PI/12 for 8 points
        var phi = 2 * Math.PI/(3 * this.numberOfHandles);

        // Start curve
        ctx.moveTo(this.squiggleArray[0].pointsArray[0].x, this.squiggleArray[0].pointsArray[0].y);
        
        // Complete curve segments
        for (var i = 0; i < this.numberOfHandles; i++)
        {
            // From and to points
            fp = this.squiggleArray[0].pointsArray[i];
            var toIndex = (i < this.numberOfHandles - 1)?i + 1:0;
            tp = this.squiggleArray[0].pointsArray[toIndex];
            
            // Control points
            cp1 = fp.tangentialControlPoint(+phi);
            cp2 = tp.tangentialControlPoint(-phi);
            
            // Draw Bezier curve
            ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, tp.x, tp.y);
        }
    }
    
    
	// Close path
	ctx.closePath();
    
	// Set line attributes
	ctx.lineWidth = 2;
    var ptrn = ctx.createPattern(this.drawing.imageArray['CribriformPattern'],'repeat');
    ctx.fillStyle = ptrn;
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
    
	// Coordinates of handles (in canvas plane)
    if (this.isBasic)
    {
        // Location of apex handle and visibility on
        this.handleArray[this.numberOfHandles].location = this.transform.transformPoint(new ED.Point(0, this.apexY));
    }
    else
    {
        for (var i = 0; i < this.numberOfHandles; i++)
        {
            this.handleArray[i].location = this.transform.transformPoint(this.squiggleArray[0].pointsArray[i]);
        }
    }
    
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
ED.OpticCup.prototype.description = function()
{
    var returnString = "";
    var ratio = 0;
    
	returnString += "Cup/disk ratio: ";
	
    if (this.isBasic)
    {
        ratio = Math.round(10 * -this.apexY/300)/10;
    }
    else
    {
        // Sum distances of control points from centre
        var sum = 0;
        for (var i = 0; i < this.numberOfHandles; i++)
        {            
            sum += this.squiggleArray[0].pointsArray[i].length();
        }
        
        // 
        ratio = Math.round(10 * sum/(300 * this.numberOfHandles))/10;
    }
    
    returnString += ratio.toString();
	
	return returnString;
}

/**
 * Toggles state of doodle from basic to expert mode, setting handle visibility and coordinates accordingly
 */
ED.OpticCup.prototype.toggleMode = function()
{
    // Toggle value
    this.isBasic = this.isBasic?false:true;
}

/**
 * Defines handles coordinates and visibility
 */
ED.OpticCup.prototype.setHandleProperties = function()
{
    // Going from basic to expert
    if (!this.isBasic)
    {
        // Clear array
        this.squiggleArray[0].pointsArray.length = 0;
        
        // Populate with handles at equidistant points around circumference
        for (var i = 0; i < this.numberOfHandles; i++)
        {
            var point = new ED.Point(0, 0);
            point.setWithPolars(-this.apexY, i * 2 * Math.PI/this.numberOfHandles);
            this.addPointToSquiggle(point);
        }
        
        // Make handles visible, except for apex handle
        for (var i = 0; i < this.numberOfHandles; i++)
        {
            this.handleArray[i].isVisible = true;
        }
        this.handleArray[this.numberOfHandles].isVisible = false;
        
    }
    // Going from expert to basic
    else
    {
        // Make handles invisible, except for apex handle
        for (var i = 0; i < this.numberOfHandles; i++)
        {
            this.handleArray[i].isVisible = false;
        }
        this.handleArray[this.numberOfHandles].isVisible = true;        
    }
}

/**
 * NerveFibreDefect
 *
 * @class NerveFibreDefect
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
ED.NerveFibreDefect = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call super-class constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order); 
	
	// Set classname
	this.className = "NerveFibreDefect";
}

/**
 * Sets superclass and constructor
 */
ED.NerveFibreDefect.prototype = new ED.Doodle;
ED.NerveFibreDefect.prototype.constructor = ED.NerveFibreDefect;
ED.NerveFibreDefect.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.NerveFibreDefect.prototype.setHandles = function()
{
	this.handleArray[0] = new ED.Handle(null, true, ED.Mode.Arc, false);
	this.handleArray[3] = new ED.Handle(null, true, ED.Mode.Arc, false);
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.NerveFibreDefect.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.25, +4);
	this.rangeOfArc = new ED.Range(Math.PI/8, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-560, -400);
}

/**
 * Sets default parameters
 */
ED.NerveFibreDefect.prototype.setParameterDefaults = function()
{
    this.arc = 20 * Math.PI/180;
    this.apexY = -460;
    //this.rotation = (this.drawing.eye == ED.eye.Right)?-Math.PI/4:Math.PI/4;
    this.rotation = Math.PI/4;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.NerveFibreDefect.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.NerveFibreDefect.superclass.draw.call(this, _point);
    
	// Radius of outer curve
	var ro = -this.apexY;
    var ri = 360;
    var r = ri + (ro - ri)/2;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    
    // Coordinates of 'corners' of NerveFibreDefect
	var topRightX = r * Math.sin(theta);
	var topRightY = - r * Math.cos(theta);
	var topLeftX = - r * Math.sin(theta);
	var topLeftY = topRightY;
    
	// Boundary path
	ctx.beginPath();
    
	// Arc across to mirror image point on the other side
	ctx.arc(0, 0, ro, arcStart, arcEnd, true);
    
	// Arc back to mirror image point on the other side
	ctx.arc(0, 0, ri, arcEnd, arcStart, false);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;	
	ctx.fillStyle = "rgba(200, 200, 200, 0.75)";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
    
	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(new ED.Point(topLeftX, topLeftY));
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(topRightX, topRightY));
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
    
	// Return value indicating successful hit test
	return this.isClicked;
}

/**
 * Returns a String which, if not empty, determines the root descriptions of multiple instances of the doodle
 *
 * @returns {String} Group description
 */
ED.NerveFibreDefect.prototype.groupDescription = function()
{
	return  "Nerve fibre layer defect at ";
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.NerveFibreDefect.prototype.description = function()
{
    var returnString = "";
	
    // Location (clockhours)
	returnString += this.clockHour() + " o'clock";
	
	return returnString;
}

/**
 * DiskHaemorrhage
 *
 * @class DiskHaemorrhage
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
ED.DiskHaemorrhage = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call super-class constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order); 
	
	// Set classname
	this.className = "DiskHaemorrhage";
}

/**
 * Sets superclass and constructor
 */
ED.DiskHaemorrhage.prototype = new ED.Doodle;
ED.DiskHaemorrhage.prototype.constructor = ED.DiskHaemorrhage;
ED.DiskHaemorrhage.superclass = ED.Doodle.prototype;

/**
 * Sets default dragging attributes
 */
ED.DiskHaemorrhage.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.25, +4);
	this.rangeOfArc = new ED.Range(Math.PI/8, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-490, -400);
}

/**
 * Sets default parameters
 */
ED.DiskHaemorrhage.prototype.setParameterDefaults = function()
{
    this.arc = 10 * Math.PI/180;
    this.apexY = -350;
    
    // Make it 30 degress to last one of same class
    var doodle = this.drawing.lastDoodleOfClass(this.className);
    if (doodle)
    {
        this.rotation = doodle.rotation + Math.PI/6;
    }
    else
    {
        this.rotation = (this.drawing.eye == ED.eye.Right)?-Math.PI/4:Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.DiskHaemorrhage.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.DiskHaemorrhage.superclass.draw.call(this, _point);
    
	// Radius of outer curve just inside ora on right and left fundus diagrams
	var ro = -this.apexY;
    var ri = 250;
    var r = ri + (ro - ri)/2;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    
    // Coordinates of 'corners' of DiskHaemorrhage
	var topRightX = r * Math.sin(theta);
	var topRightY = - r * Math.cos(theta);
	var topLeftX = - r * Math.sin(theta);
	var topLeftY = topRightY;
    
	// Boundary path
	ctx.beginPath();
    
	// Arc across to mirror image point on the other side
	ctx.arc(0, 0, ro, arcStart, arcEnd, true);
    
	// Arc back to mirror image point on the other side
	ctx.arc(0, 0, ri, arcEnd, arcStart, false);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;	
	ctx.fillStyle = "red";
	ctx.strokeStyle = "red";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Draw handles if selected
	if (this.isSelected && !this.isForDrawing) this.drawHandles(_point);
    
	// Return value indicating successful hit test
	return this.isClicked;
}

/**
 * Returns a String which, if not empty, determines the root descriptions of multiple instances of the doodle
 *
 * @returns {String} Group description
 */
ED.DiskHaemorrhage.prototype.groupDescription = function()
{
	return  "Disk haemorrhage at ";
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.DiskHaemorrhage.prototype.description = function()
{
    var returnString = "";
	
    // Location (clockhours)
	returnString += this.clockHour() + " o'clock";
	
	return returnString;
}

/**
 * A nuclear cataract
 *
 * @class Papilloedema
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
ED.Papilloedema = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Papilloedema";
}

/**
 * Sets superclass and constructor
 */
ED.Papilloedema.prototype = new ED.Doodle;
ED.Papilloedema.prototype.constructor = ED.Papilloedema;
ED.Papilloedema.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Papilloedema.prototype.setHandles = function()
{
	//this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Papilloedema.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-180, 0);
}

/**
 * Sets default parameters
 */
ED.Papilloedema.prototype.setParameterDefaults = function()
{
    this.radius = 375;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Papilloedema.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Papilloedema.superclass.draw.call(this, _point);

    var ro = this.radius + 75;
    var ri = this.radius - 75;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
    
	// Boundary path
	ctx.beginPath();
    
	// Arc across to mirror image point on the other side
	ctx.arc(0, 0, ro, 0, Math.PI * 2, true);
    
	// Arc back to mirror image point on the other side
	ctx.arc(0, 0, ri, Math.PI * 2, 0, false);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 0;
    
    // Colors for gradient
    yellowColour = "rgba(255, 255, 0, 0.75)";
    var brownColour = "rgba(240, 140, 40, 0.75)";
    
    // Radial gradient
    var gradient = ctx.createRadialGradient(0, 0, this.radius + 75, 0, 0, this.radius - 75);
    gradient.addColorStop(0, yellowColour);
    gradient.addColorStop(1, brownColour);
    
	ctx.fillStyle = gradient;
	ctx.strokeStyle = "rgba(0,0,0,0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	//this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
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
ED.Papilloedema.prototype.description = function()
{
	return "Papilloedema";
}


/**
 * Supramid suture
 *
 * @class Supramid
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
ED.Supramid = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Supramid";
}

/**
 * Sets superclass and constructor
 */
ED.Supramid.prototype = new ED.Doodle;
ED.Supramid.prototype.constructor = ED.Supramid;
ED.Supramid.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Supramid.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Supramid.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-420, -200);
    this.snapToQuadrant = true;
    this.quadrantPoint = new ED.Point(10, 10);
}

/**
 * Sets default parameters
 */
ED.Supramid.prototype.setParameterDefaults = function()
{
    this.apexX = 0;
    this.apexY = -350;
    this.originY = -10;
    
    // Tubes are usually STQ
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -10;        
        this.rotation = -Math.PI/4;
    }
    else
    {
        this.originX = 10;
        this.rotation = Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Supramid.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Supramid.superclass.draw.call(this, _point);

    // Calculate key points for supramid bezier
    var startPoint = new ED.Point(0, this.apexY);
    var tubePoint = new ED.Point(0, -450);    
    var controlPoint1 = new ED.Point(0, -600);
    
    // Calculate mid point x coordinate
    var midPointX = -450;
    var controlPoint2 = new ED.Point(midPointX, -300);
    var midPoint = new ED.Point(midPointX, 0);
    var controlPoint3 = new ED.Point(midPointX, 300);
    var controlPoint4 = new ED.Point(midPointX * 0.5, 450);
    var endPoint = new ED.Point(midPointX * 0.2, 450);

	// Boundary path
	ctx.beginPath();
    
    // Rectangle around suture
    ctx.moveTo(this.apexX, tubePoint.y);
    ctx.lineTo(midPointX, tubePoint.y);
    ctx.lineTo(midPointX, endPoint.y);
    ctx.lineTo(this.apexX, endPoint.y);            
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 1;
	ctx.fillStyle = "rgba(0, 0, 0, 0)";
	ctx.strokeStyle = "rgba(0, 0, 0, 0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{        
        // Suture
        ctx.beginPath()
        ctx.moveTo(startPoint.x, startPoint.y);
        ctx.lineTo(tubePoint.x, tubePoint.y);
        ctx.bezierCurveTo(controlPoint1.x, controlPoint1.y, controlPoint2.x, controlPoint2.y, midPoint.x, midPoint.y);
        ctx.bezierCurveTo(controlPoint3.x, controlPoint3.y, controlPoint4.x, controlPoint4.y, endPoint.x, endPoint.y);
        
        ctx.lineWidth = 4;
        ctx.strokeStyle = "purple";
        ctx.stroke();
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(0, this.apexY));
	
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
ED.Supramid.prototype.getParameter = function(_parameter)
{
    var returnValue;
    
    switch (_parameter)
    {
        // Position of end of suture
        case 'endPosition':
            var r = Math.sqrt(this.apexX * this.apexX + this.apexY * this.apexY);
            
            if (r < 280 ) returnValue = 'in the AC';
            else returnValue = ((r - 280)/14).toFixed(0) + 'mm from limbus';
            break;

        default:
            returnValue = "";
            break;
    }
    
    return returnValue;
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.Supramid.prototype.description = function()
{
    var returnString = "Supramid suture ";
    
    returnString += this.getParameter('endPosition');
    
	return returnString;
}

/**
 * Vicryl suture
 *
 * @class Vicryl
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
ED.Vicryl = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Vicryl";
}

/**
 * Sets superclass and constructor
 */
ED.Vicryl.prototype = new ED.Doodle;
ED.Vicryl.prototype.constructor = ED.Vicryl;
ED.Vicryl.superclass = ED.Doodle.prototype;

/**
 * Sets default dragging attributes
 */
ED.Vicryl.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
}

/**
 * Sets default parameters
 */
ED.Vicryl.prototype.setParameterDefaults = function()
{
    this.originY = -240;
    this.apexY = 400;
    
    // Tubes are usually STQ
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -240;        
        this.rotation = -Math.PI/4;
    }
    else
    {
        this.originX = 240;
        this.rotation = Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Vicryl.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;

	// Call draw method in superclass
	ED.Vicryl.superclass.draw.call(this, _point);
    
	// Boundary path
	ctx.beginPath();
    
    // Use arcTo to create an ellipsoid
    ctx.moveTo(-20, 0);
    ctx.arcTo(0, -20, 20, 0, 30); 
    ctx.arcTo(0, 20, -20, 0, 30);
    
	// Set line attributes
	ctx.lineWidth = 4;
	ctx.fillStyle = "rgba(0, 0, 0, 0)";
	ctx.strokeStyle = "purple";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Ends of suture
        ctx.beginPath();
        ctx.moveTo(35, -10);
        ctx.lineTo(20, 0);
        ctx.lineTo(35, 10); 
        ctx.stroke();
        
        // Knot
        this.drawSpot(ctx, 20, 0, 4, "purple");
 	}
	
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
ED.Vicryl.prototype.description = function()
{
    var returnString = "Vicryl suture";
    
	return returnString;
}

/**
 * Molteno tube
 *
 * @class Molteno
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
ED.Molteno = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Molteno";
}

/**
 * Sets superclass and constructor
 */
ED.Molteno.prototype = new ED.Doodle;
ED.Molteno.prototype.constructor = ED.Molteno;
ED.Molteno.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Molteno.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Molteno.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
	this.rangeOfApexY = new ED.Range(+100, +500);
	this.rangeOfApexX = new ED.Range(-0, +0);
    this.snapToQuadrant = true;
    this.quadrantPoint = new ED.Point(380, 380);
}

/**
 * Sets default parameters
 */
ED.Molteno.prototype.setParameterDefaults = function()
{
    this.originY = -380;
    this.apexY = 300;
    
    // Tubes are usually STQ
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -380;        
        this.rotation = -Math.PI/4;
    }
    else
    {
        this.originX = 380;
        this.rotation = Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Molteno.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Molteno.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    // Scaling factor
    var s = 0.3;
    
    // Plate
    ctx.arc(0, 0, 310 * s, 0, Math.PI * 2, true);  
    
    // Set Attributes
    ctx.lineWidth = 4;
    ctx.strokeStyle = "rgba(120,120,120,0.75)";
    ctx.fillStyle = "rgba(220,220,220,0.5)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Inner ring
        ctx.beginPath();
        ctx.arc(0, 0, 250 * s, 0, Math.PI * 2, true);
        ctx.stroke();
        
        // Suture holes
        this.drawSpot(ctx, -200 * s, 200 * s, 5, "rgba(255,255,255,1)");
        this.drawSpot(ctx, -200 * s, -200 * s, 5, "rgba(255,255,255,1)");
        this.drawSpot(ctx, 200 * s, -200 * s, 5, "rgba(255,255,255,1)");
        this.drawSpot(ctx, 200 * s, 200 * s, 5, "rgba(255,255,255,1)");
        
        // Tube
        ctx.beginPath();
        ctx.moveTo(-20 * s, 240 * s);
        ctx.lineTo(-20 * s, this.apexY);
        ctx.lineTo(20 * s, this.apexY);
        ctx.lineTo(20 * s, 240 * s);
        
        ctx.strokeStyle = "rgba(150,150,150,0.5)";
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
ED.Molteno.prototype.getParameter = function(_parameter)
{
    var returnValue;
    var isRE = (this.drawing.eye == ED.eye.Right);
    
    switch (_parameter)
    {
        // Plate position
        case 'platePosition':
            var clockHour = this.clockHour();
            
            if (clockHour < 4 ) returnValue = isRE?'SNQ':'STQ';
            else if (clockHour < 7 ) returnValue = isRE?'INQ':'ITQ';
            else if (clockHour < 10 ) returnValue = isRE?'ITQ':'INQ';
            else returnValue = isRE?'STQ':'SNQ';
            break;
            
        case 'plateLimbusDistance':
            var distance = Math.round((Math.sqrt(this.originX * this.originX + this.originY * this.originY) - 360)/20);
            returnValue = distance.toFixed(1);
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
ED.Molteno.prototype.setParameter = function(_parameter, _value)
{
    var isRE = (this.drawing.eye == ED.eye.Right);
    switch (_parameter)
    {
        // Plate position
        case 'platePosition':
            switch (_value)
            {
                case 'STQ':
                    if (isRE)
                    {
                        this.originX = -380;
                        this.originY = -380;
                        this.rotation = -Math.PI/4;
                    }
                    else
                    {
                        this.originX = 380;
                        this.originY = -380;
                        this.rotation = Math.PI/4;
                    }
                    break;
                case 'ITQ':
                    if (isRE)
                    {
                        this.originX = -380;
                        this.originY = 380;
                        this.rotation = -3*Math.PI/4;
                    }
                    else
                    {
                        this.originX = 380;
                        this.originY = 380;
                        this.rotation = 3*Math.PI/4;
                    }
                    break;
                case 'SNQ':
                    if (isRE)
                    {
                        this.originX = 380;
                        this.originY = -380;
                        this.rotation = Math.PI/4;
                    }
                    else
                    {
                        this.originX = -380;
                        this.originY = -380;
                        this.rotation = -Math.PI/4;
                    }
                    break;
                case 'INQ':
                    if (isRE)
                    {
                        this.originX = 380;
                        this.originY = 380;
                        this.rotation = 3*Math.PI/4;
                    }
                    else
                    {
                        this.originX = -380;
                        this.originY = 380;
                        this.rotation = -3*Math.PI/4;
                    }
                    break;
                default:
                    break;
            }
            break;
        case 'plateLimbusDistance':
            
            // Get angle to origin
            var origin = new ED.Point(this.originX, this.originY);
            var north = new ED.Point(0,-100);
            var angle = 2 * Math.PI - origin.clockwiseAngleTo(north);
            
            // Calculate new radius
            r = _value * 20 + 304;
            
            // Set doodle to new radius
            var newOrigin = new ED.Point()
            newOrigin.setWithPolars(r, angle);
            this.originX = newOrigin.x;
            this.originY = newOrigin.y;
            
            break;
            
        default:
            break
    }
}


/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.Molteno.prototype.description = function()
{
    var returnString = "Molteno tube";
    
    // Position
    var quadrant = this.getParameter('platePosition');
    var description = "";
    
    switch (quadrant)
    {
        case 'STQ':
            description = " in supero-temporal quadrant";
            break;
        case 'SNQ':
            description = " in supero-nasal quadrant";
            break;
        case 'INQ':
            description = " in infero-nasal quadrant";
            break;            
        case 'ITQ':
            description = " in infero-temporal quadrant";
            break;             
    }
    
    returnString += description;
    
	return returnString;
}

/**
 * Baerveldt tube
 *
 * @class Baerveldt
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
ED.Baerveldt = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Baerveldt";
}

/**
 * Sets superclass and constructor
 */
ED.Baerveldt.prototype = new ED.Doodle;
ED.Baerveldt.prototype.constructor = ED.Baerveldt;
ED.Baerveldt.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Baerveldt.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Baerveldt.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
	this.rangeOfApexY = new ED.Range(+100, +500);
	this.rangeOfApexX = new ED.Range(-0, +0);
    this.snapToQuadrant = true;
    this.quadrantPoint = new ED.Point(380, 380);
}

/**
 * Sets default parameters
 */
ED.Baerveldt.prototype.setParameterDefaults = function()
{
    this.originY = -380;
    this.apexY = 300;
    
    // Tubes are usually STQ
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -380;        
        this.rotation = -Math.PI/4;
    }
    else
    {
        this.originX = 380;
        this.rotation = Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Baerveldt.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Baerveldt.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    // Scaling factor
    var s = 0.3;
    
    // Plate
    ctx.moveTo(-400	* s, 0 * s);
    ctx.bezierCurveTo(-400 * s, -100 * s, -300 * s, -200 * s, -200 * s, -200 * s);
    ctx.bezierCurveTo(-100 * s, -200 * s, -58 * s, -136 * s, 0 * s, -135 * s);
    ctx.bezierCurveTo(54 * s, -136 * s, 100 * s, -200 * s, 200 * s, -200 * s);
    ctx.bezierCurveTo(300 * s, -200 * s, 400 * s, -100 * s, 400 * s, 0 * s);
    ctx.bezierCurveTo(400 * s, 140 * s, 200 * s, 250 * s, 0 * s, 250 * s);
    ctx.bezierCurveTo(-200 * s, 250 * s, -400 * s, 140 * s, -400 * s, 0 * s);
    
    // Connection flange
    ctx.moveTo(-160 * s, 230 * s);
    ctx.lineTo(-120 * s, 290 * s);
    ctx.lineTo(120 * s, 290 * s);
    ctx.lineTo(160 * s, 230 * s);
    ctx.bezierCurveTo(120 * s, 250 * s, -120 * s, 250 * s, -160 * s, 230 * s);   
    
    // Set Attributes
    ctx.lineWidth = 4;
    ctx.strokeStyle = "rgba(120,120,120,0.75)";
    ctx.fillStyle = "rgba(220,220,220,0.5)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Spots
        this.drawSpot(ctx, -240 * s, -40 * s, 10, "rgba(150,150,150,0.5)");
        this.drawSpot(ctx, -120 * s, 40 * s, 10, "rgba(150,150,150,0.5)");
        this.drawSpot(ctx, 120 * s, 40 * s, 10, "rgba(150,150,150,0.5)");
        this.drawSpot(ctx, 240 * s, -40 * s, 10, "rgba(150,150,150,0.5)");
        this.drawSpot(ctx, -100 * s, 260 * s, 5, "rgba(150,150,150,0.5)");
        this.drawSpot(ctx, 100 * s, 260 * s, 5, "rgba(150,150,150,0.5)");
        
        // Ridge on flange
        ctx.beginPath()
        ctx.moveTo(-30 * s, 250 * s);
        ctx.lineTo(-30 * s, 290 * s);
        ctx.moveTo(30 * s, 250 * s);
        ctx.lineTo(30 * s, 290 * s);
        
        // Tube
        ctx.moveTo(-20 * s, 290 * s);
        ctx.lineTo(-20 * s, this.apexY);
        ctx.lineTo(20 * s, this.apexY);
        ctx.lineTo(20 * s, 290 * s);
        
        ctx.strokeStyle = "rgba(150,150,150,0.5)";
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
ED.Baerveldt.prototype.getParameter = function(_parameter)
{
    var returnValue;
    var isRE = (this.drawing.eye == ED.eye.Right);
    
    switch (_parameter)
    {
            // Plate position
        case 'platePosition':
            var clockHour = this.clockHour();
            
            if (clockHour < 4 ) returnValue = isRE?'SNQ':'STQ';
            else if (clockHour < 7 ) returnValue = isRE?'INQ':'ITQ';
            else if (clockHour < 10 ) returnValue = isRE?'ITQ':'INQ';
            else returnValue = isRE?'STQ':'SNQ';
            break;
            
        case 'plateLimbusDistance':
            var distance = Math.round((Math.sqrt(this.originX * this.originX + this.originY * this.originY) - 360)/20);
            returnValue = distance.toFixed(1);
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
ED.Baerveldt.prototype.setParameter = function(_parameter, _value)
{
    var isRE = (this.drawing.eye == ED.eye.Right);
    switch (_parameter)
    {
        // Plate position
        case 'platePosition':
        switch (_value)
        {
            case 'STQ':
                if (isRE)
                {
                    this.originX = -380;
                    this.originY = -380;
                    this.rotation = -Math.PI/4;
                }
                else
                {
                    this.originX = 380;
                    this.originY = -380;
                    this.rotation = Math.PI/4;
                }
                break;
            case 'ITQ':
                if (isRE)
                {
                    this.originX = -380;
                    this.originY = 380;
                    this.rotation = -3*Math.PI/4;
                }
                else
                {
                    this.originX = 380;
                    this.originY = 380;
                    this.rotation = 3*Math.PI/4;
                }
                break;
            case 'SNQ':
                if (isRE)
                {
                    this.originX = 380;
                    this.originY = -380;
                    this.rotation = Math.PI/4;
                }
                else
                {
                    this.originX = -380;
                    this.originY = -380;
                    this.rotation = -Math.PI/4;
                }
                break;
            case 'INQ':
                if (isRE)
                {
                    this.originX = 380;
                    this.originY = 380;
                    this.rotation = 3*Math.PI/4;
                }
                else
                {
                    this.originX = -380;
                    this.originY = 380;
                    this.rotation = -3*Math.PI/4;
                }
                break;
            default:
                break;
        }
        break;
        case 'plateLimbusDistance':
            
            // Get angle to origin
            var origin = new ED.Point(this.originX, this.originY);
            var north = new ED.Point(0,-100);
            var angle = 2 * Math.PI - origin.clockwiseAngleTo(north);
            
            // Calculate new radius
            r = _value * 20 + 304;
            
            // Set doodle to new radius
            var newOrigin = new ED.Point()
            newOrigin.setWithPolars(r, angle);
            this.originX = newOrigin.x;
            this.originY = newOrigin.y;
            
            break;
            
        default:
            break
    }
}


/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.Baerveldt.prototype.description = function()
{
    var returnString = "Baerveldt tube";
    
    // Position
    var quadrant = this.getParameter('platePosition');
    var description = "";
    
    switch (quadrant)
    {
        case 'STQ':
            description = " in supero-temporal quadrant";
            break;
        case 'SNQ':
            description = " in supero-nasal quadrant";
            break;
        case 'INQ':
            description = " in infero-nasal quadrant";
            break;            
        case 'ITQ':
            description = " in infero-temporal quadrant";
            break;             
    }
    
    returnString += description;
    
	return returnString;
}

/**
 * Ahmed tube
 *
 * @class Ahmed
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
ED.Ahmed = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Ahmed";
}

/**
 * Sets superclass and constructor
 */
ED.Ahmed.prototype = new ED.Doodle;
ED.Ahmed.prototype.constructor = ED.Ahmed;
ED.Ahmed.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Ahmed.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.Ahmed.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = false;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
	this.rangeOfApexY = new ED.Range(+100, +500);
	this.rangeOfApexX = new ED.Range(-0, +0);
    this.snapToQuadrant = true;
    this.quadrantPoint = new ED.Point(380, 380);
}

/**
 * Sets default parameters
 */
ED.Ahmed.prototype.setParameterDefaults = function()
{
    this.originY = -380;
    this.apexY = 300;
    
    // Tubes are usually STQ
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -380;        
        this.rotation = -Math.PI/4;
    }
    else
    {
        this.originX = 380;
        this.rotation = Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Ahmed.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Ahmed.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
    // Scaling factor
    var s = 0.3;
    
    // Plate
    ctx.moveTo(-300	* s, 0 * s);
    ctx.bezierCurveTo(-300 * s, -100 * s, -200 * s, -400 * s, 0 * s, -400 * s);
    ctx.bezierCurveTo(200 * s, -400 * s, 300 * s, -100 * s, 300 * s, 0 * s);
    ctx.bezierCurveTo(300 * s, 140 * s, 200 * s, 250 * s, 0 * s, 250 * s);
    ctx.bezierCurveTo(-200 * s, 250 * s, -300 * s, 140 * s, -300 * s, 0 * s);
    
    // Connection flange
    ctx.moveTo(-160 * s, 230 * s);
    ctx.lineTo(-120 * s, 290 * s);
    ctx.lineTo(120 * s, 290 * s);
    ctx.lineTo(160 * s, 230 * s);
    ctx.bezierCurveTo(120 * s, 250 * s, -120 * s, 250 * s, -160 * s, 230 * s);   
    
    // Set Attributes
    ctx.lineWidth = 4;
    ctx.strokeStyle = "rgba(120,120,120,0.75)";
    ctx.fillStyle = "rgba(220,220,220,0.5)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Spots
        this.drawSpot(ctx, 0 * s, -230 * s, 20 * s, "white");
        this.drawSpot(ctx, -180 * s, -180 * s, 20 * s, "white");
        this.drawSpot(ctx, 180 * s, -180 * s, 20 * s, "white");

        // Trapezoid mechanism
        ctx.beginPath()
        ctx.moveTo(-100 * s, 230 * s);
        ctx.lineTo(100 * s, 230 * s);
        ctx.lineTo(200 * s, 0 * s);
        ctx.lineTo(40 * s, 0 * s);
        ctx.arcTo(0, -540 * s, -40 * s, 0 * s, 15);
        ctx.lineTo(-40 * s, 0 * s);
        ctx.lineTo(-200 * s, 0 * s); 
        ctx.closePath();
        
        ctx.fillStyle = "rgba(250,250,250,0.7)";
        ctx.fill();
        
        // Lines
        ctx.moveTo(-80 * s, -40 * s);
        ctx.lineTo(-160 * s, -280 * s);
        ctx.moveTo(80 * s, -40 * s);
        ctx.lineTo(160 * s, -280 * s);
        ctx.lineWidth = 8;
        ctx.strokeStyle = "rgba(250,250,250,0.7)";
        ctx.stroke();
        
        // Ridge on flange
        ctx.beginPath()
        ctx.moveTo(-30 * s, 250 * s);
        ctx.lineTo(-30 * s, 290 * s);
        ctx.moveTo(30 * s, 250 * s);
        ctx.lineTo(30 * s, 290 * s);
        
        // Tube
        ctx.moveTo(-20 * s, 290 * s);
        ctx.lineTo(-20 * s, this.apexY);
        ctx.lineTo(20 * s, this.apexY);
        ctx.lineTo(20 * s, 290 * s);
        
        ctx.lineWidth = 4;
        ctx.strokeStyle = "rgba(150,150,150,0.5)";
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
ED.Ahmed.prototype.getParameter = function(_parameter)
{
    var returnValue;
    var isRE = (this.drawing.eye == ED.eye.Right);
    
    switch (_parameter)
    {
            // Plate position
        case 'platePosition':
            var clockHour = this.clockHour();
            
            if (clockHour < 4 ) returnValue = isRE?'SNQ':'STQ';
            else if (clockHour < 7 ) returnValue = isRE?'INQ':'ITQ';
            else if (clockHour < 10 ) returnValue = isRE?'ITQ':'INQ';
            else returnValue = isRE?'STQ':'SNQ';
            break;
            
        case 'plateLimbusDistance':
            var distance = Math.round((Math.sqrt(this.originX * this.originX + this.originY * this.originY) - 304)/20);
            returnValue = distance.toFixed(1);
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
ED.Ahmed.prototype.setParameter = function(_parameter, _value)
{
    var isRE = (this.drawing.eye == ED.eye.Right);
    switch (_parameter)
    {
        // Plate position
        case 'platePosition':
            switch (_value)
            {
                case 'STQ':
                    if (isRE)
                    {
                        this.originX = -380;
                        this.originY = -380;
                        this.rotation = -Math.PI/4;
                    }
                    else
                    {
                        this.originX = 380;
                        this.originY = -380;
                        this.rotation = Math.PI/4;
                    }
                    break;
                case 'ITQ':
                    if (isRE)
                    {
                        this.originX = -380;
                        this.originY = 380;
                        this.rotation = -3*Math.PI/4;
                    }
                    else
                    {
                        this.originX = 380;
                        this.originY = 380;
                        this.rotation = 3*Math.PI/4;
                    }
                    break;
                case 'SNQ':
                    if (isRE)
                    {
                        this.originX = 380;
                        this.originY = -380;
                        this.rotation = Math.PI/4;
                    }
                    else
                    {
                        this.originX = -380;
                        this.originY = -380;
                        this.rotation = -Math.PI/4;
                    }
                    break;
                case 'INQ':
                    if (isRE)
                    {
                        this.originX = 380;
                        this.originY = 380;
                        this.rotation = 3*Math.PI/4;
                    }
                    else
                    {
                        this.originX = -380;
                        this.originY = 380;
                        this.rotation = -3*Math.PI/4;
                    }
                    break;
                default:
                    break;
            }
            break;
        case 'plateLimbusDistance':
            
            // Get angle to origin
            var origin = new ED.Point(this.originX, this.originY);
            var north = new ED.Point(0,-100);
            var angle = 2 * Math.PI - origin.clockwiseAngleTo(north);
            
            // Calculate new radius
            r = _value * 20 + 304;
            
            // Set doodle to new radius
            var newOrigin = new ED.Point()
            newOrigin.setWithPolars(r, angle);
            this.originX = newOrigin.x;
            this.originY = newOrigin.y;
            
            break;
            
        default:
            break
    }
}


/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.Ahmed.prototype.description = function()
{
    var returnString = "Ahmed tube at ";
    
    // Position
    var quadrant = this.getParameter('platePosition');
    var description = "";
    
    switch (quadrant)
    {
        case 'STQ':
            description = " in supero-temporal quadrant";
            break;
        case 'SNQ':
            description = " in supero-nasal quadrant";
            break;
        case 'INQ':
            description = " in infero-nasal quadrant";
            break;            
        case 'ITQ':
            description = " in infero-temporal quadrant";
            break;             
    }
    
    returnString += description;
    
	return returnString;
}

/**
 * Patch
 *
 * @class Patch
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
ED.Patch = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Patch";
}

/**
 * Sets superclass and constructor
 */
ED.Patch.prototype = new ED.Doodle;
ED.Patch.prototype.constructor = ED.Patch;
ED.Patch.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Patch.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
}

/**
 * Sets default dragging attributes
 */
ED.Patch.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = true;
	this.isSqueezable = true;
	this.isMoveable = true;
	this.isRotatable = true;
    this.rangeOfArc = new ED.Range(0, Math.PI);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-334, -300);
    this.rangeOfRadius = new ED.Range(250, 450);
}

/**
 * Sets default parameters
 */
ED.Patch.prototype.setParameterDefaults = function()
{
    this.originY = -260;
    
    // Patchs are usually temporal
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -260;        
        this.rotation = -Math.PI/4;
    }
    else
    {
        this.originX = 260;
        this.rotation = Math.PI/4;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Patch.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Patch.superclass.draw.call(this, _point);
    
    // Boundary path
	ctx.beginPath();
    
    ctx.rect(-50, -70, 100, 140);
    
	// Close path
	ctx.closePath();
    
    // Colour of fill
    ctx.fillStyle = "rgba(200,200,50,0.5)";
    ctx.strokeStyle = "rgba(120,120,120,0)";
    
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
    {
        // Suture knots
        this.drawSpot(ctx, -50, -50, 5, "blue");
        this.drawSpot(ctx, -50, 50, 5, "blue");
        this.drawSpot(ctx, 50, -50, 5, "blue");
        this.drawSpot(ctx, 50, 50, 5, "blue");
        
        // Suture thread ends
        this.drawLine(ctx, -60, -60, -50, -50, 2, "blue");
        this.drawLine(ctx, -50, -50, -60, -40, 2, "blue");
        this.drawLine(ctx, -60, 60, -50, 50, 2, "blue");
        this.drawLine(ctx, -50, 50, -60, 40, 2, "blue");
        this.drawLine(ctx, 60, -60, 50, -50, 2, "blue");
        this.drawLine(ctx, 50, -50, 60, -40, 2, "blue");
        this.drawLine(ctx, 60, 60, 50, 50, 2, "blue");
        this.drawLine(ctx, 50, 50, 60, 40, 2, "blue");
	}
    
    // Coordinates of handles (in canvas plane)
    this.handleArray[2].location = this.transform.transformPoint(new ED.Point(75, -50));
    
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
ED.Patch.prototype.description = function()
{
    var returnString = "Scleral patch";
    
	return returnString;
}

/**
 * OpticDiskPit Acquired Pit of Optic Nerve (APON)
 *
 * @class OpticDiskPit
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
ED.OpticDiskPit = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "OpticDiskPit";
}

/**
 * Sets superclass and constructor
 */
ED.OpticDiskPit.prototype = new ED.Doodle;
ED.OpticDiskPit.prototype.constructor = ED.OpticDiskPit;
ED.OpticDiskPit.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.OpticDiskPit.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
}

/**
 * Sets default dragging attributes
 */
ED.OpticDiskPit.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isScaleable = true;
	this.isSqueezable = true;
	this.isMoveable = true;
}

/**
 * Sets default parameters
 */
ED.OpticDiskPit.prototype.setParameterDefaults = function()
{
    this.originY = 130;
    this.apexY = 0;
    this.scaleX = 1.5;
    this.rangeOfOriginX = new ED.Range(-150, 150);
    this.rangeOfOriginY = new ED.Range(-150, 150);   
    this.rangeOfScale = new ED.Range(0.5, 3);

    
    // Tubes are usually STQ
    if(this.drawing.eye == ED.eye.Right)
    {
        this.originX = -50;        
    }
    else
    {
        this.originX = 50;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.OpticDiskPit.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
    
	// Call draw method in superclass
	ED.OpticDiskPit.superclass.draw.call(this, _point);
    
	// Boundary path
	ctx.beginPath();
	
	// Round hole
    var r = 80;
	ctx.arc(0, 0, r, 0, Math.PI*2, true);
    
	// Close path
	ctx.closePath();

    // Radial gradient
    var lightGray = "rgba(200, 200, 200, 0.75)";
    var darkGray = "rgba(100, 100, 100, 0.75)";
    var gradient = ctx.createRadialGradient(0, 0, r, 0, 0, 10);
    gradient.addColorStop(0, darkGray);
    gradient.addColorStop(1, lightGray);
    
	ctx.fillStyle = gradient;
	ctx.lineWidth = 2;
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
 	}

    // Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(55, -55));
    
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
ED.OpticDiskPit.prototype.description = function()
{
    var returnString = "Acquired pit of optic nerve";
    
	return returnString;
}
