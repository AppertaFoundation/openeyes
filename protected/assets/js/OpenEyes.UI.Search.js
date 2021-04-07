/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 *  HOW TO USE

 Intit the search by calling the
 OpenEyes.UI.Search.init( $('#patient_merge_search') );
 and pass the input DOM
 Click on one item in the autocomplete list will redirect to the patien's summery page like:  /patient/view/19942

 To override the default functionality

 Set the ajax URL to be called (default: /patient/ajaxSearch ):

 OpenEyes.UI.Search.setSourceURL('/patientMergeRequest/search');

 To override the jquery autocomplete defaults:

 use the OpenEyes.UI.Search.getElement() to get back the input DOM with jquery autocomple

 _renderItem:
 OpenEyes.UI.Search.getElement().data('autocomplete')._renderItem = function (ul, item) {
        return $("<li></li>")
          .data("item.autocomplete", item)
          .append("<a><strong>" + item.first_name + " " + item.last_name + "</strong></a>")
          .appendTo(ul);
    };

 select:
 OpenEyes.UI.Search.getElement().autocomplete('option', 'select', function(event, ui){
        alert(ui.item.id);   
    });

 close:
 OpenEyes.UI.Search.getElement().autocomplete('option', 'close', function(event, ui){
        console.log(event, ui);
    });



 *
 *
 */

(function (exports) {
    /**
     * OpenEyes UI namespace
     * @namespace OpenEyes.UI
     * @memberOf OpenEyes
     */

    var autocompleteSource = '/patient/ajaxSearch';
    var $searchInput;
    var loader = '.loader';

    /**
     * Render an item
     *
     * @param ul
     * @param item
     * @returns {*|jQuery}
     */
    var renderItem = function (ul, item) {
        ul.addClass("oe-autocomplete patient-ajax-list");
        return $("<li></li>")
            .data("item.autocomplete ui-menu-item oe-menu-item", item)
            .append("<a><strong>" + item.first_name + " " + item.last_name + "</strong>" + " (" + item.age + ") " +
                "<span class='icon icon-alert icon-alert-" + item.gender.toLowerCase() + "_trans'>" + item.gender +
                "</span>" + "<div class='nhs-number'>" + item.secondary_identifier_value + "</div><br>" +
                item.primary_patient_identifiers.title + ": " + item.primary_patient_identifiers.value +
                "<br>Date of birth: " + item.dob + "</a>")
            .appendTo(ul);
    };

    /**
     * Init the search
     */
    function initAutocomplete($input) {

        $input.on('keyup', function () {
            if($input.val().trim() === '' ) {
                $input.siblings('.no-result-patients').slideUp();
            }
        });
        $input.autocomplete({
            minLength: 3,
            delay: 700,
            source: function (request, response) {
                $.getJSON(autocompleteSource, {
                    term: request.term,
                    ajax: 'ajax'
                }, response);
            },
            search: function () {
                $(loader).show();
            },
            select: function (event, ui) {
                window.location.href = "/patient/view/" + ui.item.id;
            },
            response: function (event, ui) {
                $(loader).hide();
                if (ui.content.length === 0) {
                    $input.siblings('.no-result-patients').slideDown();
                } else {
                    $input.siblings('.no-result-patients').slideUp();
                }
            }
        });

        if ($input !== 'undefined' && $input.length) {
            $input.data("autocomplete")._renderItem = renderItem;
        }
    }

    exports.Search = {
        init: function ($input) {
            $searchInput = $input;
            initAutocomplete($input);

            return exports.Search;
        },
        setSourceURL: function (url) {
            autocompleteSource = url;
        },
        setRenderItem: function (renderItem) {
            $searchInput.data("autocomplete")._renderItem = renderItem;
        },
        getElement: function () {
            return $searchInput;
        },
        setLoader: function (selector) {
            loader = selector;
        }
    };
}(this.OpenEyes.UI));