/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$(document).ready(function() {
	$('body').append('<div class="printable" id="printable"></div>');
});

function clearPrintContent() {
	$('#printable').empty();
}

function appendPrintContent(content) {
	$('#printable').append(content);
}

function printContent() {
	$('#printable').printElement({
		pageTitle : 'OpenEyes printout',
		//leaveOpen: true,
		//printMode: 'popup',
		printBodyOptions : {
			styleToAdd : 'width: auto !important; margin: 0.75em !important;',
			classNameToAdd : 'openeyesPrintout'
		},
		overrideElementCSS : [ {
			href : '/css/printcontent.css',
			media : 'all'
		} ]
	});
}

function printUrl(url, data) {
	$.post(url, data, function(content) {
		$('#printable').html(content);
		printContent();
	});
}

/**
 * Chromium 'Ignoring too frequent calls to print().' work around.
 * Is a wrapper around the printContent() function.
 */
if(navigator.userAgent.toLowerCase().indexOf("chrome") >  -1) {
	// wrap private vars in a closure
	(function() {
		var realPrintFunc = window.printContent;
		var interval = 35000; // 35 secs
		var timeout_id = null;
		
//		var timing_id = null;

		var nextAvailableTime = +new Date(); // when we can safely print again

		var runPrint = function(){
			realPrintFunc();
			timeout_id = null;
			nextAvailableTime += interval;
		}

//		var timing = function(){
//			now = +new Date();
//			console.log(nextAvailableTime - now);
//			timing_id = setTimeout(function(){timing();}, 1000);
//		}

		// overwrite window.printContent function
		window.printContent = function() {
			var now = +new Date();
			
//			if(timing_id !== null){
//				clearTimeout(timing_id);
//			}
//			
			// if the next available time is in the past, print now
			if(now > nextAvailableTime) {
				realPrintFunc();
				nextAvailableTime = now + interval;
			} else {
				// Skip if setTimeout has already been called (prevents user from calling print multiple times)
				if(timeout_id !== null){
					console.log('Skipping print as count down already started '+(nextAvailableTime - now)/1000+'s left until next print');
					alert("New print request has been queued. "+Math.floor((nextAvailableTime - now)/1000)+"secs until print.");
					return;
				}else{
					// print when next available
					timeout_id = setTimeout(runPrint, nextAvailableTime - now);
					alert("Print request has been queued. "+Math.floor((nextAvailableTime - now)/1000)+"secs until print.");
//					timing();
				}
			}
		}

	})();
}