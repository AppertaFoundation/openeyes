/**
 * Javascript file containing functions for the EyeDraw widget
 *
 * @link http://www.openeyes.org.uk/
 * @copyright Copyright &copy; 2012 OpenEyes Foundation
 * @license http://www.yiiframework.com/license/
 * Modification date: 9th February 2012
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
 * @package EyeDraw
 * @author Bill Aylward <bill.aylward@openeyes.org>
 * @version 0.9
 */
 
/**
 * Function runs on page load to initialise an EyeDraw canvas
 *
 * @param {array} _properties Array of properties passed from widget 
 * - drawingName The EyeDraw drawing object
 * - canvasId The DOM id of the associated canvas element
 * - eye The eye (right = 0, left = 1) ***TODO*** handle this better
 * - idSuffix A suffix for DOM elements to distinguish those associated with this drawing object
 * - isEditable Flag indicating whether drawing object is editable or not
 * - graphicsPath Path to folder containing EyeDraw graphics,
 * - onLoadedCommandArray Array of commands and arguments to be run when images are loaded
 */
function eyeDrawInit(_properties)
{
    // Get reference to the canvas
    var canvas = document.getElementById(_properties.canvasId);
    
    // Create drawing
    window[_properties.drawingName] = new ED.Drawing(canvas, _properties.eye, _properties.idSuffix, _properties.isEditable, _properties.offset_x, _properties.offset_y);
    
    // Preload any images
    window[_properties.drawingName].preLoadImagesFrom(_properties.graphicsPath);
    
    // Set focus to the canvas element
		if (_properties.focus) {
			canvas.focus();
		}
    
    // Wait for the drawing object to be ready before adding objects or other commands
    window[_properties.drawingName].onLoaded = function()
    {
    	// Check for an element containing data
    	var dataElement = document.getElementById(_properties.inputId);
    	
    	// If dataElement exists and contains data, load it into the drawing
    	if (dataElement != null && dataElement.value.length > 0)
    	{
    		window[_properties.drawingName].loadDoodles(_properties.inputId);
    		window[_properties.drawingName].drawAllDoodles();
    	}
    	// Otherwise iterate through the command array, constructing argument string and running them
    	else
    	{
	        for (var i = 0; i < _properties.onLoadedCommandArray.length; i++)
	        {
	            // Get function name
	            var func = _properties.onLoadedCommandArray[i][0];
	            
	            // Get arguments into a string
	            var args = "";
	            for (var j = 0; j < _properties.onLoadedCommandArray[i][1].length; j++)
	            {
	                args += _properties.onLoadedCommandArray[i][1][j] + ","; // ***TODO*** will this work >1 one argument?
	            }
	            
	            // Remove final comma
	            if (_properties.onLoadedCommandArray[i][1].length > 0) args = args.substring(0, args.length - 1);
	
	            // Run function and arguments  ***TODO*** investigate possible bugs from translation of 'this'
	            window[_properties.drawingName][func](args);
	        }
    	}
			
			// Mark the drawing unmodified
			window[_properties.drawingName]["isReady"]();
    }
    
    // Detects changes in doodle parameters (eg from mouse dragging)
    window[_properties.drawingName].parameterListener = function()
    {
    	// Pass drawing object to user function        
        eDparameterListener(window[_properties.drawingName]);
        
        // Save changes to value of hidden element
        document.getElementById(_properties.inputId).value = window[_properties.drawingName].save();
    }
}
