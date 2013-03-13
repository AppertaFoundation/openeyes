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

var redirectHomeAfterChangingFirm = true;

$('select[id=selected_firm_id]').die('change').live('change', function() {
	var firmId = $('select[id=selected_firm_id]').val();
	$.ajax({
		type: 'post',
		url: baseUrl+'/',
		data: {'selected_firm_id': firmId },
		success: function(data) {
			if (data.match(/change-firm-succeeded/)) {
				if (redirectHomeAfterChangingFirm) {
					url = window.location.href
					if (m = url.match(/firm_id=[0-9]+/)) {
						url = url.replace(m[0],'firm_id='+firmId);
					}
					window.location.href = url;
				}
				return false;
			} else {
				alert("Sorry, changing the firm failed. Please try again or contact support for assistance.");
			}
		}
	});
});

$(document).ready(function(){
	$('.sprite.showhide').click( function(e){
		e.preventDefault();
		var sprite = $(this).children('span');
		var whiteBox = $(this).parents('.whiteBox');
		
		if(sprite.hasClass('hide')) {
			whiteBox.children('.data_row').slideUp("fast");
			sprite.removeClass('hide');
			sprite.addClass('show');
		} else {
			whiteBox.children('.data_row').slideDown("fast");
			sprite.removeClass('show');
			sprite.addClass('hide');
		}
	});

	// show hide
	$('.sprite.showhide2').click(function(e){
		var episode_id = $(this).parent().parent().prev('input').val();
		if (episode_id == undefined) {
			episode_id = 'legacy';
		}

		e.preventDefault();
		changeState($(this).parents('.episode_nav'),$(this).children('span'),episode_id);
	});
	
	function changeState(wb,sp,episode_id) {
		if (sp.hasClass('hide')) {
			wb.children('.events').slideUp('fast');
			sp.removeClass('hide');
			sp.addClass('show');
			$.ajax({
				'type': 'GET',
				'url': '/patient/hideepisode?episode_id='+episode_id,
				'success': function(html) {
				}
			});
		} else {
			wb.children('.events').slideDown('fast');
			sp.removeClass('show');
			sp.addClass('hide');
			$.ajax({
				'type': 'GET',
				'url': '/patient/showepisode?episode_id='+episode_id,
				'success': function(html) {
				} 
			});
		}
	}
	
	/**
	 * Sticky stuff
	 */ 
	$('#alert_banner').waypoint('sticky', {
		offset: -30,
		wrapper: '<div class="alert_banner_sticky_wrapper" />'
	});
	$('#header').waypoint('sticky', {
		offset: -20,
	});
	$('.event_tabs').waypoint('sticky', {
		offset: 39,
		wrapper: '<div class="event_tabs_sticky_wrapper" />'
	});
	$('.event_actions').waypoint('sticky', {
		offset: 44,
		wrapper: '<div class="event_actions_sticky_wrapper" />'
	});
	$('body').delegate('#header.stuck, .event_tabs.stuck, .event_actions.stuck', 'hover', function(e) {
		$('#header, .event_tabs, .event_actions').toggleClass('hover', e.type === 'mouseenter');
	});

	/**
	 * Tab hover
	 */
	$('.event_tabs li').hover(
			function() {
				$(this).addClass('hover');
			},
			function() {
				$(this).removeClass('hover');
			}
	);
	
});

function changeState(wb,sp) {
	if (sp.hasClass('hide')) {
		wb.children('.events').slideUp('fast');
		sp.removeClass('hide');
		sp.addClass('show');
	} else {
		wb.children('.events').slideDown('fast');
		sp.removeClass('show');
		sp.addClass('hide');
	}
}

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function format_date(d) {
	if (window["NHSDateFormat"] !== undefined) {
		var date = window["NHSDateFormat"];
		var m = date.match(/[a-zA-Z]+/g);

		for (var i in m) {
			date = date.replace(m[i],format_date_get_segment(d,m[i]));
		}

		return date;
	}
}

function format_date_get_segment(d,segment) {
	switch (segment) {
		case 'j':
			return d.getDate();
		case 'd':
			return (d.getDate() <10 ? '0' : '') + d.getDate();
		case 'M':
			return getMonthShortName(d.getMonth());
		case 'Y':
			return d.getFullYear();
	}
}

function getMonthShortName(i) {
	var months = {0:'Jan',1:'Feb',2:'Mar',3:'Apr',4:'May',5:'Jun',6:'Jul',7:'Aug',8:'Sep',9:'Oct',10:'Nov',11:'Dec'};
	return months[i];
}

function getMonthNumberByShortName(m) {
	var months = {'Jan':0,'Feb':1,'Mar':2,'Apr':3,'May':4,'Jun':5,'Jul':6,'Aug':7,'Sep':8,'Oct':9,'Nov':10,'Dec':11};
	return months[m];
}
