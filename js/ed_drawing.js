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
 * Define the EyeDraw namespace and EyeDraw classes
 */
if (EyeDraw == null || typeof(EyeDraw) != "object") { var EyeDraw = new Object();}
var ED = EyeDraw;

/**
 * Constants
 */
 
/**
 * Radius of handle of bounding rectangle displayed around selected doodle
 */
var handleRadius = 15;

/**
 * Eye
 */
var Eye = 
{
	Right:0,
	Left:1
}

/**
 * Draw function mode (Canvas pointInPath function needs a draw-ready path)
 */
var DrawFunctionMode = 
{
	Draw:0,
	HitTest:1
}

/**
 * Dragging mode
 */
var Mode = 
{
	None:0,
	Move:1,
	Scale:2,
	Arc:3,
	Rotate:4,
	Apex:5
}

/**
 * Handle ring
 */
var HandleRing =
{
	Inner:0,
	Outer:1
}
	

/**
 * Selected handle of bounding rectangle
 */

/**
 * Drawing class
 *
 * Doodles are drawn in the 'doodle plane' consisting of a 1001 pixel square grid -500 to 500) with central origin, and negative Y at the top
 * Affine transforms are used to convert points in the doodle plane to the canvas plane, the plane of the canvas element;
 * Each doodle contains additional transforms to handle individual position, rotation, and scale
 * 
 * @package Clinical
 * @property Canvas canvas A canvas element used to edit and display the drawing
 * @property Eye eye Right or left eye (some doodles display differently according to side)
 * @property Context the 2d context of the canvas element
 * @property Array doodleArray Array of doodles in the drawing
 * @property AffineTransform transform Converts doodle plane -> canvas plane
 * @property AffineTransform inverseTransform Converts canvas plane -> doodle plane
 * @property Doodle selectedDoodle The currently selected doodle, null if no selection
 * @property Bool mouseDown Flag indicating whether mouse is down in canvas
 * @property Mode mode The current dragging mode
 * @property Point lastMousePosition Last position of mouse in canvas coordinates
 */
 
/**
 * Constructor
 * 
 * @param Canvas _canvas Canvas element 
 * @param Eye _eye Right or left eye
 * @param string _IDSuffix String suffix to identify elements related to this drawing
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
	this.mode = Mode.Move;
	this.lastMousePosition = new ED.Point(0, 0);

	// Set transform to map from doodle to canvas plane
	this.transform.translate(this.canvas.width/2, this.canvas.height/2);
	this.transform.scale(this.canvas.width/1001, this.canvas.height/1001);
	
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
}

/**
 * Load function
 * 
 * Loads doodles from passed set into doodleArray
 *
 * @param Set _doodleSet Set of doodles to display output from database
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
	}
	
	// Sort array by order (puts back doodle first)
	this.doodleArray.sort(function(a,b){return a.order - b.order});
}

/**
 * dataString function
 * 
 * Outputs a semicolon deliminated set of comma deliminated strings containing doodle information
 */
ED.Drawing.prototype.dataString = function()
{
	// Set data string to empty string
	var dataString = "";
	
	// Go through doodle array adding to string
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		var d =  this.doodleArray[i];
		var v = "";
		v = v + d.originX.toFixed(0) + ",";
		v = v + d.originY.toFixed(0) + ",";
		v = v + d.apexX.toFixed(0) + ",";
		v = v + d.apexY.toFixed(0) + ",";
		v = v + d.scaleX.toFixed(2) + ",";
		v = v + d.scaleY.toFixed(2) + ",";
		v = v + (d.arc * 180/Math.PI).toFixed(0) + ",";
		v = v + (d.rotation * 180/Math.PI).toFixed(0) + ",";
		v = v + "'" + d.className + "',";
		v = v + d.order.toFixed(0);
		
		// Add values to string and add semicolon
		dataString = dataString + v + ";";
	}
    
	return dataString;
}

/**
 * jsonString function
 * 
 * Outputs JSON of doodle information
 * EG:
 *   [
 *       {subclass: "Fundus", originX: "0", originY: "0", apexX: "0", apexY: "0", scaleX: "1", scaleY: "1", arc: "0", rotation: "0", order: "0"},
 *       {subclass: "RRD", originX: "0", originY: "0", apexX: "0", apexY: "105", scaleX: "1", scaleY: "1", arc: "160", rotation: "330", order: "1"},
 *       {subclass: "UTear", originX: "-212", originY: "-320", apexX: "0", apexY: "-22", scaleX: "1", scaleY: "1", arc: "0", rotation: "327", order: "2"}
 *   ]
 */
ED.Drawing.prototype.jsonString = function()
{
	var jsonString = '[';
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		jsonString = jsonString + '{';
		var d =  this.doodleArray[i];
		var v = "";
		v = v + 'subclass: ' + '"' + d.className + '", '
		v = v + 'originX: ' + '"' + d.originX.toFixed(0) + '", '
		v = v + 'originY: ' + '"' + d.originY.toFixed(0) + '", '
		v = v + 'apexX: ' + '"' + d.apexX.toFixed(0) + '", '
		v = v + 'apexY: ' + '"' + d.apexY.toFixed(0) + '", '
		v = v + 'scaleX: ' + '"' + d.scaleX.toFixed(2) + '", '
		v = v + 'scaleY: ' + '"' + d.scaleY.toFixed(2) + '", '
		v = v + 'arc: ' + '"' + (d.arc * 180/Math.PI).toFixed(0)  + '", '
		v = v + 'rotation: ' + '"' + (d.rotation * 180/Math.PI).toFixed(0) + '", '
		v = v + 'order: ' + '"' + d.order.toFixed(0) + '", '
		jsonString = jsonString + v + '},';
	}
	jsonString = jsonString.slice(0, -1);
	jsonString = jsonString + ']';
	return jsonString;
}

/**
 * Draw doodles
 *
 * Draw all doodles in array
 */ 
ED.Drawing.prototype.drawAllDoodles = function()
{	
	// Draw each doodle
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
 * Mouse down
 *
 * Responds to mouse down event in canvas
 * Cycles through doodles from front to back
 * Selected is first selectable object to have click within boundary path
 *
 * @param Point _point coordinates of mouse in canvas plane
 */  
ED.Drawing.prototype.mousedown = function(_point)
{
	// Set flag to indicate dragging can now take place
	this.mouseDown = true;
	
	// Set flag to indicate success
	var found = false;
	this.selectedDoodle = null;

	// Cycle through doodles from front to back
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
					this.doodleArray[i].isSelected = true;
					this.selectedDoodle = this.doodleArray[i];
					found = true;
				}
			}
			// Ensure that unselected doodles are marked as such
			else
			{
				this.doodleArray[i].isSelected = false;
			}
			
			// Restore context
			this.context.restore();
		}
		else
		{
			this.doodleArray[i].isSelected = false;
		}
		
		// Ensure drag flagged is off for each doodle
		this.doodleArray[i].isBeingDragged = false;
	}
	
	// Repaint
	this.repaint();
}

/**
 * Mouse move
 *
 * Responds to mouse move event in canvas
 *
 * @param Point _point coordinates of mouse in canvas plane
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
				case Mode.None:
					break;
				case Mode.Move:
					// If isMoveable is true, move doodle
					if (this.selectedDoodle.isMoveable)
					{
						// Move doodle to new position
						this.selectedDoodle.originX += (mousePosDoodlePlane.x - lastMousePosDoodlePlane.x);
						this.selectedDoodle.originY += (mousePosDoodlePlane.y - lastMousePosDoodlePlane.y);
						
						// If doodle isOriented is true, rotate doodle around centre of canvas (eg makes 'U' tears point to centre)
						if (this.selectedDoodle.isOrientated)
						{
							// New position of doodle
							var newDoodleOrigin = new ED.Point(this.selectedDoodle.originX, this.selectedDoodle.originY);
							
							// Calculate angle to current position from centre relative to north
							var angle = this.innerAngle(canvasTop, canvasCentre, newDoodleOrigin);
							
							// Alter orientation of doodle
							this.selectedDoodle.rotation = angle;
						}
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
						}
					}
					break;
				case Mode.Scale:
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
							this.mode = Mode.None;
						}
					}
					break;
				case Mode.Arc:
					if (true)
					{
						// Calculate angles from centre to mouse positions relative to north
						var newAngle = this.innerAngle(doodleTop, doodleOrigin, mousePosSelectedDoodlePlane);
						var oldAngle = this.innerAngle(doodleTop, doodleOrigin, lastMousePosSelectedDoodlePlane);
						
						// Work out difference, and sign of rotation correction
						var deltaAngle = newAngle - oldAngle;
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
							this.mode = Mode.none;
						}
						else if (this.selectedDoodle.rangeOfArc.isAbove(this.selectedDoodle.arc + deltaAngle))
						{
							deltaAngle = this.selectedDoodle.rangeOfArc.max - this.selectedDoodle.arc;
							this.selectedDoodle.arc = this.selectedDoodle.rangeOfArc.max;
							this.mode = Mode.none;
						}
						else
						{
							this.selectedDoodle.arc += deltaAngle;
						}
						
						// Correct rotation with counter-rotation
						rotationCorrection = rotationCorrection * deltaAngle/2;
						this.selectedDoodle.rotation += rotationCorrection;
					}
					break;
				case Mode.Rotate:
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
				case Mode.Apex:
					// Move apex to new position
					this.selectedDoodle.apexX += (mousePosSelectedDoodlePlane.x - lastMousePosSelectedDoodlePlane.x);
					this.selectedDoodle.apexY += (mousePosSelectedDoodlePlane.y - lastMousePosSelectedDoodlePlane.y);
					
					// Enforce bounds
					this.selectedDoodle.apexX = this.selectedDoodle.rangeOfApexX.constrain(this.selectedDoodle.apexX);
					this.selectedDoodle.apexY = this.selectedDoodle.rangeOfApexY.constrain(this.selectedDoodle.apexY);
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
 * Mouse up
 *
 * Responds to mouse up event in canvas
 *
 * @param Point _point coordinates of mouse in canvas plane
 */  
ED.Drawing.prototype.mouseup = function(_point)
{
	// Reset flag
	this.mouseDown = false;
	
	// Reset selected doodle's dragging flag
	if (this.selectedDoodle != null)
	{
		this.selectedDoodle.isBeingDragged = false;
	}
}

// Move selected doodle to front
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

// Move selected doodle to back
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
 * deleteDoodle
 *
 * Deletes selected doodle from the drawing
 */
ED.Drawing.prototype.deleteDoodle = function()
{
	// Should only be called if a doodle is selected, but check anyway
	if (this.selectedDoodle != null)
	{
		// Go through doodles removing any that are selected (should be just one)
		for (var i = 0; i < this.doodleArray.length; i++)
		{
			if (this.doodleArray[i].isSelected)
			{
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
 * lock
 *
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
 * unlock
 *
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
 * Add Doodle
 *
 * Adds a doodle to the drawing
 *
 * @param string _class Classname of doodle
 */
ED.Drawing.prototype.addDoodle = function(_className)
{
	// Ensure no other doodles are selected
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		this.doodleArray[i].isSelected = false;
	}

	// Create a new doodle of the specified class
	var newDoodle = new ED[_className](this);
	
	// Set default parameters
	newDoodle.setParameterDefaults();
	
	// New doodles are selected by default
	this.selectedDoodle = newDoodle;
	
	// Add to array
	this.doodleArray[this.doodleArray.length] = newDoodle;
	
	// Refresh canvas
	this.repaint();
}

/**
 * report
 *
 * Returns a string containing a description of the drawing
 */
ED.Drawing.prototype.report = function()
{
	var returnString = "";
	
	// Go through every doodle
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		// Get description
		var description = this.doodleArray[i].description() + " code;" +  this.doodleArray[i].snomedCode();
		
		// If its not an empty string, add to the return
		if (description.length > 0)
		{
			returnString = returnString + description;
			
			// Add optional comma (***TODO***) will fail if last doodle description is empty
			if (i < this.doodleArray.length - 1) returnString = returnString + ", ";
		}
	}
	
	return returnString;
}

/**
 * Clear 
 *
 * Clears drawing and set context
 */
ED.Drawing.prototype.clear = function()
{
	// Resetting a dimension attribute clears the canvas and resets the context
	this.canvas.width = this.canvas.width;
	
	// But, might not clear canvas, so do it explicitly
	this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
	
	// Set context transform to map from doodle plane to canvas plane	
	this.context.translate(this.canvas.width/2, this.canvas.height/2);
	this.context.scale(this.canvas.width/1001, this.canvas.height/1001);	
}

/**
 * Repaint 
 *
 * Clears canvas and draws all doodles
 */
ED.Drawing.prototype.repaint = function()
{
	// Clear canvas
	this.clear();
	
	// Redraw all doodles
	this.drawAllDoodles();
	
	// Enable or disable buttons which work on selected doodle
	if (this.selectedDoodle != null)
	{
		this.moveToFrontButton.disabled = false;
		this.moveToBackButton.disabled = false;
		this.deleteButton.disabled = false;
		this.lockButton.disabled = false;
	}
	else
	{
		this.moveToFrontButton.disabled = true;
		this.moveToBackButton.disabled = true;
		this.deleteButton.disabled = true;
		this.lockButton.disabled = true;
	}
	
	// Go through doodles looking for any that are locked and enable/disable unlock button
	this.unlockButton.disabled = true;
	for (var i = 0; i < this.doodleArray.length; i++)
	{
		if (!this.doodleArray[i].isSelectable)
		{
			this.unlockButton.disabled = false;
		}
	}
}

/**
 * Inner angle
 *
 * Calculates angle between three points (clockwise from _pointA to _pointB in radians)
 *
 * @param Point _pointA coordinates of first point
 * @param Point _pointM coordinates of mid point
 * @param Point _pointB coordinates of last point
 */
ED.Drawing.prototype.innerAngle = function(_pointA, _pointM, _pointB)
{
//	var ab = Math.sqrt((_pointA.x - _pointB.x) * (_pointA.x - _pointB.x) + (_pointA.y - _pointB.y) * (_pointA.y - _pointB.y));
//	var bc = Math.sqrt((_pointB.x - _pointC.x) * (_pointB.x - _pointC.x) + (_pointB.y - _pointC.y) * (_pointB.y - _pointC.y));
//	var ca = Math.sqrt((_pointC.x - _pointA.x) * (_pointC.x - _pointA.x) + (_pointC.y - _pointA.y) * (_pointC.y - _pointA.y));
//	var t = ab * ab + bc * bc - ca * ca;
//	
//	// Avoid NAN errors by ensuring no division by zero
//	var z = 2 * ab * bc;
//	return z > 0?Math.acos((ab * ab + bc * bc - ca * ca)/z):0;
	
	// Get vectors from midpoint to A and B
	var a = new ED.Point(_pointA.x - _pointM.x, _pointA.y - _pointM.y);
	var b = new ED.Point(_pointB.x - _pointM.x, _pointB.y - _pointM.y);
	
	return a.clockwiseAngleTo(b);
}

/**
 * Doodle class
 * Doodles are drawn in 'doodle plane' consisting of 1001 pixel square grid with central origin (ie -500 to 500)
 *
 * @property drawing	Drawing to which this doodle belongs
 * @property originX	X coordinate of origin in doodle plane
 * @property originY	Y coordinate of origin in doodle plane
 * @property apexX		X coordinate of apex in doodle plane
 * @property apexY		Y coordinate of apex in doodle plane
 * @property scaleX		Scale of doodle along X axis
 * @property scaleY		Scale of doodle along Y axis
 * @property arc		Angle of arc for doodles that extend in a circular fashion
 * @property rotation	Angle of rotation from 12 o'clock
 * @property order		Order in which doodle is drawn (0 first ie backmost layer)
 */
 

/**
 * Constructor
 *
 * @param Drawing _drawing
 * @param Int _originX
 * @param Int _originY
 * @param Int _apexX
 * @param Int _apexY
 * @param Float _scaleX
 * @param Float _scaleY
 * @param Float _arc
 * @param Float _rotation
 * @param Int _order
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
		}
		
		// Transform used to draw doodle (includes additional transforms specific to the doodle)
		this.transform = new ED.AffineTransform();
		this.inverseTransform = new ED.AffineTransform();
		
		// Dragging defaults - set individual values in subclasses
		this.isSelectable = true;			// True if doodle is locked (ie non-selectable)
		this.isOrientated = false;			// True if doodle should always point to the centre (default = false)
		this.isScaleable = true;			// True if doodle can be scaled. If false, doodle increases its arc angle
		this.isSqueezable = false;			// True if scaleX and scaleY can be independently modifed (ie no fixed aspect ratio)
		this.isMoveable = true;				// True if doodle can be moved. When combined with isOrientated allows quick rotation.
		this.isRotatable = true;			// Trye if doodle can be rotated
		this.rangeOfScale = new ED.Range(+0.5, +4.0);
		this.rangeOfArc = new ED.Range(Math.PI/6, Math.PI*2);
		this.rangeOfApexX = new ED.Range(-500, +500);
		this.rangeOfApexY = new ED.Range(-500, +500);
		
		// Other attributes
		this.isFilled = true;				// If true, then boundary path is filled as well as stroked
		
		// Flags other properties
		this.isBeingDragged = false;
		this.draggingHandleIndex = null;
		this.draggingHandleRing = null;
		this.isClicked = false;
		this.drawFunctionMode = DrawFunctionMode.Draw;
		
		// Array of 5 handles
		this.handleArray = new Array();
		this.handleArray[0] = new ED.Handle(new ED.Point(-50, 50), false, Mode.Scale, false);
		this.handleArray[1] = new ED.Handle(new ED.Point(-50, -50), false, Mode.Scale, false);
		this.handleArray[2] = new ED.Handle(new ED.Point(50, -50), false, Mode.Scale, false);
		this.handleArray[3] = new ED.Handle(new ED.Point(50, 50), false, Mode.Scale, false);
		this.handleArray[4] = new ED.Handle(new ED.Point(this.apexX, this.apexY), false, Mode.Apex, false);
		this.setHandles();
		
		// Set dragging default settings
		this.setDraggingDefaults();
	}
}

/**
 * Set default handle attributes
 *
 * Overridden by subclasses
 */
ED.Doodle.prototype.setHandles = function()
{
}

/**
 * Set defaultm dragging attributes
 *
 * Overridden by subclasses
 */
ED.Doodle.prototype.setDraggingDefaults = function()
{
}

/**
 * Set default parameters
 *
 * Overridden by subclasses to determine initial parameters
 */
ED.Doodle.prototype.setParameterDefaults = function()
{
}


/**
 * Draw
 *
 * Sets transforms for use by subclass drawing methods
 */
ED.Doodle.prototype.draw = function(_point)
{
	// Determine function mode
	if (typeof(_point) != 'undefined')
	{
		this.drawFunctionMode = DrawFunctionMode.HitTest;
	}
	else
	{
		this.drawFunctionMode = DrawFunctionMode.Draw;
	}

	// Get context
	var ctx = this.drawing.context;
	
	// Augment transform with properties of this doodle
	ctx.translate(this.originX, this.originY);
	ctx.rotate(this.rotation);
	ctx.scale(this.scaleX, this.scaleY);
	
	// Mirror with internal transform
	this.transform.setToTransform(this.drawing.transform);
	this.transform.translate(this.originX, this.originY);
	this.transform.rotate(this.rotation);
	this.transform.scale(this.scaleX, this.scaleY);
	
	// Update inverse transform
	this.inverseTransform = this.transform.createInverse();
	
	// Reset hit test flag
	this.isClicked = false;
}

/**
 * Draw Handles
 *
 * Draws selection handles, and sets dragging mode which is determined by which handle and part of handle is selected
 * Function either performs a hit test or draws the handles depending on whether a valid Point object is passed
 *
 * @param Context _point
 */
ED.Doodle.prototype.drawHandles = function(_point)
{
	// Reset handle index and selected ring
	if (this.drawFunctionMode == DrawFunctionMode.HitTest)
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
			ctx.arc(handle.location.x, handle.location.y, handleRadius/2, 0, arc, true);

			// Hit testing for inner ring
			if (this.drawFunctionMode == DrawFunctionMode.HitTest)
			{
				if (ctx.isPointInPath(_point.x, _point.y))
				{
					this.draggingHandleIndex = i;
					this.draggingHandleRing = HandleRing.Inner;
					this.drawing.mode = handle.mode;
					this.isClicked = true;
				}
			}
			
			// Path for optional outer ring
			if (this.isRotatable && handle.isRotatable)
			{
				ctx.moveTo(handle.location.x + handleRadius, handle.location.y);
				ctx.arc(handle.location.x, handle.location.y, handleRadius, 0, arc, true);
				
				// Hit testing for outer ring
				if (this.drawFunctionMode == DrawFunctionMode.HitTest)
				{
					if (ctx.isPointInPath(_point.x, _point.y))
					{
						this.draggingHandleIndex = i;
						if (this.draggingHandleRing == null)
						{
							this.draggingHandleRing = HandleRing.Outer;
							this.drawing.mode = Mode.Rotate;
						}
						this.isClicked = true;
					}
				}
			}
			

			// Draw handles
			if (this.drawFunctionMode == DrawFunctionMode.Draw)
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
 * drawBoundary
 *
 * Draws the boundary path and performs a hit test if a Point parameter is passed
 *
 * @param Point _point A point parameter to perform hit test
 */
ED.Doodle.prototype.drawBoundary = function(_point)
{
	// Get context
	var ctx = this.drawing.context;
	
	// HitTest
	if (this.drawFunctionMode == DrawFunctionMode.HitTest)
	{
		// Workaround for Mozilla bug 405300 https://bugzilla.mozilla.org/show_bug.cgi?id=405300
		if (isFirefox())
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
			// Set default dragging mode
			this.drawing.mode = Mode.Move;
			
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
			ctx.shadowOffsetX = 6;
			ctx.shadowOffsetY = 6;
			ctx.shadowBlur = 10;
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
 * description
 *
 * Returns a string containing a textual description of the doodle
 * Overridden by subclasses
 */
ED.Doodle.prototype.description = function()
{
	var returnString = "Description of " + this.className;
	
	return returnString;
}

/**
 * snomedCode
 *
 * Returns the SnoMed code of the doodle
 * Overridden by subclasses
 */
ED.Doodle.prototype.snomedCode = function()
{
	return 0;
}

/**
 * diagnosticHierarchy
 *
 * Returns a number indicating position in a hierarchy of diagnoses
 * Overridden by subclasses
 */
ED.Doodle.prototype.diagnosticHierarchy = function()
{
	return 0;
}

/**
 * clockHour
 *
 * Returns the rotation converted to clock hours
 */
ED.Doodle.prototype.clockHour = function()
{
	var clockHour = ((this.rotation * 6/Math.PI) + 12) % 12;
	clockHour = clockHour.toFixed(0);
	if (clockHour == 0) clockHour = 12;
	return clockHour;
}

/**
 * Handle Class
 *
 * @property Point location coordinates in doodle plane
 * @property Bool isVisible
 * @property Enum mode The drawing mode that selection of the handle triggers
 * @property Bool isRotatable The handle can have an outer ring which is used for rotation
 */

/**
 * Constructor
 *
 * @param Point _location
 * @param Bool _isVisible
 * @param Enum _mode
 * @param Bool _isRotatable
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
 * Range Class
 *
 * @property Float min
 * @property Float max
 */

/**
 * Constructor
 *
 * @property float min
 * @property float max
 */
ED.Range = function(_min, _max)
{
	// Properties
	this.min = _min;
	this.max = _max;
}

/**
 * isBelow
 *
 * Returns true if parameter is less than minimum of range
 *
 * @param float _num
 * @return boolean
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
 * isAbove
 *
 * Returns true if parameter is greater than maximum of range
 *
 * @param float _num
 * @return boolean
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
 * includes
 *
 * Returns true if parameter is within range
 *
 * @param float _num
 * @return boolean
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
 * Constrain
 *
 * Returns value constrained to range
 *
 * @param float _num
 * @return float
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
 * Point Class
 *
 * @property float x
 * @property float y
 */

/**
 * Constructor
 *
 * @param Float _x
 * @param Float _y
 */ 
ED.Point = function(_x, _y)
{
	// Properties
    this.x = +_x;
    this.y = +_y;
    this.components = [this.x, this.y, 1];
}

/**
 * Distance to
 *
 * Calculates distance from this point to another
 *
 * @param Point _point
 */ 
ED.Point.prototype.distanceTo = function(_point)
{
	return Math.sqrt(Math.pow(this.x - _point.x, 2) + Math.pow(this.y - _point.y, 2));
}

// Dot product (treating points as 2D vectors)
ED.Point.prototype.dotProduct = function(_point)
{
	return this.x * _point.x + this.y * _point.y;
}

// Cross product (treating points as 2D vectors)
ED.Point.prototype.crossProduct = function(_point)
{
	return this.x * _point.y - this.y * _point.x;
}

// Length (treating point as a 2D vector)
ED.Point.prototype.length = function()
{
	return Math.sqrt(this.x * this.x + this.y * this.y);
}

// Angle to other vector
//ED.Point.prototype.angleTo = function(_point)
//{
//	return Math.acos(this.dotProduct(_point)/(this.length() * _point.length()));
//}

// Inner angle to other vector from same origin going round clockwise from vector a to vector b
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
 * Affine Transform Class
 *
 * @property Array components	Array representing 3x3 matrix
 */

/**
 * Constructor
 *
 * Returns a new transformation matrix initialised to the identity matrix
 */
ED.AffineTransform = function()
{
	// Properties - array of arrays of column values one for each row
 	this.components = [[1,0,0],[0,1,0],[0,0,1]];
}

/**
 * Set to identity
 *
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
 * Set to transform
 *
 * Sets the transform matrix to another
 *
 * @param AffineTransform _transform
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
 * Translate
 *
 * Adds translation transform
 *
 * @param float _x
 * @param float _y
 */
ED.AffineTransform.prototype.translate = function(_x, _y)
{
	this.components[0][2] = this.components[0][0] * _x + this.components[0][1] * _y + this.components[0][2];
	this.components[1][2] = this.components[1][0] * _x + this.components[1][1] * _y + this.components[1][2];
	this.components[2][2] = this.components[2][0] * _x + this.components[2][1] * _y + this.components[2][2];
}

/**
 * Scale
 *
 * Adds scale transform
 *
 * @param float _sx
 * @param float _sy
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
 * Rotate
 *
 * Adds transform to rotate by an angle clockwise in radians)
 *
 * @param float _rad
 */
ED.AffineTransform.prototype.rotate = function(_rad)
{
	// Convert to radians
	//var rad =  _deg * Math.PI/180;
	
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
 * Transform point
 *
 * Applies transform to a passed point
 *
 * @param Point _point
 */
ED.AffineTransform.prototype.transformPoint = function(_point)
{
	//_point.print();
	var newX = _point.x * this.components[0][0] + _point.y * this.components[0][1] + 1 * this.components[0][2];
	var newY = _point.x * this.components[1][0] + _point.y * this.components[1][1] + 1 * this.components[1][2];

	return new ED.Point(newX, newY);
}

/**
 * Determinant
 *
 * Return determinant
 */
ED.AffineTransform.prototype.determinant = function()
{
	return  this.components[0][0] * (this.components[1][1] * this.components[2][2] - this.components[1][2] * this.components[2][1]) - 
			this.components[0][1] * (this.components[1][0] * this.components[2][2] - this.components[1][2] * this.components[2][0]) +
			this.components[0][2] * (this.components[1][0] * this.components[2][1] - this.components[1][1] * this.components[2][0]);
}

/**
 * Create Inverse
 *
 * Return inverse of transform
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
