$(function() {

	$('.view-markup').on('click', function(e) {

		var element = $('#' + $(this).data('markup'));
		var markup = element.html();

		markup = markup.replace(/<header>[\s\S.]*<\/header>/gm, '');
		markup = markup.replace(/\t/gm, '    '); // tabs to 4 spaces
		markup = markup.replace(/^\n/gm, '');    // remove blank lines

		// Get the indentation level from the first line
		var indentation = markup.split('\n')[0].replace(/^([^>]+)<.*/, '$1').length

		markup = markup.replace(new RegExp('^\\s{0,'+indentation+'}', 'gm'), ''); // remove leading whitespace
		markup = markup.replace(/^\n/gm, '');    // remove blank lines

		new OpenEyes.Dialog({
			title: 'Markup',
			content: '<pre class="prettyprint lang-html">' + prettyPrintOne(htmlEntities(markup)) + '</pre>',
			width: 800,
			height: 800,
			constrainToViewport: true
		}).open();
	});

	function htmlEntities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
});