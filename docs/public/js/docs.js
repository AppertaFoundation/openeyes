(function docs() {

	function htmlEntities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	var Docs = {
		init: function() {
			this.getElements();
			this.createMarkupAnchors();
		},
		getElements: function() {
			this.examples = $('.example');
		},
		createMarkupAnchors: function() {
			this.examples.find('header').prepend(this.createAnchor.bind(this));
		},
		createAnchor: function() {
			return $('<a />', {
				'class': 'view-markup right',
				'html': 'View Markup'
			})
			.on('click', this.onViewMarkupClick.bind(this));
		},
		onViewMarkupClick: function(e) {

			var element = $(e.currentTarget).parents('.example');
			var markup = element.html();

			markup = markup.replace(/<header>[\s\S.]*<\/header>/gm, '');
			markup = markup.replace(/\t/gm, '    '); // tabs to 4 spaces
			markup = markup.replace(/^\n/gm, '');    // remove blank lines

			// Get the indentation level from the first line
			var indentation = markup.split('\n')[0].replace(/^([^>]+)<.*/, '$1').length

			markup = markup.replace(new RegExp('^\\s{0,'+indentation+'}', 'gm'), ''); // remove leading whitespace
			markup = markup.replace(/^\n/gm, '');    // remove blank lines

			// Prettify the markup
			markup = prettyPrintOne(htmlEntities(markup));

			this.showMarkupDialog(markup);
		},
		showMarkupDialog: function(markup) {
			new OpenEyes.Dialog({
				title: 'Markup',
				content: '<pre class="prettyprint lang-html">' + markup + '</pre>',
				width: 800,
				height: 800,
				constrainToViewport: true,
				show: null
			}).open();
		}
	};

	$(Docs.init.bind(Docs));

}());