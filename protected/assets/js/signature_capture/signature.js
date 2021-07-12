/*************************************************

 Signsend - The signature capture webapp sample using HTML5 Canvas

 Author: Jack Wong <jack.wong@zetakey.com>
 Copyright (c): 2014 Zetakey Solutions Limited, all rights reserved

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 You may contact the author of Jack Wong by e-mail at:
 jack.wong@zetakey.com

 The latest version can obtained from:
 https://github.com/jackccwong/signsend

 The live demo is located at:
 http://apps.zetakey.com/signsend

 **************************************************/
var zkSignature = (function () {
        
	var empty = true;
        var widgetID;
        var widgetid;
        
        /**
         * Small canvas identifier
         * @type type
         */
        var canvasCopyID;
       
	return {
		//public functions
		capture: function (){
				var scale = 4;
				//var parent = document.getElementById("canvas");
				var parent = $('.'+this.widgetID).find('#canvas')[0];
                                //if(parent.childNodes[0].nodeValue ){
                                    parent.childNodes[0].nodeValue = "";
                               // }

				var canvasArea = document.createElement("canvas");
				canvasArea.setAttribute("id", this.widgetID);
				parent.appendChild(canvasArea);

				var canvas = document.getElementById(this.canvasCopyID);
				var canvas_event = document.getElementById( this.widgetID );

				if (!canvas.getContext) {
                                    parent.childNodes[0].nodeValue = "Failed to get canvas' 2d context";
                                    throw new Error("Failed to get canvas' 2d context");
				}
                                
                                var context = canvas.getContext("2d");
				screenwidth = screen.width;
				screenheight = screen.height;

				canvas_event.width = screenwidth - 8;
				canvas_event.height = screenheight - 8;
				
				canvas.width = canvas_event.width/scale;
				canvas.height = canvas_event.height/scale;

				context.lineWidth = 2;
				context.lineCap = "round";

				//context.fillRect(0, 0, canvas.width, canvas.height);

				//context.fillStyle = "#3a87ad";
				//context.strokeStyle = "#3a87ad";
				context.lineWidth = 2;
				//context.moveTo((canvas.width * 0.042), (canvas.height * 0.7));
				//context.lineTo((canvas.width * 0.958), (canvas.height * 0.7));
				//context.stroke();

				context.fillStyle = "#fff";
				context.strokeStyle = "#000";
                                context.fillRect(0, 0, canvas.width, canvas.height);

				var disableSave = true;
				var pixels = [];
				var cpixels = [];
				var xyLast = {};
				var xyAddLast = {};
				var calculate = false;
				//functions
				{
					function remove_event_listeners() {
						canvas_event.removeEventListener('mousemove', on_mousemove, false);
						canvas_event.removeEventListener('mouseup', on_mouseup, false);
						canvas_event.removeEventListener('touchmove', on_mousemove, false);
						canvas_event.removeEventListener('touchend', on_mouseup, false);

						document.body.removeEventListener('mouseup', on_mouseup, false);
						document.body.removeEventListener('touchend', on_mouseup, false);
					}

					function get_board_coords(e) {
						var x, y;

						if (e.changedTouches && e.changedTouches[0]) {
							var offsety = canvas_event.offsetTop || 0;
							var offsetx = canvas_event.offsetLeft || 0;

							//x = e.changedTouches[0].pageX - offsetx;
							//y = e.changedTouches[0].pageY - offsety;
                                                        
                                                        x = e.changedTouches[0].clientX - offsetx;
							y = e.changedTouches[0].clientY - offsety;
						} else if (e.layerX || 0 == e.layerX) {
							x = e.layerX;
							y = e.layerY;
						} else if (e.offsetX || 0 == e.offsetX) {
							x = e.offsetX;
							y = e.offsetY;
						}

						return {
							x : x/scale,
							y : y/scale
						};
					};

					function on_mousedown(e) {
						e.preventDefault();
						e.stopPropagation();

						canvas_event.addEventListener('mousemove', on_mousemove, false);
						canvas_event.addEventListener('mouseup', on_mouseup, false);
						canvas_event.addEventListener('touchmove', on_mousemove, false);
						canvas_event.addEventListener('touchend', on_mouseup, false);

						document.body.addEventListener('mouseup', on_mouseup, false);
						document.body.addEventListener('touchend', on_mouseup, false);
						
						empty = false;
						var xy = get_board_coords(e);
						context.beginPath();
						pixels.push('moveStart');
						context.moveTo(xy.x, xy.y);
						pixels.push(xy.x, xy.y);
						xyLast = xy;
					};

					function on_mousemove(e, finish) {

						e.preventDefault();
						e.stopPropagation();

						var xy = get_board_coords(e);
						var xyAdd = {
							x : (xyLast.x + xy.x) / 2,
							y : (xyLast.y + xy.y) / 2
						};

						if (calculate) {
							var xLast = (xyAddLast.x + xyLast.x + xyAdd.x) / 3;
							var yLast = (xyAddLast.y + xyLast.y + xyAdd.y) / 3;
							pixels.push(xLast, yLast);
						} else {
							calculate = true;
						}
                                                
						context.quadraticCurveTo(xyLast.x, xyLast.y, xyAdd.x, xyAdd.y);
						pixels.push(xyAdd.x, xyAdd.y);
						context.stroke();
						context.beginPath();
						context.moveTo(xyAdd.x, xyAdd.y);
						xyAddLast = xyAdd;
						xyLast = xy;

					};

					function on_mouseup(e) {
						remove_event_listeners();
						disableSave = false;
						context.stroke();
						pixels.push('e');
						calculate = false;
					};

				}

				canvas_event.addEventListener('mousedown', on_mousedown, false);
				canvas_event.addEventListener('touchstart', on_mousedown, false);

		}
		,
		save : function(){

				var canvas = document.getElementById( this.widgetID );
				// save canvas image as data url (png format by default)
				var dataURL = canvas.toDataURL("image/png");
				document.getElementById("saveSignature").src = dataURL;

		}
		,
		clear : function(){

				//var parent = document.getElementById("canvas");
            	var parent = $('.'+this.widgetID).find('#canvas')[0];
				var child = document.getElementById( this.widgetID );
				parent.removeChild(child);
				empty = true;
				this.capture();

		}
		,
		send : function(){

				if (empty == false){

				var canvas = document.getElementById(this.widgetID);
				var dataURL = canvas.toDataURL("image/png");
				document.getElementById("saveSignature").src = dataURL;
				var sendemail = document.getElementById('sendemail').value;
				var replyemail = document.getElementById('replyemail').value;

				var dataform = document.createElement("form");
				document.body.appendChild(dataform);
				dataform.setAttribute("action","upload_file.php");
				dataform.setAttribute("enctype","multipart/form-data");
				dataform.setAttribute("method","POST");
				dataform.setAttribute("target","_self");
				dataform.innerHTML = '<input type="text" name="image" value="' + dataURL + '"/>' + '<input type="email" name="email" value="' + sendemail + '"/>' + '<input type="email" name="replyemail" value="' + replyemail + '"/>'+'<input type="submit" value="Click me" />';
				dataform.submit();

				}
		}

	}

})()

var zkSignature;