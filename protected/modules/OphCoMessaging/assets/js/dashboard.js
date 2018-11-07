/** Javascript for message dashboard on home page **/

$(document).ready(function() {
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
		let $message = $expandIcon.closest('tr').find('.message');
		new Expander( $expandIcon, $message );
		toggleExpandIconVisibility($expandIcon, $message);
	});

	$(window).resize(function(){
		$('.js-expand-message').each(function(){
			toggleExpandIconVisibility($(this), $(this).closest('tr').find('.message'));
		});
	});
});

function toggleExpandIconVisibility($expandIcon, $message) {
	if ($expandIcon.hasClass('collapse') || isExpandableMessage($message)) {
		$expandIcon.show();
	} else {
		$expandIcon.hide();
	}
}

function isExpandableMessage($message) {
	let isMultiLine = $message[0].scrollHeight > $message.innerHeight();
	let isOverflowing = $message[0].scrollWidth > $message.innerWidth();
	return isMultiLine || isOverflowing;
}

function Expander( $icon, $message){
	let expanded = false;
	$icon.click( change );

	function change(){
		$icon.toggleClass('expand collapse');
		if(expanded){
			$message.removeClass('expand');
		} else {
			$message.addClass('expand');
		}
		toggleExpandIconVisibility($icon, $message);
		expanded = !expanded;
	}
}