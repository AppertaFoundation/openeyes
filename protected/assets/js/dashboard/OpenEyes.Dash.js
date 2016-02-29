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

(function(exports) {
    /**
     * OpenEyes Dash namespace
     * @namespace OpenEyes.Dash
     * @memberOf OpenEyes
     */
    var Dash = {
        reports: {},
        itemWrapper: '<div id="{$id}" class="mdl-cell mdl-cell--{$size}-col"><div class="mdl-spinner mdl-js-spinner is-active"></div></div>'
    };

    /**
     * appends a wrapper to the grid for a dash item
     *
     * @param report
     * @param size
     * @returns {string}
     */
    function appendDashWrapper(report, size) {
        var container,
            id;

        if(size == undefined){
            size = 6;
        }

        id = report.replace(new RegExp('[\W/:?=\\\\]', 'g'), '_');
        Dash.$container.append($(Dash.itemWrapper.replace('{$id}', id).replace('{$size}', size)));
        container = '#' + id;
        return container;
    }


    /**
     * Loads a piece of HTML in to a dash container.
     *
     * @param report
     * @param container
     */
    function loadBespokeHtml(report, container) {
        $.ajax({
                url: report,
                dataType: 'html',
                success: function (data, textStatus, jqXHR) {
                    $(container).html(data);
                }
            }
        );
    }

    /**
     * Inits the Dash.
     *
     * @param container
     */
    Dash.init = function(container)
    {
        var $dateInputs = $('#from-date, #to-date');
        Dash.$container = $(container);

        Dash.$container.on('click', '.search-icon', function(){
            $(this).parent('.report-container').find('.report-search').removeClass('visuallyhidden').animate({
                height: '100%'
            }, 300);
        });


        $dateInputs.on('focus', function(){
            $(this).parent().addClass('is-dirty');
        });
        $dateInputs.datepicker({
            prevText: "<i class='material-icons'>chevron_left</i>",
            nextText: "<i class='material-icons'>chevron_right</i>",
            dateFormat: 'd M yy',
            onClose: function(date, inst) {
                if(!date){
                    $(inst.input).parent().removeClass('is-dirty');
                }
            }
        });

        $('#search-form').on('submit', function(e){
            e.preventDefault();

            $('.report-search-form').trigger('submit');

        });

        Dash.$container.on('submit', '.report-search-form', function(e){
            e.preventDefault();
            var chart,
                $searchForm = $(this),
                chartId = $searchForm.parents('.report-container').find('.chart-container').attr('id');

            $.ajax({
                url: $(this).attr('action'),
                data: $searchForm.serialize() + '&' + $('#search-form').serialize(),
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    chart = window[chartId];
                    chart.series[0].setData(data);

                    $searchForm.parent('.report-search').animate({
                        height: '0'
                    }, 300, function(){
                        $(this).addClass('visuallyhidden');
                    });
                }
            });
        });
    };

    Dash.addBespokeReport = function(report, dependency, size)
    {
        var container;

        container = appendDashWrapper(report, size);
        Dash.loadBespokeReport(report, dependency, container)
    };

    Dash.updateBespokeReport = function(report, container)
    {
        loadBespokeHtml(report, container);
    };

    Dash.loadBespokeReport = function(report, dependency, container)
    {
        if(dependency){
            $.getScript(dependency, function(){
                loadBespokeHtml(report, container);
            });
        } else {
            loadBespokeHtml(report, container);
        }
    };

    exports.Dash = Dash;
}(this.OpenEyes));