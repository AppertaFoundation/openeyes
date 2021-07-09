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

$(document).ready(function() {

	$('select.generic-admin-filter').change(function () { this.form.submit(); });

	$('.editRow').die('click').live('click',function(e) {
		e.preventDefault();

		$(this).closest('tr').children('td:first').children('span').hide();
		$(this).closest('tr').children('td:first').children('input').show();
	});

	$('.deleteRow').die('click').live('click',function(e) {
		e.preventDefault();

		$(this).closest('tr').remove();
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

	$('.generic-admin.sortable tbody').sortable({
		start: function() {
			$('.generic-admin tbody').data('default',$('.generic-admin tbody tr').find('input[type="radio"][name="default"]:checked').closest('tr').data('row'));
		},
		stop: function() {
			GenericAdmin_ReindexDefault();
			$('.generic-admin tbody tr[data-row="' + $('.generic-admin tbody').data('default') + '"]').find('input[type="radio"][name="default"]').prop('checked',true);
		}
	});

	function getNextDisplayOrder() {
		var keys = $('table.generic-admin tr').map(function(index, el) {
			v = parseInt($(el).find('input[name^="display_order"]').val());
			return isNaN(v) ? -Infinity : v;
		}).get();
		var v = Math.max.apply(null, keys);
		if (v >= 0) {
			return v+1;
		}
		return 1;
	}

	function appendRow(form) {
		let dOrder = getNextDisplayOrder();
		let $tbody = $('.generic-admin tbody');
		let $last_tr;

        $tbody.append(form);
        $last_tr = $tbody.children('tr:last');
        $tbody.children('tr:last').find('input[name^="display_order"]').val(dOrder);
        $last_tr.children('td:nth-child(2)').children('input').focus();
		//TODO: what is i?
        $last_tr.find('input[type="radio"][name="default"]').attr('value',i-1);
	}

	$('.generic-admin-add').unbind('click').click(function(e) {
		e.preventDefault();

		var data = {
			"key" : getNextKey()
		};
		var template = $('.generic-admin .newRow').html();

		if (template) {
			// insert the new row before the template row
            $('.generic-admin tbody tr:last').before('<tr data-row="'+data.key+'">' + Mustache.render(template, data).replace(/ disabled="disabled"/g,'') + '</tr>');
		}
		else {
			$.ajax({
				'url': $(this).data('new-row-url'),
				'data': {'key': data.key, 'model': $(this).data('model')},
				'success': function(resp) {
					appendRow(resp);
				}
			});
		}
    });

    $('#select-all').change(function () {
        if (this.checked == true) {
            $('input[id^="selected_"]').not('input[id="selected_{{key}}"]').prop('checked', true);
        } else {
            $('input[id^="selected_"]').not('input[id="selected_{{key}}"]').prop('checked', false);
        }
    });
});

function GenericAdmin_ReindexDefault()
{
	var i = 0;

	$('.generic-admin tbody tr').map(function() {
		var display_order = $(this).find('input[name*="display_order"]');

		if (display_order.length > 0) {
			display_order.val(i+1);
		}

		var def = $(this).find('input[type="radio"][name="default"]');

		if (def.length >0) {
			def.attr('value',i);
		}

		i += 1;
	});
}
