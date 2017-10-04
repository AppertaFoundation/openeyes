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

function showActiveChildElements() {
	$('#active_elements .active_child_elements').each(function() {
		if($('.element', this).length) {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}

function addElement(element, animate, is_child, previous_id, params, callback) {
	if (typeof (animate) === 'undefined')
		animate = true;
	if (typeof (is_child) === 'undefined')
		is_child = false;
	if (typeof (previous_id) === 'undefined')
		previous_id = 0;
	if (typeof (params) === 'undefined')
		params = {};

	var element_type_id = $(element).data('element-type-id');
	var element_type_class = $(element).data('element-type-class');
	var display_order = $(element).data('element-display-order');

	var core_params = {
		id: element_type_id,
		patient_id: OE_patient_id,
		previous_id: previous_id
	};

	$.extend(params, core_params);

	$.get(baseUrl + "/" + moduleName + "/Default/ElementForm", params, function(data) {
		var new_element = $(data);
		var elClass = $(element).data('element-type-class');

		if ($(element).prop('tagName') !== 'LI') {
			new_element.find(".sub-elements.active").replaceWith($(element).find(".sub-elements.active"));
			new_element.find(".sub-elements.inactive").replaceWith($(element).find(".sub-elements.inactive"));
		}

		var container = undefined;
		if (is_child) {
			if ($(element).data('container-selector')) {
				container = $($(element).data('container-selector')).find('.sub-elements.active');
			} else if ($(element).prop('tagName') == 'LI') {
				container = $(element).closest('.sub-elements.inactive').parent().find('.sub-elements:first');
			} else {
				container = $(element).closest('.sub-elements.active').parent().find('.sub-elements:first');
			}
		} else {
			container = $('.js-active-elements');
		}

		$(element).remove();

		var insert_before = container.find('.sub-element, .element').first();
		while (parseInt(insert_before.data('element-display-order')) < parseInt(display_order)) {
			insert_before = insert_before.nextAll('.sub-element, .element').first();
		}
		if (insert_before.length) {
			insert_before.before(new_element);
		} else {

			$(container).append(new_element);
		}

		if (is_child) {
			// check if this is sided
			// and match the parent active sides if it is
			var cel = $(container).find('.'+element_type_class);
			var pel = $(container).parents('.sub-element, .element');
			var sideField = $(cel).find('input.sideField');
			if ($(sideField).length && $(pel).find('.element-fields input.sideField').length) {
				$(sideField).val($(pel).find('.element-fields input.sideField').val());

				if($(sideField).val() == '1') {
					$(cel).find('.side.left').addClass('inactive');
				}
				else if ($(sideField).val() == '2') {
					$(cel).find('.side.right').addClass('inactive');
				}
			}
		}

		$('#event-content textarea.autosize:visible').autosize();
		showActiveChildElements();

		var initFunctionName;
		if (typeof OE_MODEL_PREFIX != 'undefined') {
			initFunctionName = elClass.replace(OE_MODEL_PREFIX+'Element_', '') + '_init';
		}
		else {
			initFunctionName = elClass.replace('Element_', '') + '_init';
		}

		if(typeof(window[initFunctionName]) == 'function') {
			window[initFunctionName](previous_id);
		}

		// now init any children
		$(".element." + elClass).find('.active_child_elements').find('.element').each(function() {
			var initFunctionName;
			if (typeof OE_MODEL_PREFIX != 'undefined') {
				initFunctionName = $(this).data('element-type-class').replace(OE_MODEL_PREFIX + 'Element_', '') + '_init';
			}
			else {
				initFunctionName = $(this).data('element-type-class').replace('Element_', '') + '_init';
			}
			if(typeof(window[initFunctionName]) == 'function') {
				window[initFunctionName]();
			}
		});

		var inserted = (insert_before.length) ? insert_before.prevAll('section:first') : container.find('.sub-element:last, .element:last').last();

		$(inserted).find('textarea').autosize();

		if (animate) {
			// note this flag is a bit of a misnomer now, as we've removed the animation in favour of moving straight to the
			// relevant element. This is an intentional change intended to reduce eyestrain for heavy OE users.
			setTimeout(function() {moveToElement(inserted)}, 100)
		}

		// Update text macros (if defined)
		if(typeof updateTextMacros == 'function') {
			updateTextMacros();
		}

		if(callback) {
			callback();
		}

	});

}

function removeElement(element, is_child) {
	if (typeof(is_child) == 'undefined') {
		is_child = false;
	}

	var element_type_class = $(element).data('element-type-class');
	var element_type_id = $(element).data('element-type-id');
	var element_type_name = $(element).data('element-type-name');
	var display_order = $(element).data('element-display-order');

	if (is_child) {
		var container = $(element).closest('.sub-elements.active').parent().find('.sub-elements.inactive:last .sub-elements-list');
	} else {
		var container = $('.optional-elements-list');
	}

	$(element).remove();

	var element = '<li data-element-type-class="'+element_type_class+'" data-element-type-id="'+element_type_id+'" data-element-type-name="'+element_type_name+'" data-element-display-order="'+display_order+'"><a href="#">'+element_type_name+'</a></li>';

	var insert_before = $(container).find('li').first();

	while (parseInt(insert_before.data('element-display-order')) < parseInt(display_order)) {
		insert_before = insert_before.next();
	}

	if (insert_before.length) {
		insert_before.before(element);
	} else {
		$(container).append(element);
	}

	showActiveChildElements();

	// Update sticky elements to cope with change in page size
	OpenEyes.UI.StickyElements.refresh();

	// Update text macros (if defined)
	if(typeof updateTextMacros == 'function') {
		updateTextMacros();
	}

	$('.js-active-elements').trigger('ElementRemoved', [ element_type_class ]);
  var checker = getOEEyeDrawChecker();
  checker.resync();
}

function moveToElement(element) {
	var offTop = element.offset().top - 130;
	$('html, body').scrollTop(offTop);
	var $title = $('.element-title', element);
	if (!$title.length) {
		$title = $('.sub-element-title', element);
	}
	$title.effect('pulsate', {
		times : 2
	}, 600);
}

$(document).ready(function() {

	/**
	 * Show/hide activechildelements containers (necessary in order to deal with padding)
	 */
	showActiveChildElements();

	/**
	 * Autoadjust height of textareas
	 */
	$('#event-content textarea.autosize:visible').autosize();

	/**
	 * Add all optional elements
	 */
	$('.optional-elements').delegate('.add-all', 'click', function(e) {
		if($(this).closest('.element').length) {
			$(this).closest('.element').find('.inactive_child_elements .element').each(function() {
				$(this).addClass('clicked');
				addElement(this, true, true);
			});
		}
		else {
			$('.optional-elements-list li').each(function() {
				$(this).addClass('clicked');
				addElement(this, false);
			});
		}
		e.preventDefault();
	});

	/**
	 * Add an optional element
	 */
	$('.optional-elements-list').delegate('li', 'click', function(e) {
		if (!$(this).hasClass('clicked')) {
			$(this).addClass('clicked');
			addElement(this);
		}
		e.preventDefault();
	});

	/**
	 * View previous elements
	 */
	$('.js-active-elements').delegate('.viewPrevious', 'click', function(e) {
    e.preventDefault();
		if ($(this).hasClass('subElement')) {
			var elementType = 'sub-element';
		} else {
			var elementType = 'element';
		}

		var element = $(this).closest('.' + elementType);

		var dialog = new OpenEyes.UI.Dialog({
			url: baseUrl + '/' + moduleName + '/default/viewpreviouselements',
      data: { element_type_id: element.data('element-type-id'), patient_id: OE_patient_id },
			width: 1070,
      title: 'Previous '+element.data('element-type-name')+' Elements',
			autoOpen: true
		});

    $(dialog.content).on('click', '.copy_element', function(dialog, element, event) {
				var element_id = $(event.target).data('element-id');
			 	$(element).addClass('clicked');
				$(element).find('> .element-fields').css('opacity', '0.5');
				$(element).find('> .element-fields').find('input, select, textarea').prop('disabled', true);
				dialog.close();
				addElement(element, false, (elementType == 'sub-element'), element_id);
		}.bind(undefined, dialog, element));
	});

	/**
	 * Remove all optional elements
	 */
	$('.optional-elements').delegate('.remove-all', 'click', function(e) {
		if($(this).closest('.element').length) {
			$(this).closest('.element').find('.sub-elements.active .sub-element:not(.required)').each(function() {
				removeElement(this, true);
			});
		} else {
			$('.js-active-elements .sub-element:not(.required)').each(function() {
				removeElement(this, true);
			});
			$('.js-active-elements .element:not(.required)').each(function() {
				removeElement(this);
			});
		}
		e.preventDefault();
	});

	/**
	 * Remove an optional element
	 */
	$('#event-content').on('click', '.js-remove-element', function(e) {
		if (!$(this).parents('.elements.active').length && !$(this).hasClass('disabled')) {
			var element = $(this).closest('.element');
			removeElement(element);
		}
		e.preventDefault();
	});

	/**
	 * Remove a child element
	 */
	$('#event-content').on('click','.js-remove-child-element', function(e) {
        if(!$(this).hasClass('disabled')){
            var element = $(this).closest('.sub-element');
            removeElement(element, true);
        }
		e.preventDefault();
	});

	/**
	 * Add optional child element
	 */
	$('#event-content').on('click', '.sub-elements-list li', function(e) {
		if (!$(this).hasClass('clicked')) {
			$(this).addClass('clicked');
			addElement(this, true, true);
		}
		e.preventDefault();
	});
});
