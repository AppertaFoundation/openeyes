/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(document).ready(() => {
    /**
     * Search for, and execute save handler functions, on save
     */
    let handle_event;
    if (window.CustomEvent && typeof window.CustomEvent === 'function') {
        handle_event = new CustomEvent('handle');
    } else {
        handle_event = document.createEvent('CustomEvent');
        handle_event.initCustomEvent('handle_event', true, true);
    }

    function handleClick(e) {
        e.preventDefault();
        let status;
        let $handler_functions = document.querySelectorAll('.js-save-handler-function');

        for (let handler of $handler_functions) {
            handler.dispatchEvent(handle_event);
            status = handler.getAttribute('status');
            if (status === "stop") {
                break;
            }
        };

        if (status !== "stop") {
            // get form ends with -create or -update
            let form = document.querySelector('#event-content form[id$="-create"], #event-content form[id$="-update"]');
            $(form).submit(); // Non JQuery method causes odd behaviour so left for now.
        }
    }
    let $save_button = document.getElementById('et_save');
    let $bottom_save_button = document.getElementById('et_save_footer');

    $save_button.addEventListener('click', handleClick);
    $bottom_save_button.addEventListener('click', handleClick);
});
