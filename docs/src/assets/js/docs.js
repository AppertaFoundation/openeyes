(function docs() {

	var Docs = (function() {

		function init() {
			addSectionClassName();
			initGridTools();
			initSyntaxHighlight();
			initNavBar();
			initMarkupAnchors();
			initJsDocMoreInfo();
		}

		// Add's a CSS classname to the main container.
		function addSectionClassName() {
			var section = $(document.body).data('section');
			if (section) {
				section = section.replace(/\s+/g, '-').toLowerCase();
				$('.container.main').addClass('section-'+section);
			}
		}

		function initGridTools() {

			if ($(document.body).hasClass('jsdoc')) {
				return;
			}

			var anchor = $('<a href="#" class="show-grid-anchor">Toggle grid</a>');
			anchor.appendTo(document.body);

			var grid = $('<div />', { 'class': 'grid-overlay' });
			grid.appendTo(document.body);

			var style = 0;

			anchor.on('click', function(e) {
				e.preventDefault();
				if (!style) {
					grid.show();
				}
				style++;
				if (style === 3) {
					style = 0;
					grid.hide();
				} else {
					grid.removeClass(function(index, className) {
						return (className.match(/(style-[0-9]+)/g) || []).join(' ');
					}).addClass('style-'+style);
				}
			});
		}

		/** Add syntax highlighting to code blocks */
		function initSyntaxHighlight() {

			// Ensure all <pre> blocks will have syntax highlighting
			$('pre').addClass('prettyprint');

			// Due to markdown's inability to correctly handle code blocks within list items,
			// we have to remove the excess whitespace manually. Gah!
			$('code').each(function() {
				// this.innerHTML = this.innerHTML.replace(/^\s{0,4}/mg, '');
			});

			// Add syntax highlighting to <pre> blocks.
			$('pre').addClass('prettyprint').each(function() {
				$(this).append($(this).find('code').html());
				$(this).find('code').remove();
			})
			if (window.prettyPrint) prettyPrint()
		}

		/** Control the positioning of the navbar */
		function initNavBar() {

			var win = $(window);
			var nav = $('.box.navigation');

			if (!nav.length) {
				return;
			}

			var navOffset = nav.offset();

			function onWinScroll() {

				var marginTop = (win.scrollTop() >= navOffset.top) ? win.scrollTop() - navOffset.top + 20 : 0;

				nav.css({
					marginTop: marginTop
				});
			}

			function onNavbarResize() {

				win.off('scroll.navbar');

				if (win.height() <= nav.height() || win.width() <= 1024) {
					return nav.css({
						marginTop: 0
					});
				}

				win.on('scroll.navbar', onWinScroll);
				win.trigger('scroll.navbar');
			}

			win.on('resize.navbar', onNavbarResize);
			win.trigger('resize.navbar');
		}

		function initMarkupAnchors() {
			$('.example').find('header:first').prepend(function() {
				return $('<a />', {
					'class': 'view-markup right',
					'html': 'View Markup'
				})
				.on('click', onViewMarkupClick);
			});
		}

		function initJsDocMoreInfo() {
			$('.jsdoc .name a').on('click', function(e){
				e.preventDefault();
				$('#more-info-'+ this.href.replace(/^.*#/, '')).toggle();
			});
		}

		function onViewMarkupClick(e) {

			var combinedMarkup = '';

			$(e.currentTarget).parents('.example').find('.show-markup').each(function(i, elem) {

				var markup = $(elem).html();

				markup = markup.replace(/<header>[\s\S.]*<\/header>/gm, '');
				markup = markup.replace(/\t/gm, '    '); // tabs to 4 spaces
				markup = markup.replace(/^\n/gm, '');    // remove blank lines

				// Get the indentation level from the first line
				var indentation = markup.split('\n')[0].replace(/^([^>]+)<.*/, '$1').length

				markup = markup.replace(new RegExp('^\\s{0,'+indentation+'}', 'gm'), ''); // remove leading whitespace
				markup = markup.replace(/^\n/gm, '');    // remove blank lines

				// Prettify the markup
				combinedMarkup += '<pre class="prettyprint lang-html">' + prettyPrintOne(htmlEntities(markup)) + '</pre>';

			});

			showMarkupDialog(combinedMarkup);
		};

		function showMarkupDialog(markup) {
			new OpenEyes.UI.Dialog({
				title: 'Markup',
				content: markup,
				width: 800,
				constrainToViewport: true,
				show: 'fade'
			}).open();
		}

		function htmlEntities(str) {
			return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}

		return {
			init: init
		}
	}());

	$(Docs.init);
}());