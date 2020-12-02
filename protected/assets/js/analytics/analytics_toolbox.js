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
        const $charts = $('#plot');
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
        const main = document.querySelector('.analytics-v2');
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
        const date_from_elem_query = '#analytics_datepicker_from';
        const date_to_elem_query = '#analytics_datepicker_to';

        pickmeup(date_from_elem_query, {
            format: 'd-b-Y',
            date: date_from,
            hide_on_select: true,
            default_date: date_from,
        });

        pickmeup(date_to_elem_query, {
            format: 'd-b-Y',
            date: date_to,
            hide_on_select: true,
            default_date: date_to,
        });
    }

    function getVATitle() {
        const va_mode = $('.custom-filter input[name="analytics_plot"]');
        let va_title;
        const va_type = document.querySelector('td[data-name="analytics_va_unit"] span').dataset.label;
        if (va_mode.val().includes('change')) {
            va_title = "Visual acuity change from baseline (" + va_type + ")";
        } else {
            va_title = "Visual acuity (" + va_type + ")";
        }
        return va_title;
    }

    const report_title = $('#js-charts-service .js-plot-display-label.selected');

    function getReportTitleEle() {
        return report_title;
    }

    const drillDown = $('.analytics-patient-list table').html();

    function getCurrentSpecialty() {
        return $('.oescape-icon-btns li a.active.selected').data('specialty');
    }

    function changeEyeSide() {
        const $checked_eye_filter = $('.vf-filter input[name$="vf-eye"]:checked');
        if($checked_eye_filter.length === 0){
            $(this).prop('checked', true);
            return;
        }
        let side = '';
        if($(this).attr('checked')){
            side = $(this).data('side');
        }
        const opposite_side = side === 'left' ? 'right' : 'left';
        $(this).attr('checked', true);
        $(`.vf-filter input[name="${opposite_side}-vf-eye"]`).attr('checked', false);
        $('#js-hs-chart-analytics-vf-' + side).show();
        $('#js-hs-chart-analytics-vf-' + opposite_side).hide();

        $('#js-hs-chart-analytics-vf-hedgehog-' + side).show();
        $('#js-hs-chart-analytics-vf-hedgehog-' + opposite_side).hide();
    }

    function initVf(data) {
        let service_layout = oePlotly_v1.getLayout({theme: 'dark'});
        const $eye_filter = $('.vf-filter input[name$="vf-eye"]');
        service_layout['xaxis']['title'] = "Mean Deviation Rate (dB/year)";
        service_layout['xaxis']['range'] = [-30, 15];
        service_layout['yaxis']['title'] = 'Number of Patients';
        service_layout['showlegend'] = false;
        
        $eye_filter.off('change').on('change', changeEyeSide);
        

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
                initVf(null);
            } else {
                title = title || 'Patient count';
                service_layout['xaxis']['rangemode'] = 'nonnegative';
                service_layout['yaxis']['title'] = 'Patient count';
                service_layout['yaxis']['tickformat'] = 'd';
                service_layout['xaxis']['title'] = title;
                service_layout['yaxis']['range'] = [0, data.max];
                Plotly.newPlot(
                    'js-hs-chart-analytics-service', [data], service_layout, analytics_options
                );
            }
        } else {
            if (type === 'vf') {
                initVf(data);
            } else {
                title = title || 'Patient count';
                service_layout['xaxis']['rangemode'] = 'nonnegative';
                service_layout['yaxis']['title'] = 'Patient count';
                service_layout['yaxis']['tickformat'] = 'd';
                service_layout['xaxis']['title'] = title;
                service_layout['yaxis']['range'] = [0, data.max];
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

    function getDataFilters() {
        let updates = {};
        const filters = document.querySelectorAll('td[data-name^="analytics_"]');
        filters.forEach(function (td, i) {
            let filter_values = [];
            const selected_items = td.querySelectorAll('span');
            if (td.dataset.name === 'analytics_time_interval') {
                if (selected_items.length > 0) {
                    selected_items.forEach(function (span, i) {
                        const type = span.dataset.type;
                        const value = span.dataset.label;
                        updates[td.dataset.name.replace('analytics_', '') + '_' + type] = value;
                    })

                }
            } else {
                if (selected_items.length > 0) {
                    for (let i = 0; i < selected_items.length; i++) {
                        const item = selected_items[i];
                        if (item.dataset.id && item.dataset.id !== 'none') {
                            filter_values.push(item.dataset.id);
                        }

                    }
                    filter_values = filter_values.join(',');
                }
                updates[td.dataset.name.replace('analytics_', '')] = filter_values;
            }
        });
        let url_params = "";
        for (const key in updates) {
            if (url_params != "") {
                url_params += "&";
            }
            if (updates[key]) {
                url_params += key + "=" + encodeURIComponent(updates[key]);
            }
        }
        return url_params;
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
        const time_interval_unit = document.querySelector('td[data-name="analytics_time_interval"] span[data-type="unit"]').dataset.label;

        const time_interval_num = document.querySelector('td[data-name="analytics_time_interval"] span[data-type="num"]').dataset.label;

        if (!layout['xaxis']) {
            layout['xaxis'] = {};
        }

        layout['xaxis']['title'] = {
            text: time_interval_unit,
            font: {
                color: '#fff'
            },
        };

        if (time_interval_unit === 'Week' && time_interval_num < 4) {
            layout['xaxis']['dtick'] = null;
            layout['xaxis']['tickmode'] = 'auto';
        } else {
            layout['xaxis']['tickmode'] = null;
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
                const max_name_length = Math.max(...clinical_data['text'].map(function (item) {
                    return item.length;
                }));
                clinical_chart.data[0]['x'] = clinical_data.x;
                clinical_chart.data[0]['y'] = clinical_data.y;
                clinical_chart.data[0]['customdata'] = clinical_data.customdata;
                clinical_chart.data[0]['text'] = clinical_data.text;
                clinical_chart.layout['yaxis']['tickvals'] = clinical_data['y'];
                clinical_chart.layout['yaxis']['ticktext'] = clinical_data['text'];
                clinical_chart.layout['hoverinfo'] = 'x+y';
                clinical_chart.layout['margin']['l'] = max_name_length * 7;
                Plotly.redraw(clinical_chart);
            }
            if (specialty !== 'All' && $('#js-hs-clinical-custom').hasClass('selected')) {
                $('#js-hs-chart-analytics-clinical-others .js-plotly-plot').each(function (i, item) {
                    const side = $(item).attr('id').replace('js-hs-chart-analytics-clinical-others-', '');
                    const side_index = side === 'right' ? 1 : 0;
                    item.data = data[2][side_index];
                    const layout = setCustomLayout(specialty, data['va_final_ticks']);
                    layout['title']['text'] = document.querySelector('td[data-name="analytics_procedure"] span').dataset.label + ": " + side;
                    layout['yaxis2'] = setYaxisRange(item.data[1], layout['yaxis2']);
                    Plotly.newPlot(
                        $(item).attr('id'), item.data, layout, analytics_options
                    );
                    $('.custom-filter input[name$="eye"]').trigger('change');
                })
            }
        }
    }

    function setCustomLayout(specialty, va_ticks) {
        let layout = JSON.parse(JSON.stringify(analytics_layout));

        const va_mode = document.querySelector('.custom-filter input[name="analytics_plot"]:checked').value;

        layout['title'] = {
            text: '',
            font: {
                color: '#fff'
            },
        };
        layout = setXaxisTick(layout);

        layout['xaxis']['rangeslider'] = {};
        layout['yaxis']['title'] = getVATitle();
        //Set VA unit tick labels
        if (va_mode === 'absolute') {
            layout['yaxis']['tickmode'] = 'array';
            layout['yaxis']['tickvals'] = va_ticks['tick_position'];
            layout['yaxis']['ticktext'] = va_ticks['tick_labels'];
        } else {
            layout['yaxis']['tickmode'] = 'auto';
        }

        layout['yaxis2'] = {
            title: specialty === 'Glaucoma' ? "IOP (mm Hg)" : "CRT &mu;m",
            titlefont: {
                family: 'sans-serif',
                size: 12,
                color: '#fff',
            },
            side: 'right',
            overlaying: 'y',
            linecolor: '#fff',
            tickcolor: '#fff',
            tickfont: {
                color: '#fff',
            },
        };

        return layout;
    }

    return {
        getCurrentShownPlotId: getCurrentShownPlotId,
        getDataFilters: getDataFilters,
        plotUpdate: plotUpdate,
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
        getReportTitleEle: getReportTitleEle,
        setCustomLayout: setCustomLayout,
    };
})();
