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
		window.location.href = jQuery.query
			.set('OphCoMessaging_from', $('#OphCoMessaging_from').val())
			.set('OphCoMessaging_to', $('#OphCoMessaging_to').val());
	});

	$('.js-expand-message').each(function(){
		let $expandIcon = $(this);
		let $messagePreview = $expandIcon.closest('tr').find('.js-preview-message');
		let $messageExpanded = $messagePreview.siblings('.js-expanded-message');
		new Expander( $expandIcon, $messagePreview, $messageExpanded );
		toggleExpandIconVisibility($expandIcon, $messagePreview, $messageExpanded);
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