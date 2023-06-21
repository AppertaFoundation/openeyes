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

function addElement(element, animate, previous_id, params, callback) {
    if (typeof (animate) === 'undefined')
        animate = true;
    if (typeof (previous_id) === 'undefined')
        previous_id = 0;
    if (typeof (params) === 'undefined')
        params = {};


    const element_type_id = $(element).data('element-type-id');
    var element_type_class = $(element).data('element-type-class');

    var $menuLi = findMenuItemForElementClass(element_type_class);

    if ($menuLi) {
        if (!$menuLi.find('a').hasClass('selected')) {
            $menuLi.find('a').addClass('selected');
        }
    }

    const core_params = {
        id: element_type_id,
        patient_id: OE_patient_id,
        event_id: OE_event_id,
        previous_id: previous_id
    };

    $.extend(params, core_params);
    $.get(baseUrl + "/" + moduleName + "/Default/ElementForm", params, function (data) {
        const new_element = $(data);
        const elClass = $(new_element).data('element-type-class');
        $(new_element).attr('data-element-display-order', $(element).data('element-display-order'));
        const element_display_order = Number($(new_element).data('element-display-order'));

        const container = $('.js-active-elements');
        $(element).remove();

        // If there aren't any elements, then insert the new element at the end (after the event date)
        if (container.find('section[data-element-type-name]').length === 0) {
            container.append(new_element);
        } else {
            // Otherwise find the first element that has a greater display order...
            let $toInsertBefore = null;
            container.find('section[data-element-type-name]').each(function () {
                const target_display_order = Number($(this).data('element-display-order'));
                if (Math.abs(target_display_order) > Math.abs(element_display_order)) {
                    $toInsertBefore = $(this);
                    return false;
                }
            });

            // ... and insert before it
            if ($toInsertBefore) {
                new_element.insertBefore($toInsertBefore);
            } else {
                // If there are no elements with greater display order, then the new element should go last
                container.append(new_element);
            }
        }

        autosize($('#event-content textarea.autosize:visible'));

        // now init any children
        $(".element." + elClass).find('.active_child_elements').find('.element').each(function () {
            let initFunctionName;
            if (typeof OE_MODEL_PREFIX !== 'undefined') {
                initFunctionName = $(this).data('element-type-class').replace(OE_MODEL_PREFIX + 'Element_', '') + '_init';
            } else {
                initFunctionName = $(this).data('element-type-class').replace('Element_', '') + '_init';
            }
            if (typeof (window[initFunctionName]) === 'function') {
                window[initFunctionName]();
            }
        });

        const $inserted = container.children('section[data-element-type-id="' + element_type_id + '"]').first();
        autosize($inserted.find('textarea'));

        if (animate) {
            // note this flag is a bit of a misnomer now, as we've removed the animation in favour of moving straight to the
            // relevant element. This is an intentional change intended to reduce eyestrain for heavy OE users.
            setTimeout(function () {
                moveToElement($inserted);
            }, 100);
        }
        // Update text macros (if defined)
        if (typeof updateTextMacros === 'function') {
            updateTextMacros();
        }

        if (callback) {
            callback();
        }

    });
}

/**
 * Simple convenience wrapper to grab out the menu entry
 *
 * @param elementTypeClass
 * @returns {*}
 */
function findMenuItemForElementClass(elementTypeClass) {
    return $('#episodes-and-events').find('.collapse-group-content .element').filter(
        function () {
            return $(this).data('elementTypeClass') === elementTypeClass;
        }
    ).first();
}

function removeElement(e) {

    const element_type_class = $(e).data('element-type-class');
    const element_type_id = $(e).data('element-type-id');
    const element_type_name = $(e).data('element-type-name');
    const display_order = $(e).data('element-display-order');

    const $menuLi = findMenuItemForElementClass(element_type_class);

    if ($menuLi) {
        $menuLi.find('a').removeClass('selected').removeClass('error');
    }
    const container = $('.optional-elements-list');

    $(e).remove();
    $('div.' + element_type_class).remove();

    const element = $('<li></li>')
        .data('element-type-class', element_type_class)
        .data('element-type-id', element_type_id)
        .data('element-type-name', element_type_name)
        .data('element-display-order', display_order)
        .append($('<a href="#">' + element_type_name + '</a>'));

    let insert_before = $(container).find('li').first();

    while (parseInt(insert_before.data('element-display-order')) < parseInt(display_order)) {
        insert_before = insert_before.next();
    }

    if (insert_before.length) {
        insert_before.before(element);
    } else {
        $(container).append(element);
    }

    // Update text macros (if defined)
    if (typeof updateTextMacros === 'function') {
        updateTextMacros();
    }

    $('.js-active-elements').trigger('ElementRemoved', [element_type_class]);
    if (typeof (getOEEyeDrawChecker) === 'function') {
        const checker = getOEEyeDrawChecker();
        checker.resync();
    }
}

function moveToElement($element) {
    const $container = $('main.main-event');
    $container.scrollTop(
        $element.offset().top - $container.offset().top + $container.scrollTop() - 130
    );

    const $title = $('.element-title', $element);
    $title.effect('pulsate', {
        times: 2
    }, 600);
}

$(document).ready(function () {

    /**
     * Autoadjust height of textareas
     */
    autosize($('#event-content textarea.autosize:visible'));

    /**
     * Add all optional elements
     */
    $('.optional-elements').delegate('.add-all', 'click', function (e) {
        if ($(this).closest('.element').length === 0) {
            $('.optional-elements-list li').each(function () {
                $(this).addClass('clicked');
                addElement(this, false);
            });
        }
        e.preventDefault();
    });

    /**
     * Add an optional element
     */
    $('.optional-elements-list').delegate('li', 'click', function (e) {
        if (!$(this).hasClass('clicked')) {
            $(this).addClass('clicked');
            addElement(this);
        }
        e.preventDefault();
    });

    /**
     * View previous elements
     */
    $('.js-active-elements').delegate('.js-duplicate-element', 'click', function (e) {
        const element = $(this).closest('.element');
        const callback = $(this).data('copy-element-callback') ?? null;
        const dialog = new OpenEyes.UI.Dialog({
            url: baseUrl + '/' + moduleName + '/default/viewpreviouselements',
            data: { element_type_id: element.data('element-type-id'), patient_id: OE_patient_id },
            width: 1070,
            title: 'Previous ' + element.data('element-type-name') + ' Elements',
            autoOpen: true,
            popupContentClass: 'oe-popup-content previous-elements'
        });
        dialog.open();

        $(dialog.content).on('click', '.copy_element', function (dialog, element, event) {
            const element_id = $(event.target).data('element-id');
            $(element).addClass('clicked');
            $(element).find('> .element-fields').css('opacity', '0.5');
            $(element).find('> .element-fields').find('input, select, textarea').prop('disabled', true);
            $('.oe-popup-wrap').not('#js-overlay').remove();
            addElement(element, false, element_id, {}, callback);
        }.bind(undefined, dialog, element));
        e.preventDefault();

    });

    /**
     * Remove all optional elements
     */
    $('.optional-elements').delegate('.remove-all', 'click', function (e) {
        if ($(this).closest('.element').length) {
            $(this).closest('.element').find('.sub-elements.active .sub-element:not(.required)').each(function () {
                removeElement(this, true);
            });
        } else {
            $('.js-active-elements .sub-element:not(.required)').each(function () {
                removeElement(this, true);
            });
            $('.js-active-elements .element:not(.required)').each(function () {
                removeElement(this);
            });
        }
        e.preventDefault();
    });
});
