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

function AuditLog() {if (this.init) this.init.apply(this, arguments); }

AuditLog.prototype = {
	init : function() {
		this.refresh_rate = 5000;
		this.data = $('#auditListData');
		this.run = true;

		setTimeout('auditLog.refresh();',1000);
	},
	refresh : function() {
		if (!this.run) {
			this.running = false;
			return;
		}

		this.running = true;

		last_id = null;
		$('#auditListData').children('tr').map(function() {
			if (last_id == null && $(this).attr('id') != 'undefined') {
				last_id = $(this).attr('id').match(/[0-9]+/);
			}
		});

		var user_id = $('#previous_user_id').val();

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/audit/updateList?last_id='+last_id+'&site_id='+$('#previous_site_id').val()+'&firm_id='+$('#previous_firm_id').val()+'&user_id='+user_id+'&action='+$('#previous_action').val()+'&target_type='+$('#previous_target_type').val()+'&date_from='+$('#previous_date_from').val()+'&date_to='+$('#previous_date_to').val()+'&hos_num='+$('#previous_hos_num').val(),
			'success': function(html) {
				if ($.trim(html).length >0) {
					auditLog.data.html(html + auditLog.data.html());

					auditLog.lines = [];

					auditLog.data.children('tr').map(function() {
						if (!$(this).attr('class').match(/auditextra/) && $(this).is(':hidden')) {
							auditLog.lines.push($(this));
						}
					});

					auditLog.showLines();
				} else {
					setTimeout('auditLog.refresh();',auditLog.refresh_rate);
				}
			}
		});
	},
	showLines : function() {
		if (this.lines.length == 0) {
			setTimeout('auditLog.refresh();',auditLog.refresh_rate);
		} else {
			var line = this.lines.pop();

			var even = $('#auditListData').children('tr:visible').attr('class').match(/even/);

			if (even) {
				line.attr('class',line.attr('class').replace(/even/,'odd'));
			} else {
				line.attr('class',line.attr('class').replace(/odd/,'even'));
			}

			var lines = this.lines;

			line.slideToggle('fast',function() {
				var last_extra = auditLog.data.children('tr').last();
				if (!last_extra.is(':hidden')) {
					last_extra.slideToggle('fast',function() {
						$(this).remove();
						auditLog.data.children('tr').last().slideToggle('fast',function() {
							$(this).remove();
							if (lines == 0) {
								setTimeout('auditLog.refresh();',1000);
							} else {
								auditLog.showLines();
							}
						});
					});
				} else {
					last_extra.remove();
					auditLog.data.children('tr').last().slideToggle('fast',function() {
						$(this).remove();
						if (lines == 0) {
							setTimeout('auditLog.refresh();',1000);
						} else {
							auditLog.showLines();
						}
					});
				}
			});
		}
	},
	loadItems : function() {

		if (this.running) {
			setTimeout('auditLog.loadItems()',50);
			return;
		}

		var loadingMsg = $('#search-loading-msg');
		loadingMsg.show();

		$.ajax({
			'url': baseUrl+'/audit/search',
			'type': 'POST',
			'data': $('#auditList-filter').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(data) {
				var s = data.split('<!-------------------------->');

				$('.loader').hide();
				$('#searchResults').html(s[0]);
				$('.pagination').html(s[1]).show();

				return false;
			},
			'complete': function() {
				loadingMsg.hide();
			}
		});
	}
}

$(document).ready(function() {
	$('a[id^="detail"]').die('click').live('click',function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		if ($('tr.auditextra'+id).is(':hidden')) {
			$('tr.auditextra'+id).show();
		} else {
			$('tr.auditextra'+id).hide();
		}
		return false;
	});

	$('a.auditItem').die('click').live('click',function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		$('.auditextra'+id).slideToggle('fast');
		return false;
	});

	$('a.showData').die('click').live('click',function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		var data = $(this).next('input').val();
		$(this).closest('.link').hide();
		$('#dataspan'+id).html(data);
		return false;
	});

	$('a.changePage').die('click').live('click',function() {
		$('#page').val($(this).attr('id').match(/[0-9]+/));

		$('.loader').show();

		auditLog.run = false;
		auditLog.running = false;
		setTimeout('auditLog.loadItems()',50);

		return false;
	});
});

if (window.auditLog == undefined) {
	var auditLog = new AuditLog;
}
