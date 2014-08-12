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
	$('.editRow').die('click').live('click',function(e) {
		e.preventDefault();

		$(this).closest('tr').children('td:first').children('span').hide();
		$(this).closest('tr').children('td:first').children('input').show();
	});

	$('.deleteRow').die('click').live('click',function(e) {
		e.preventDefault();

		$(this).closest('tr').remove();
	});

	$('.generic-admin tbody').sortable({
		helper:'clone',
		update: function(event, ui) {
			var i = 1;
			$('.generic-admin tbody tr').each(function() {
				$(this).find('input[name^="display_order"]').val(i++);
			});
		}
	});

	function getNextKey() {
		var keys = $('table.generic-admin tr').map(function(index, el) {
			v = parseInt($(el).attr('data-row'));
			return isNaN(v) ? -Infinity : v;
		}).get();
		var v = Math.max.apply(null, keys);
		if (v >= 0) {
			return v+1;
		}
		return 0;
	}

	$('.generic-admin-add').unbind('click').click(function(e) {
		e.preventDefault();

		var template = $('.generic-admin .newRow').html();
		var data = {
			"key" : getNextKey()
		};
		var form = Mustache.render(template, data).replace(/ disabled="disabled"/g,'');

		$('.generic-admin tbody').append('<tr data-row="'+data.key+'">' + form + '</tr>');
		$('.generic-admin tbody').children('tr:last').children('td:nth-child(2)').children('input').focus();
	});
});
