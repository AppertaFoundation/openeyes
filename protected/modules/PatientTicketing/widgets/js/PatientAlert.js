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

var doPatientTicketingSpacing = false;

function spaceElement(selector, height, cssProp) {
	if (cssProp === undefined) {
		cssProp = 'top';
	}
	var $el = $(selector);
    var previouslyAdded = parseInt($el.data('patient-ticketing-height'), 10);
    if (isNaN(previouslyAdded))
        previouslyAdded = 0;
    var current = parseInt($el.css(cssProp), 10);
    if (previouslyAdded > height) {
        $el.css(cssProp, current-(previouslyAdded-height));
    } else {
        $el.css(cssProp, current+(height-previouslyAdded));
    }
    $el.data('patient-ticketing-height', height);
}

function patientTicketingSpacer() {
	if (!doPatientTicketingSpacing)
		return;

	var height = parseInt($('#patient-alert-patientticketing').height(), 10);
	spaceElement('aside.episodes-and-events', height);
    spaceElement('.event-header', height);
    spaceElement('.event-content', height, 'padding-top');
    spaceElement('.episode-content', height, 'padding-top');
}

$(document).ready(function () {
	if ($('#patient-alert-patientticketing').parents('.messages.patient').hasClass('fixed')) {
		doPatientTicketingSpacing = true;
	}
    patientTicketingSpacer();

    $(document).on('click', '#patient-alert-patientticketing .alert-box .toggle-trigger', function(e) {
		if ($(this).hasClass('toggle-show')) {
			target = "/PatientTicketing/default/collapseTicket";
		}
		else {
			target = "/PatientTicketing/default/expandTicket";
		}

		var getData = {ticket_id: $(this).parent().data('ticket-id')};

		$.ajax({
			url: target,
			data: getData,
			error: function() {
				e.preventDefault();
			}
		});
	});
    $('#patient-alert-patientticketing .js-toggle-container').on('oe:toggled', function(event) {
    	patientTicketingSpacer();
	});


});
