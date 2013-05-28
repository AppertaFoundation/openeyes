/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 *  Square (Template doodle)
 */

// Constructor
ED.Square = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Square";
}

// Set superclass and constructor
ED.Square.prototype = new ED.Doodle;
ED.Square.prototype.constructor = ED.Square;
ED.Square.superclass = ED.Doodle.prototype;

// Set handles
ED.Square.prototype.setHandles = function()
{
	this.handleArray[0] = new ED.Handle(null, true, Mode.Scale, false);
	this.handleArray[1] = new ED.Handle(null, true, Mode.Scale, false);
	this.handleArray[2] = new ED.Handle(null, true, Mode.Scale, false);
	this.handleArray[3] = new ED.Handle(null, true, Mode.Scale, true);
	this.handleArray[4] = new ED.Handle(null, true, Mode.Apex, false);
}

// Assign dragging settings for doodle
ED.Square.prototype.setDraggingDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+1, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-400, +100);
}

// Default Parameters
ED.Square.prototype.setParameterDefaults = function()
{
	this.originY = -300;
}

// Dual mode function, passing a Point object specifies hit test
ED.Square.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Square.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
	
	// Square
	ctx.rect(-50, -50, 100, 100);
	
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
	ctx.fillStyle = "green";
	ctx.strokeStyle = "blue";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == DrawFunctionMode.Draw)
	{
		ctx.beginPath();
		ctx.rect(-40, -20, 20, 20);
		ctx.lineWidth = 2;
		ctx.fillStyle = "red";
		ctx.strokeStyle = "blue";
		ctx.fill();
		ctx.stroke();
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(new ED.Point(-50, 50));
	this.handleArray[1].location = this.transform.transformPoint(new ED.Point(-50, -50));
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(50, -50));
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(50, 50));
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
	// Draw handles if selected
	if (this.isSelected) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

/**
 * Fundus
 */

// Constructor
ED.Fundus = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "Fundus";
}

// Set superclass and constructor
ED.Fundus.prototype = new ED.Doodle;
ED.Fundus.prototype.constructor = ED.Fundus;
ED.Fundus.superclass = ED.Doodle.prototype;

// Assign dragging settings for doodle
ED.Fundus.prototype.setDraggingDefaults = function()
{
	this.isSelectable = false;
}

// Dual mode function, passing a Point object specifies hit test
ED.Fundus.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Fundus.superclass.draw.call(this, _point);

	// Boundary path
	ctx.beginPath();
	
	// Ora
	ctx.arc(0,0,480,0,Math.PI*2,true);
	
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
	this.isFilled = false;
	ctx.strokeStyle = "red";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == DrawFunctionMode.Draw)
	{
		// These values different for right and left side
		if(this.drawing.eye != Eye.Right)
		{
			var startX = 200;
			var midX = 100;
			var ctrlX = -50;
			var endX = -100;
			var foveaX = 100;
		}
		else
		{
			var startX = -200;
			var midX = -100;
			var ctrlX = 50;
			var endX = 100;
			var foveaX = -100;			
		}
		
		// Superior arcades
		ctx.moveTo(startX, -50);
		ctx.bezierCurveTo(midX, -166, 0, -62, 0, -12);
		ctx.bezierCurveTo(0, -40, ctrlX, -100, endX, -50);
		
		// Inferior arcades
		ctx.moveTo(startX, 50);
		ctx.bezierCurveTo(midX, 166, 0, 62, 0, 12);
		ctx.bezierCurveTo(0, 40, ctrlX, 100, endX, 50);
		
		// Small cross marking fovea
		var crossLength = 10;
		ctx.moveTo(foveaX, -crossLength);
		ctx.lineTo(foveaX, crossLength);
		ctx.moveTo(foveaX - crossLength, 0);
		ctx.lineTo(foveaX + crossLength, 0);
		
		// Optic disc and cup 
		ctx.moveTo(25, 0);
		ctx.arc(0,0,25,0,Math.PI*2,true);
		ctx.moveTo(12, 0);
		ctx.arc(0,0,12,0,Math.PI*2,true);
		
		// Draw it
		ctx.stroke();
	}
	
	// Return value indicating successful hittest
	return this.isClicked;
}

// Description
ED.Fundus.prototype.description = function()
{
	return "";
}

/**
 *  UTear
 */

// Constructor
ED.UTear = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "UTear";
}

// Set superclass and constructor
ED.UTear.prototype = new ED.Doodle;
ED.UTear.prototype.constructor = ED.UTear;
ED.UTear.superclass = ED.Doodle.prototype;

// Set handles
ED.UTear.prototype.setHandles = function()
{
	this.handleArray[3] = new ED.Handle(null, true, Mode.Scale, false);
	this.handleArray[4] = new ED.Handle(null, true, Mode.Apex, false);
}

// Assign dragging settings for doodle
ED.UTear.prototype.setDraggingDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = true;
	this.isSqueezable = true;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-40, +30);
}

// Default Parameters
ED.UTear.prototype.setParameterDefaults = function()
{
	this.originY = -300;
    this.apexY = -20;
}

// Dual mode function, passing a Point object specifies hit test
ED.UTear.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.UTear.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
	
	// U tear
	ctx.moveTo(0, 40);
	ctx.bezierCurveTo(-20, 40, -40, -20, -40, -40);
	ctx.bezierCurveTo(-40, -60, -20, this.apexY, 0, this.apexY);
	ctx.bezierCurveTo(20, this.apexY, 40, -60, 40, -40);
	ctx.bezierCurveTo(40, -20, 20, 40, 0, 40);

	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;
	ctx.fillStyle = "red";
	ctx.strokeStyle = "blue";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == DrawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(40, -40));
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
	// Draw handles if selected
	if (this.isSelected) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

// Description
ED.UTear.prototype.description = function()
{
    var returnString = "";
    
    // Size description
    if (this.scaleX < 1) returnString = "Small ";
    if (this.scaleX > 1.5) returnString = "Large ";
    
    // U tear
	returnString += "'U' tear at ";
	
    // Location (clockhours)
	returnString += this.clockHour() + " o'clock";
	
	return returnString;
}

/**
 *  RoundHole
 */

// Constructor
ED.RoundHole = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "RoundHole";
}

// Set superclass and constructor
ED.RoundHole.prototype = new ED.Doodle;
ED.RoundHole.prototype.constructor = ED.RoundHole;
ED.RoundHole.superclass = ED.Doodle.prototype;

// Set handles
ED.RoundHole.prototype.setHandles = function()
{
	this.handleArray[2] = new ED.Handle(null, true, Mode.Scale, false);
}

// Assign dragging settings for doodle
ED.RoundHole.prototype.setDraggingDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = true;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = true;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.5, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-40, +30);
}

// Default Parameters
ED.RoundHole.prototype.setParameterDefaults = function()
{
	this.originY = -376;
}

// Dual mode function, passing a Point object specifies hit test
ED.RoundHole.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.RoundHole.superclass.draw.call(this, _point);
	
	// Boundary path
	ctx.beginPath();
	
	// Round hole
	ctx.arc(0,0,30,0,Math.PI*2,true);
    
	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;
	ctx.fillStyle = "red";
	ctx.strokeStyle = "blue";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == DrawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(21, -21));
	
	// Draw handles if selected
	if (this.isSelected) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

// Description
ED.RoundHole.prototype.description = function()
{
    var returnString = "";
    
    // Size description
    if (this.scaleX < 1) returnString = "Small ";
    if (this.scaleX > 1.5) returnString = "Large ";
    
    // U tear
	returnString += "Round hole ";
	
    // Location (clockhours)
	returnString += this.clockHour() + " o'clock";
	
	return returnString;
}

/**
 *  RRD
 */

// Constructor
ED.RRD = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call superclass constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order);
	
	// Set classname
	this.className = "RRD";
}

// Set superclass and constructor
ED.RRD.prototype = new ED.Doodle;
ED.RRD.prototype.constructor = ED.RRD;
ED.RRD.superclass = ED.Doodle.prototype;

// Set handles
ED.RRD.prototype.setHandles = function()
{
	this.handleArray[1] = new ED.Handle(null, true, Mode.Arc, false);
	this.handleArray[2] = new ED.Handle(null, true, Mode.Arc, false);
	this.handleArray[4] = new ED.Handle(null, true, Mode.Apex, false);
}

// Assign dragging settings for doodle
ED.RRD.prototype.setDraggingDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+1, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-400, +400);
}

// Default Parameters
ED.RRD.prototype.setParameterDefaults = function()
{
    this.arc = 120 * Math.PI/180;
    this.apexY = -100;
}

// Dual mode function, passing a Point object specifies hit test
ED.RRD.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.RRD.superclass.draw.call(this, _point);
	
	// Fit outer curve just inside ora on right and left fundus diagrams
	var r = 952/2;

	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
	
	// Coordinates of corners of arc
	var topRightX = r * Math.sin(theta);
	var topRightY = - r * Math.cos(theta);
	var topLeftX = - r * Math.sin(theta);
	var topLeftY = topRightY;
	
	// Boundary path
	ctx.beginPath();
	
	// Start at top right
	//ctx.moveTo(topRightX, topRightY);
	
	// Arc across from top right to to mirror image point on the other side
	ctx.arc(0, 0, r, arcStart, arcEnd, true);
	
	// Connect across the bottom via the apex point
	var bp = +0.6;
	
	// Radius of disc (from Fundus doodle)
	var dr = +25;
	
	// RD above optic disc
	if (this.apexY < -dr)
	{
		ctx.bezierCurveTo(topLeftX, topLeftY, bp * topLeftX, this.apexY, this.apexX, this.apexY);
		ctx.bezierCurveTo(-bp * topLeftX, this.apexY, topRightX, topRightY, topRightX, topRightY);
	}
	// RRD involves optic disc
	else if (this.apexY < dr)
	{
		// Angle from origin to intersection of disc margin with a horizontal line through apexY
		var phi = Math.acos((0 - this.apexY)/dr);
		
		// Curve to disc, curve around it, then curve out again
		var xd = dr * Math.sin(phi);
		ctx.bezierCurveTo(topLeftX, topLeftY, bp * topLeftX, this.apexY, -xd, this.apexY);
		ctx.arc(0, 0, dr, -Math.PI/2 - phi, -Math.PI/2 + phi, false);
		ctx.bezierCurveTo(-bp * topLeftX, this.apexY, topRightX, topRightY, topRightX, topRightY);
	}
	// RRD beyond optic disc
	else
	{
		ctx.bezierCurveTo(topLeftX, topLeftY, bp * topLeftX, this.apexY, 0, 25);
		ctx.arc(0, 0, dr, Math.PI/2, 2.5*Math.PI, false);
		ctx.bezierCurveTo(-bp * topLeftX, this.apexY, topRightX, topRightY, topRightX, topRightY);
	}
	
	// Close path
	//ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 4;
	ctx.fillStyle = "rgba(0, 0, 255, 0.75)";
	ctx.strokeStyle = "blue";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other stuff here
	if (this.drawFunctionMode == DrawFunctionMode.Draw)
	{
	}
	
	// Coordinates of handles (in canvas plane)
	this.handleArray[1].location = this.transform.transformPoint(new ED.Point(topLeftX, topLeftY));
	this.handleArray[2].location = this.transform.transformPoint(new ED.Point(topRightX, topRightY));
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
	// Draw handles if selected
	if (this.isSelected) this.drawHandles(_point);
	
	// Return value indicating successful hittest
	return this.isClicked;
}

// Description
ED.RRD.prototype.description = function()
{
    // Get side
    if(this.drawing.eye == Eye.Right)
	{
		var isRightSide = true;
	}
	else
	{
		var isRightSide = false;
	}
    
	// Construct description
	var returnString = "";
	
	// Use trigonometry on rotation field to determine quadrant
	returnString = returnString + (Math.cos(this.rotation) > 0?"Supero":"Infero");
	returnString = returnString + (Math.sin(this.rotation) > 0?(isRightSide?"nasal":"temporal"):(isRightSide?"temporal":"nasal"));
	returnString = returnString + " retinal detachment";
	returnString = returnString + (this.isMacOff()?" (macula off)":" (macula on)");
	
	// Return description
	return returnString;
}

// SnoMed code
ED.RRD.prototype.snomedCode = function()
{
	return (this.isMacOff()?232009009:232008001);
}

// DiagnosticHierarchy
ED.RRD.prototype.diagnosticHierarchy = function()
{
	return (this.isMacOff()?10:9);
}

ED.RRD.prototype.isMacOff = function()
{
	// Get coordinates of macula in doodle plane
	if(this.drawing.eye == Eye.Right)
	{
		var macula = new ED.Point(-100,0);
	}
	else
	{
		var macula = new ED.Point(100,0);
	}
	
	// Convert to canvas plane
	var maculaCanvas = this.drawing.transform.transformPoint(macula);
	
	// determine whether macula is on or not
	if (this.draw(maculaCanvas)) return true;
	else return false;
}

/**
 * Buckle
 */

// Constructor
ED.Buckle = function(_drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order)
{
	// Call super-class constructor
	ED.Doodle.call(this, _drawing, _originX, _originY, _apexX, _apexY, _scaleX, _scaleY, _arc, _rotation, _order); 
	
	// Set classname
	this.className = "Buckle";
}

// Set superclass and constructor
ED.Buckle.prototype = new ED.Doodle;
ED.Buckle.prototype.constructor = ED.Buckle;
ED.Buckle.superclass = ED.Doodle.prototype;

// Set handles
ED.Buckle.prototype.setHandles = function()
{
	this.handleArray[0] = new ED.Handle(null, true, Mode.Arc, false);
	this.handleArray[3] = new ED.Handle(null, true, Mode.Arc, false);
	this.handleArray[4] = new ED.Handle(null, true, Mode.Apex, false);
}

// Assign dragging settings for doodle
ED.Buckle.prototype.setDraggingDefaults = function()
{
	this.isSelectable = true;
	this.isOrientated = false;
	this.isScaleable = true;
	this.isSqueezable = false;
	this.isMoveable = false;
	this.isRotatable = true;
	this.rangeOfScale = new ED.Range(+0.25, +4);
	this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
	this.rangeOfApexX = new ED.Range(-0, +0);
	this.rangeOfApexY = new ED.Range(-400, +100);
}

// Dual mode function, passing a Point object specifies hit test
ED.Buckle.prototype.draw = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// Call draw method in superclass
	ED.Buckle.superclass.draw.call(this, _point);

	// Radius of outer curve just inside ora on right and left fundus diagrams
	var r = 952/2;
	
	// Inner arc depends on value of apexY
	var ri = Math.abs(this.apexY);
	
	// Lock value of ri within a certain range
	if (ri > (r - 50)) ri = r - 50;
	if (ri < 250) ri = 250;
	
	// Similarly lock value of apexY 
	if (this.apexY != 0 ) this.apexY = (this.apexY/Math.abs(this.apexY)) * ri;
	
	// Calculate parameters for arcs
	var theta = this.arc/2;
	var arcStart = - Math.PI/2 + theta;
	var arcEnd = - Math.PI/2 - theta;
	
	// Coordinates of 'corners' of buckle
	var topRightX = r * Math.sin(theta);
	var topRightY = - r * Math.cos(theta);
	var topRightInnerX = ri * Math.sin(theta);
	var topRightInnerY = - ri * Math.cos(theta);
	var topLeftX = - r * Math.sin(theta);
	var topLeftY = topRightY;
	var topLeftInnerX = - ri * Math.sin(theta);
	var topLeftInnerY = topRightInnerY;
	
	// Boundary path
	ctx.beginPath();
	
	// Arc across to mirror image point on the other side
	ctx.arc(0, 0, r, arcStart, arcEnd, true);

	// Arc back to mirror image point on the other side
	ctx.arc(0, 0, ri, arcEnd, arcStart, false);

	// Close path
	ctx.closePath();
	
	// Set line attributes
	ctx.lineWidth = 2;
	ctx.fillStyle = "lightgray";
	ctx.strokeStyle = "gray";
	
	// Draw boundary path (also hit testing)
	this.drawBoundary(_point);
	
	// Other paths and drawing here
	if (this.drawFunctionMode == DrawFunctionMode.Draw)
	{
	}

	// Coordinates of handles (in canvas plane)
	this.handleArray[0].location = this.transform.transformPoint(new ED.Point(topLeftInnerX - (topLeftInnerX - topLeftX)/2, topLeftInnerY - (topLeftInnerY - topLeftY)/2));
	this.handleArray[3].location = this.transform.transformPoint(new ED.Point(topRightInnerX + (topRightX - topRightInnerX)/2, topRightInnerY - (topRightInnerY - topRightY)/2));
	this.handleArray[4].location = this.transform.transformPoint(new ED.Point(this.apexX, this.apexY));
	
	// Draw handles if selected
	if (this.isSelected) this.drawHandles(_point);

	// Return value indicating successful hit test
	return this.isClicked;
}




