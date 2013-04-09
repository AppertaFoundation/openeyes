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
	$('select.MultiSelectList').unbind('change').bind('change',function() {
		var selected = $(this).children('option:selected');

		if (selected.val().length >0) {
			$(this).parent().children('div').children('ul').append('<li>'+selected.text()+' (<a href="#" class="MultiSelectRemove '+selected.val()+'">remove</a>)</li>');

			var element_class = $(this).attr('name').replace(/\[.*$/,'');

			var m = $(this).parent().parent().prev('input').attr('name').match(/\[MultiSelectList_(.*?)\]$/);
			var multiSelectField = m[1];
			
			var attrs = {};
			$(selected[0].attributes).each(function() {
				attrs[this.nodeName] = this.nodeValue;
			});
			var inp_str = '<input type="hidden" name="'+multiSelectField+'[]"';
			for (var key in attrs) {
				inp_str += ' ' + key + '="' + attrs[key] + '"';
			}
			inp_str += ' />';
			
			$(this).parent().children('div').children('ul').append(inp_str);

			selected.remove();

			$(this).val('');
		}

		$(this).trigger('MultiSelectChanged');
		return false;
	});

	$(this).undelegate('a.MultiSelectRemove','click').delegate('a.MultiSelectRemove','click',function(e) {
		e.preventDefault();
		var inp = $(this).parent().next();
		var attrs = {};
		$(inp[0].attributes).each(function() {
			if (this.nodeName != 'type' && this.nodeName != 'name') {
				attrs[this.nodeName] = this.nodeValue;
			}
		});
		
		var text = $(this).parent().text().trim().replace(/ \(.*$/,'');

		var select = $(this).parent().parent().parent().parent().children('select');
		
		var attr_str = '';
		for (var key in attrs) {
			attr_str += ' ' + key + '="' + attrs[key] + '"';
		}
		select.append('<option' + attr_str + '>'+text+'</option>');

		sort_selectbox(select);

		$(this).parent().next().remove();
		$(this).parent().remove();
		
		$(select).trigger('MultiSelectChanged');
		
		return false;
	});
});
