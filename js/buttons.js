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

var button_colours = ["red","blue","green"];
var button_cache = {};

function handleButton(button, callback) {
	button.click(function(e) {
		if (!button.hasClass('inactive')) {
			disableButtons();
			if (callback) {
				callback(e,button);
			}
		} else {
			e.preventDefault();
		}
	});
}

function disableButtons() {
	for (var i in button_colours) {
		var col = button_colours[i];
		var selection = $('button.'+col);
		selection.removeClass(col).addClass('inactive');
		selection.children('span').removeClass('button-span-'+col).addClass('button-span-inactive');
		selection.children('a').children('span').removeClass('button-span-'+col).addClass('button-span-inactive');
		button_cache[col] = selection;
	}
	$('.loader').show();
}

function enableButtons() {
	for (var i in button_colours) {
		var col = button_colours[i];
		if (button_cache[col]) {
			button_cache[col].removeClass('inactive').addClass(col);
			button_cache[col].children('span').removeClass('button-span-inactive').addClass('button-span-'+col);
		}
	}
	$('.loader').hide();
}

$(document).ready(function() {
	$('button.auto').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			return true;
		}
		return false;
	});
});
