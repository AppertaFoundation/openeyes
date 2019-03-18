/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
	$('.' + cls).find('.js-element-eye.' + display_side + ' .active-form').show();
  $('.' + cls).find('.js-element-eye.' + display_side + ' .inactive-form').hide();
	// side for data is the opposite side for display ...
	if ($('.' + cls).find('.js-element-eye.' + side + ' .active-form').is(':hidden')) {
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

	$('.' + cls).find('.js-element-eye.' +  display_side + ' .active-form').hide();
  $('.' + cls).find('.js-element-eye.' +  display_side + ' .inactive-form').show();
  // side for data is the opposite side for display ...
	if ($('.' + cls).find('.js-element-eye.' + side + ' .active-form').is(':hidden')) {
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
	$(this).delegate('.main-event .remove-side', 'click', function(e) {
		// Update side field to indicate other side
		var side = $(this).closest('.js-element-eye');

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
		$(this).closest('.sub-element, .element').find('.js-element-eye.'+show_physical_side+' .active-form').show();
        $(this).closest('.sub-element, .element').find('.js-element-eye.'+show_physical_side+' .inactive-form').hide();

		// Make this side inactive
		$(this).closest('.sub-element, .element').find('.js-element-eye.'+remove_physical_side+' .active-form').hide();
        $(this).closest('.sub-element, .element').find('.js-element-eye.'+remove_physical_side+' .inactive-form').show();

		e.preventDefault();
	});

	$(this).delegate('.main-event .js-element-eye .inactive-form a', 'click', function(e) {
		var element = $(this).closest('.sub-element, .element');
		element.find('input.sideField').each(function() {
			$(this).val(3); // Both eyes
		});

		element.find('.js-element-eye .active-form[style="display: none;"]').parent().find('.inactive-form').hide();
		element.find('.js-element-eye .active-form[style="display: none;"]').show();

		e.preventDefault();
        if (typeof(getOEEyeDrawChecker) === 'function') {
            var checker = getOEEyeDrawChecker();
            checker.resync();
        }
	});
});
