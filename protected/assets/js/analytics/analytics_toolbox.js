const analytics_toolbox = (function () {
    const typeData = {
        waiting: {
            name: "Followups waiting time",
            title: "Waiting time for new patients (weeks)",
            htmlText: 'Waiting time for new patients',
            type: "bar"
        },
        overdue: {
            name: "Overdue followups",
            title: "Overdue followups (weeks)",
            htmlText: 'Overdue followups',
            type: "bar"
        },
        coming: {
            name: "Followups coming due",
            title: "Followups coming due (weeks)",
            htmlText: 'Followups coming due',
            type: "bar"
        },
        vf: {
            name: "Progression of Visual Fields",
            title: "Mean Deviation Rate (dB/year)",
            htmlText: "Progression of Visual Fields",
            type: "bar"
        }
    };

    function hideDrillDownShowChart() {
        const $analytics_patient_list = $('.analytics-patient-list');
        const $back_to_common_disorder = $('#js-back-to-common-disorder');
        const $charts = $('.analytics-charts');
        // hide drill down patient list
        if ($analytics_patient_list.css('display') !== 'none') {
            $analytics_patient_list.hide();
            // clear out patient list
            $analytics_patient_list.find('table').html(analytics_toolbox.getCleanDrillDownList());
        }
        // hide back to common disorders
        if ($back_to_common_disorder.css('display') !== 'none' &&
            $('.analytics-section.selected').data('options') !== 'clinical') {
            $back_to_common_disorder.hide();
        }
        // bring up chart(s)
        if ($charts.css('display') === 'none') {
            $charts.show();
        }
    }

    function ajaxErrorHandling(statusCode, errorMsg) {
        const main = document.querySelector('.oe-analytics-v2');
        const div = document.createElement('div');

        main.innerHTML = '';

        div.setAttribute('class', 'oe-err-msg');

        div.innerText = 'HTTP status code ' + statusCode + ': ' + errorMsg + '. Please contact your admin or support and try again.';

        main.appendChild(div);
    }

    function getThrottleTime() {
        return 100;
    }

    function getAjaxThrottleTime() {
        return 1000;
    }

    function processDate(date) {
        return date.getFullYear().toString() + '-' +
            (date.getMonth() + 1 < 10 ? '0' : '') + (date.getMonth() + 1) +
            '-' + (date.getDate() < 10 ? '0' : '') + date.getDate();
    }

    function initDatePicker(def) {
        const date_from = typeof (def) === 'undefined' ? false : new Date(def['date_from']);
        const date_to = typeof (def) === 'undefined' ? false : new Date(def['date_to']);

        pickmeup('#analytics_datepicker_from', {
            format: 'd-b-Y',
            date: date_from,
            hide_on_select: true,
            default_date: date_from,
        });

        pickmeup('#analytics_datepicker_to', {
            format: 'd-b-Y',
            date: date_to,
            hide_on_select: true,
            default_date: date_to,
        });
    }

    function getVATitle() {
        const va_mode = $('#js-chart-filter-plot');
        let va_title;
        if (va_mode.html().includes('change')) {
            va_title = "Visual acuity change from baseline (LogMAR)";
        } else {
            va_title = "Visual acuity (LogMAR)";
        }
        return va_title;
    }

    var drillDown = $('.analytics-patient-list table').html();

    function getCurrentSpecialty() {
        return $('.oescape-icon-btns li a.selected').data('specialty');
    }

    function changeVfEyeSide(side) {
        let opposite_side = side === 'left' ? 'right' : 'left';
        $('#js-hs-chart-analytics-vf-' + side).show();
        $('#js-hs-chart-analytics-vf-' + opposite_side).hide();

        $('#js-hs-chart-analytics-vf-hedgehog-' + side).show();
        $('#js-hs-chart-analytics-vf-hedgehog-' + opposite_side).hide();
    }

    function initVf(data) {
        let service_layout = oePlotly_v1.getLayout({theme: 'dark'});
        service_layout['xaxis']['title'] = "Mean Deviation Rate (dB/year)";
        service_layout['xaxis']['range'] = [-30, 15];
        service_layout['yaxis']['title'] = 'Number of Patients';
        service_layout['showlegend'] = false;

        $('#filter-options-popup-custom-service-filters #js-btn-selected-eye-service').click(function () {
            $('#js-chart-filter-service-eye-side').trigger("changeVfEyeSide");
        });

        $('#js-chart-filter-service-eye-side').bind("changeVfEyeSide", function () {
            let side = $('#js-chart-filter-service-eye-side').text().toLowerCase();
            changeVfEyeSide(side);
        });

        Plotly.newPlot(
            'js-hs-chart-analytics-vf-right', [data[0]], service_layout, analytics_options
        );

        Plotly.newPlot(
            'js-hs-chart-analytics-vf-left', [data[1]], service_layout, analytics_options
        );

        document.getElementById('js-hs-chart-analytics-vf-right').on(
            'plotly_click',
            function (data) {
                // Use the selected MDR to retrieve the hedgehog plot of max/min MD for all patients for the given eye.
                $('#js-analytics-spinner').show();
                let mdr = data.points[0].x;
                $.ajax({
                    url: '/analytics/getVfHedgehogplot',
                    data: {
                        "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                        'mdr': mdr,
                        'side': 'right'
                    },
                    dataType: 'json',
                    success: function (data) {
                        // custom plot
                        $('#vf-hedgehog h3').text(data.length + ' patients last recorded Mean was ' + mdr + 'dB');
                        Plotly.react('js-hs-chart-analytics-vf-hedgehog-right', data, service_layout, analytics_options);
                    },
                    complete: function () {
                        $('#js-analytics-spinner').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                    }
                });
            });

        document.getElementById('js-hs-chart-analytics-vf-left').on(
            'plotly_click',
            function (data) {
                // Use the selected MDR to retrieve the hedgehog plot of max/min MD for all patients for the given eye.
                $('#js-analytics-spinner').show();
                let mdr = data.points[0].x;
                $.ajax({
                    url: '/analytics/getVfHedgehogplot',
                    data: {
                        "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                        'mdr': mdr,
                        'side': 'left'
                    },
                    dataType: 'json',
                    success: function (data) {
                        // custom plot
                        $('#vf-hedgehog h3').text(data.length + ' patients last recorded Mean was ' + mdr + 'dB');
                        Plotly.react('js-hs-chart-analytics-vf-hedgehog-left', data, service_layout, analytics_options);
                    },
                    complete: function () {
                        $('#js-analytics-spinner').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                    }
                });
            });

        service_layout['xaxis']['title'] = "Age (Yrs)";
        service_layout['xaxis']['range'] = [0, 120];
        service_layout['yaxis']['title'] = 'Mean Deviation (dB)';
        service_layout['yaxis']['range'] = [-40, 40];
        service_layout['showlegend'] = false;
        Plotly.newPlot(
            'js-hs-chart-analytics-vf-hedgehog-right', [], service_layout, analytics_options
        );
        Plotly.newPlot(
            'js-hs-chart-analytics-vf-hedgehog-left', [], service_layout, analytics_options
        );

        document.getElementById('js-hs-chart-analytics-vf-hedgehog-right').on(
            'plotly_click',
            function (data) {
                // Use the selected patient ID value to retrieve the scatterplot of MD for the selected patient for both eyes.
                $('#js-analytics-spinner').show();
                let patient_id = data.points[0].customdata;
                $.ajax({
                    url: '/analytics/getVfRaw',
                    data: {
                        "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                        'patient_id': patient_id
                    },
                    dataType: 'json',
                    success: function (data) {
                        // custom plot
                        let $dataTable = $('#vf-patient table tbody');
                        $('#vf-patient h3').html('Patient ID: <a href="/patient/summary/' + patient_id + '">' + patient_id + '</a>');
                        $dataTable.empty();
                        $dataTable.append('<tr><td colspan="2"><i class="oe-i laterality R medium"></td></tr>');

                        $.each(data[2].x, function (index, item) {
                            $dataTable.append('<tr><th>' + item + '</th><td>' + data[2].y[index] + '</td></tr>');
                        });

                        $dataTable.append('<tr><td colspan="2"><i class="oe-i laterality L medium"></td></tr>');
                        $.each(data[0].x, function (index, item) {
                            $dataTable.append('<tr><th>' + item + '</th><td>' + data[0].y[index] + '</td></tr>');
                        });

                        if (data.length > 0) {
                            data[0].marker = { color: oePlotly_v1.getColorFor('leftEye', 'dark') };
                            data[1].line = { color: oePlotly_v1.getColorFor('leftEye', 'dark') };
                            data[2].marker = {color: oePlotly_v1.getColorFor('rightEye', 'dark')};
                            data[3].line = { color: oePlotly_v1.getColorFor('rightEye', 'dark') };
                        }

                        Plotly.react('js-hs-chart-analytics-vf-scatter', data, service_layout, analytics_options);
                    },
                    complete: function () {
                        $('#js-analytics-spinner').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                    }
                });
            });

        document.getElementById('js-hs-chart-analytics-vf-hedgehog-left').on(
            'plotly_click',
            function (data) {
                // Use the selected patient ID value to retrieve the scatterplot of MD for the selected patient for both eyes.
                $('#js-analytics-spinner').show();
                let patient_id = data.points[0].customdata;
                $.ajax({
                    url: '/analytics/getVfRaw',
                    data: {
                        "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                        'patient_id': patient_id
                    },
                    dataType: 'json',
                    success: function (data) {
                        // custom plot
                        let $dataTable = $('#vf-patient table tbody');
                        $('#vf-patient h3').html('Patient ID: <a href="/patient/summary/' + patient_id + '">' + patient_id + '</a>');
                        $dataTable.empty();
                        $dataTable.append('<tr><td colspan="2"><i class="oe-i laterality R medium"></td></tr>');

                        $.each(data[2].x, function (index, item) {
                            $dataTable.append('<tr><th>' + item + '</th><td>' + data[2].y[index] + '</td></tr>');
                        });

                        $dataTable.append('<tr><td colspan="2"><i class="oe-i laterality L medium"></td></tr>');
                        $.each(data[0].x, function (index, item) {
                            $dataTable.append('<tr><th>' + item + '</th><td>' + data[0].y[index] + '</td></tr>');
                        });

                        if (data.length > 0) {
                            data[0].marker = { color: oePlotly_v1.getColorFor('leftEye', 'dark') };
                            data[1].line = { color: oePlotly_v1.getColorFor('leftEye', 'dark') };
                            data[2].marker = {color: oePlotly_v1.getColorFor('rightEye', 'dark')};
                            data[3].line = { color: oePlotly_v1.getColorFor('rightEye', 'dark') };
                        }
                        Plotly.react('js-hs-chart-analytics-vf-scatter', data, service_layout, analytics_options);
                    },
                    complete: function () {
                        $('#js-analytics-spinner').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                    }
                });
            });

        Plotly.newPlot(
            'js-hs-chart-analytics-vf-scatter-plot', [], service_layout, analytics_options
        );
    }

    function loadPlot(flag, data, title, type = '') {
        const service_layout = JSON.parse(JSON.stringify(analytics_layout));

        if (flag === 'init') {
            if (type === 'vf') {
                $('#js-service-data-filter-custom').show();
                $('#js-service-data-filter').hide();
                initVf(null);
            } else {
                title = title || 'Patient count';
                service_layout['xaxis']['rangemode'] = 'nonnegative';
                service_layout['yaxis']['title'] = 'Patient count';
                service_layout['yaxis']['tickformat'] = 'd';
                service_layout['xaxis']['title'] = title;
                service_layout['yaxis']['range'] = [0, data.max];
                $('#js-service-data-filter').show();
                $('#js-service-data-filter-custom').hide();
                Plotly.newPlot(
                    'js-hs-chart-analytics-service', [data], service_layout, analytics_options
                );
            }
        } else {
            if (type === 'vf') {
                $('#js-service-data-filter-custom').show();
                $('#js-service-data-filter').hide();
                initVf(data);
            } else {
                title = title || 'Patient count';
                service_layout['xaxis']['rangemode'] = 'nonnegative';
                service_layout['yaxis']['title'] = 'Patient count';
                service_layout['yaxis']['tickformat'] = 'd';
                service_layout['xaxis']['title'] = title;
                service_layout['yaxis']['range'] = [0, data.max];
                $('#js-service-data-filter').show();
                $('#js-service-data-filter-custom').hide();
                Plotly.react(
                    'js-hs-chart-analytics-service', [data], service_layout, analytics_options
                );
            }
        }
    }

    function getTypeData() {
        return typeData;
    }

    function getCleanDrillDownList() {
        return drillDown;
    }

    function processPlotData(type, datasource) {
        const res = {
            name: typeData[type].name,
            x: Object.keys(datasource),
            y: Object.values(datasource).map(function (item) {
                return item.length;
            }),
            customdata: Object.values(datasource),
            type: typeData[type].type,
        };
        const max = Math.max(...res['y']) + 5 > 20 ? Math.max(...res['y']) + 5 : 20;
        const count = res['y'].reduce((a, b) => a + b, 0);
        return {
            data: res,
            count: count,
            max: max,
            title: typeData[type].title
        };
    }

    function getCurrentShownPlotId() {
        let plot_id = null;
        $('.js-plotly-plot').each(function () {
            if ($(this).is(':visible')) {
                plot_id = $(this)[0].id;
                return false;
            }
        });
        return plot_id;
    }

    function getDataFilters(specialty, side_bar_user_list, common_disorders, current_user) {
        const service_common_disorders = common_disorders;
        const mr_custom_diagnosis = ['AMD(wet)', 'BRVO', 'CRVO', 'DMO'];
        const gl_custom_diagnosis = ['Glaucoma', 'Open Angle Glaucoma', 'Angle Closure Glaucoma', 'Low Tension Glaucoma', 'Ocular Hypertension'];
        const mr_custom_treatment = ['Lucentis', 'Elyea', 'Avastin', 'Triamcinolone', 'Ozurdex'];
        const gl_custom_procedure = ['Cataract Extraction', 'Trabeculectomy', 'Aqueous Shunt', 'Cypass', 'SLT', 'Cyclodiode'];
        let filters = '';
        let diagnosis_array;
        let diagnoses;
        $('.js-hs-filters').each(function () {
            if ($(this).is('span') && $(this).is(':visible')) {
                if ($(this).html() !== 'All') {
                    if ($(this).hasClass('js-hs-surgeon')) {
                        if (side_bar_user_list !== null) {
                            filters += '&' + $(this).data('name') + '=' + side_bar_user_list[$(this).html().trim()];
                        } else {
                            filters += '&' + $(this).data('name') + '=' + current_user;
                        }
                    } else if ($(this).data('name') === "service_diagnosis") {
                        filters += '&' + $(this).data('name') + '=' + Object.keys(service_common_disorders).find(key => service_common_disorders[key] === $(this).html());
                    } else if ($(this).hasClass('js-hs-custom-mr-diagnosis')) {
                        diagnosis_array = $(this).html().split(",");
                        diagnoses = "";
                        diagnosis_array.forEach(
                            function (item) {
                                diagnoses += mr_custom_diagnosis.indexOf(item) + ',';
                            }
                        );
                        diagnoses = diagnoses.slice(0, -1);
                        filters += '&' + $(this).data('name') + '=' + diagnoses;
                    } else if ($(this).hasClass('js-hs-custom-mr-treatment')) {
                        var treatment = mr_custom_treatment.indexOf($(this).html());
                        filters += '&' + $(this).data('name') + '=' + treatment;
                    } else if ($(this).hasClass('js-hs-custom-gl-procedure')) {
                        var procedure = gl_custom_procedure.indexOf($(this).html());
                        filters += '&' + $(this).data('name') + '=' + procedure;
                    } else if ($(this).hasClass('js-hs-custom-gl-diagnosis')) {
                        diagnosis_array = $(this).html().split(",");
                        diagnoses = "";
                        diagnosis_array.forEach(
                            function (item) {
                                diagnoses += gl_custom_diagnosis.indexOf(item) + ',';
                            }
                        );
                        diagnoses = diagnoses.slice(0, -1);
                        filters += '&' + $(this).data('name') + '=' + diagnoses;
                    } else if ($(this).hasClass('js-hs-custom-mr-plot-type')) {
                        if ($(this).html().includes('change')) {
                            filters += '&' + $(this).data('name') + '=change';
                        }
                    } else {
                        filters += '&' + $(this).data('name') + '=' + $(this).html();

                    }
                }
            } else if ($(this).is('select')) {
                filters += '&' + $(this).data('name') + '=' + $(this).val();
            }
        });
        return filters;
    }

    function setYaxisRange(data, layout, target = 'iop', min = 0, max = null) {
        if (data['name'].toLowerCase() === target.toLowerCase() && Array.isArray(data['y']) && data['y'].length > 0) {
            if (!max) {
                max = Math.max(...data['y']) + 20;
                max = max >= 50 ? 70 : max;
            }
            layout['range'] = [min, max];
        } else {
            layout['rangemode'] = 'tozero';
        }
        return layout;
    }

    function setXaxisTick(layout) {
        var time_interval_unit = $('#js-chart-filter-time-interval-unit').text();

        var time_interval_num = $('#js-chart-filter-time-interval-num').text();

        if (!layout['xaxis']) {
            layout['xaxis'] = {};
        }

        layout['xaxis']['title'] = {
            text: time_interval_unit.replace(/[{()}]/g, ''),
            font: {
                color: '#fff'
            },
        };

        if (time_interval_unit === 'Week(s)' && time_interval_num < 4) {
            layout['xaxis']['dtick'] = null;
        } else {
            layout['xaxis']['dtick'] = time_interval_num;
        }
        return layout;
    }

    function plotUpdate(data, specialty, flag) {
        if (flag === 'service') {
            const service_type = $('#js-charts-service .charts li a.selected').data('report');
            let plot_data;

            if (service_type === 'vf') {
                plot_data = data[1]['plot_data'];
                loadPlot('update', plot_data, plot_data[0]['name'], service_type);
                $('#js-btn-service').data(
                    'section',
                    '#js-hs-chart-analytics-vf, #js-hs-chart-analytics-vf-hedgehog, #js-hs-chart-analytics-vf-scatter'
                );
            } else {
                plot_data = processPlotData(service_type, data[1]['plot_data']);
                loadPlot('update', plot_data['data'], plot_data['title']);
                $('#js-btn-service').data('section', '#js-hs-chart-service');
            }
        } else {
            if ($('#js-hs-clinical-diagnoses').hasClass('selected')) {
                const clinical_chart = $('#js-hs-chart-analytics-clinical')[0];
                const clinical_data = data[0];
                clinical_chart.data[0]['x'] = clinical_data.x;
                clinical_chart.data[0]['y'] = clinical_data.y;
                clinical_chart.data[0]['customdata'] = clinical_data.customdata;
                clinical_chart.data[0]['text'] = clinical_data.text;
                clinical_chart.layout['yaxis']['tickvals'] = clinical_data['y'];
                clinical_chart.layout['yaxis']['ticktext'] = clinical_data['text'];
                clinical_chart.layout['hoverinfo'] = 'x+y';
                Plotly.redraw(clinical_chart);
            }
            if (specialty !== 'All') {
                if ($('#js-hs-clinical-custom').hasClass('selected')) {
                    const custom_charts = ['js-hs-chart-analytics-clinical-others-left', 'js-hs-chart-analytics-clinical-others-right'];
                    const custom_data = data[2];
                    const op_type = specialty === 'Glaucoma' ? 'procedure' : 'treatment';
                    const $chart_filter = $('#js-chart-filter-' + op_type);
                    for (let i = 0; i < custom_charts.length; i++) {
                        const chart = $('#' + custom_charts[i])[0];
                        chart.layout['title'] = {
                            text: (i) ? $chart_filter.text() + ': Right' : $chart_filter.text() + ': Left',
                            font: {
                                color: '#fff',
                            }
                        };
                        chart.layout = setXaxisTick(chart.layout);
                        chart.layout['yaxis']['title'] = {
                            font: {
                                family: 'sans-serif',
                                size: 12,
                                color: '#fff',
                            },
                            text: getVATitle(),
                        };
                        //Set VA unit tick labels
                        const va_mode = $('#js-chart-filter-plot');
                        if (va_mode.html().includes('change')) {
                            chart.layout['yaxis']['tickmode'] = 'auto';
                        } else {
                            let tick_position = $.parseJSON(data['va_final_ticks']['tick_position']);
                            let tick_labels = $.parseJSON(data['va_final_ticks']['tick_labels']);
                            tick_position = tick_position ? tick_position : data['va_final_ticks']['tick_position'];
                            tick_labels = tick_labels ? tick_labels : data['va_final_ticks']['tick_labels'];
                            chart.layout['yaxis']['tickmode'] = 'array';
                            chart.layout['yaxis']['tickvals'] = data['va_final_ticks']['tick_position'];
                            chart.layout['yaxis']['ticktext'] = data['va_final_ticks']['tick_labels'];
                        }
                        setYaxisRange(custom_data[i][1], chart.layout['yaxis2']);
                        chart.data[0]['x'] = custom_data[i][0]['x'];
                        chart.data[0]['y'] = custom_data[i][0]['y'];
                        chart.data[0]['customdata'] = custom_data[i][0]['customdata'];
                        chart.data[0]['error_y'] = custom_data[i][0]['error_y'];
                        chart.data[0]['hoverinfo'] = custom_data[i][0]['hoverinfo'];
                        chart.data[0]['hovertext'] = custom_data[i][0]['hovertext'];
                        chart.data[1]['x'] = custom_data[i][1]['x'];
                        chart.data[1]['y'] = custom_data[i][1]['y'];
                        chart.data[1]['customdata'] = custom_data[i][1]['customdata'];
                        chart.data[1]['error_y'] = custom_data[i][1]['error_y'];
                        chart.data[1]['hoverinfo'] = custom_data[i][1]['hoverinfo'];
                        chart.data[1]['hovertext'] = custom_data[i][1]['hovertext'];
                        Plotly.redraw(chart);
                    }
                }
            }
        }
    }


    // update UI to show how Filter works
    // this is pretty basic but only to demo on IDG
    function updateUI($optionGroup) {
        // get the ID of the IDG demo text element
        const textID = $optionGroup.data('filter-ui-id');
        const $allListElements = $('.btn-list li', $optionGroup);
        const $filter_id = $('#' + textID);

        $allListElements.click(function () {
            if ($(this).parent().hasClass('js-multi-list') && $(this).text() !== "All") {
                if ($(this).hasClass('selected')) {
                    if ($filter_id.text().includes(',')) {
                        $(this).removeClass('selected');
                        $filter_id.text($filter_id.text().replace($(this).text() + ",", ""));
                        $filter_id.text($filter_id.text().replace("," + $(this).text(), ""));
                    }
                } else {
                    $(this).addClass('selected');
                    $allListElements.filter(function () {
                        return $(this).text() === "All";
                    }).removeClass('selected');
                    if ($filter_id.text() === "All") {
                        $filter_id.text($(this).text());
                    } else {
                        $filter_id.text($filter_id.text() + ',' + $(this).text());
                    }
                }
            } else {
                $filter_id.text($(this).text());
                $allListElements.removeClass('selected');
                $(this).addClass('selected');
            }
        });
    }

    return {
        getCurrentShownPlotId: getCurrentShownPlotId,
        getDataFilters: getDataFilters,
        plotUpdate: plotUpdate,
        updateUI: updateUI,
        getTypeData: getTypeData,
        loadPlot: loadPlot,
        processPlotData: processPlotData,
        getCurrentSpecialty: getCurrentSpecialty,
        getVATitle: getVATitle,
        getCleanDrillDownList: getCleanDrillDownList,
        initDatePicker: initDatePicker,
        processDate: processDate,
        getThrottleTime: getThrottleTime,
        getAjaxThrottleTime: getAjaxThrottleTime,
        ajaxErrorHandling: ajaxErrorHandling,
        hideDrillDownShowChart: hideDrillDownShowChart,
        setYaxisRange: setYaxisRange,
        setXaxisTick: setXaxisTick,
    };
})();
