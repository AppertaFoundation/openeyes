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
 * Create a global getSVG method that takes an array of charts as an argument
 */
Highcharts.getSVG = function(charts) {
    var svgArr = [],
        top = 60,
        width = 0,
        header = '',
        hasDates = $('#from-date').val() || $('#to-date').val();

    $.each(charts, function(i, chart) {
        var currentWidth = chart.chartWidth;
        chart.setSize(700, chart.chartHeight);
        var svg = chart.getSVG();
        svg = svg.replace('<svg', '<g transform="translate(0,' + top + ')" ');
        svg = svg.replace('</svg>', '</g>');

        top += chart.chartHeight + 50;
        width = Math.max(width, chart.chartWidth);

        svgArr.push(svg);
        chart.setSize(currentWidth, chart.chartHeight);
    });
    width += 50;

    header = '<svg height="60" width="' + width + '" ><text y="20" x="' + (width / 2) + '" text-anchor="middle">'
        + $('.mdl-layout-title').text() + '</text>';

    if(hasDates){
        header += '<text y="40" x="' + (width / 2) + '" text-anchor="middle">';

        if($('#from-date').val()){
            header += 'From: ' +  $('#from-date').val() + ' ';
        } else {
            header += ' From: All time ';
        }

        if($('#to-date').val()){
            header += 'To: ' +  $('#to-date').val();
        } else {
            header += 'To: Present day';
        }

        header += '</text>';
    }
    header += '</svg>';

    return '<svg height="'+ top +'" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + header + svgArr.join('') + '</svg>';
};

/**
 * Create a global exportCharts method that takes an array of charts as an argument,
 * and exporting options as the second argument
 */
Highcharts.exportCharts = function(charts, options) {
    var form,
    svg = Highcharts.getSVG(charts);

    // merge the options
    options = Highcharts.merge(Highcharts.getOptions().exporting, options);

    // create the form
    form = Highcharts.createElement('form', {
        method: 'post',
        action: '/dashboard/printSvg'
    }, {
        display: 'none'
    }, document.body);

    // add the values
    Highcharts.each(['filename', 'type', 'width', 'svg'], function(name) {
        Highcharts.createElement('input', {
            type: 'hidden',
            name: name,
            value: {
                filename: options.filename || 'chart',
                type: options.type,
                width: options.width,
                svg: svg
            }[name]
        }, null, form);
    });

    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "YII_CSRF_TOKEN";
    input.value = YII_CSRF_TOKEN;
    form.appendChild(input);
    //console.log(svg); return;
    // submit
    form.submit();

    // clean up
    form.parentNode.removeChild(form);
};

Highcharts.setOptions({
    lang: {
        noData: "No data found for this report"
    }
});

