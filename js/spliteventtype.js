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

$(document).ready(function() {
	$(this).delegate('#event_content .side .activeForm a.removeSide', 'click', function(e) {
		
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

		$(this).closest('.element').find('input.sideField').each(function() {
			$(this).val(eye_side);
		});
		
		// If other side is already inactive, then activate it (can't have both sides inactive)
		$(this).closest('.element').find('.side.'+show_physical_side).removeClass('inactive');
		
		// Make this side inactive
		$(this).closest('.element').find('.side.'+remove_physical_side).addClass('inactive');
		
		e.preventDefault();
	});

	$(this).delegate('#event_content .side .inactiveForm a', 'click', function(e) {
		var element = $(this).closest('.element'); 
		element.find('input.sideField').each(function() {
			$(this).val(3); // Both eyes
		});
		
		element.find('.side').removeClass('inactive');
		
		e.preventDefault();
	});
});