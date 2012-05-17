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

function printContent(dateleft) {
	if (dateleft) {
		var css = '/css/printcontent-left.css';
	} else {
		var css = '/css/printcontent.css';
	}

	$('#printable').printElement({
		pageTitle : 'OpenEyes printout',
		//leaveOpen: true,
		//printMode: 'popup',
		printBodyOptions : {
			styleToAdd : 'width: auto !important; margin: 0.75em !important;',
			classNameToAdd : 'openeyesPrintout'
		},
		overrideElementCSS : [ {
			href : css,
			media : 'all'
		} ]
	});
}

function printUrl(url, data, dateleft) {
	$.post(url, data, function(content) {
		$('#printable').html(content);
		printContent(dateleft);
	});
}
