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

(function(Modernizr, document) {

	var Polyfills = {
		// Ensure the modernizr test fails before adding the polyfill on document ready.
		add: function(name, polyfill) {
			if (!Modernizr[name]) {
				$(polyfill);
			}
		}
	};

	// Form attribute polyfill (specific to input/submit buttons).
	// @example
	// <form method="post" id="example" action="?"></form>
	// <input type="submit" form="example" value="Submit" />
	Polyfills.add('formattribute', function() {

		function stopPropagation(e){
			e.stopPropagation();
		};

		function onButtonClick(e) {

			if (e.target.form || e.isDefaultPrevented() || !~$.inArray(e.target.type, ['image', 'submit'])) {
				return;
			}

			var targetForm = $('#' + $.attr(e.target, 'form'));

			if (!targetForm.length) {
				return;
			}

			var clone = $(e.target)
				.clone()
				.removeAttr('form')
				.css({
					position: 'absolute',
					top: -9999,
					left: -9999,
					visibility: 'hidden'
				})
				.on('click', stopPropagation)
				.appendTo(targetForm)
				.trigger('click');

			setTimeout(function(){
				clone.remove();
			}, 9);
		}

		$(document).on('click', 'input[form],button[form]', onButtonClick);
	});
}(window.Modernizr, document));