/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports, Util, EventEmitter) {
    function MedicationBrowser(settings) {

        /**
         * Widget initialization settings
         */

        /* A button that should trigger the popup (jQuery object) */
        var $btn = settings.btn;
        /* The popup body (jQuery object) */
        var $element = settings.element;
        /* A function that is called when an item is selected. An object representing the selected medication will be passed. */
        var callback = settings.onSelected;

        var _delayFn = (function(){
            var itcTimer = 0;
            return function(callback, ms){
                clearTimeout (itcTimer);
                itcTimer = setTimeout(callback, ms);
            };
        })();

        init();

        function init() {

            $element.find(".main-close-btn").on("click", function(){
               $element.hide();
            });

            init_lists();

            $btn.on("click", function (e) {
                e.preventDefault();
                open_dialog();
            });
        }

        function init_lists() {

            var element_id = $element.attr("id");

            /* Click on container element */
            $.each([0, 2], function(i, column_no){
                $(document).on("click", "#"+element_id+" .level[data-level="+column_no+"] .listelement", function(e){
                    var $listelem = $(e.target).closest('li');
                    $listelem.siblings('li').removeClass("selected");
                    $listelem.addClass("selected");
                    var target_col = column_no+1;
                    var $wrapper = $("#"+element_id+" .level[data-level="+target_col+"]");
                    refresh_column($wrapper);
                });
            });

            /* Click on leaf element */
            $.each([1, 3], function(i, column_no){
                $(document).on("click", "#"+element_id+" .level[data-level="+column_no+"] .add-options li", function(e){
                    var $listelem = $(e.target).closest('li');
                    $element.hide();
                    callback($listelem.data("ref_medication"));
                });
            });

            /* Filtering */
            $(document).on("keyup", "#"+element_id+" .column-filter", function(e){
                var $target = $(e.target);
                var $column = $target.closest('.level');
                _delayFn(function(){refresh_column($column, true)}, 500);
            });

            /* Auto-select container if there is only one
            $.each([0, 2], function(i, column_no){
                var $col = $("#"+element_id+" .level[data-level="+column_no+"] ul.add-options");
                var $elements = $col.find('li.listelement');
                if($elements.length === 1) {
                    $elements.trigger("click");
                }
            });
            */

            /* Auto-select first container */
            $.each([0, 2], function(i, column_no){
                var $col = $("#"+element_id+" .level[data-level="+column_no+"] ul.add-options");
                var $elements = $col.find('li.listelement');
                $elements.first().trigger("click");
            });
        }

        function refresh_column($column, keep_filter) {

            if(typeof keep_filter === "undefined") {
                keep_filter = false;
            }

            var term;

            if(!keep_filter) {
                $column.find('.column-filter').val('');
                term = '';
            }
            else {
                term = $column.find('.column-filter').val();
            }

            var this_level = parseInt($column.attr("data-level"));
            var prev_level = this_level - 1;
            var ref_set_id = $element.find(".level[data-level="+prev_level+"]").find('li.selected').attr("data-id");

            if($column.hasClass("fixed")) {

                if(term==='') {
                    $column.find('.listelement').show();
                }
                else {
                    $column.find('.listelement').each(function(i, e){
                        if($(e).text().toLowerCase().indexOf(term.toLowerCase()) === -1) {
                            $(e).hide();
                        }
                    });
                }

                return;
            }

            $column.find('ul.add-options').empty();
            $column.find(".loader").show();

            $.getJSON('/MedicationManagement/findRefMedications?ref_set_id='+ref_set_id + '&term=' + term, function(data){
                populate_column($column, data);
                $column.find(".loader").hide();
            });
        }

        function populate_column($column, elements) {

            var $col = $column.find("ul.add-options");

            $.each(elements, function(i, e){
                $li = $('<li><span style="margin-left: '+(e.tabsize * 20)+'px; ">'+ e.preferred_term+'</span></li>');
                $li.data('ref_medication', e);
                $li.appendTo($col);
            });
        }

        function open_dialog() {
            $element.show();
        }
    }

    Util.inherits(EventEmitter, MedicationBrowser);
    exports.MedicationBrowser = MedicationBrowser;

})(OpenEyes.UI, OpenEyes.Util, OpenEyes.Util.EventEmitter);