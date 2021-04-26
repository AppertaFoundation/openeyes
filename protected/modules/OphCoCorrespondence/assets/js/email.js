/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 */

$(window).load(function() {
    // On editing an already created event, the OE_event_id contains the event id, but
    // when the event is created, the OE_event_id will be null but the cookie email will exist at that point.
    // Hence, checking the existence of the cookie.
    if ($.cookie('email') === OE_event_id || typeof $.cookie('email') !== 'undefined') {
        // deleting the email cookie by setting the expire time in the past so that the sendEmail request won't execute if the browser is refreshed
        document.cookie = "email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $.get(baseUrl + '/OphCoCorrespondence/default/sendEmail?event_id=' + OE_event_id);
    }
});