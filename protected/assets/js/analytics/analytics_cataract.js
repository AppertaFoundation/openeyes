const analytics_cataract = (function () {
    const ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 100;
    const throttleTime = analytics_toolbox.getThrottleTime() || 1000;
    const dict = {
        '/report/ajaxReport?report=PcrRisk&template=analytics': [
            'PcrRiskReport',
            '#pcr-risk-grid',
            'PCR',
        ],
        '/report/ajaxReport?report=CataractComplications&template=analytics': [
            'CataractComplicationsReport',
            '#cataract-complication-grid',
            'CP',
        ],
        '/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\VisualOutcome&template=analytics': [
            'OEModule_OphCiExamination_components_VisualOutcomeReport',
            '#visual-acuity-grid',
            'VA',
        ],
        '/report/ajaxReport?report=NodAudit&template=analytics': [
            'NodAuditReport',
            '#nod-audit-grid',
            'NOD',
        ],
        '/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics&procedures[]=all': [
            'OEModule_OphCiExamination_components_RefractiveOutcomeReport',
            '#refractive-outcome-grid',
            'RO',
        ],
        '/report/ajaxReport?report=\\OEModule\\OphOuCatprom5\\components\\Catprom5&template=analytics&mode=0&eye=0': [
            'OEModule_OphOuCatprom5_components_Catprom5Report',
            '#catprom5-grid',
            'CP5',
        ]
    };

    let currentPlot = null;

    // to config the colors for the plots
    // config: the config object from the beginning of reportPlotToPDF function
    // or the analytics_layout from analytics_plotly.js
    function configPlotPDF(plot, config) {
        // in case the plot is not passed in
        if (plot) {
            plot.layout.paper_bgcolor = config.paper_bgcolor;
            plot.layout.plot_bgcolor = config.plot_bgcolor;
            plot.layout.font.color = config.font ? config.font.color : 'white';
            plot.layout.yaxis.linecolor = config.yaxis.linecolor;
            plot.layout.xaxis.linecolor = config.xaxis.linecolor;
        }
    }

    function pageStampDetails(doc, date, surgeon_name) {
        doc.setFontSize(8);
        doc.text(15, 10, 'Surgeon Name: ' + surgeon_name);
        doc.text(15, 20, 'Date: ' + date);
    }

    // the callback for download pdf click event
    const reportPlotToPDF = function (event_date, current_user) {
        const eventFromDate = analytics_toolbox.processDate(new Date(event_date['date_from']));
        const eventToDate = analytics_toolbox.processDate(new Date(event_date['date_to']));
        const $datepicker_from = $('#analytics_datepicker_from');
        const $datepicker_to = $('#analytics_datepicker_to');
        const from_date = $datepicker_from.val() ? $datepicker_from.val() : eventFromDate;
        const to_date = $datepicker_to.val() ? $datepicker_to.val() : eventToDate;
        // make sure the entry is logical
        if (new Date(from_date) > new Date(to_date)) {
            alert('From date cannot be later than To date');
            return;
        }
        $('#js-analytics-spinner').show();
        const surgeon_name = current_user['name'];
        // grab dates
        // if from, to not filled, the max / min date from event data will be filled in
        let date = "";
        date = format_date(new Date(from_date)) + " to " + format_date(new Date(to_date));
        // prevent click during downloading
        if ($(this).text() === 'Downloading...') {
            return false;
        }

        // for better user experience to let them know it is downloading
        const originalText = $(this).text();
        $(this).text('Downloading...');

        // plot config
        const config = {
            // transparent plot bg color for not blocking texts
            paper_bgcolor: 'rgba(0, 0, 0, 0)',
            plot_bgcolor: 'rgba(0, 0, 0, 0)',
            font: {
                color: 'black',
            },
            yaxis: {
                linecolor: 'black',
            },
            xaxis: {
                linecolor: 'black',
            }
        };

        // instantiate jsPDF
        const doc = new jspdf.jsPDF({
            orientation: "landscape",
            unit: "pt",
            format: "a4",
            compress: true
        });
        // get page size
        const pageW = doc.internal.pageSize.width;
        const pageH = doc.internal.pageSize.height;

        // store total number of reports
        const total = Object.keys(dict).length;

        // initialize the counter for controlling the logic,
        // because when the page load, there is always one plot is initialized
        let counter = 1;

        // margin top
        const marginT = 15;
        // margin left
        const marginL = 10;

        // fix plot width
        // marginL * 3 means: left, middle, right
        const plotWidth = (pageW - marginL * 3);
        // fix plot width
        // marginL * 3 means: top, middle, bottom
        const plotHeight = (pageH - marginT * 3);

        // get current selected cataract report type
        const selected = $('.js-cataract-report-type.selected').data('report');

        for (const key in dict) {
            // whichever plot is initialized will be put into pdf first
            if (dict[key][2] === selected) {
                // get the plot and set required color
                const currentPlot = document.getElementById(dict[key][0]);
                // set plot color in pdf
                configPlotPDF(currentPlot, config);
                Plotly.toImage(currentPlot)
                    .then((dataURL) => {
                        pageStampDetails(doc, date, surgeon_name);

                        doc.addImage(dataURL, 'PNG', marginL, marginT, plotWidth, plotHeight);
                        counter++;
                    });
                // put the color back for update chart function
                // analytics_layout is from analytics_plotly.js
                configPlotPDF(currentPlot, analytics_layout);
                continue;
            }
            // hide all the none current plots to avoid page shake
            $(dict[key][1]).hide();
            // initialize all the none current plots
            OpenEyes.Dash.init(dict[key][1]);
            OpenEyes.Dash.addBespokeReport(key, null, 10);
        }
        // within this ajaxSuccess, the ajax request tirggered by download pdf button will be caught
        // and add generated plot into pdf after the requestcomplete
        $(document).ajaxSuccess(function (event, request, settings) {
            // flag for if the pdf is saved
            let saved = false;
            // due to the post url changes, need to use loop to match with equest url
            let dict_key = '';

            Object.keys(dict).forEach(function (key) {
                if (settings.url.includes(key)) {
                    dict_key = key;
                }
            });
            // prevent getting wrong things
            if (!dict_key) {
                return;
            }
            // only the events triggered by js-download-pdf will be captured
            if (event.target.activeElement.id && event.target.activeElement.id === 'js-download-pdf') {
                // get plot
                const plot = document.getElementById(dict[dict_key][0]);
                // set plot color
                configPlotPDF(plot, config);

                // convert the plot into image
                Plotly.toImage(plot)
                    .then((dataURL) => {
                        doc.addPage();
                        pageStampDetails(doc, date, surgeon_name);
                        // put the image into pdf
                        doc.addImage(dataURL, 'PNG', marginL, marginT, plotWidth, plotHeight);

                        if (counter >= total) {
                            doc.save('Cataract_Plots.pdf');
                            saved = true;
                            return saved;
                        } else {
                            counter++;
                            // See Jira OE-8869 to find the removed code (every four plots add new page)
                        }
                    }).then(function (flag) {
                        // once the plot is added into pdf, it will be cleared out
                        // and show it (it is hidden before) to avoid crashing other
                        // functions
                        $(dict[dict_key][1]).html("");
                        $(dict[dict_key][1]).show();

                        // the search form will be affected by initializing all the plots
                        // bring it back at this stage
                        if (flag) {
                            // to reset the search form
                            $('.js-cataract-report-type.selected').click();
                            // without doing so, previous requests will be captured
                            $(document).off('ajaxSuccess');
                            $('#js-download-pdf').text(originalText);
                            $('#js-analytics-spinner').hide();
                        }
                    });
            }
        });
        return true;
    };

    function getSelectedReportURL(ele) {
        const selected_ele = ele ? $(ele) : $('.js-cataract-report-type.selected');
        currentPlot = selected_ele;
        const selected_container = selected_ele.data('container');
        const selected_report = selected_ele.data('report');
        let report_url = '';
        for (const key in dict) {
            if (selected_report === dict[key][2]) {
                report_url = key;
                currentPlot = dict[key][0];
                return {
                    selected_container: selected_container,
                    report_url: report_url
                };
            }
        }
    }

    function cataractPlotType(e) {
        $('#js-analytics-spinner').show();
        e.stopPropagation();
        e.preventDefault();

        analytics_toolbox.hideDrillDownShowChart();

        const selected_item = getSelectedReportURL(this);
        const selected_container = selected_item['selected_container'];
        const selected_url = selected_item['report_url'];
        $(this).addClass("selected");
        $('.js-cataract-report-type').not(this).removeClass("selected");
        $('.analytics-cataract').not($(selected_container)).html("");
        if ($(selected_container).html()) {
            $(dict[selected_url][1]).html("");
            OpenEyes.Dash.addBespokeReport(selected_url, null, 10);
        } else {
            OpenEyes.Dash.init(selected_container);
            OpenEyes.Dash.addBespokeReport(selected_url, null, 10);
        }
    }

    function updateChart(e) {
        e.preventDefault();
        // get current selected container
        const selected_container = getSelectedReportURL()['selected_container'];
        // to match the variable in Openeyes.Dash which is the second top level of the plot (under div#xxx-xxx-grid)
        const wrapper = $(selected_container).children().closest('div').attr('id');
        // deep copy the search elements for current plot (the part above "Filter by Date") in the search form
        analytics_dataCenter.cataract.setCataractSearchForm('#' + wrapper, $('#search-form #search-form-report-search-section').clone());
        $('.report-search-form').trigger('submit');
    }

    function toggleAllSurgeonOpt() {
        $(this).toggleClass('green hint');
        $(this).blur();
        const $allsurgeons = $('#analytics_allsurgeons');
        if ($allsurgeons.val() === 'on') {
            $allsurgeons.val('');
        } else {
            $allsurgeons.val('on');
        }
        $('#search-form').submit();
    }

    function clearDate(event_date) {
        const date_from = new Date(event_date['date_from']);
        const date_to = new Date(event_date['date_to']);
        pickmeup('#analytics_datepicker_from').set_date(date_from);

        pickmeup('#analytics_datepicker_to').set_date(date_to);
    }

    return function (data) {
        const current_user = data['current_user'];
        const event_date = data['event_date'][0];
        analytics_toolbox.initDatePicker(event_date);

        if (!$('.analytics-cataract').html()) {
            const selected_item = getSelectedReportURL();
            const init_container = selected_item['selected_container'];
            const init_url = selected_item['report_url'];
            OpenEyes.Dash.init(init_container);
            OpenEyes.Dash.addBespokeReport(init_url, null, 10);
        }
        $('.js-cataract-report-type').off('click').on('click', _.throttle(cataractPlotType, ajaxThrottleTime));

        $('#js-clear-date-range').off('click').on('click', _.throttle(clearDate.bind(this, event_date), throttleTime));

        $('#js-all-surgeons').off('click').on('click', _.throttle(toggleAllSurgeonOpt, ajaxThrottleTime));

        $('#search-form').off('submit').on('submit', _.throttle(updateChart, ajaxThrottleTime));

        const pdfDownloadBTN = document.getElementById('js-download-pdf');

        pdfDownloadBTN.addEventListener('click', _.throttle(reportPlotToPDF.bind(pdfDownloadBTN, event_date, current_user), ajaxThrottleTime, {
            'trailing': false
        }));

        $(document).off('ajaxComplete').on("ajaxComplete", function (event, request, settings) {
            settings.global = false;
            if ((settings.url.replace(/_/g, '\\')).includes(currentPlot.replace('Report', '').replace(/_/g, '\\')) &&
                event.target.activeElement.id !== 'js-download-pdf') {
                const report = document.getElementById(currentPlot);
                analytics_drill_down(report, null);
                analytics_csv_cataract();
                $('#js-analytics-spinner').hide();
            }
        });
    };
})();
