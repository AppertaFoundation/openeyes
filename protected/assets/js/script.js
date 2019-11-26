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

$(document).ready(function () {

  var openeyes = new OpenEyes.UI.NavBtnPopup('logo', $('#js-openeyes-btn'), $('#js-openeyes-info')).useWrapperEvents($('.openeyes-brand'));
  $('.openeyes-brand').off('mouseenter');
  var shortcuts = new OpenEyes.UI.NavBtnPopup('shortcuts', $('#js-nav-shortcuts-btn'), $('#js-nav-shortcuts-subnav')).useWrapperEvents($('#js-nav-shortcuts'));

  // If the patient ticketing popup exists ...
  var $patientTicketingPopup = $('#patient-alert-patientticketing');
  var $hotlistNavButton = $('#js-nav-hotlist-btn');
  if ($patientTicketingPopup.length > 0) {
    // ... then set it up to use the hotlist nav button
    var vc_nav = new OpenEyes.UI.NavBtnSidebar({'panel_selector': '#patient-alert-patientticketing'});
    $hotlistNavButton.find('svg').get(0).classList.add('vc');
    $('#js-hotlist-panel').hide();
  } else if ($('#js-hotlist-panel').length > 0) {
    // .. otherwise set up the hotlist
    var hotlist = new OpenEyes.UI.NavBtnPopup.HotList('hotlist', $hotlistNavButton, $('#js-hotlist-panel'), {autoHideWidthPixels: 1890});
    hotlist.useAdvancedEvents($('.js-hotlist-panel-wrapper'));
  }

	// override the behaviour for showing search results
	$.ui.autocomplete.prototype._renderItem = function( ul, item ) {
		var re = new RegExp( '(' + $.ui.autocomplete.escapeRegex(this.term) + ')', 'gi' );
		var highlightedResult = item.label.replace( re, '<span class="autocomplete-match">$1</span>' );
		return $( '<li></li>' )
				.data( 'item.autocomplete', item )
				.append( '<a>' + highlightedResult + '</a>' )
				.appendTo( ul );
	};


	$(document).on('click', '.js-toggle', function(e) {

		e.preventDefault();

		var trigger = $(this);
		var container = trigger.closest('.js-toggle-container');
		var toggleState = 0;

		if (!container.length) {
			throw new Error('Unable to find js-toggle container.')
		}

		var body = container.find('.js-toggle-body');

		if (!body.length) {
			throw new Error('Unable to find js-toggle body.')
		}

		// close
		if (trigger.hasClass('toggle-hide')) {
			// set state to close
			toggleState = 0;
			trigger
			.removeClass('toggle-hide')
			.addClass('toggle-show');
			body.slideUp('fast', function() {
                $(container).trigger('oe:toggled');
			});
		// open
		} else {
			// set state to open
			toggleState = 1;
			trigger
			.removeClass('toggle-show')
			.addClass('toggle-hide');
			body.slideDown('fast', function() {
				body.css('overflow', 'visible');
                $(container).trigger('oe:toggled');
			});
		}

		// session cookie to save open/closed state of the box
		$.cookie( container.attr('id') + '-state', toggleState);
	});

	$(document).on('hover', '.dropdown', function(e)
	{
		$(this).find('ul').slideToggle('medium');
	});

	(function sidebarEventsToggle() {

		var triggers = $('.sidebar.episodes-and-events .toggle-trigger');
		triggers.on('click', onTriggerClick);

		function onTriggerClick(e) {

			e.preventDefault();

			var trigger = $(this);
			var episodeContainer = trigger.closest('.episode');
			var input = episodeContainer.find('[name="episode-id"]');
			var episode_id = input.val() || 'legacy';
			var state = trigger.hasClass('toggle-hide') ? 'hide' : 'show';

			changeState(episodeContainer, trigger, episode_id, state);
		}

		function changeState(episodeContainer, trigger, episode_id, state) {

			trigger.toggleClass('toggle-hide toggle-show');

			episodeContainer
			.find('.events-container,.events-overview')
			.slideToggle('fast', function() {
				$(this).css({ overflow: 'visible' });
			});

			updateEpisode(episode_id, state);
		}

		function updateEpisode(episode_id, state) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/patient/' + state + 'episode?episode_id='+episode_id,
			});
		}
	}());

	(function patientWarningTooltip() {

		var warning = $('.panel.patient .warning');
		if (!warning.length) {
			return;
		}
		var messages = warning.find('.messages');
		var box = $('<div class="quicklook warning"></div>');

		box.hide();
		box.html(messages.html());
		box.appendTo('body');

		warning.hover(function() {

			var offsetPos = $(this).offset();
			var top = offsetPos.top + $(this).height() + 6;
			var middle = offsetPos.left + $(this).width()/2;
			var left = middle - box.width()/2 - 8;

			box.css({
				position: 'absolute',
				top: top,
				left: left
			});
			box.fadeIn('fast');
		}, function(e){
			box.hide();
		});
	}());

  (function expandElementList() {
    // check for view elementss
    if ($('.element-data').length == 0) return;

    $('.js-listview-expand-btn').each(function () {
      // id= js-listview-[data-list]-full | quick
      let listid = $(this).data('list');
      let listview = new ListView($(this),
        $('#js-listview-' + listid + '-pro'),
        $('#js-listview-' + listid + '-full'));
    });

    function ListView($iconBtn, $quick, $full) {
      let quick = $quick.css('display') !== 'none';

      $iconBtn.click(function () {
        $(this).toggleClass('collapse expand');
        quick = !quick;

        if (quick) {
          $quick.show();
          $full.hide();
        } else {
          $quick.hide();
          $full.show();
        }
      });
    }
  })();

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

	/**
	 * Warn on leaving edit mode
	 */
	window.formHasChanged = false;
	window.changedTickets = {};
	window.patientTicketChanged = false;

	var submitted = false;


  $('#patient-alerts form').on("change", function(e) {
		window.changedTickets[$(e.target).closest('#PatientTicketing-queue-assignment').data('queue')]=true;
		window.patientTicketChanged = true;
	});

	$("#event-content, #patient-summary-form-container").on("change", function (e) {
		OpenEyes.Form.edit($(e.target).closest('form'));
	});

	//if the save button is on page
	if($('#et_save').length){
		$(".EyeDrawWidget").on("click", function (e) {
			window.formHasChanged = true;
		});
	}

	$(window).on('beforeunload', function (e) {
		if ((window.formHasChanged && !submitted) || window.patientTicketChanged && !submitted) {
			var message = "You have not saved your changes.", e = e || window.event;
			if (e) {
				e.returnValue = message;
			}
			return message;
		}
	});

	$(document).on('submit', 'form', function() {
		submitted = true;
	});

	/**
	 * Site / firm switcher
	 */
	(function firmSwitcher() {

		// Default dialog options.
		var options = {
			id: 'site-and-firm-dialog',
			title: 'Select a new Site and/or Context'
		};

		// Show the 'change firm' dialog when clicking on the 'change firm' link.
		$(document).on('click', '#change-firm', function(e) {

			e.preventDefault();
			var returnUrl = window.location.href;

			new OpenEyes.UI.Dialog($.extend({}, options, {
				url: baseUrl + '/site/changesiteandfirm',
				width: 500,
				data: {
					returnUrl: returnUrl,
					patient_id: window.OE_patient_id || null
				}
			})).open();
		});
	}());

	$('#checkall').click(function() {
		$('input.'+$(this).attr('class')).attr('checked',$(this).is(':checked') ? 'checked' : false);
	});

    $(this).on('click', '.alert-box .close' , function(e) {
        $(e.srcElement).closest('.alert-box').fadeOut(500);
    });



	$(this).on('mouseout', '.js-has-tooltip', function () {
		$('body').find(".oe-tooltip").remove();
	});

	$(this).on('mouseover', '.js-has-tooltip', function () {
		var text = $(this).data('tooltip-content');
		var leftPos, toolCSS;

		// get icon DOM position
		let iconPos = $(this)[ 0 ].getBoundingClientRect();
		let iconCenter = iconPos.width / 2;

		// check for the available space for tooltip:
		if ( ( $( window ).width() - iconPos.left) < 100 ){
			leftPos = (iconPos.left - 188) + iconPos.width; // tooltip is 200px (left offset on the icon)
			toolCSS = "oe-tooltip offset-left";
		} else {
			leftPos = (iconPos.left - 100) + iconCenter - 0.5; 	// tooltip is 200px (center on the icon)
			toolCSS = "oe-tooltip";
		}

		// check if tooltip is in a popup(oe-popup, oe-popup-wrap)
		let z_index;
		if ($('body > .oe-popup-wrap > .oe-popup').length > 0) {
			z_index = parseInt($('body > .oe-popup-wrap').css('z-index')) + 5;
		}

		// add, calculate height then show (remove 'hidden')
		var tip = $( "<div></div>", {
			"class": toolCSS,
			"style":"left:"+leftPos+"px; top:0;"+(z_index !== "undefined" ? "z-index:"+z_index+";" : "")
		});
		// add the tip (HTML as <br> could be in the string)
		tip.html(text);

		$('body').append(tip);
		// calc height:
		var h = $(".oe-tooltip").height();
		// update position and show
		var top = iconPos.y - h - 25;

		$(".oe-tooltip").css({"top":top+"px"});

	});

	let $headerPrintDropdown = $('#js-header-print-dropdown');
	let $headerPrintDropdownButton = $('#js-header-print-dropdown-btn');
	let $headerPrintSubnav = $('#js-header-print-subnav');

	$headerPrintDropdown.on({
		mouseover: function() {
			$headerPrintDropdownButton.addClass('active');
			$headerPrintSubnav.show();
			},
		mouseout: function() {
			$headerPrintDropdownButton.removeClass('active');
			$headerPrintSubnav.hide();
		}
	});

	$headerPrintSubnav.on({
		mouseover: function() { $(this).show(); },
		mouseout: function() { $(this).hide();}
	});

    (function elementSubgroup() {
        let $viewstate_btns = $('.js-element-subgroup-viewstate-btn');
        if( $viewstate_btns.length === 0 ) {
            return;
        }
        $viewstate_btns.each( function(){
            new Viewstate( $(this) );
        });

        function Viewstate( $icon ){
            let view_state = this;
            let $content = $('#' + $icon.data('subgroup') );

            $icon.click( function( e ){
                e.preventDefault();
                view_state.changeState();
            });

            this.changeState = function(){
                $content.toggle();
                $icon.toggleClass('collapse expand');
            }

        }
    })();

    (function notificationBanner() {
        if ($('#oe-admin-notifcation').length === 0) {
            return;
        }
        // icon toggles Short/ Full Message
        $('#oe-admin-notifcation .oe-i').click(toggleNotification);
		$('#oe-admin-notifcation .oe-i').on('mouseenter' , toggleNotification);
		$('#oe-admin-notifcation .oe-i').on('mouseout' , toggleNotification);


        function toggleNotification() {
            $('#notification-short').toggle();
            $('#notification-full').toggle();
        }
    }());
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
	if (NHSDateFormat !== undefined) {
		var date = NHSDateFormat;
		var m = date.match(/[a-zA-Z]+/g);

		for (var i in m) {
			date = date.replace(m[i],format_date_get_segment(d,m[i]));
		}

		return date;
	}
}

function format_pickmeup_date(d) {
  return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
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


function showhidePCR(id) {
    var e = document.getElementById(id);
    e.style.display = (e.style.display == 'block') ? 'none' : 'block';
}

function getMonthNumberByShortName(m) {
	var months = {'Jan':0,'Feb':1,'Mar':2,'Apr':3,'May':4,'Jun':5,'Jul':6,'Aug':7,'Sep':8,'Oct':9,'Nov':10,'Dec':11};
	return months[m];
}

/**
 * sort comparison function for html elements based on the inner html content, but will check for the presence of data-order attributes and
 * sort on those if present
 *
 * @param a
 * @param b
 * @return
 */
function selectSort(a, b) {
		if (a.innerHTML == rootItem) {
				return -1;
		}
		else if (b.innerHTML == rootItem) {
				return 1;
		}
		// custom ordering
		if ($(a).data('order')) {
			return ($(a).data('order') > $(b).data('order')) ? 1 : -1;
		} else if ($(a).data('display_order')) {
			return ($(a).data('display_order') > $(b).data('display_order')) ? 1 : -1;
		}

		return (a.innerHTML > b.innerHTML) ? 1 : -1;
}

var rootItem = null;

function sort_selectbox(element) {
	rootItem = element.children('option:first').text();
	var rootVal = element.children('option:first').val();
	element.append(element.children('option').sort(selectSort));
	element.val(rootVal);
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(haystack[i] == needle) return true;
	}
	return false;
}

function arrayIndex(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(haystack[i] == needle) return i;
	}
	return false;
}

function formatStringToEndWithCommaAndWhitespace(value){
	if (typeof value !== 'string'){
		throw new TypeError('formatStringToEndWithWhiteSpace requires a string argument');
	}
	let outputString = value.trimEnd();
	if(outputString){
		outputString += outputString.slice(-1) === ',' ? ' ' : ', ';
	}
	return outputString;
}

function concatenateArrayItemLabels(arrayItems){
	let outputString = '';
	$(arrayItems).each(function (key, item) {
		if(item['label']){
			outputString += item['label'];
		}
	});
	return outputString;
}
