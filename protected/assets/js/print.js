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

/*
function printPDF(url, data) {
	$('#print_pdf_iframe').remove();
	var iframe = $('<iframe></iframe>');
	iframe.attr({
		id: 'print_pdf_iframe',
		src: url + '?' + $.param(data),
		style: 'display: none;'
	});
	$('body').append(iframe);
	
	// re-enable the buttons
	$('#print_pdf_iframe').load(function() {
		enableButtons();
	});
}

$(document).ready(function() {
	$('body').append('<div class="printable" id="printable"></div>');
});

function clearPrintContent() {
	$('#printable').empty();
}

function appendPrintContent(content) {
	$('#printable').append(content);
}

function printContent(csspath) {

	var css = [ { href: baseUrl+'/css/printcontent.css', media: 'all' } ];
	if (csspath) {
		css = [ { href: csspath+'/print.css', media: 'all' } ];
	}

	$('#printable').printElement({
		pageTitle : 'OpenEyes printout',
		//leaveOpen: true,
		//printMode: 'popup',
		printBodyOptions : {
			styleToAdd : 'width: auto !important; margin: 0.75em !important;',
			classNameToAdd : 'openeyesPrintout'
		},
		overrideElementCSS : css,
	});
}
*/

/*
 * creates an iframe in the current document, and populates with the given url and GET data
 * 
 * NOTE: the call to print the iFrame must be part of the document returned by the server. By having
 * this in the $(document).ready() function, we can ensure that all the requisite objects (specifically
 * eyedraw) are loaded before the print is attempted.
 * 
 * @param url - url of page to load
 * @param data - associative array of GET values to append to URL
 */
function printIFrameUrl(url, data) {
	if (data) {
		url += '?' + $.param(data);
	}
	
	$('#print_content_iframe').remove();
	var iframe = $('<iframe></iframe>');
	iframe.attr({
		id: 'print_content_iframe',
		name: 'print_content_iframe',
		src: url,
		style: 'display: none;',
	});
	$('body').append(iframe);
	
	// re-enable the buttons
	$('#print_content_iframe').load(function() {
		enableButtons();

		var iframe = document.getElementById('print_content_iframe');
		iframe.focus();
		iframe.contentWindow.print();
	});
}

function printEvent(printOptions)
{
	var data = {canvas: {}};
	var has_canvas_data = false;

	$('canvas.ed-canvas-display').map(function() {
		data['canvas'][$(this).data('drawing-name')] = $(this).get(0).toDataURL();
	});

	data['last_modified_date'] = OE_event_last_modified;

	$.ajax({
		'type': 'POST',
		'url': baseUrl + '/' + OE_module_class + '/default/saveCanvasImages/' + OE_event_id,
		'data': $.param(data) + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
		'success': function(resp) {
			switch (resp) {
				case "ok":
					printIFrameUrl(OE_print_url, printOptions);
					break;
				case "outofdate":
					$.cookie('print',1);
					window.location.reload();
					break;
				default:
					alert("Something went wrong trying to print the event. Please try again or contact support for assistance.");
					break;
			}
		}
	});
}

$(document).ready(function() {
	if ($.cookie('print') == 1) {
		disableButtons();

		$.removeCookie('print');
		setTimeout(function() {
			printEvent(null);
		}, 2000);
	}
});

/*
 * DEPRECATED - should migrate to using printIFrameUrl
 */
function printUrl(url, data, csspath) {
	$.post(url, data, function(content) {
		$('#printable').html(content);
		printContent(csspath);
	});
}

/**
 * Chromium 'Ignoring too frequent calls to print().' work around. Is a wrapper
 * around the printContent() function.
 */
if (navigator.userAgent.toLowerCase().indexOf("chrome") > -1) {
	
	// Wrap private vars in a closure
	(function() {
		var realPrintFunc = window.printContent;
		var interval = 35000; // 35 secs
		var timeout_id = null;
		var nextAvailableTime = +new Date(); // when we can safely print again

		function runPrint(csspath) {
			realPrintFunc(csspath);
			timeout_id = null;
			nextAvailableTime += interval;
		}

		// Overwrite window.printContent function
		window.printContent = function(csspath) {
			var now = +new Date();

			if (now > nextAvailableTime) {
				// if the next available time is in the past, print now
				realPrintFunc(csspath);
				nextAvailableTime = now + interval;
			} else {
				if (timeout_id !== null) {
					// Skip if setTimeout has already been called (prevents user
					// from calling print multiple times)
					console.log('Skipping print as count down already started '
							+ (nextAvailableTime - now) / 1000
							+ 's left until next print');
					alert("New print request has been queued. "
							+ Math.floor((nextAvailableTime - now) / 1000)
							+ "secs until print.");
					return;
				} else {
					// print when next available
					timeout_id = setTimeout(function() { runPrint(csspath); }, nextAvailableTime - now);
					alert("Print request has been queued. "
							+ Math.floor((nextAvailableTime - now) / 1000)
							+ "secs until print.");
				}
			}
		}

	})();
}
