<?php
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
?>
<link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.css')?>">
<link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('css/dashboard.css')?>">
<script src="<?= Yii::app()->assetManager->createUrl('components/material-design-lite/material.min.js')?>"></script>
<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<div id="<?=$report->graphId();?>_container" class="report-container">
    <?php if(method_exists($report, 'renderSearch')):?>
        <i class="mdl-color-text--blue-grey-400 material-icons search-icon" role="presentation">search</i>
        <?= $report->renderSearch(); ?>
    <?php endif;?>
    <div id="<?=$report->graphId();?>"></div>
</div>
<script>

    $(document).ready(function() {
        $('.search-icon').on('click', function(){
            $(this).parent('.report-container').find('.report-search').removeClass('visuallyhidden').animate({
                height: '100%'
            }, 300);
        });

    });

    $('#<?=$report->graphId();?>').highcharts({
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
            data: <?= $report->dataSetJson();?>
        }]
    });
</script>