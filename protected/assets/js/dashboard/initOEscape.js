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

$(document).ready(function() {
    // Create the IOP chart
    var IOPchart = new Highcharts.StockChart({
        chart:{
            renderTo: 'iopchart',
            events: {
                click: function (e) {
                    //alert(Highcharts.dateFormat('%A, %b %e, %Y', e.xAxis[0].value));
                    loadAllImages(Highcharts.dateFormat('%Y-%m-%d', e.xAxis[0].value));
                }
            }
        },

        rangeSelector : {
            enabled: 1,
            inputEnabled: false,
            selected: 4
        },

        legend: {
            enabled: 1,
            floating: true,
            align: 'right',
            verticalAlign: 'top',
            borderColor: '#dddddd',
            borderWidth: 1,
            layout: 'vertical',
            shadow: true,
            y: 24
        },

        title : {
            text : 'IOP values'
        },
    });


    $.ajax({
        url: '/OphCiExamination/OEScapeData/DataSet/'+patientId,
        type: "GET",
        dataType: "json",
        data : {side : 1},
        success: function(data) {
            IOPchart.addSeries({
                name: "IOP left",
                data: data,
                color: "#ff9933"
            });
        },
        cache: false
    });

    $.ajax({
        url: '/OphCiExamination/OEScapeData/DataSet/'+patientId,
        type: "GET",
        dataType: "json",
        data : {side : 2},
        success: function(data) {
            IOPchart.addSeries({
                name: "IOP right",
                data: data,
                color: "#33ccff"
            });
        },
        cache: false
    });


    $.ajax({
        url: '/OphCiExamination/OEScapeData/GetOperations/'+patientId,
        type: "GET",
        dataType: "json",
        success: function(data) {
            data.forEach(AddOperation, IOPchart);
        },
        cache: false
    });

    loadAllImages(Highcharts.dateFormat('%Y-%m-%d', new Date().getTime()));

    // create the Visual Acuity chart
    var VAchart = new Highcharts.StockChart({
        chart:{
            renderTo: 'vachart',
        },

        rangeSelector : {
            enabled: 1,
            inputEnabled: false,
            selected: 4
        },

        legend: {
            enabled: 1,
            floating: true,
            align: 'right',
            verticalAlign: 'top',
            borderColor: '#dddddd',
            borderWidth: 1,
            layout: 'vertical',
            shadow: true,
            y: 24
        },

        title : {
            text : 'Visual Acuity (LogMar single-letter) values'
        },
    });

    $.ajax({
        url: '/OphCiExamination/OEScapeData/DataSetVA/'+patientId,
        type: "GET",
        dataType: "json",
        data : {side : 1},
        success: function(data) {
            VAchart.addSeries({
                name: "VA left",
                data: data,
                color: "#90D49C"
            });
        },
        cache: false
    });

    $.ajax({
        url: '/OphCiExamination/OEScapeData/DataSetVA/'+patientId,
        type: "GET",
        dataType: "json",
        data : {side : 0},
        success: function(data) {
            VAchart.addSeries({
                name: "VA right",
                data: data,
                color: "#90A6D4"
            });
        },
        cache: false
    });

});

function loadAllImages(eventDate){
    loadImage(eventDate, 1, 'vfgreyscale');
    loadImage(eventDate, 2, 'vfgreyscale');
    loadImage(eventDate, 1, 'vfcolorplot');
    loadImage(eventDate, 2, 'vfcolorplot');

}

function loadImage(eventDate, side, mediaType){
    console.log('Loading image for patient: '+patientId+' date: '+eventDate);
    $.ajax({
        url: '/OphCiExamination/OEScapeData/LoadImage/'+patientId,
        type: "GET",
        dataType: "html",
        data : {eventDate : eventDate,
                side: side,
                eventType: 'OphInVisualfields',
                mediaType: mediaType},
        success: function(data) {
            //console.log("Image loaded "+data);
            //console.log(mediaType+'_'+getSideName(side));
            $('#'+mediaType+'_'+getSideName(side)).html(data);
        },
        cache: false
    });
}

function getSideName(side){
    if(side==1){
        return 'left';
    }else{
        return 'right';
    }
}


function AddOperation(item, index){
    console.log(item);

    var color;

    if(item[2] == 1){
        color = '#ff9933';
    }else{
        color = '#33ccff';
    }
    this.xAxis[0].addPlotLine({
        value: item[0],
        color: color,
        width: 2,
        id: 'plot-line-'+index,
        label: {
            text: item[1],
            align: 'left'
        }
    });
}