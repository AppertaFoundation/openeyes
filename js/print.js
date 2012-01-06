$(document).ready(function() {
	$('body').append('<div class="printable" id="printable"></div>');
});

function clearPrintContent() {
	$('#printable').html('');
}

function appendPrintContent(content) {
	$('#printable').append(content);
}

function printContent() {
	$('#printable').printElement({
		pageTitle : 'OpenEyes printout',
		//leaveOpen: true,
		//printMode: 'popup',
		printBodyOptions : {
			styleToAdd : 'width: auto !important; margin: 0.75em !important;',
			classNameToAdd : 'openeyesPrintout'
		},
		overrideElementCSS : [ {
			href : '/css/printcontent.css',
			media : 'all'
		} ]
	});
}

function printUrl(url) {
	$('#printable').load(url, function() {
		printContent();
	});
}
