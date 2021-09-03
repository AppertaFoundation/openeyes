/** Javascript for message dashboard on home page **/

$(document).ready(function() {
	$('.js-expanded-message').hide();

	$('.filter-messages').on('click', 'a', function (e) {
		e.preventDefault();
		window.location.href = jQuery.query.set('messages', $(this).data('filter'));
	});

	$('#OphCoMessaging_to').add('#OphCoMessaging_from').each(function () {
		pickmeup('#' + $(this).attr('id'), {
			format: 'd b Y',
			hide_on_select: true,
			default_date: false
		});
	}).on('pickmeup-change change', function () {
        $('#OpCoMessaging_All').prop("checked", false);
	});

	$('#OpCoMessaging_All').on('click', function () {
        if ($(this).prop("checked")) {
            $('#OphCoMessaging_from').val('');
            $('#OphCoMessaging_to').val('');
        }
    });

	$('#OphCoMessaging_Submit').on('click', function (e) {
        e.preventDefault();
        window.location.href = jQuery.query
            .set('OphCoMessaging_from', $('#OphCoMessaging_from').val())
            .set('OphCoMessaging_to', $('#OphCoMessaging_to').val())
            .set('OphCoMessaging_Search_Sender', $('#OphCoMessaging_Search_Sender').val())
            .set('OphCoMessaging_Search_MessageType', $('#OphCoMessaging_Search_MessageType').val())
            .set('OphCoMessaging_Search', $('#OphCoMessaging_Search').val());
    });

	$('.js-expand-message').each(function(){
		let $expandIcon = $(this);
		let $messagePreview = $expandIcon.closest('tr').find('.js-preview-message');
		let $messageExpanded = $messagePreview.siblings('.js-expanded-message');
		new Expander( $expandIcon, $messagePreview, $messageExpanded );
		toggleExpandIconVisibility($expandIcon, $messagePreview, $messageExpanded);
	});

    $('.range').on('click', function () {
        $('#OpCoMessaging_All').prop("checked", false);
        setPastDates($(this).attr('data-range'));
    });

	$(window).resize(function(){
		$('.js-expand-message').each(function(){
			toggleExpandIconVisibility($(this), $(this).closest('tr').find('.js-preview-message'), $(this).closest('tr').find('.js-expanded-message'));
		});
	});
});

function toggleExpandIconVisibility($expandIcon, $messagePreview, $messageExpanded) {
	if ($expandIcon.hasClass('collapse') || isExpandableMessage($messagePreview, $messageExpanded)) {
		$expandIcon.show();
	} else {
		$expandIcon.hide();
	}
}

function isExpandableMessage($messagePreview, $messageExpanded) {
	let isOverflowing = $messagePreview[0].scrollWidth > $messagePreview.innerWidth();
	let isChangedByExpand = $messagePreview[0].innerHTML !== $messageExpanded[0].innerHTML;
	return isOverflowing || isChangedByExpand;
}

function Expander( $icon, $messagePreview, $messageExpanded){
	$icon.click(function () {
		$icon.toggleClass('expand collapse');
		$messagePreview.toggle();
		$messageExpanded.toggle();
		toggleExpandIconVisibility($icon, $messagePreview, $messageExpanded);
	});
}

function setPastDates($dateRange) {
    var from;
    var to;
    switch ($dateRange) {
        case 'today':
            from = new Date();
            to = new Date();
            break;
        case 'yesterday':
            from = -1;
            to = -1;
            break;
        case 'last-week':
            var startDay = new Date();
            startDay = startDay.getDay();
            from = -1*(startDay+6);
            to = from+4;
            break;
        case 'this-week':
            var today = new Date();
            today = today.getDay();
            if (today === 1) {
                from = new Date();
                to = today+3;
            } else {
                from = -1*(today-1);
                to = from+4;
                if (to === 0) {
                    to = new Date();
                }
            }
            break;
    }
    $('#OphCoMessaging_from').datepicker({
        dateFormat: 'd M yy',
    }).datepicker("setDate", from);
    $('#OphCoMessaging_to').datepicker({
        dateFormat: 'd M yy',
    }).datepicker("setDate", to);
    var datePicker = document.getElementById('ui-datepicker-div');
    if (datePicker !== null) {
        datePicker.remove();
    }
}