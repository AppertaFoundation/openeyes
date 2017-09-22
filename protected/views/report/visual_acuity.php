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
?>

<div id="visual-acuity" style="height: 400px; min-width: 310px; max-width: 600px; margin: 0 auto"></div>

<script>
        $('#visual-acuity').highcharts({
            credits: {
                enabled: false
            },
            chart: {
                type: 'scatter',
                zoomType: 'xy'
            },
            legend: {
                enabled: false
            },
            title: {
                text: 'Visual Acuity'
            },
            xAxis: {
                gridLineWidth: 1,
                title: {
                    text: 'At Surgery'
                },
                labels: {
                    format: '{value}'
                }
            },
            yAxis: {
                gridLineWidth: 1,
                title: {
                    text: '4 months after Surgery'
                },
                labels: {
                    format: '{value}'
                }
            },
            plotOptions: {
                scatter: {
                    marker: {
                        radius: 5,
                        states: {
                            hover: {
                                enabled: true,
                                lineColor: 'rgb(100,100,100)'
                            }
                        }
                    },
                    states: {
                        hover: {
                            marker: {
                                enabled: false
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<b>Visual Acuity</b><br>',
                        pointFormat: 'Before Surgery {point.x}, And after {point.y}'
                    }
                }
            },
            series: [{
                data: [
                    [0.1,0], [0.2,0], [0.1,0], [0.9, 0.4], [0.8, 0.2], [1.3, 0.4], [1.1, 0.3], [0.8,0.1], [0.3,0.5],
                    [0.5,0.0], [0.4,0.1],[0.8,0.1],[0.8,0.1],[1.2,0.8],[1.8,0.9],[0.9,0.3], [0.8,0.4],[0.5,0.1],[0.4,0.1]
                ]
            }]
        });
</script>