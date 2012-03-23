$(document).ready(function(){
	$collapsed = true;

	$('#addNewEvent').unbind('click').click(function(e) {
		e.preventDefault();
		$collapsed = false;

		$('#add-event-select-type').slideToggle(100,function() {
			if($(this).is(":visible")){
				$('#addNewEvent').removeClass('green').addClass('inactive');
				$('#addNewEvent span.button-span-green').removeClass('button-span-green').addClass('button-span-inactive');
				$('#addNewEvent span.button-icon-green').removeClass('button-icon-green').addClass('button-icon-inactive');
			} else {
				$('#addNewEvent').removeClass('inactive').addClass('green');
				$('#addNewEvent span.button-span-inactive').removeClass('button-span-inactive').addClass('button-span-green');
				$('#addNewEvent span.button-icon-inactive').removeClass('button-icon-inactive').addClass('button-icon-green');
				$collapsed = true;
			}
			return false;
		});

		return false;
	});
});

function addOptionalElement(element_name) {
	$.ajax({
		'url': 'loadElement?element_name='+element_name,
		'type': 'GET',
		'success': function(data) {
			$('#event_content').append(data);
		}
	});
}
