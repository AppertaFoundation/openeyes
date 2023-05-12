<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

if (!empty($subspecialty)) { ?>
<style>
    div.plotly-notifier {
        visibility: hidden;
    }
</style>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/initStack.js')?>"></script>
    <?php $this->renderPartial('//base/_messages'); ?>
<div class="oes-left-side" style="width: 50%;">
    <div id="charts-container" class="highchart-area <?= $subspecialty->short_name; ?>">
        <?php $summaryItems = array();
            $summaryItems = OescapeSummaryItem::model()->enabled($subspecialty->id)->findAll();
        if (!$summaryItems) {
            $summaryItems = OescapeSummaryItem::model()->enabled()->findAll();
        } ?>
        <div id='oes-side-indicator' style="height: 25px">
            <div id='oes-zoom-btns' style="display:inline-block;">
                <button class="selected plot-display-label reset-zoom">Reset Zoom Level</button>
                <button class="selected plot-display-label reset-zoom">1m</button>
                <button class="selected plot-display-label reset-zoom">6m</button>
                <button class="selected plot-display-label reset-zoom">1y</button>
                <button class="selected plot-display-label reset-zoom">YTD</button>
            </div>
            <h4 id='oes-side-indicator-left' class='cols-5' style="color:#fe6767;
            text-align: center; font-weight: 500; display:none;">
                Left
            </h4>
            <h4 id='oes-side-indicator-right' class='cols-5' style="color:#9fec6d;
            text-align: center; font-weight: 500; display:inline-block;">
                Right
            </h4>
        </div>
        <?php if (count($summaryItems)) { ?>
            <?php foreach ($summaryItems as $summaryItem) {
                Yii::import("{$summaryItem->event_type->class_name}.widgets.{$summaryItem->getClassName()}");
                $widget = $this->createWidget($summaryItem->getClassName(), array(
                'patient' => $this->patient,
                'subspecialty' => $subspecialty,
                'event_type' => $summaryItem->event_type,
                )); ?>
                <?php $widget->run_oescape(count($summaryItems));
            }
        } ?>
    </div>
</div>
<div class="oes-right-side" style="width: 50%;">
    <?php
    if (isset($widget)) {
        $widget->run_right_side();
    } ?>
</div>
<?php } ?>

<script type="text/javascript">
    // init min and max
    let min_value = new Date();
    let max_value = new Date();

    const chartRangeChangedEvent = new Event('chartRangeChanged');

    $(document).ready(function () {
        $('.js-oes-eyeside[data-side="both"]').click();

        //Set chart zooms to initial value
        resetChartZoom();

        let charts = getOEscapeCharts();
        ['right', 'left'].forEach(function (eye_side) {
            for (let key in charts) {
                $(charts[key][eye_side]).find('.cursor-crosshair, .cursor-ew-resize').css("cursor", 'none');
            }
        });

        function rangesAreEqual(range0, range1) {
            if(range0.length != 2 || range1.length != 2) {
                return false;
            }
            range0_min = range0[0];
            range0_max = range0[1];
            range1_min = range1[0];
            range1_max = range1[1];
            if((range0_min instanceof Date) && (range0_max instanceof Date) &&
               (range1_min instanceof Date) && (range1_max instanceof Date) &&
               (range0_min.getTime() == range1_min.getTime()) &&
               (range0_max.getTime() == range1_max.getTime())) {
                return true;
            }
            return false;
        }

        $('.plotly-right, .plotly-left').on('mouseover', function (e) {
            if ($(this).hasClass('plotly-right') || $(this).hasClass('plotly-left')) {
                let eye_side = $('.js-oes-eyeside.selected').data('side');
                let chart_list = [];
                if (eye_side == 'both') {
                    chart_list = $('.plotly-left, .plotly-right');
                } else {
                    chart_list = $('.plotly-' + eye_side);
                }
                // init locals
                let my_min_value = new Date(chart_list[0]['layout']['xaxis']['range'][0]);
                let my_max_value = new Date(chart_list[0]['layout']['xaxis']['range'][1]);
                //set min max
                for (let i = 0; i < chart_list.length; i++) {
                    //test min
                    if (my_min_value < chart_list[i]['layout']['xaxis']['range'][0])
                        my_min_value = new Date(chart_list[i]['layout']['xaxis']['range'][0]);
                    //test max
                    if (my_max_value > chart_list[i]['layout']['xaxis']['range'][1])
                        my_max_value = new Date(chart_list[i]['layout']['xaxis']['range'][1]);
                }
                // set these ranges to the min and max values
                let current_range = [my_min_value, my_max_value];
                // end
                for (let i = 0; i < chart_list.length; i++) {
                    const previous_range = chart_list[i]['layout']['xaxis']['range'];
                    if(!rangesAreEqual(current_range, previous_range)) {
                        Plotly.relayout(chart_list[i], 'xaxis.range', current_range);
                        chart_list[i].dispatchEvent(chartRangeChangedEvent);
                    }
                }
            };
        });

        $('.rangeslider-container').on('mouseenter mouseover', function (slider) {
            let parent_chart = $(this).parents('.js-plotly-plot')[0];
            let current_range = parent_chart['layout']['xaxis']['range'];

            let chart_list = getOEscapeCharts();

            $.each(['right', 'left'], function (index, eye_side) {
                Object.keys(chart_list).forEach(function (chart_key) {
                    const previous_range = chart_list[chart_key][eye_side]['layout']['xaxis']['range'];
                    if(!rangesAreEqual(current_range, previous_range)) {
                        Plotly.relayout(chart_list[chart_key][eye_side], 'xaxis.range', current_range);
                        chart_list[chart_key][eye_side].dispatchEvent(chartRangeChangedEvent);
                    }
                })
            })
        });

    });

    function getOEscapeCharts() {
        let charts = [];
        //Check if charts exist to avoid operating on undefined elements
        if ($('.plotly-VA').length) {
            charts['VA'] = [];
            charts['VA']['right'] = $('.plotly-VA')[0];
            charts['VA']['left'] = $('.plotly-VA')[1];
        }
        if ($('.plotly-Meds').length) {
            charts['Med'] = [];
            charts['Med']['right'] = $('.plotly-Meds')[0];
            charts['Med']['left'] = $('.plotly-Meds')[1];
        }
        if ($('.plotly-MR').length) {
            charts['MR'] = [];
            charts['MR']['right'] = $('.plotly-MR')[0];
            charts['MR']['left'] = $('.plotly-MR')[1];
        }
        if ($('.plotly-IOP').length) {
            charts['IOP'] = [];
            charts['IOP']['right'] = $('.plotly-IOP')[0];
            charts['IOP']['left'] = $('.plotly-IOP')[1];
        }
        return charts;
    }

    function resetChartZoom(level = "reset") {
        let charts = getOEscapeCharts();

        let limits = {};
        ['right', 'left'].forEach(function (eye_side) {

            limits[eye_side] = {};
            let min = null;
            let max = null;

            //Find the minimum and maximum x values across all charts to determine new chart range
            Object.keys(charts).forEach(function (chart_key) {
                let chartData = charts[chart_key][eye_side]['data'];

                switch (level) {
                    case "1m": // 1 month intentionally flows down
                    case "6m": // 6 month intentionally flows down
                    case "YTD": // YTD intentionally flows down
                    case "1y": // 1 year    
                        // init locals
                        min = new Date(-8640000000000000);
                        max = new Date(8640000000000000);
                        //set min max
                        for (let i in charts) {
                            //test min
                            if (min < charts[i][eye_side]['layout']['xaxis']['range'][0])
                                min = new Date(charts[i][eye_side]['layout']['xaxis']['range'][0]);
                            //test max
                            if (max > charts[i][eye_side]['layout']['xaxis']['range'][1])
                                max = new Date(charts[i][eye_side]['layout']['xaxis']['range'][1]);
                        }
                        break;

                        // Reset Zoom
                    default:
                        for (let i in chartData) {
                            for (let x in chartData[i]['x']) {
                                let value = chartData[i]['x'][x];
                                if (min === null || value < min) {
                                    min = value;
                                } else if (max === null || value > max) {
                                    max = value;
                                }
                            }
                        }
                        break;
                }
            });

            let temp = new Date(max);
            switch (level) {
                case "YTD": //Year to current date
                    temp = new Date();
                    limits[eye_side].max = new Date(temp);
                    temp.setMonth(0);
                    temp.setDate(1);
                    limits[eye_side].min = new Date(temp.setYear(temp.getFullYear()));
                    break;

                case "1y": // 1 year                       
                    limits[eye_side].max = new Date(temp);
                    limits[eye_side].min = new Date(temp.setYear(temp.getFullYear() - 1));
                    break;

                case "1m": // 1 month              
                    limits[eye_side].max = new Date(temp);
                    limits[eye_side].min = new Date(temp.setMonth(temp.getMonth() - 1));
                    break;

                case "6m": // 6 month                      
                    limits[eye_side].max = new Date(temp);
                    limits[eye_side].min = new Date(temp.setMonth(temp.getMonth() - 6));
                    break;

                default: //reset chart
                    // set the min and max to min and max
                    limits[eye_side].min = min;
                    limits[eye_side].max = max;
                    break;
            };
            //For each chart, resize to fit aforementioned range
            if (min !== max){
                for(let key in charts){

                    let updateParams = null;

                    if (key==='IOP'){
                        //set the iop target line
                        let index = charts[key][eye_side].layout.shapes.length-1;
                        if (index>=0 && charts[key][eye_side].layout.shapes[index].y0 == charts[key][eye_side].layout.shapes[index].y1){
                            Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x0', limits[eye_side].min);
                            Plotly.relayout(charts[key][eye_side], 'shapes['+index+'].x1', limits[eye_side].max);
                            Plotly.relayout(charts[key][eye_side], 'annotations['+index+'].x', limits[eye_side].min);
                        }
                    }
                    if(limits[eye_side].min || limits[eye_side].max){
                        updateParams = {
                            'xaxis.range': [limits[eye_side].min, limits[eye_side].max]
                        };
                    }
                    if(updateParams){
                        Plotly.relayout(charts[key][eye_side], updateParams);
                    }
                }
            }


        });
    }

    //get all reset buttons
    var els = document.getElementsByClassName('reset-zoom');

    Array.prototype.forEach.call(els, function (el) {
        // for each reset button
        el.addEventListener('click', function () {
            resetChartZoom(this.textContent);
        })
    });
</script>