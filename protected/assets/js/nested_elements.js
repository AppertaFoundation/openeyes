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

  var element_type_id = $(element).data('element-type-id');

    var core_params = {
        id: element_type_id,
        patient_id: OE_patient_id,
        previous_id: previous_id
    };

  $.extend(params, core_params);
  $.get(baseUrl + "/" + moduleName + "/Default/ElementForm", params, function (data) {
    var new_element = $(data);
    var elClass = $(new_element).data('element-type-class');
    var element_display_order = Number($(new_element).data('element-display-order'));

        var container = $('.js-active-elements');
        $(element).remove();

    // If there aren't any elements, then insert the new element at the end (after the event date)
    if (container.find('section[data-element-type-name]').length === 0) {
      container.append(new_element);
    } else {
      // Otherwise find the first element that has a greater display order...
      var $toInsertBefore = null;
      container.find('section[data-element-type-name]').each(function () {
        var target_display_order = Number($(this).data('element-display-order'));
        if (target_display_order > element_display_order) {
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

    $('#event-content textarea.autosize:visible').autosize();

        // now init any children
        $(".element." + elClass).find('.active_child_elements').find('.element').each(function () {
            var initFunctionName;
            if (typeof OE_MODEL_PREFIX != 'undefined') {
                initFunctionName = $(this).data('element-type-class').replace(OE_MODEL_PREFIX + 'Element_', '') + '_init';
            }
            else {
                initFunctionName = $(this).data('element-type-class').replace('Element_', '') + '_init';
            }
            if (typeof(window[initFunctionName]) == 'function') {
                window[initFunctionName]();
            }
        });

        var $inserted = container.children('section[data-element-type-id="' + element_type_id + '"]').first();
        $inserted.find('textarea').autosize();
        if (animate) {
            // note this flag is a bit of a misnomer now, as we've removed the animation in favour of moving straight to the
            // relevant element. This is an intentional change intended to reduce eyestrain for heavy OE users.
            setTimeout(function () {
                moveToElement($inserted);
            }, 100);
        }
        // Update text macros (if defined)
        if (typeof updateTextMacros == 'function') {
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

function removeElement(element) {

    var element_type_class = $(element).data('element-type-class');
    var element_type_id = $(element).data('element-type-id');
    var element_type_name = $(element).data('element-type-name');
    var display_order = $(element).data('element-display-order');

    var $menuLi = findMenuItemForElementClass(element_type_class);

    if ($menuLi) {
        $menuLi.find('a').removeClass('selected').removeClass('error');
    }
        var container = $('.optional-elements-list');

    $(element).remove();

    var element = $('<li></li>')
      .data('element-type-class', element_type_class)
      .data('element-type-id', element_type_id)
      .data('element-type-name', element_type_name)
      .data('element-display-order', display_order)
      .append($('<a href="#">' + element_type_name + '</a>'));

    var insert_before = $(container).find('li').first();

    while (parseInt(insert_before.data('element-display-order')) < parseInt(display_order)) {
        insert_before = insert_before.next();
    }

    if (insert_before.length) {
        insert_before.before(element);
    } else {
        $(container).append(element);
    }

    // Update sticky elements to cope with change in page size
    OpenEyes.UI.StickyElements.refresh();

    // Update text macros (if defined)
    if (typeof updateTextMacros == 'function') {
        updateTextMacros();
    }

    $('.js-active-elements').trigger('ElementRemoved', [element_type_class]);
    if (typeof(getOEEyeDrawChecker) === 'function') {
        var checker = getOEEyeDrawChecker();
        checker.resync();
    }
}

function moveToElement($element) {
    var $container = $('main.main-event');
    $container.scrollTop(
        $element.offset().top - $container.offset().top + $container.scrollTop() - 130
    );

    var $title = $('.element-title', $element);
    $title.effect('pulsate', {
        times: 2
    }, 600);
}

function swapElement(element_to_swap, elementTypeClass, params){
    const nva = elementTypeClass.endsWith("NearVisualAcuity");
    const sidebar = $('#episodes-and-events').data('patient-sidebar');
    const $menuLi = sidebar.findMenuItemForElementClass(elementTypeClass);
    let $parentLi;

    if ($menuLi) {
        let $href = $menuLi.find('a');
        $href.removeClass('selected').removeClass('error');
        if (!$href.hasClass('selected')) {
            sidebar.markSidebarItems(sidebar.getSidebarItemsForExistingElements($href));

            const $container = $href.parent();
            $parentLi = $($container);
            if (params === undefined)
            params = {};
            $href.addClass('selected');
        }
    }

    element_to_swap.css('opacity','0.5').find('select, input, button').prop('disabled','disabled');
    const element = $parentLi.clone(true);
    const element_type_id = $(element).data('element-type-id');
    const element_type_class = $(element).data('element-type-class');

    let core_params = {
        id: element_type_id,
        patient_id: OE_patient_id,
        previous_id: 0
    };

    $.extend(params, core_params);
    $.get(baseUrl + "/" + moduleName + "/Default/ElementForm", params, function (data) {
        const new_element = $(data);
        const container = $('.js-active-elements');
        const cel = $(container).find('.' + element_type_class);
        const pel = $(container).parents('.element');
        const sideField = $(cel).find('input.sideField');
        if ($(sideField).length && $(pel).find('.element-fields input.sideField').length) {
            $(sideField).val($(pel).find('.element-fields input.sideField').val());

            if ($(sideField).val() == '1') {
                $(cel).find('.js-element-eye.left').addClass('inactive');
            }
            else if ($(sideField).val() == '2') {
                $(cel).find('.js-element-eye.right').addClass('inactive');
            }
        }

        let reading_val_index = [];
        let method_index = [];
        let current_eye_va_reading;

        $.each(['right-eye', 'left-eye'], function(i, eye_side){
            current_eye_va_reading = element_to_swap.find('.'+eye_side+' table.'+(nva ? 'near-va-readings' : 'va_readings'));

            // look for .va_readings values
            if(current_eye_va_reading.find('tr').length > 0){
                reading_val_index[eye_side] = [];
                method_index[eye_side] = [];
                $.each(current_eye_va_reading.find('tr'), function(i, row){
                    // get value
                    let reading_val = $(row).find('td:eq(0) input').val();
                    let method = $(row).find('td:eq(2) input').val();

                    // look up value
                    let reading_val_li = $('.'+eye_side+' ul[data-id="reading_val"]').find('li[data-id="'+reading_val+'"]');
                    let method_li = $('.'+eye_side+' ul[data-id="method"]').find('li[data-id="'+method+'"]');

                    // get index and label
                    reading_val_index[eye_side].push({
                        val_index: reading_val_li.index(),
                        label:reading_val_li.data('label')
                    });

                    method_index[eye_side].push({
                        val_index: method_li.index(),
                        label:method_li.data('label')
                    });
                });
            }
        });

        element_to_swap.replaceWith(new_element);

        // select equivalent
        if(Object.keys(reading_val_index).length > 0 && Object.keys(method_index).length > 0){
            $.each(Object.keys(reading_val_index), function(eye_index, eye_side){                
                $.each(reading_val_index[eye_side], function(i, val){
                    let target = $('section[data-element-type-name="'+(nva ? 'Near ' : '')+'Visual Acuity"] .'+eye_side);
                    let target_reading_val_by_label = target.find('ul[data-id="reading_val"] li[data-label="'+val.label+'"]');
                    let target_reading_val_by_index = target.find('ul[data-id="reading_val"] li:eq('+val.val_index+')');

                    if(target_reading_val_by_label.length){
                        target_reading_val_by_label.addClass('selected');
                    } else if(target_reading_val_by_index.length){
                        target_reading_val_by_index.addClass('selected');
                    }

                    target.find('ul[data-id="method"] li:eq('+method_index[eye_side][i].val_index+')').addClass('selected');
                    target.find('.oe-add-select-search .add-icon-btn').trigger('click');
                });
            });
        }

        element_to_swap.css('opacity','');
    });
}

$(document).ready(function () {

    /**
     * Autoadjust height of textareas
     */
    $('#event-content textarea.autosize:visible').autosize();

    /**
     * Add all optional elements
     */
    $('.optional-elements').delegate('.add-all', 'click', function (e) {
      if ($(this).closest('.element').length == 0) {
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
        var element = $(this).closest('.element');
        var dialog = new OpenEyes.UI.Dialog({
            url: baseUrl + '/' + moduleName + '/default/viewpreviouselements',
            data: {element_type_id: element.data('element-type-id'), patient_id: OE_patient_id},
            width: 1070,
            title: 'Previous ' + element.data('element-type-name') + ' Elements',
            autoOpen: true,
            popupContentClass: 'oe-popup-content previous-elements'
        });
        dialog.open();

      $(dialog.content).on('click', '.copy_element', function (dialog, element, event) {
        var element_id = $(event.target).data('element-id');
        $(element).addClass('clicked');
        $(element).find('> .element-fields').css('opacity', '0.5');
        $(element).find('> .element-fields').find('input, select, textarea').prop('disabled', true);
        $('.oe-popup-wrap').remove();
        addElement(element, false, element_id);
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