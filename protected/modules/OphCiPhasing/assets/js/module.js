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

$(document).ready(function() {

	/**
	 * Save event
	 */
	handleButton($('#et_save'));

	/**
	 * Delete event
	 */
	$('#event-content').delegate('#et_deleteevent', 'click', function(e) {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			return true;
		}
		e.preventDefault();
		return false;
	});

	/**
	 * Cancel event delete
	 */
	handleButton($('#et_canceldelete'));

	$(this).delegate('.removeReading', 'click', function(e) {
		$(this).closest('tr').remove();
		e.preventDefault();
	});

	$(this).delegate('.addReading', 'click', function(e) {
		var side = $(this).closest('.element-eye').attr('data-side');
		OphCiPhasing_IntraocularPressure_addReading(side);
		e.preventDefault();
	});

	$(this).delegate('#event-content .side .active-form .remove-side', 'click', function(e) {

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

		$(this).closest('.element').find('input.sideField').val(eye_side);

		// If other side is already inactive, then activate it (can't have both sides inactive)
		$(this).closest('.element').find('.side.'+show_physical_side).removeClass('inactive');

		// Make this side inactive
		$(this).closest('.element').find('.side.'+remove_physical_side).addClass('inactive');

		e.preventDefault();
	});

	$(this).delegate('#event-content .side .inactive-form a', 'click', function(e) {
		var element = $(this).closest('.element');
		element.find('input.sideField').val(3);  // Both eyes

		element.find('.side').removeClass('inactive');

		e.preventDefault();
	});

	handleButton($('#et_print'),function(e) {
		e.preventDefault();
		printEvent(null);
	});
});

function OphCiPhasing_IntraocularPressure_getNextKey() {
	var keys = $('#event-content .Element_OphCiPhasing_IntraocularPressure .intraocularPressureReading').map(function(index, el) {
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
