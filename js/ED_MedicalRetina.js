/**
 * @fileOverview Contains doodle subclasses for Medical Retina
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
 * PostPole template with disk and arcades
 *
 * @class PostPole
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
ED.PostPole = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "PostPole";
}

/**
 * Sets superclass and constructor
 */
ED.PostPole.prototype = new ED.Doodle;
ED.PostPole.prototype.constructor = ED.PostPole;
ED.PostPole.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.PostPole.prototype.setHandles = function()
{
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.PostPole.prototype.setPropertyDefaults = function()
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
	this.rangeOfApexY = new ED.Range(-80, -8);
}

/**
 * Sets default parameters
 */
ED.PostPole.prototype.setParameterDefaults = function()
{
	this.apexY = -50;
    
    if(this.drawing.eye != ED.eye.Right)
    {
        this.originX = -300;
    }
    else
    {
        this.originX = 300;
    }
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.PostPole.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.PostPole.superclass.draw.call(this, _point);
    
	// Boundary path
	ctx.beginPath();
	
    // These values different for right and left side
    if(this.drawing.eye != ED.eye.Right)
    {
        var startX = 600;
        var midX1 = 350;
        var midX2 = 0;
        var midX3 = 0;
        var endX1 = 0;
        var endX2 = -50;
        var endX3 = -100;
		var foveaX = 300;
    }
    else
    {
        var startX = -600;
        var midX1 = -350;
        var midX2 = 0;
        var midX3 = 0;
        var endX1 = 0;
        var endX2 = 50;
        var endX3 = 100;
		var foveaX = -300;
    }
	
    // Optic disk and cup
	var ro = 84;
    var ri = -this.apexY;
	
	// Calculate parameters for arcs
	var arcStart = 0;
	var arcEnd = 2 * Math.PI;
    
	// Boundary path
	ctx.beginPath();
    
	// Do a 360 arc
	ctx.arc(0, 0, ro, arcStart, arcEnd, true);
    
    // Move to inner circle
    ctx.moveTo(ri, 0);
    
	// Arc back the other way
	ctx.arc(0, 0, ri, arcEnd, arcStart, false);
	
	// Set attributes
	ctx.lineWidth = 4;
	ctx.strokeStyle = "red";
    ctx.fillStyle = "rgba(255,200,200,0.5)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        ctx.beginPath();
        
        // Superior arcades
        ctx.moveTo(startX, -100);
        ctx.bezierCurveTo(midX1, -500, midX2, -200, midX3, -24);
        ctx.bezierCurveTo(endX1, -80, endX2, -140, endX3, -160);
        
        // Inferior arcades
        ctx.moveTo(endX3, 160);
        ctx.bezierCurveTo(endX2, 140, endX1, 80, midX3, 24);
        ctx.bezierCurveTo(midX2, 200, midX1, 500, startX, 100);
        
		// Small cross marking fovea
		var crossLength = 10;
		ctx.moveTo(foveaX, -crossLength);
		ctx.lineTo(foveaX, crossLength);
		ctx.moveTo(foveaX - crossLength, 0);
		ctx.lineTo(foveaX + crossLength, 0);
		
		// Draw it
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
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.PostPole.prototype.description = function()
{
	return "Cup-disk ratio of " + this.getParameter('cdRatio');
}

/**
 * Returns parameters
 *
 * @returns {String} value of parameter
 */
ED.PostPole.prototype.getParameter = function(_parameter)
{
    var returnValue;
    var isRE = (this.drawing.eye == ED.eye.Right);
    
    switch (_parameter)
    {
        // Plate position
        case 'cdRatio':
            returnValue = -this.apexY/80;    
            returnValue = returnValue.toFixed(1);
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
ED.PostPole.prototype.setParameter = function(_parameter, _value)
{
    var isRE = (this.drawing.eye == ED.eye.Right);
    switch (_parameter)
    {
        // CD ratio
        case 'cdRatio':
            this.apexY = -(+_value * 80);
            break;
            
        default:
            break
    }
}


/**
 * PRP
 *
 * @class PRP
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
ED.PRP = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "PRP";
}

/**
 * Sets superclass and constructor
 */
ED.PRP.prototype = new ED.Doodle;
ED.PRP.prototype.constructor = ED.PRP;
ED.PRP.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.PRP.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
	//this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Sets default dragging attributes
 */
ED.PRP.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = true;
	this.isMoveable = true;
	this.isRotatable = false;
    this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-100, -10);
}

/**
 * Sets default parameters
 */
ED.PRP.prototype.setParameterDefaults = function()
{
    this.originX = 0;
	this.originY = 0;
    this.apexY = -50;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.PRP.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.PRP.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
    
	// PRP
    var ro = 400;
    var ri = 140;
    
    // Arc across to mirror image point on the other side
	ctx.arc(0, 0, ro, 0, 2 * Math.PI, true);
    
	// Arc back to mirror image point on the other side
	ctx.arc(-80, 0, ri, 2 * Math.PI, 0, false);
    
	// Close path
	ctx.closePath();
    
    // Create fill pattern
    ctx.fillStyle = "rgba(100,100,100,0)";
    
    // Transparent stroke
	ctx.strokeStyle = "rgba(100,100,100,0)";
    //ctx.strokeStyle = "red";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        var sep = 60;
        var rows = 12;
        var d = ro * 2/rows;
        var i;
        
        this.rowOfBurns(-90, -360, 4, sep);
        this.rowOfBurns(-180, -300, 7, sep);
        this.rowOfBurns(-270, -240, 10, sep);
        this.rowOfBurns(-300, -180, 11, sep);
        
        this.rowOfBurns(-330, -120, 5, sep);
        this.rowOfBurns(-30, -120, 7, sep);
        
        this.rowOfBurns(-360, -60, 3, sep);
        this.rowOfBurns(60, -60, 6, sep);
        
        this.rowOfBurns(-320, 0, 2, sep);
        this.rowOfBurns(90, 0, 5, sep);
        
        this.rowOfBurns(-360, 60, 3, sep);
        this.rowOfBurns(60, 60, 6, sep);

        this.rowOfBurns(-330, 120, 3, sep);
        this.rowOfBurns(30, 120, 6, sep);

        this.rowOfBurns(-180, 300, 7, sep);
        this.rowOfBurns(-270, 240, 10, sep);
        this.rowOfBurns(-300, 180, 11, sep);
        this.rowOfBurns(-90, 360, 4, sep);
	}
	
	// Coordinates of handles (in canvas plane)
    var point = new ED.Point(0, 0);
    point.setWithPolars(ro, Math.PI/4);
	this.handleArray[2].location = this.transform.transformPoint(point);
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
ED.PRP.prototype.description = function()
{
    var returnString = "PRP";
    
	return returnString;
}

/**
 * Doodle specific function to draw a row of laser spots
 */
ED.PRP.prototype.rowOfBurns = function(_startX, _startY, _num, _sep)
{
    // Radius of burn
    var r = 12;
    
    var ctx = this.drawing.context;
    
    for (i = 0; i < _num; i++)
    {
        // Draw laser spot
        ctx.beginPath();
        ctx.arc(_startX + i * _sep, _startY, r, 0, 2 * Math.PI, true);
        //ctx.closePath();
        ctx.fillStyle = "yellow";
        ctx.lineWidth = 8;
        ctx.strokeStyle = "brown";
        ctx.fill();
        ctx.stroke();
    }
}

/**
 * Geographic atrophy with variabel foveal sparing
 *
 * @class Geographic
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
ED.Geographic = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Geographic";
}

/**
 * Sets superclass and constructor
 */
ED.Geographic.prototype = new ED.Doodle;
ED.Geographic.prototype.constructor = ED.Geographic;
ED.Geographic.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Geographic.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.Geographic.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
	this.rangeOfScale = new ED.Range(+0.5, +1);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-100, 0);
}

/**
 * Sets default parameters
 */
ED.Geographic.prototype.setParameterDefaults = function()
{
	this.apexY = -100;
    this.scaleX = 0.7;
    this.scaleY = 0.7;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Geographic.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;

	// Call draw method in superclass
	ED.Geographic.superclass.draw.call(this, _point);
    
	// Radius of limbus
	var ro = 200;
    var ri = -this.apexY;
    var phi = -this.apexY * Math.PI/800;
    
    // Boundary path
	ctx.beginPath();
    
    var point = new ED.Point(0, 0);

	// Outer arc
    if(this.drawing.eye == ED.eye.Right)
    {
        ctx.arc(0, 0, ro, phi, 2 * Math.PI - phi, false);
        point.setWithPolars(ri, Math.PI/2 - phi);
        ctx.lineTo(point.x, point.y);
        ctx.arc(0, 0, ri, 2 * Math.PI - phi, phi, true);
    }
    else
    {
        ctx.arc(0, 0, ro, Math.PI - phi, -Math.PI + phi, true);
        point.setWithPolars(ri, phi - Math.PI/2);
        ctx.lineTo(point.x, point.y);
        ctx.arc(0, 0, ri, -Math.PI + phi, Math.PI - phi, false);
    }
   
    // Close path
    ctx.closePath();
	
	// Set attributes
	ctx.lineWidth = 4;	
    ctx.fillStyle = "rgba(255,255,50,0.8)";
	ctx.strokeStyle = "rgba(100,100,100,0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
    
	// Coordinates of handles (in canvas plane)
    point = new ED.Point(0, 0);
    point.setWithPolars(ro, Math.PI/4);
	this.handleArray[2].location = this.transform.transformPoint(point);
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
ED.Geographic.prototype.description = function()
{
	return "Geographic atrophy";
}

/**
 * CNV
 *
 * @class CNV
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
ED.CNV = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "CNV";
}

/**
 * Sets superclass and constructor
 */
ED.CNV.prototype = new ED.Doodle;
ED.CNV.prototype.constructor = ED.CNV;
ED.CNV.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.CNV.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.CNV.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = true;
	this.rangeOfScale = new ED.Range(+0.5, +2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-80, 0);
}

/**
 * Sets default parameters
 */
ED.CNV.prototype.setParameterDefaults = function()
{
	this.apexY = -80;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.CNV.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
    
	// Call draw method in superclass
	ED.CNV.superclass.draw.call(this, _point);
    
    // Boundary path
	ctx.beginPath();
    
    // Radius of CNV
    var rb = 125;
    
    // Circle
    ctx.arc(0, 0, rb, 0, 2 * Math.PI, false);
    
    // Close path
    ctx.closePath();

	// Set attributes
	ctx.lineWidth = 4;	
    ctx.fillStyle = "rgba(200,200,0,0)";
	ctx.strokeStyle = "rgba(100,100,100,0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Parameters
        var ro = 100;
        var rh = this.apexY < 0?((this.apexY + 80)/4):20;
        var nh = 8;
        var ne = 20;
        
        // Point objects
        var cp = new ED.Point(0, 0);
        var ep = new ED.Point(0, 0);
        
        // Loop through making haemorrhages
        ctx.fillStyle = "rgba(200,0,0,0.8)";
        var phi = 2 * Math.PI/nh;
        var i;
        for (i = 0; i < nh; i++)
        {
            ctx.beginPath();
            cp.setWithPolars(ro, i * phi);
            ctx.arc(cp.x, cp.y, rh, 0, 2 * Math.PI, false);
            ctx.closePath();
            ctx.fill();
        }

        // Yellow centre
        ctx.beginPath();
        ctx.arc(0, 0, ro, 0, 2 * Math.PI, false);
        ctx.closePath();
        ctx.fillStyle = "rgba(160,160,0,1)";
        ctx.fill();
        
        // Exudates
        /*
        phi = 2 * Math.PI/ne;
        var el = this.apexY > 0?(this.apexY/3):0;
        for (i = 0; i < ne; i++)
        {
            ctx.beginPath();
            cp.setWithPolars(ro + 10, i * phi);
            ep.setWithPolars(ro + 10 + el, i * phi);
            ctx.moveTo(cp.x, cp.y);
            ctx.lineTo(ep.x, ep.y);
            ctx.closePath();
            ctx.lineWidth = 18;
            ctx.strokeStyle = "rgba(220,220,0,1)";
            ctx.stroke();
        }
         */
	}
    
	// Coordinates of handles (in canvas plane)
    point = new ED.Point(0, 0);
    point.setWithPolars(rb, Math.PI/4);
	this.handleArray[2].location = this.transform.transformPoint(point);
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
ED.CNV.prototype.description = function()
{
	return "CNV";
}

/**
 * VitreousOpacity template with disk and arcades
 *
 * @class VitreousOpacity
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
ED.VitreousOpacity = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "VitreousOpacity";
}

/**
 * Sets superclass and constructor
 */
ED.VitreousOpacity.prototype = new ED.Doodle;
ED.VitreousOpacity.prototype.constructor = ED.VitreousOpacity;
ED.VitreousOpacity.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.VitreousOpacity.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
	this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.VitreousOpacity.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = false;
    this.isUnique = false;
	this.rangeOfScale = new ED.Range(+0.5, +2);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-200, 0);
}

/**
 * Sets default parameters
 */
ED.VitreousOpacity.prototype.setParameterDefaults = function()
{
    this.apexY = -200;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.VitreousOpacity.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;

	// Call draw method in superclass
	ED.VitreousOpacity.superclass.draw.call(this, _point);
    
	// Boundary path
	ctx.beginPath();
    
	// Boundary path
	ctx.beginPath();
    
    // Radius of opacity
    var ro = 400;
    
	// Do a 360 arc
	ctx.arc(0, 0, ro, 0, 2 * Math.PI, true);
    
    // Opacity from apexY
    var opacity = 0.3  + 0.6 * (200 + this.apexY)/200;
    ctx.fillStyle = "rgba(255, 0, 0," + opacity + ")";
	
	// Set attributes
	ctx.lineWidth = 0;
	ctx.strokeStyle =  "rgba(255, 0, 0, 0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
    
    // Coordinates of handles (in canvas plane)
    point = new ED.Point(0, 0);
    point.setWithPolars(300, Math.PI/4);
	this.handleArray[2].location = this.transform.transformPoint(point);
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
ED.VitreousOpacity.prototype.description = function()
{
	return "Vitreous opacity ";
}

/**
 * DiabeticNV template with disk and arcades
 *
 * @class DiabeticNV
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
ED.DiabeticNV = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "DiabeticNV";
}

/**
 * Sets superclass and constructor
 */
ED.DiabeticNV.prototype = new ED.Doodle;
ED.DiabeticNV.prototype.constructor = ED.DiabeticNV;
ED.DiabeticNV.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.DiabeticNV.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
}

/**
 * Set default properties
 */
ED.DiabeticNV.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
    this.isUnique = false;
	this.rangeOfScale = new ED.Range(+0.5, +2);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-200, 0);
}

/**
 * Sets default parameters
 */
ED.DiabeticNV.prototype.setParameterDefaults = function()
{
    if (this.drawing.eye == ED.eye.Right) this.originX = 300;
    else this.originX = -300;
    this.originY = -100;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.DiabeticNV.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;

	// Call draw method in superclass
	ED.DiabeticNV.superclass.draw.call(this, _point);
    
	// Boundary path
	ctx.beginPath();
    
    // Radius of NV
    var r = 60;
    var c = r/2;
    var phi = 0;
    var theta = Math.PI/8;
    var n = 8;
    
	// Do a vessel
    var cp1 = new ED.Point(0, 0);
    var cp2 = new ED.Point(0, 0);
    var tip = new ED.Point(0, 0);
    var cp3 = new ED.Point(0, 0);
    var cp4 = new ED.Point(0, 0);
    
    // Move to centre
    ctx.moveTo(0,0);
    
    // Loop through making petals
    var i;
    for (i = 0; i < n; i++)
    {
        phi = i * 2 * Math.PI/n;
        
        cp1.setWithPolars(c, phi - theta);
        cp2.setWithPolars(r, phi - theta);
        tip.setWithPolars(r, phi);
        cp3.setWithPolars(r, phi + theta);
        cp4.setWithPolars(c, phi + theta);
        
        // Draw petal
        ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, tip.x, tip.y);
        ctx.bezierCurveTo(cp3.x, cp3.y, cp4.x, cp4.y, 0, 0);
    }

    // Transparent fill
    ctx.fillStyle = "rgba(100, 100, 100, 0)";
	
	// Set attributes
	ctx.lineWidth = 3;
	ctx.strokeStyle =  "red";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
	}
    
    // Coordinates of handles (in canvas plane)
    point = new ED.Point(0, 0);
    point.setWithPolars(r, Math.PI/4);
	this.handleArray[2].location = this.transform.transformPoint(point);
	
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
ED.DiabeticNV.prototype.groupDescription = function()
{
	return "Diabetic new vessels ";
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.DiabeticNV.prototype.description = function()
{
	returnString = "";

    var locationString = "";
    
    // Right eye
    if(this.drawing.eye == ED.eye.Right)
    {
        if (this.originX > 180 && this.originX < 420 && this.originY > -120 && this.originY < 120)
        {
            locationString = "at the disk";
        }
        else
        {
            locationString += this.originY <= 0?"supero":"infero";
            locationString += this.originX <= 300?"temporally":"nasally";
        }
    }
    // Left eye
    else
    {
        if (this.originX < -180 && this.originX > -420 && this.originY > -120 && this.originY < 120)
        {
            locationString = "at the disk";
        }
        else
        {
            locationString += this.originY <= 0?"supero":"infero";
            locationString += this.originX >= -300?"temporally":"nasally";
        }     
    }

    returnString += locationString;
    
    return returnString;
}


/**
 * Circinate
 *
 * @class Circinate
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
ED.Circinate = function(_drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _radius, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Circinate";
}

/**
 * Sets superclass and constructor
 */
ED.Circinate.prototype = new ED.Doodle;
ED.Circinate.prototype.constructor = ED.Circinate;
ED.Circinate.superclass = ED.Doodle.prototype;

/**
 * Sets handle attributes
 */
ED.Circinate.prototype.setHandles = function()
{
    this.handleArray[2] = new ED.Handle(null, true, ED.Mode.Scale, false);
	//this.handleArray[4] = new ED.Handle(null, true, ED.Mode.Apex, false);
}

/**
 * Set default properties
 */
ED.Circinate.prototype.setPropertyDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = false;
	this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-80, 0);
}

/**
 * Sets default parameters
 */
ED.Circinate.prototype.setParameterDefaults = function()
{
    this.originX = this.drawing.eye == ED.eye.Right?-40:40;
    this.originY = -40;
}

/**
 * Draws doodle or performs a hit test if a Point parameter is passed
 *
 * @param {Point} _point Optional point in canvas plane, passed if performing hit test
 */
ED.Circinate.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
    
	// Call draw method in superclass
	ED.Circinate.superclass.draw.call(this, _point);
    
    // Boundary path
	ctx.beginPath();
    
    // Radius of Circinate
    var rc = 80;
    
    // Circle
    ctx.arc(0, 0, rc, 0, 2 * Math.PI, false);
    
    // Close path
    ctx.closePath();
    
	// Set attributes
	ctx.lineWidth = 4;	
    ctx.fillStyle = "rgba(200,200,0,0)";
	ctx.strokeStyle = "rgba(100,100,100,0)";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == ED.drawFunctionMode.Draw)
	{
        // Parameters
        var ro = 40;
        var rh = 10
        var ne = 12;
        var el = 30;
        
        // Point objects
        var cp = new ED.Point(0, 0);
        var ep = new ED.Point(0, 0);
        
        // Red centre
        ctx.beginPath();
        ctx.arc(0, 0, rh, 0, 2 * Math.PI, false);
        ctx.closePath();
        ctx.fillStyle = "red";
        ctx.fill();
        
        // Exudates
        phi = 2 * Math.PI/ne;
        for (i = 0; i < ne; i++)
        {
            ctx.beginPath();
            cp.setWithPolars(ro, i * phi);
            ep.setWithPolars(ro + el, i * phi);
            ctx.moveTo(cp.x, cp.y);
            ctx.lineTo(ep.x, ep.y);
            ctx.closePath();
            ctx.lineWidth = 12;
            ctx.strokeStyle = "rgba(220,220,0,1)";
            ctx.stroke();
        }
	}
    
	// Coordinates of handles (in canvas plane)
    point = new ED.Point(0, 0);
    point.setWithPolars(rc, Math.PI/4);
	this.handleArray[2].location = this.transform.transformPoint(point);
	//this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
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
ED.Circinate.prototype.groupDescription = function()
{
	return "Circinate maculopathy ";
}

/**
 * Returns a string containing a text description of the doodle
 *
 * @returns {String} Description of doodle
 */
ED.Circinate.prototype.description = function()
{
	returnString = "";
    
    var locationString = "";
    
    // Right eye
    if(this.drawing.eye == ED.eye.Right)
    {
        locationString += this.originY <= 0?"supero":"infero";
        locationString += this.originX <= 0?"temporal":"nasal";
    }
    // Left eye
    else
    {
        locationString += this.originY <= 0?"supero":"infero";
        locationString += this.originX >= 0?"temporally":"nasally";  
    }
    
    returnString += locationString;
    returnString += " to the fovea";
    
    return returnString;
}

