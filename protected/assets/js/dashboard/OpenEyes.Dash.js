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
        if (id.includes('&')){
            id = id.substring(0, id.indexOf('&'));
        }        Dash.$container.append($(Dash.itemWrapper.replace('{$id}', id).replace('{$size}', size)));
        container = '#' + id;
        return container;
    }


    /**
     * Loads a piece of HTML in to a dash wrapper.
     *
     * @param report
     * @param wrapper
     */
    function loadBespokeHtml(report, wrapper) {
        $.ajax({
                url: report,
                dataType: 'html',
                success: function (data, textStatus, jqXHR) {
                    $(wrapper).html(data);
                    Dash.upgradeMaterial();
                    Dash.selectCheckList(wrapper);
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

                    if(typeof Dash.postUpdate[chartId] === 'function'){
                        Dash.postUpdate[chartId](data);
                    }

                    $searchForm.parent('.report-search').animate({
                        height: '0'
                    }, 300, function(){
                        $(this).addClass('visuallyhidden');
                    });
                }
            });
        });
    };

    /**
     * Upgrade elements from ajax with material design javascript
     */
    Dash.upgradeMaterial = function()
    {
        var mdlUpgrades = {
            MaterialRadio: '.mdl-radio',
            MaterialCheckbox: '.mdl-checkbox'
        };
        for(var upgrade in mdlUpgrades){
            if(!mdlUpgrades.hasOwnProperty(upgrade)){
                continue;
            }
            if(typeof componentHandler !== "undefined"){
                var elements = document.querySelectorAll(mdlUpgrades[upgrade]);
                for(var i = 0; i < elements.length; i++){
                    componentHandler.upgradeElement(elements[i], upgrade);
                }
            }
        }
    };

    Dash.selectCheckList = function(wrapper)
    {

        $(wrapper).find('.checkbox-select').each(function(){
            var $checkboxes;

            $checkboxes = $(this).find(':input[type="checkbox"]');
            $checkboxes.on('change', function(){
                if(this.value == 'all' && this.checked){
                    $checkboxes.filter(':input[value!="all"]').removeAttr('checked').parents('label').removeClass('is-checked');
                } else {
                    $checkboxes.filter(':input[value="all"]').removeAttr('checked').parents('label').removeClass('is-checked');
                }
            })
        });
    };

    Dash.addBespokeReport = function(report, dependency, size)
    {
        var wrapper;

        wrapper = appendDashWrapper(report, size);
        Dash.loadBespokeReport(report, dependency, wrapper)
    };

    Dash.updateBespokeReport = function(report, wrapper)
    {
        loadBespokeHtml(report, wrapper);
    };

    Dash.loadBespokeReport = function(report, dependency, wrapper)
    {
        if(dependency){
            $.getScript(dependency, function(){
                loadBespokeHtml(report, wrapper);
            });
        } else {
            loadBespokeHtml(report, wrapper);
        }
    };

    Dash.postUpdate = {
        'PcrRiskReport': function(data){
          var newTitle = '';
          if($('#pcr-risk-mode').val() == 0){
            newTitle = 'PCR Rate (risk adjusted)';
          }else if($('#pcr-risk-mode').val() == 1){
            newTitle = 'PCR Rate (risk unadjusted)';
          }else if($('#pcr-risk-mode').val() == 2){
            newTitle = 'PCR Rate (risk adjusted & unadjusted)';
          }
            var chart = $('#PcrRiskReport')[0];
            var surgon_data = [[],[]];
            var totaleyes = 0;
            for (var i =0; i<(chart.data[0]['x']).length; i++){
                totaleyes += chart.data[0]['x'][i];
            }
            data.forEach(function (item) {
                if (item['color'] == 'red'){
                    surgon_data[1].push(item);
                }else{
                    surgon_data[0].push(item);
                }
            });
            for (var i = 0; i < surgon_data.length; i++) {
                chart.data[i]['x'] = surgon_data[i].map(function (item) {
                    return item['x'];
                });
                chart.data[i]['y'] = surgon_data[i].map(function (item) {
                    return item['y'];
                });
                chart.data[i]['hovertext'] = surgon_data[i].map(function (item){
                    return '<b>'+newTitle+'</b><br><i>Operations:</i>' + item['x'] + '<br><i>PCR Avg:</i>' + item['y'].toFixed(2) + item['surgeon'] ;
                });
                chart.data[i]['marker']['color'] = surgon_data[i].map(function (item) {
                    return item['color'];
                });
            }
            chart.layout['title'] = newTitle + '<br><sub>Total Operations: '+totaleyes+'</sub>';

            Plotly.redraw(chart);
        },
        'OEModule_OphCiExamination_components_RefractiveOutcomeReport': function(data){
            var total = 0,
                plusOrMinusOne = 0,
                plusOrMinusHalf = 0,
                plusOrMinusOnePercent = 0,
                plusOrMinusHalfPercent = 0,
                chart = $('#OEModule_OphCiExamination_components_RefractiveOutcomeReport')[0];

            for(var i = 0; i < data.length; i++){
                total += parseInt(data[i][1], 10);                              
                
                // 18 and 22 are the indexes of the -1 and +1 columns
                if(data[i][0] < 18 || data[i][0] > 22){
                    plusOrMinusOne += parseFloat(data[i][1], 10);
                }
                
                // 19 and 21 are the indexes of the -0.5 and +0.5 columns
                if(data[i][0] < 19 || data[i][0] > 21){
                    plusOrMinusHalf += parseFloat(data[i][1], 10);
                }
            }
            
            plusOrMinusHalfPercent = plusOrMinusOne > 0 ? ( (plusOrMinusOne / total) * 100 ) : 0;
            plusOrMinusOnePercent = plusOrMinusHalf > 0 ? ( (plusOrMinusHalf / total) * 100 ) : 0;
            chart.layout['title'] = 'Refractive Outcome: mean sphere (D)<br>' +
              '<sub>Total eyes: ' + total +
              ', ±0.5D: ' + plusOrMinusOnePercent.toFixed(1) + '%, ±1D: '+ plusOrMinusHalfPercent.toFixed(1)+'%</sub>';
            chart.data[0]['x'] = data.map(function (item) {
              return item[0];
            });
            chart.data[0]['y'] = data.map(function (item) {
              return item[1];
            });
            chart.data[0]['customdata'] = data.map(function (item) {
                return item[2];
            });
            chart.layout['yaxis']['range']=Math.max(...chart.data[0]['y']);
            chart.data[0]['hovertext'] = data.map(function (item) {
              return '<b>Refractive Outcome</b><br><i>Diff Post</i>: ' +
                chart.layout['xaxis']['ticktext'][item[0]] +
                '<br><i>Num Eyes:</i> '+ item[1];
            });
            Plotly.redraw(chart);
        },
        'CataractComplicationsReport': function(data){
          var chart = $('#CataractComplicationsReport')[0];
          chart.data[0]['x'] = data.map(function (item) {
            if (item['total']) {
              return item['total'];
            } else {
              return 0;
            }
          });

          chart.data[0]['customdata'] = data.map(function (item) {
              return item['event_list']? item['event_list']:0;
          });
          chart.data[0]['hovertext'] = data.map((item, index) => {
            if (item['total']){
              return '<b>Cataract Complications</b><br><i>Complication</i>: ' +
                chart.layout['yaxis']['ticktext'][index] +
                '<br><i>Percentage:</i> '+ item['y'].toFixed(2) +
                '%<br>Total Operations: '+ item['total'];
            } else {
              return '';
            }
          });

          var max_complications = 0;
          for (var i =0; i<chart.data[0]['x'].length;i++){
              var current_complication =  parseInt(chart.data[0]['x'][i]);
              if (current_complication > max_complications){
                  max_complications = current_complication;
              }
          }

          chart.layout['xaxis']['range'] = max_complications;



          $.ajax({
                data: $('#search-form').serialize(),
                url: "/OphTrOperationnote/report/cataractComplicationTotal",
                success: function (data, textStatus, jqXHR) {
                    chart.layout['title'] =  'Complication Profile<br>' +
                      '<sub>Total Complications: '+ data[0] +
                      ' Total Operations: ' + data[1] + '</sub>';
                  Plotly.redraw(chart);
                }
            });

          Plotly.redraw(chart);

        },
        'OEModule_OphCiExamination_components_VisualOutcomeReport':function(data){
          var chart = $('#OEModule_OphCiExamination_components_VisualOutcomeReport')[0];
            var months = $('#visual-acuity-months').val();
            var type = $('input[name="type"]:checked').val();
            var type_text = type.charAt(0).toUpperCase() + type.slice(1);
             
            var length = data.length;
            var total = 0;
             
            if( length ){
                for(var i = 0; i < length; i++){
                    total += data[i][2];
                }
            }
            chart.layout['title'] = 'Visual Acuity ('+ type_text +')<br><sub>Total Eyes: ' + total +'</sub>';
            chart.layout['yaxis']['title'] = 'Visual acuity '+months+' months'+ (months > 1 ? 's' : '') +' after surgery (LogMAR)';

            chart.data[1]['x'] = data.map(function (item){
              return item[0];
            });
            chart.data[1]['y'] = data.map(function (item){
              return item[1];
            });
            chart.data[1]['text'] = data.map(function (item){
              return item[2];
            });
            chart.data[1]['customdata'] = data.map(function (item){
                return item[3];
            });
            chart.data[1]['hovertext'] = data.map(function (item){
              return '<b>Visual Outcome</b><br>Number of eyes: ' + item[2];
            });
            chart.data[1]['marker']['size']=data.map(function (item){
                return item[2];
            });

            Plotly.redraw(chart);
        },
        'NodAuditReport': function(data){
            var chart = $('#NodAuditReport')[0];
            var completedData = [
                data['VA']['pre-complete'],
                data['VA']['post-complete'],
                data['RF']['pre-complete'],
                data['RF']['post-complete'],
                data['BM']['pre-complete'],
                data['PRE-EXAM']['pre-complete'],
                data['PCR_RISK']['known'],
                data['COMPLICATION']['post-complete'],
                data['INDICATION_FOR_SURGERY']['complete'],
                data['E/I']['eligible'],
            ];
            var incompletedData = [
                data['VA']['pre-incomplete'],
                data['VA']['post-incomplete'],
                data['RF']['pre-incomplete'],
                data['RF']['post-incomplete'],
                data['BM']['pre-incomplete'],
                data['PRE-EXAM']['pre-incomplete'],
                data['PCR_RISK']['not_known'],
                data['COMPLICATION']['post-incomplete'],
                data['INDICATION_FOR_SURGERY']['incomplete'],
                data['E/I']['ineligible'],
            ];
            chart.data[0]['y'] = completedData.map(function (item) {
                return item.length/data['total'];
            });
            chart.data[1]['y'] = incompletedData.map(function (item) {
                return item.length/data['total'];
            });
            chart.data[0]['customdata'] = completedData.map(function (item) {
                return item;
            });
            chart.data[1]['customdata'] = incompletedData.map(function (item) {
                return item;
            });
            Plotly.redraw(chart);
        }
    };

    exports.Dash = Dash;
}(this.OpenEyes));