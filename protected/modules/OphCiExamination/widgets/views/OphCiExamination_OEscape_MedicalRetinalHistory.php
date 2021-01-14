<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var OphCiExamination_Episode_MedicalRetinalHistory $this */
?>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/oescape-plotly.js') ?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/oescape/plotly-MR.js') ?>"></script>
<div id="js-hs-chart-MR" class="highchart-area" data-highcharts-chart="0" dir="ltr" style="min-width: 500px; left: 0px; top: 0px;">
<form id="mr-history-form" action="#OphCiExamination_Episode_MedicalRetinalHistory">
    <input name="subspecialty_id" value=<?= $this->subspecialty->id ?> type="hidden">
    <input name="patient_id" value=<?= $this->patient->id ?> type="hidden">
    <?= CHtml::dropDownList(
        'mr_history_va_unit_id',
        $va_unit->id,
        CHtml::listData(
            OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::
            model()->active()->findAllByAttributes(array('is_near' => 0)),
            'id',
            'name'
        )
    ) ?>
</form>

    <div id="plotly-MR-right" class="plotly-MR plotly-right plotly-section" data-eye-side="right"></div>
    <div id="plotly-MR-left" class="plotly-MR plotly-left plotly-section" data-eye-side="left"
         style="display: none;"></div>
</div>
<div class="oes-data-row-input">
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //right side image
        var doc_list = <?= CJavaScript::encode($this->getDocument()); ?>;
        //left side plots
        $('#mr_history_va_unit_id').change(function () {
            this.form.submit();
        });

        var va_ticks = <?= CJavaScript::encode($this->getVaTicks()); ?>;
        OEScape.full_va_ticks = va_ticks;
        var injections_data = <?= CJavaScript::encode($this->getInjectionsList()); ?>;

        var VA_lines_data = <?= CJavaScript::encode($this->getLossLetterMoreThan5()); ?>;
        var opnote_marking = <?= CJavaScript::encode($this->getOpnoteEvent()); ?>;
        var laser_marking = <?= CJavaScript::encode($this->getLaserEvent()); ?>;

        var sides = ['left', 'right'];


        //plotly
        var va_plotly = <?= CJavaScript::encode($this->getPlotlyVaData()); ?>;

        var crt_plotly = <?= CJavaScript::encode($this->getPlotlyCRTData()); ?>;
        var va_plotly_ticks = pruneYTicks(va_ticks, 1000, 30);
        const flag_height = 5;
        const flag_width = 5;
        const flag_height_perc = 0.8;
        const oneday_time = 86400000;

        function getVATrace(side) {
            let colour = {
                'right': '#9fec6d',
                'left': '#fe6767',
                'beo': '#e8b131'}[side];

            return {
                name: 'VA(' + side + ')',
                x: va_plotly[side]['x'],
                y: va_plotly[side]['y'],
                line: {
                    color: colour,
                },
                hovertext: va_plotly[side]['x'].map(function (item, index) {
                    var d = new Date(item);
                    return OEScape.toolTipFormatters_plotly.VA(d, va_plotly[side]['y'][index], 'VA(' + side + ')');
                }),
                text: side,
                hoverinfo: 'text',
                hoverlabel: trace_hoverlabel,
                yaxis: 'y',
                type: 'scatter',
                mode: 'lines+markers',
                marker: {
                    symbol: 'circle',
                    size: 10,
                },
            };
        }

        let beoTrace = getVATrace('beo');

        for (var side of sides) {
            var layout_MR = JSON.parse(JSON.stringify(layout_plotly));
            layout_MR['shapes'] = [];
            layout_MR['annotations'] = [];
            va_yaxis['tickvals'] = va_plotly_ticks['tick_position'];
            va_yaxis['ticktext'] = va_plotly_ticks['tick_labels'];
            layout_MR['xaxis']['rangeslider'] = {};

            setMarkingEvents_plotly(layout_MR, marker_line_plotly_options, marking_annotations, opnote_marking, side, -10, 150);
            setMarkingEvents_plotly(layout_MR, marker_line_plotly_options, marking_annotations, laser_marking, side, -10, 150);

            //Set trace for VA
            var trace1 = getVATrace(side);

            //Set trace for CRT
            var trace2 = {
                name: 'CRT(' + side + ')',
                x: crt_plotly[side]['x'],
                y: crt_plotly[side]['y'],
                line: {
                    color: (side == 'right') ? '#9fec6d' : '#fe6767',
                    dash: 'dot',
                },
                hovertext: crt_plotly[side]['x'].map(function (item, index) {
                    var d = new Date(item);
                    return OEScape.epochToDateStr(d) + '<br>CRT(' + side + '):' + crt_plotly[side]['y'][index];
                }),
                hoverinfo: 'text',
                hoverlabel: trace_hoverlabel,
                yaxis: 'y2',
                mode: 'lines+markers',
                marker: {
                    symbol: 'circle',
                    size: 10,
                },
            };


            if (!crt_plotly[side]['y'].length) {
                crt_yaxis['range'] = [250, 600];
                crt_yaxis['tick0'] = 250;
            } else {
                crt_yaxis['range'] = [Math.min(crt_plotly[side]['y']) - 10, Math.max(crt_plotly[side]['y']) + 20];
                crt_yaxis['tick0'] = Math.min(crt_plotly[side]['y']);
            }
            crt_yaxis['dtick'] = Math.round((crt_yaxis['range'][1] - crt_yaxis['range'][0]) / 10);
            crt_yaxis['overlaying'] = 'y';


            var j = Object.keys(injections_data[side]).length + 2;
            flags_yaxis['range'] = [0, flag_height * j];
            flags_yaxis['domain'] = [0, 0.04 * j];
            flags_yaxis['ticktext'] = [];
            flags_yaxis['tickvals'] = [];


            va_yaxis['domain'] = [0.04 * j + 0.1, 1];
            crt_yaxis['domain'] = [0.04 * j + 0.1, 1];
            var text = {
                showlegend: false,
                x: [],
                y: [],
                hovertext: [],
                hoverinfo: 'text',
                hoverlabel: trace_hoverlabel,
                yaxis: 'y3',
                mode: 'text',
            };

            //set flags for oct fly
            flags_yaxis['ticktext'].push('OCT');
            flags_yaxis['tickvals'].push(flag_height * (j - flag_height_perc) + 2);
            for (var i in doc_list[side]) {
                text['x'].push(new Date(doc_list[side][i]['date']));
                text['y'].push(flag_height * (j - flag_height_perc));
                text['hovertext'].push('OCT<br>ID: ' + doc_list[side][i]['doc_id'] + '<br>Side: ' + side);

                var line_shape = {
                    x0: new Date(doc_list[side][i]['date']),
                    y0: flag_height * j,
                    x1: new Date(doc_list[side][i]['date'] + oneday_time * flag_width),
                    y1: flag_height * (j - flag_height_perc),
                    color: (side == 'right') ? '#9fec6d' : '#fe6767',
                    yaxis: 'y3',
                };
                layout_MR['shapes'].push(setMRFlags_options(line_shape));
            }
            j--;

            //Set the flags for injections
            for (var key in injections_data[side]) {
                flags_yaxis['ticktext'].push(key);
                flags_yaxis['tickvals'].push(flag_height * (j - flag_height_perc) + 2);

                var count = 1;
                for (var i in injections_data[side][key]) {
                    if (i == 0) {
                        text['hovertext'].push(key + '<br>Count: ' + count);
                    } else {
                        var gap = Math.round((injections_data[side][key][i]['x'] - injections_data[side][key][i - 1]['x']) / oneday_time);
                        text['hovertext'].push(key + '<br>Count: ' + count + '<br>Previous injection: ' + gap + ' days ago');
                    }
                    text['x'].push(new Date(injections_data[side][key][i]['x']));
                    text['y'].push(flag_height * (j - flag_height_perc));
                    count++;

                    var inj_shape = {
                        x0: new Date(injections_data[side][key][i]['x']),
                        y0: flag_height * j,
                        x1: new Date(injections_data[side][key][i]['x'] + oneday_time * flag_width),
                        y1: flag_height * (j - flag_height_perc),
                        color: (side == 'right') ? '#9fec6d' : '#fe6767',
                        yaxis: 'y3',
                    };
                    layout_MR['shapes'].push(setMRFlags_options(inj_shape));
                }
                j--;
            }

            //set the flags for letters >5
            flags_yaxis['ticktext'].push('>5 letters lost');
            flags_yaxis['tickvals'].push(flag_height * (j - flag_height_perc) + 2);
            count = 1;
            for (var i in VA_lines_data[side]) {
                text['x'].push(new Date(VA_lines_data[side][i]['x']));
                text['y'].push(flag_height * (j - flag_height_perc));
                text['hovertext'].push('>5' + '<br>Count: ' + count);
                count++;

                var line_shape = {
                    x0: new Date(VA_lines_data[side][i]['x']),
                    y0: flag_height * j,
                    x1: new Date(VA_lines_data[side][i]['x'] + oneday_time * flag_width),
                    y1: flag_height * (j - flag_height_perc),
                    color: (side == 'right') ? '#9fec6d' : '#fe6767',
                    yaxis: 'y3',
                };
                layout_MR['shapes'].push(setMRFlags_options(line_shape));
            }


            layout_MR['yaxis3'] = setYAxis_MR(flags_yaxis);
            layout_MR['yaxis2'] = setYAxis_MR(crt_yaxis);
            layout_MR['yaxis'] = setYAxis_MR(va_yaxis);

            var data = [trace1, trace2, text, beoTrace];

            Plotly.newPlot(
                'plotly-MR-' + side, data, layout_MR, options_plotly
            );

            //Set the right image stack and mouse hover events
            octImgStack = [];
            octImgStack['right'] = new initStack($('#oct-stack'), 'oct_img_', doc_list['right'].length ? doc_list['right'][0]['doc_id'] : null);
            octImgStack['left'] = new initStack($('#oct-stack'), 'oct_img_', doc_list['left'].length ? doc_list['left'][0]['doc_id'] : null);

            var currentPlot = document.getElementById('plotly-MR-' + side);

            for (var i = Object.keys(injections_data[side]).length + 2; i > 0; i--) {
                var inj_background = {
                    x0: currentPlot.layout.xaxis.range[0],
                    y0: flag_height * i,
                    x1: currentPlot.layout.xaxis.range[1],
                    y1: flag_height * (i - flag_height_perc),
                    layer: 'below',
                    color: '#242e3a',
                    yaxis: 'y3',
                };
                currentPlot.layout.shapes.push(setMRFlags_options(inj_background));
            }

            layout_MR['shapes'].push(setMRFlags_options(inj_background));
            currentPlot.on('plotly_hover', function (data) {
                for (var i = 0; i < data.points.length; i++) {
                    var tn = data.points[i].curveNumber;
                    if (tn === 2) {
                        var data_info = data.points[i].hovertext.split('<br>');
                        if (data_info[0] === 'OCT') {
                            var side = data_info[2].substring(6);
                            var id = data_info[1].substring(4);
                            octImgStack[side].setImg(id, side); // link chart points to OCTs
                        }
                    }
                }
            });


            //resize the injection bars after xaxis rangeslider changed
            document.body.onmouseup = function (e) {
                var chart_MR = $('.rangeslider-container').first().parents('.plotly-MR')[0];

                var date_range = (new Date(chart_MR.layout.xaxis.range[1]).getTime() - new Date(chart_MR.layout.xaxis.range[0]).getTime()) / oneday_time;
                var shapes = chart_MR.layout.shapes;
                var new_width = oneday_time * flag_width / 600 * date_range;
                for (var i in shapes) {
                    if (shapes[i].layer !== "below") {
                        shapes[i].x1 = new Date(shapes[i].x0).getTime() + new_width;
                    }
                }

                Plotly.redraw(chart_MR);
            }
        }

        document.body.onmousemove = function (data) {
            var side = $('#js-hs-chart-MR').find('.plotly-MR:visible')[0].dataset['eyeSide'];
            var current_img = $('#oct_stack_'+side).find('.oct-img:visible');
            var chart_MR = $('#plotly-MR-'+side)[0];
            var start_time = new Date(chart_MR.layout.xaxis.range[0]).getTime();
            var end_time = new Date(chart_MR.layout.xaxis.range[1]).getTime();
            var date_range = end_time - start_time;
            var rangeslider = $('.plotly-' + side + ' .rangeslider-container')[0];
            var rangesliderLeftX = rangeslider.getBoundingClientRect().left + window.scrollX;
            var rangesliderRightX = rangeslider.getBoundingClientRect().right + window.scrollX;
            var current_time = (data.clientX - rangesliderLeftX) / (rangesliderRightX - rangesliderLeftX) * date_range + start_time;
            if (doc_list[side].length > 0){
                var closest_img = doc_list[side][0]['id'];
                var closest_time = Math.abs(doc_list[side][0]['date'] - current_time);
                for (var i = 0; i < doc_list[side].length; i++) {
                    var img_date = doc_list[side][i]['date'];
                    if (Math.abs(current_time - img_date) <= closest_time) {
                        closest_time = Math.abs(current_time - img_date);
                        closest_img = doc_list[side][i]['doc_id'];
                    }
                }
                current_img.hide();
                $('#oct_img_'+side+'_'+closest_img).show();
                current_img = $('#oct_img_'+side+'_'+closest_img);
            }
        };

    });
</script>
