/**
 * OpenEyes
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(document).ready(function() {

	/**
	 * Delete event
	 */
	$('#event-content').delegate('#et_deleteevent', 'click', function(e) {
		if ($(this).css('display') !== 'none') {
			disableButtons();
			return true;
		}
		e.preventDefault();
		return false;
	});


	$(this).delegate('.removeReading', 'click', function(e) {
		$(this).closest('tr').remove();
		e.preventDefault();
	});

	$(this).delegate('.addReading', 'click', function(e) {
		var side = $(this).closest('.js-element-eye').attr('data-side');
		OphCiPhasing_IntraocularPressure_addReading(side);
		e.preventDefault();
	});

	$(this).delegate('.main-event .js-element-eye .active-form .remove-side', 'click', function(e) {

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

		$(this).closest('.element').find('input.sideField').val(eye_side);

		// If other side is already inactive, then activate it (can't have both sides inactive)
		$(this).closest('.element').find('.js-element-eye.'+show_physical_side + ' .active-form').show();
        $(this).closest('.element').find('.js-element-eye.'+show_physical_side + ' .inactive-form').hide();

		// Make this side inactive
		$(this).closest('.element').find('.js-element-eye.'+remove_physical_side + ' .active-form').hide();
        $(this).closest('.element').find('.js-element-eye.'+remove_physical_side + ' .inactive-form').show();

		e.preventDefault();
	});

	$(this).delegate('.main-event .js-element-eye .inactive-form a', 'click', function(e) {
		var element = $(this).closest('.element');
		element.find('input.sideField').val(3);  // Both eyes

		element.find('.js-element-eye .active-form').show();
        element.find('.js-element-eye .inactive-form').hide();

		e.preventDefault();
	});

	$(this).on('click','#et_print',function(e) {
		e.preventDefault();
		printEvent(null);
	});
});

function OphCiPhasing_IntraocularPressure_getNextKey() {
	var keys = $('.main-event .Element_OphCiPhasing_IntraocularPressure .intraocularPressureReading').map(function(index, el) {
		return parseInt($(el).attr('data-key'));
	}).get();
	return Math.max.apply(null, keys) + 1;
}

function OphCiPhasing_IntraocularPressure_addReading(side) {
	var template = $('#intraocularpressure_reading_template').html();
	var data = {
		"key" : OphCiPhasing_IntraocularPressure_getNextKey(),
		"side" : (side == 'right' ? 0 : 1),
	};
	var form = Mustache.render(template, data);
	$('.readings-'+side).append(form);
}
