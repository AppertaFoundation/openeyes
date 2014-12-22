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

	// Prevent the events from being bound multiple times.
	if ($(this).data('multi-select-free-text-events')) {
		return;
	}
	$(this).data('multi-select-free-text-events', true);

	$(this).on('click', '.multi-select-free-text .remove-all', function(e) {
		e.preventDefault();
		var container = $(this).closest('.multi-select-free-text');
		container.find('.remove-one').trigger('click');
	});

	$(this).on('change', 'select.MultiSelectFreeTextList', function() {

		var select = $(this);
		var selected = select.children('option:selected');

		if (selected.val().length >0) {

			var container = select.closest('.multi-select-free-text');
			var selections = container.find('.multi-select-free-text-selections');
			var descriptions = container.find('.multi-select-free-text-descriptions');
			var inputField = container.find('.multi-select-free-text-list-name');
			var fieldName = inputField.attr('name').match(/\[MultiSelectFreeTextList_(.*?)\]$/)[1];
			var noSelectionsMsg = container.find('.no-selections-msg');
			var removeAll = container.find('.remove-all');
			var options = container.data('options');

			var attrs = {};
			$(selected[0].attributes).each(function() {
				attrs[this.nodeName] = this.nodeValue;
			});

			var i = 0;
			selections.find('input[type="hidden"]').map(function() {
				while (parseInt($(this).data('i')) >= i) {
					i++;
				}
			});

			var inp_str = '<input type="hidden" name="'+fieldName+'[' + i + '][id]" data-i="' + i + '"';
			for (var key in attrs) {
				inp_str += ' ' + key + '="' + attrs[key] + '"';
			}
			inp_str += ' />';

			var input = $(inp_str);

			var remote_data = {
				'href': '#',
				'class': 'MultiSelectFreeTextRemove remove-one '+selected.val(),
				'text': 'Remove',
				'data-name': fieldName+'[]',
				'data-text': selected.text()
			};

			if ($(this).hasClass('linked-fields')) {
				remote_data['class'] += ' linked-fields';
				remote_data['data-linked-fields'] = $(this).data('linked-fields');
				remote_data['data-linked-values'] = $(this).data('linked-values');
			}

			var remove = $('<a />', remote_data);

			var item = $('<li><span class="text">'+selected.text()+'</span></li>');
			item.append(remove);
			item.append(input);

			selections.append(item).removeClass('hide');

			noSelectionsMsg.addClass('hide');
			removeAll.removeClass('hide');

			if (selected.data('requires-description')) {
				descriptions.append(
					'<div class="row data-row" data-option="' + selected.text() + '">' +
						'<div class="large-2 column">' +
							'<div class="data-label">' +
								selected.text() + ':' +
							'</div>' +
						'</div>' +
						'<div class="large-4 column end">' +
							'<div class="data-value">' +
								'<textarea name="' + fieldName + '[' + i + '][description]" data-option="' + selected.text() + '"></textarea>' +
							'</div>' +
						'</div>' +
					'</div>');

				container.find('textarea[data-option="' + selected.text() + '"]').focus();
			}

			selected.remove();
			select.val('');

			if (options.sorted) {
				selections.append(selections.find('li').sort(function(a,b) {
					return $.trim($(a).find('.text').text()) > $.trim($(b).find('.text').text());
				}));
			}
		}

		select.trigger('MultiSelectFreeTextChanged');
		return false;
	});

	$(this).on('click', 'a.MultiSelectFreeTextRemove', 'click',function(e) {
		e.preventDefault();
		var anchor = $(this);
		var container = anchor.closest('.multi-select-free-text');
		var selections = container.find('.multi-select-free-text-selections');
		var descriptions = container.find('.multi-select-free-text-descriptions');
		var noSelectionsMsg = container.find('.no-selections-msg');
		var removeAll = container.find('.remove-all');
		var input = anchor.closest('li').find('input');

		var attrs = {};
		$(input[0].attributes).each(function() {
			if (this.nodeName != 'type' && this.nodeName != 'name') {
				attrs[this.nodeName] = this.nodeValue;
			}
		});

		var text = anchor.data('text');
		var select = container.find('select');

		var attr_str = '';
		for (var key in attrs) {
			attr_str += ' ' + key + '="' + attrs[key] + '"';
		}

		select.append('<option' + attr_str + '>'+text+'</option>');
		sort_selectbox(select);

		anchor.closest('li').remove();
		input.remove();
		descriptions.find('div[data-option="' + text + '"]').remove();

		if (!selections.children().length) {
			selections.add(removeAll).addClass('hide');
			noSelectionsMsg.removeClass('hide');
		}

		if ($(this).hasClass('linked-fields')) {
			if (inArray($(this).data('text'),$(this).data('linked-values').split(','))) {
				var element_name = container.children('input[type="hidden"]').attr('name').replace(/\[.*$/,'');
				var fields = $(this).data('linked-fields').split(',');
				var values = $(this).data('linked-values').split(',');

				for (var i in fields) {
					if (values.length == 1 || i == arrayIndex($(this).data('text'),values)) {
						hide_linked_field(element_name,fields[i]);
					}
				}
			}
		}

		select.trigger('MultiSelectFreeTextChanged');

		return false;
	});
});
