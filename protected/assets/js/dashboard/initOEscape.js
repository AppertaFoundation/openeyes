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
var lastIndex = 0;
var currentMedY = 70;

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
        xAxis:{
            labels:
            {
                enabled: false
            }
        },
        yAxis: {
            min: 0,
            max: 70,
            labels:
            {
                align: 'left',
                x: 0,
                y:-2
            }
        },
        credits: {
            enabled: false
        },
        navigator: {
            margin: 2,
            height: 20,
            series:{
                lineWidth: 0,
            }
        }
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


    $.ajax({
        url: '/OphCiExamination/OEScapeData/GetMedications/'+patientId,
        type: "GET",
        dataType: "json",
        success: function(data) {
            data.forEach(AddMedication, IOPchart);
        },
        cache: false
    });



    loadAllImages(Highcharts.dateFormat('%Y-%m-%d', new Date().getTime()));
    loadAllVFImages('vfgreyscale');

    // create the Visual Acuity chart
    var VAchart = new Highcharts.StockChart({
        chart:{
            renderTo: 'vachart',
            margin: 20
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
        xAxis:{
            labels:
            {
                enabled: false
            }
        },
        yAxis: {
            reversed: true,
            min: -1,
            max: 1,
            labels:
            {
                enabled: true
            }
        },
        credits: {
            enabled: false
        },
        navigator: {
            margin: 2,
            height: 20,
            series:{
                lineWidth: 0,
            }
        }
    });

    addSeries(VAchart, 2, "VA", "DataSetVA", "#33ccff");
    addSeries(VAchart, 1, "VA", "DataSetVA", "#90D49C");

    // create the Mean Deviation chart
    var MDchart = new Highcharts.StockChart({
        chart:{
            renderTo: 'mdchart',
            margin: 20
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
            text : 'Mean deviation values'
        },
        xAxis:{
            labels:
            {
                enabled: false
            }
        },
        yAxis: {
            min: -15,
            max: 15,
            labels:
            {
                enabled: true,
                align: 'left'
            }
        },
        credits: {
            enabled: false
        },
        navigator: {
            margin: 2,
            height: 20,
            series:{
                lineWidth: 0,
            }
        }
    });

    addSeries(MDchart, 2, 'MD', 'DataSetMD', "#993399");
    addSeries(MDchart, 1, 'MD', 'DataSetMD', "#264d00");

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
            var legindex = 0;
            if(side==1){
                legindex = 1;
            }
            chart.addSeries({
                name: title+" "+getSideName(side),
                data: data,
                color: seriescol,
                legendIndex: legindex
            });
        },
        cache: false
    });
}

function loadAllImages(eventDate){
    loadImage(eventDate, 1, 'vfgreyscale');
    loadImage(eventDate, 2, 'vfgreyscale');
}

function loadImage(eventDate, side, mediaType){
    //console.log('Loading image for patient: '+patientId+' date: '+eventDate);
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
    //console.log(item);

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
            align: 'left',
            rotation: 0
        }
    });
}

function AddMedication(item, index){
    var toValue, color;
    if(item[1] == 0 || item[1] > this.xAxis[0].max){
        toValue = this.xAxis[0].max;
    }else{
        toValue = item[1];
    }
    if(item[2] == 1){
        color = '#ffd9b3';
    }else{
        color = '#b3ecff';
    }
    this.addSeries({
        type: 'arearange',
        data: [[item[0],currentMedY,currentMedY-5],[toValue,currentMedY,currentMedY-5]],
        color: color,
        id: 'medication-'+index,
        showInLegend: false,
        enableMouseTracking: false,
        dataLabels:{
            enabled: true,
            formatter: function(){
                return item[3];
            },
            inside: true,
            align: 'left',
            padding: 0,
            style: {"color": "contrast", "fontSize": "11px", "fontWeight": "normal", "textShadow": "0 0 6px contrast, 0 0 3px contrast" }
        },
        label: {
            text: item[3],
            enabled: true
        }

    });
    currentMedY = currentMedY-5;
}

function getPlotData(plotNr, side, dateIndex){
    var i = 0;
    var returnArray = [];
    $.each( VFImages, function(index, data){
        if(index <= dateIndex){
            plotArray = $.parseJSON(data[side][1]);
            returnArray[i] = [Math.round(parseInt(index)/1000000), plotArray[plotNr]];
            i++;
        }
    });

    return returnArray
}

function getPlotColour( m, P ){
    m = Math.round(m*100);

    //console.log("m: "+m+" P: "+P);

    if(m > 255){
        m = 255;
    }

    var hsv = RGBtoHSV([0+m,255-m,0]);

    // we change the saturation
    hsv[1] = hsv[1]*Math.abs(P*100);
    var rgb = HSVtoRGB(hsv);

    return 'rgb('+rgb[0]+','+rgb[1]+','+rgb[2]+')';
}

function setPlotColours(side, dateIndex){

    //console.log(getPlotData(0, 1, dateIndex));
    var myRegression;
    for(i=0;i<54;i++) {
        //console.log(getPlotData(i, side, dateIndex));
        //myRegression = regression('linear', getPlotData(i, side, dateIndex));
        plotData = getPlotData(i, side, dateIndex);
        myRegression = linearRegression(plotData);
        myStat = new jStat(plotData);
        testValue = plotData[plotData.length-1][1];
        $('#vfcp_'+getSideName(side)+'_'+i).attr('fill',getPlotColour(myRegression.m, myRegression.pb));
        //console.log(myRegression);
    }
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
            setPlotColours(1,new Date().getTime());
            setPlotColours(2,new Date().getTime());
            //console.log("All VF images data loaded ");
        },
        cache: false
    });
}

function changeVFImages(xCoord, imageWidth){
    var allImagesNr = Object.keys(VFImages).length;
    var currentIndex = Math.round(xCoord/(imageWidth/allImagesNr));

    i = 0;

    $.each( VFImages, function(index, data){
        if( i == currentIndex && currentIndex != lastIndex){
            //console.log($('#vfgreyscale_left').next('img'));
            $('#vfgreyscale_left').html( $('#vfg_left_'+index).clone() );
            $('#vfgreyscale_right').html( $('#vfg_right_'+index).clone() );
            setPlotColours(1,index);
            setPlotColours(2,index);
            lastIndex = currentIndex;
        }
        i++;
    });
    //console.log(xCoord+' imgNr: '+allImagesNr+' width: '+imageWidth+' index: '+currentIndex+' last indx:'+lastIndex);
}

function linearRegression(data){

    var point, ybar=0.0, xbar=0.0;

    var n= data.length;

    for ( var i = 0; i < n ; i++ ) {
        point = data[i];
        ybar = ybar + point[1];
        xbar = xbar + point[0];
    }
    ybar = ybar/(n*1.0);
    xbar = xbar/(n*1.0);

    var bhat = 0.0;
    var ssqx = 0.0;

    for ( var i = 0; i < n; i++ ) {
        point = data[i];
        bhat = bhat + (point[1] - ybar)*(point[0] - xbar);
        ssqx = ssqx + (point[0] - xbar)*(point[0] - xbar);
    }
    bhat = bhat/ssqx;
    var ahat = ybar - bhat*xbar;

    //console.log("n: "+ n);
    //console.log("alpha-hat: "+ ahat);
    //console.log("beta-hat: "+ bhat);

    var sigmahat2 = 0.0;
    var ri = new Array(n);
    for ( var i = 0; i < n; i++ ) {
        point = data[i];
        ri[i] = point[1] - (ahat + bhat*point[0]);
        sigmahat2 = sigmahat2 + ri[i]*ri[i];
    }
    sigmahat2 = sigmahat2 / ( n*1.0 - 2.0 );

    //console.log("sigma-hat square: "+ sigmahat2);

    var seb = Math.sqrt(sigmahat2/ssqx);

    //console.log("se(b): "+ seb);

    var sigmahat = Math.sqrt((seb*seb)*ssqx);
    //console.log("sigma-hat: "+ sigmahat);

    var sea = Math.sqrt(sigmahat*sigmahat * ( 1 /(n*1.0) + xbar*xbar/ssqx));

    //console.log("se(a): "+ sea);

    var Tb = (bhat - 0.0) / seb;

    pvalb = studpval(Tb, n);
    //console.log("pval B "+pvalb);

    var Ta = (ahat - 0.0) / sea;
    pvala = studpval(Ta, n);
    //console.log("pval A "+pvala);

    return{
        m: bhat,
        b: ahat,
        pa: pvala,
        pb: pvalb
    }
}

function statcom ( mq, mi, mj, mb )
{
    zz = 1;
    mz = zz;
    mk = mi;
    while ( mk <= mj ) {
        zz = zz * mq * mk / ( mk - mb);
        mz = mz + zz;
        mk = mk + 2;
    }
    return mz;
}

function studpval ( mt , mn )
{
    PI = 3.1415926535897932384626433832795028841971693993751058209749445923078164062862089986280348253421170679;
    if ( mt < 0 )
        mt = -1*mt;
    mw = mt / Math.sqrt(mn);
    th = Math.atan2(mw, 1);
    if ( mn == 1 )
        return 1.0 - th / (PI/2.0);
    sth = Math.sin(th);
    cth = Math.cos(th);
    if ( mn % 2 == 1 )
        return 1.0 - (th+sth*cth*statcom(cth*cth, 2, mn-3, -1))/(PI/2.0);
    else
        return 1.0 - sth * statcom(cth*cth, 1, mn-3, -1);
}

function RGBtoHSV(color) {
    var r,g,b,h,s,v;
    r= color[0];
    g= color[1];
    b= color[2];
    min = Math.min( r, g, b );
    max = Math.max( r, g, b );


    v = max;
    delta = max - min;
    if( max != 0 )
        s = delta / max;        // s
    else {
        // r = g = b = 0        // s = 0, v is undefined
        s = 0;
        h = -1;
        return [h, s, undefined];
    }
    if( r === max )
        h = ( g - b ) / delta;      // between yellow & magenta
    else if( g === max )
        h = 2 + ( b - r ) / delta;  // between cyan & yellow
    else
        h = 4 + ( r - g ) / delta;  // between magenta & cyan
    h *= 60;                // degrees
    if( h < 0 )
        h += 360;
    if ( isNaN(h) )
        h = 0;
    return [h,s,v];
};

function HSVtoRGB(color) {
    var i;
    var h,s,v,r,g,b;
    h= color[0];
    s= color[1];
    v= color[2];
    if(s === 0 ) {
        // achromatic (grey)
        r = g = b = v;
        return [r,g,b];
    }
    h /= 60;            // sector 0 to 5
    i = Math.floor( h );
    f = h - i;          // factorial part of h
    p = v * ( 1 - s );
    q = v * ( 1 - s * f );
    t = v * ( 1 - s * ( 1 - f ) );
    switch( i ) {
        case 0:
            r = v;
            g = t;
            b = p;
            break;
        case 1:
            r = q;
            g = v;
            b = p;
            break;
        case 2:
            r = p;
            g = v;
            b = t;
            break;
        case 3:
            r = p;
            g = q;
            b = v;
            break;
        case 4:
            r = t;
            g = p;
            b = v;
            break;
        default:        // case 5:
            r = v;
            g = p;
            b = q;
            break;
    }
    return [r,g,b];
}