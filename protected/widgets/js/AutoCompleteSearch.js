/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
/**
 *  HOW TO USE
 Create the search by adding
 <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
 to the html. Then call
 OpenEyes.UI.AutoCompleteSearch.init({
			input: $('#oe-autocompletesearch'),
			url: URL,
			onSelect: function(){
				let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
				// DESIRED ACTION
			}
		});
 Change URL to suit. To define what happens when the user clicks on a search option, add you code in the onSlect function
 AutoCompleteResponse will return an object of the option clicked.

 To have more than 1 search box add
 <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => NAME]); ?>
 to the html. Then call
 OpenEyes.UI.AutoCompleteSearch.init($('NAME'), URL);
 Set NAME to what ever you want. If used correctly you will have
 <?php $this->widget('application.widgets.AutoCompleteSearch'); ?> and <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => NAME]); ?>
 on the same page along with
 OpenEyes.UI.AutoCompleteSearch.init({input: $('#oe-autocompletesearch'), ...}); and OpenEyes.UI.AutoCompleteSearch.init({input: $('NAME'), ...});

 To create the search without the auto complete add
 <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
 to the html

 To change placeholder
 <?php $this->widget('application.widgets.AutoCompleteSearch', ['htmlOptions' => ['placeholder' => <PLACEHOLDER TEXT>]]); ?>

 */
var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    'use strict';

    var search_term;
    var searching = false;
    var xhr;
    var response;
    var current_focus;
    var inputbox;
    var onSelect = [];
    var timeout_id = null;
    var max_height = null;

    RegExp.escape= function(s) {
        return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    };

    function matchSearchTerm (str) {
        var myRegExp = new RegExp(RegExp.escape(search_term),'ig');
        var matches = str.match(myRegExp);
        if (matches && matches.length > 0) {
            $.each(matches,function(index, match){
                str = str.replace(match, `<span class="autocomplete-match">`+match+`</span>`);
            });
        }

        return str.trim();
    }

    function initAutocomplete(input, autocomplete_url, autocomplete_max_height, params, minimum_character_length, search_data_prefix) {
        function _debounce(func, wait = 300) {
            let timer;
            return function (...args) {
                const later = () => {
                    clearTimeout(timer);
                    func(...args);
                };
                clearTimeout(timer);
                timer = setTimeout(later, wait);
            };
        }

        const debouncedInputHandler = _debounce(function(input_value) {
            search_term = input_value;
            inputbox = input;
            inputbox.parent().find('.alert-box').addClass('hidden');

            let minLength = minimum_character_length;
            // if input is empty
            if (search_term.length < minLength) {
                timeout_id = setTimeout(function () {
                    if (search_term.length === 1) {
                        inputbox.parent().find('.js-min-chars').removeClass('hidden');
                    }
					$('.oe-autocomplete').addClass('hidden');
                    return false;
                }, 1000);
            } else {
                // cancel the current search and start a new one
                if (searching) {
                    xhr.abort();
                }
                searching = true;

                //stop the empty search hidding timeout
                if (timeout_id !== null) {
                    clearTimeout(timeout_id);
                    timeout_id = null;
                }
                let data = {
                    term: search_term,
                    ajax: 'ajax'
                };

                if (typeof params !== "undefined") {
                    Object.keys(params).forEach(function (key) {
                        data[key] = params[key]();
                    });
                }

                xhr = $.getJSON(
                    autocomplete_url,
                    data,
                    function (data, status) {
                        if (status === 'success') {
                            response = data;
                            if (response.length > 0) {
                                successResponse(response);
                            } else {
                                inputbox.parent().find('.js-no-result').removeClass('hidden');
                                inputbox.parent().find('ul').addClass('hidden');
                            }
                            searching = false;
                            current_focus = -1;
                        } else if (status === 'error' || status === 'timeout') {
                            console.warning('Error with AutoCompleteSearch');
                        }
                    });
            }
        });

        input.on('input', function () {
            exports.input = $(this);
            debouncedInputHandler(this.value.trim());
        });

        input.parent().find(".oe-autocomplete").on('click', '.oe-menu-item', function () {
			exports.item_clicked = response[$(this).index()];
            inputbox.val('');
            onSelect[inputbox.selector.replace(/[^A-z]/, '')]();
			$('.oe-autocomplete').addClass('hidden');
        });

        input.keydown(function (e) {
            if (e.keyCode === 40) {
                // if the arrow down key is pressed
                if (current_focus < (response.length - 1)) {
                    current_focus++;
                }
            } else if (e.keyCode === 38) {
                // if the arrow up key is pressed
                if (current_focus !== 0) {
                    current_focus--;
                }
            } else if (e.keyCode === 13) {
                // if the enter key is pressed
                if (current_focus > -1) {
                    $('.oe-menu-item a:eq(' + current_focus + ')').trigger('click');
                }
            }
			max_height = autocomplete_max_height;
            $('.oe-autocomplete a').removeClass('hint');
            $('.oe-autocomplete a:eq(' + current_focus + ')').addClass('hint');
        });

    	$(document).click(function(){ $('.oe-autocomplete').addClass('hidden'); });
    }

    function successResponse(response) {
	    $(".oe-autocomplete").empty();
	    var search_options = ``;
        $.each(response,function(index, value) {
            search_options += `<li class="oe-menu-item" role="presentation" data-test="autocomplete-match"><a id="ui-id-` + index + `" tabindex="-1">`;
            if (value.fullname !== undefined) {
                search_options += matchSearchTerm(value.fullname);
            }

        	let id_string = '';
        	if(value.patient_identifiers) {
				value.patient_identifiers.forEach(function (identifier) {
					id_string += `${identifier.title}: ${identifier.value}<br>`;
				});
			}

			let primary_identifier = '';
			if (typeof value.primary_patient_identifiers !== 'undefined' && value.primary_patient_identifiers.title && value.primary_patient_identifiers.value) {
				primary_identifier = value.primary_patient_identifiers.value;
			}

        	if (value.first_name !== undefined && value.last_name !== undefined) {
        		const name = matchSearchTerm(`${value.first_name} ${value.last_name} `);
        		search_options += name + ` (${value.age}) ${value.gender}<br>${primary_identifier}<br><br>${matchSearchTerm(id_string)}` +
				`<br>Date of birth: `+value.dob;
            }

            if (value.label !== undefined) {
                search_options += matchSearchTerm(value.label);
            }

            if (typeof value === 'string') {
                search_options += matchSearchTerm(value);
            }

            search_options += `</a></li>`;
        });
        var input_box_css = {'position':'absolute', 'top':inputbox.outerHeight()};
        if (max_height != 'null'){
        	input_box_css['overflow-y'] = 'auto';
        	input_box_css['max-height'] = max_height;
		}
        inputbox.parent().find(".oe-autocomplete").append(search_options).css(input_box_css).removeClass('hidden');
    }

    function set_onSelect(input, f) {
        var input_selector = input.selector.replace(/[^A-z]/, '');
        onSelect[input_selector] = f;
    }

    exports.AutoCompleteSearch = {
        init: function (options) {
            if (options.input) {
                set_onSelect(options.input, options.onSelect);
                let minimumCharacterLength = ('minimumCharacterLength' in options )? options.minimumCharacterLength : 2;
	    		initAutocomplete(options.input, options.url, ('maxHeight' in options )? options.maxHeight:null, options.params, minimumCharacterLength);
                return exports.AutoCompleteSearch;
            }
        },
        getResponse: function () {
            return exports.item_clicked;
        },
        getInput: function() {
            return exports.input;
        }
    };

})(OpenEyes.UI);
