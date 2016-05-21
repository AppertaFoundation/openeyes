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

var VFImages;

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
        yAxis: {
            min: 0,
            max: 70
        },
        credits: {
            enabled: false
        },
    });

    addSeries(IOPchart, 2, "IOP", "DataSet", "#33ccff");
    addSeries(IOPchart, 1, "IOP", "DataSet", "#ff9933");

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
    loadAllVFImages('vfgreyscale');

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
            margin: 10,
            y: 24
        },

        title : {
            text : 'Visual Acuity (LogMar single-letter) values'
        },
        yAxis: {
            reversed: true,
            min: -1,
            max: 1
        },
        credits: {
            enabled: false
        },
    });

    addSeries(VAchart, 2, "VA", "DataSetVA", "#33ccff");
    addSeries(VAchart, 1, "VA", "DataSetVA", "#90D49C");

    $('#vfgreyscale_left, #vfgreyscale_right').mousemove(function(e){
        changeVFImages(e.pageX - this.offsetLeft, $(this).width());
    });

});

function addSeries(chart, side, title, dataurl, seriescol){
    $.ajax({
        url: '/OphCiExamination/OEScapeData/'+dataurl+'/'+patientId,
        type: "GET",
        dataType: "json",
        data : {side : side},
        success: function(data) {
            chart.addSeries({
                name: title+" "+getSideName(side),
                data: data,
                color: seriescol
            });
        },
        cache: false
    });
}

function loadAllImages(eventDate){
    loadImage(eventDate, 1, 'vfgreyscale');
    loadImage(eventDate, 2, 'vfgreyscale');
    //loadImage(eventDate, 1, 'vfcolorplot');
    //loadImage(eventDate, 2, 'vfcolorplot');
    setPlotColours(getSideName(1),'');
    setPlotColours(getSideName(2),'');

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

function setPlotColours(side, data){
    $('[id^=vfcp_'+side+']').each(function () {
        $(this).attr('fill', getRandomColor());
    });
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function loadAllVFImages(mediaType){
    $.ajax({
        url: '/OphCiExamination/OEScapeData/LoadAllImages/'+patientId,
        type: "GET",
        dataType: "json",
        data : {
            eventType: 'OphInVisualfields',
            mediaType: mediaType},
        success: function(data) {
            VFImages = data;
            $.each( VFImages, function(index, data){
                $('#vfgreyscale_left_cache').append('<img id="vfg_left_'+index+'" class="vfthumbnail" src="/OphCiExamination/OEScapeData/GetImage/'+data[1][0]+'">');
                $('#vfgreyscale_right_cache').append('<img id="vfg_right_'+index+'" class="vfthumbnail" src="/OphCiExamination/OEScapeData/GetImage/'+data[2][0]+'">');
            });
            console.log("All VF images data loaded ");
        },
        cache: false
    });
}

function changeVFImages(xCoord, imageWidth){
    var allImagesNr = Object.keys(VFImages).length;
    var currentIndex = Math.round(xCoord/(imageWidth/allImagesNr));

    i = 0;
    lastIndex = 0;

    $.each( VFImages, function(index, data){
        if( i == currentIndex && currentIndex != lastIndex){
            //console.log($('#vfgreyscale_left').next('img'));
            $('#vfgreyscale_left').html( $('#vfg_left_'+index).clone() );
            $('#vfgreyscale_right').html( $('#vfg_right_'+index).clone() );
            setPlotColours(getSideName(1),'');
            setPlotColours(getSideName(2),'');
            lastIndex = currentIndex;
        }
        i++;
    });
    console.log(xCoord+' imgNr: '+allImagesNr+' width: '+imageWidth+' index: '+currentIndex+' last indx:'+lastIndex);
}