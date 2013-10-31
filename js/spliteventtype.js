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
 * Given a dom element, will try to determine the side that the element is on for split elements
 */
function getSplitElementSide(el) {
	// Get side (if set)
	var side = null;
	if (el.closest('[data-side]').length) {
		side = el.closest('[data-side]').attr('data-side');
	}
	return side;
}

/**
 * Helper function to show a given element side
 *
 * @param cls element class to be shown
 * @param side 'left' or 'right'
 */
function showSplitElementSide(cls, side) {
	var other_side = 'left';
	var side_val = 2; // Right in db

	if (side == 'left') {
		other_side = 'right';
		side_val = 1; // Left in db
	}
	var display_side = other_side;

	$('.' + cls).find('.side.' + display_side).removeClass('inactive');
	// side for data is the opposite side for display ...
	if ($('.' + cls).find('.side.' + side).hasClass('inactive')) {
		// the other side is not visible, so can set the input value to that of the side being shown
		$('.' + cls).find('input.sideField').each(function() {
			$(this).val(side_val);
		});
	}
	else {
		// both sides are visible
		$('.' + cls).find('input.sideField').each(function() {
			$(this).val('3');
		});
	}
}

/**
 * This will hide the side element of the given the element class.
 * NOTE: this will not automatically show the other side (leave the standard functions set up below for this functionality)
 *
 * @param cls element class to be hidden
 * @param side 'left' or 'right'
 *
 */
function hideSplitElementSide(cls, side) {
	var other_side = 'left';
	var other_side_val = 1; // Left in db

	if (side == 'left') {
		other_side = 'right';
		other_side_val = 2; // Right in db
	}
	var display_side = other_side;
	$('.' + cls).find('.side.' +  display_side).addClass('inactive');
	// side for data is the opposite side for display ...
	if ($('.' + cls).find('.side.' + side).hasClass('inactive')) {
		// the other side is not visible, so need to set the eye value to null
		$('.' + cls).find('input.sideField').each(function() {
			$(this).val('');
		});
	}
	else {
		// the other side is visible
		$('.' + cls).find('input.sideField').each(function() {
			$(this).val(other_side_val);
		});
	}
}

$(document).ready(function() {
	$(this).delegate('.event-content .remove-side', 'click', function(e) {
		// Update side field to indicate other side
		var side = $(this).closest('.side');

		var remove_physical_side = 'left';
		var show_physical_side = 'right';

		var eye_side = 1;
		if(side.attr('data-side') == 'left') {
			eye_side = 2; // Right
			remove_physical_side = 'right';
			show_physical_side = 'left';
		}

		$(this).closest('.sub-element, .element').find('input.sideField').each(function() {
			$(this).val(eye_side);
		});

		// If other side is already inactive, then activate it (can't have both sides inactive)
		$(this).closest('.sub-element, .element').find('.side.'+show_physical_side).removeClass('inactive');

		// Make this side inactive
		$(this).closest('.sub-element, .element').find('.side.'+remove_physical_side).addClass('inactive');

		e.preventDefault();
	});

	$(this).delegate('#event-content .side .inactive-form a', 'click', function(e) {
		var element = $(this).closest('.sub-element, .element');
		element.find('input.sideField').each(function() {
			$(this).val(3); // Both eyes
		});

		element.find('.side.inactive').removeClass('inactive');

		e.preventDefault();
	});
});
