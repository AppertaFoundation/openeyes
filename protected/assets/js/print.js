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

/*
 * creates an iframe in the current document, and populates with the given url and GET data
 *
 * NOTE: the call to print the iFrame must be part of the document returned by the server. By having
 * this in the $(document).ready() function, we can ensure that all the requisite objects (specifically
 * eyedraw) are loaded before the print is attempted.
 *
 * @param url - url of page to load
 * @param data - associative array of GET values to append to URL
 */
function printIFrameUrl(url, data) {

    $('#print_content_iframe').remove();
    var iframe = $('<iframe></iframe>');
    iframe.attr({
        id: 'print_content_iframe',
        name: 'print_content_iframe',
        style: 'display: none;',
        src: '#'
    });
    $('body').append(iframe);

    var formdata = "YII_CSRF_TOKEN=" + YII_CSRF_TOKEN;
    if(data) {
       formdata += "&" + $.param(data);
    }
    var xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.responseType = "blob";
    xhr.onload = function () {
        if (this.status === 200) {
            const responseContentType = filterResponseContentType(this.getResponseHeader('content-type'));

            if (responseContentType !== ''){
                const blob = new Blob([xhr.response], {type: responseContentType});
                const bloburl = window.URL.createObjectURL(blob);

                $('#print_content_iframe').attr('src', bloburl);
                $("#print_content_iframe").load(function () {
                    window.frames.print_content_iframe.print();
                    // re-enable the buttons
                    enableButtons();
                    window.URL.revokeObjectURL(bloburl);
                });
            }
        }
    };
    xhr.send(formdata);
}

function filterResponseContentType(header) {
    const parametersStartIndex = header.indexOf(';');
    const type = parametersStartIndex === -1 ? header.trim() : header.slice(0, parametersStartIndex).trim();

    return ['application/pdf', 'text/html'].indexOf(type.toLowerCase()) !== -1 ? header : '';
}

function printEvent(printOptions) {
    printIFrameUrl(OE_print_url, printOptions);
}

$(window).load(function () {
    var data = {};
    if (typeof OE_event_last_modified !== "undefined") {
        data['last_modified_date'] = OE_event_last_modified;

        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/eventImage/generateImage/' + OE_event_id,
            'data': $.param(data) + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'success': function (resp) {
                switch (resp.trim()) {
                    case "ok":
                        break;
                    case "outofdate":
                        $.cookie('print', 1);
                        window.location.reload();
                        break;
                    default:
                        alert("Something went wrong trying to (re)generate the event image. Please try again or contact support for assistance.");
                        break;
                }
            }
        });
    }
});

$(document).ready(function () {
    if ($.cookie('print') == 1) {
        disableButtons();

        $.removeCookie('print');
        setTimeout(function () {
            printWhenReady();
        }, 500);
    }

});

function printWhenReady() {
    var ready = true;

    $('canvas.ed-canvas-display').map(function () {
        var drawing = window.ED ? ED.getInstance($(this).data('drawing-name')) : false;

        if (!drawing || !drawing.isReady) {
            ready = false;
        }
    });

    if (ready) {
        printEvent(null);
    } else {
        setTimeout(function () {
            printWhenReady();
        }, 500);
    }
}

/**
 * Chromium 'Ignoring too frequent calls to print().' work around. Is a wrapper
 * around the printContent() function.
 */
if (navigator.userAgent.toLowerCase().indexOf("chrome") > -1) {

    // Wrap private vars in a closure
    (function () {
        var realPrintFunc = window.printContent;
        var interval = 35000; // 35 secs
        var timeout_id = null;
        var nextAvailableTime = +new Date(); // when we can safely print again

        function runPrint(csspath) {
            realPrintFunc(csspath);
            timeout_id = null;
            nextAvailableTime += interval;
        }

        // Overwrite window.printContent function
        window.printContent = function (csspath) {
            var now = +new Date();

            if (now > nextAvailableTime) {
                // if the next available time is in the past, print now
                realPrintFunc(csspath);
                nextAvailableTime = now + interval;
            } else {
                if (timeout_id !== null) {
                    // Skip if setTimeout has already been called (prevents user
                    // from calling print multiple times)
                    console.log('Skipping print as count down already started ' + (nextAvailableTime - now) / 1000 + 's left until next print');
                    alert("New print request has been queued. " + Math.floor((nextAvailableTime - now) / 1000) + "secs until print.");
                    return;
                } else {
                    // print when next available
                    timeout_id = setTimeout(function () {
                        runPrint(csspath);
                    }, nextAvailableTime - now);
                    alert("Print request has been queued. " + Math.floor((nextAvailableTime - now) / 1000) + "secs until print.");
                }
            }
        };
    })();
}
