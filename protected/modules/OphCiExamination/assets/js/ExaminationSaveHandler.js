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
            if (close_incomplete_exam_elements === 'on') {
                verifyElements(e, $(form));
            } else {
                $(form).submit();
            }
        }
    }

    function verifyElements(e, $form) {
        let $all_elements = $('section.element');
        let empty_elements = [];
        let empty_element_names = [];
        let dirty_elements = $('input[name^="[element_dirty]"][value="1"]').map(function() { return $(this).parent().data('element-type-class'); }).get();
        let empty_mandatory_element = false;
        $all_elements.each(function() {
            if (!dirty_elements.find((element) => element == $(this).data('element-type-class'))) {
                if ($(this).data('mandatory') === 'true' && !empty_mandatory_element) {
                    empty_mandatory_element = true;
                }
                empty_elements.push($(this).data('element-type-class'));
                empty_element_names.push($(this).data('element-type-name'));
            }
        });

        if (empty_mandatory_element || dirty_elements.length === 0) {
            // There should be at least one dirtied element in an examination event, and there must be no empty mandatory elements.
            $form.submit();
        } else if (empty_elements.length > 0) {
            // Need to stop propagation to ensure the OK button is enabled in the confirmation dialog (this handler fires before the base handler that disables all buttons on the screen).
            e.stopPropagation();

            let list = '<ul class="row-list">';
            $.each(empty_element_names, function (index, item) {
                list += '<li>' + item + '</li>';
            });
            list += '</ul>';
            
            // Display confirmation dialog. When confirmed, wipe out the optional elements.
            let dialog = new OpenEyes.UI.Dialog.Confirm({
                title: 'Discard empty elements?',
                templateSelector: '#dialog-confirm-splitview-template',
                leftPanelContent: list,
                rightPanelContent: "The listed elements have received no input or are in-complete, would you like to continue to save and discard them?",
                okButton: 'Discard & save',
                okButtonClassList: 'green hint cols-5 ok',
                cancelButtonClassList: 'cols-5 cancel',
                popupContentClass: 'oe-popup-content wide'
            });
            dialog.on('ok', function () {
                disableButtons();
                $.each(empty_elements, function (index, value) {
                    const $elem = $('section[data-element-type-class="' + value + '"]');
                    $elem.trigger('element_removed');
                    removeElement($elem);
                    $(document).trigger('element_removed');
                });
                $form.submit();
            }.bind(this));
            dialog.on('cancel', function () {
                $form.submit();
            }.bind(this));
            dialog.open();
        }
    }
    let $save_button = document.getElementById('et_save');
    let $bottom_save_button = document.getElementById('et_save_footer');

    $save_button.addEventListener('click', handleClick);
    $bottom_save_button.addEventListener('click', handleClick);
});
