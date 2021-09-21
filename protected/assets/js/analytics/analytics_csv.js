const analytics_csv_download = (function () {
    const ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;
    let g_custom_flag = false;
    const request_url = '/analytics/';
    const custom_url = 'DownloadCustomCSV';
    const non_custom_url = 'DownloadCSV';

    function csv_export(filename, csv_file) {
        const blob = new Blob([csv_file], {
            type: 'text/csv;charset=utf-8;'
        });
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename + '.csv');
        } else {
            const link = document.createElement("a");
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        $('#js-analytics-spinner').hide();
    }

    /**
     *
     * @param anonymise_flag
     * @param selected_tab
     * @param data
     * @param additional_file_name?
     */
    function current_none_custom_data_to_csv(anonymise_flag, selected_tab, data, additional_file_name = '', id_headers = 'Primary identifier, Secondary identifier') {
        const current_specialty = analytics_toolbox.getCurrentSpecialty();

        let csv_file;

        if (additional_file_name === 'vf') {
            csv_file = anonymise_flag ? 'EyeSide, Date of Fields, MD, VFI' : 'First Name, Last Name, ' + id_headers + ', EyeSide, Date of Fields, MD, VFI';
        } else {
            csv_file = anonymise_flag ? 'DOB, Age, Diagnoses' : 'First Name, Last Name, ' + id_headers + ', DOB, Age, Diagnoses';
        }

        let file_name = current_specialty + '_clinical_diagnoses';

        if (additional_file_name === 'vf') {
            file_name = current_specialty + '_service_vf_progression';
        }

        if (selected_tab && selected_tab === 'service' && additional_file_name !== 'vf') {
            csv_file += ', Weeks';
            file_name = current_specialty + '_service_' + additional_file_name + '_followups';
        }

        file_name = anonymise_flag ? file_name + ' - Anonymised' : file_name;

        if (!anonymise_flag) {
            csv_file += ', All IDs';
        }
        csv_file += '\n';
        data.forEach(function (item) {
            let temp = [];
            if (!anonymise_flag) {
                temp = temp.concat([
                    item['first name'],
                    item['last name'],
                    item['primary identifier'],
                    item['secondary identifier'],
                ]);
            }

            if (additional_file_name === 'vf') {
                temp = temp.concat([
                    item['eye_side'],
                    item['event_date'],
                    item['md'],
                    item['vfi']
                ]);
            } else {
                temp = temp.concat([
                    item['dob'],
                    item['age'],
                    item['diagnoses'] ? item['diagnoses'].replace(/,/g, '|') : 'N/A',
                ]);
            }

            if (selected_tab && selected_tab === 'service' && additional_file_name !== 'vf') {
                temp.push(item['weeks']);
            }

            if (!anonymise_flag) {
                temp.push(item['all ids'] ? item['all ids'].replace(/,/g, '|') : 'N/A');
            }
            
            csv_file += temp.join(', ');
            csv_file += '\n';
        });
        csv_export(file_name, csv_file);
    }

    function current_custom_data_to_csv(additional_type, custom_data, anonymized = false) {
        const other = analytics_toolbox.getCurrentSpecialty() === 'Glaucoma' ? 'IOP' : 'CRT';

        let statistical_file = "Time,Visual Acuity (Mean),Visual Acuity SD,Visual Acuity Patient (N)," + other + " (mean)," + other + " (SD)," + other + " Patient (N)\n";

        let patient_file = "";

        if (!anonymized) {
            patient_file += "Name,";
        }
        patient_file += "Patient ID,Age,Eye Side," + other + "," + "VA,Date\n";

        const statistical_file_name = analytics_toolbox.getCurrentSpecialty() + "_Statistical_Report";
        const patient_file_name = analytics_toolbox.getCurrentSpecialty() + "_Patient_Report";

        const side_bar_user_list = analytics_dataCenter.user.getSidebarUser();
        const current_user = analytics_dataCenter.user.getCurrentUser();

        $.ajax({
            url: request_url + custom_url,
            type: 'POST',
            data: "YII_CSRF_TOKEN=" + YII_CSRF_TOKEN + '&' + $('#search-form').serialize() + '&' +
                analytics_toolbox.getDataFilters(),
            success: function (response) {
                // actionDownloadCustomCSV will render out json straight away, so no need to do JSON.parse again.
                const data = response;
                const statistical_report = data['statistical_report'];
                const patient_report = data['patient_report'];
                for (const key in statistical_report) {
                    const va_mean = statistical_report[key]['va']['average'] !== null ? statistical_report[key]['va']['average'] : 'N/A';
                    const va_sd = statistical_report[key]['va']['SD'] !== null ? statistical_report[key]['va']['SD'] : 'N/A';

                    const other_mean = statistical_report[key]['other']['average'] !== null ? statistical_report[key]['other']['average'] : 'N/A';
                    const other_sd = statistical_report[key]['other']['SD'] !== null ? statistical_report[key]['other']['SD'] : 'N/A';

                    statistical_file += key + ',' + va_mean + ',' + va_sd + ',' + statistical_report[key]['va']['count'] + ',' + other_mean + ',' + other_sd + ',' + statistical_report[key]['other']['count'] + '\n';

                }
                patient_report.forEach(function (patient) {
                    if (!anonymized) {
                        patient_file += patient['full_name'].replace(',', ' ') + ',';
                    }
                    patient_file += patient['patient_id'] + ',' + patient['age'] + ',' + patient['va_side'] + ',' + patient['other_reading'] + ',' + patient['va_reading'] + ',' + patient['va_date'] + '\n';
                });
                csv_export(statistical_file_name, statistical_file);
                csv_export(patient_file_name, patient_file);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#js-analytics-spinner').hide();
                analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
            }
        });
    }

    function downLoadClick(e) {
        e.stopPropagation();
        e.preventDefault();
        $('#js-analytics-spinner').show();

        g_custom_flag = false;
        if ($('#js-hs-chart-analytics-clinical-others').css('display') !== 'none') {
            $('#js-hs-chart-analytics-clinical-others div.js-plotly-plot').each(function (i, item) {
                if ($(item).css('display') !== 'none') {
                    g_custom_flag = true;
                }
            });
        }
        // anonymise_flag = 0 || 1
        const anonymise_flag = $(this).data('anonymised');

        // get current selected tab: service / clinical
        const selected_tab = $('.analytics-section.selected').data('options');

        // temp: deep copy from original csv_data
        // patient_ids: store patient ids data extracted from csv_data array
        // patient_weeks: store weeks data extracted from csv_data array
        let temp, patient_ids, patient_weeks;
        // report_type for csv file name
        const report_type = $('#js-charts-service ul a.selected').data('report');
        const $datepicker_from = $('#analytics_datepicker_from');
        const $datepicker_to = $('#analytics_datepicker_to');
        if (selected_tab === 'service') {
            // sort array to align patient_ids and patient_weeks
            temp = analytics_dataCenter.service.getServiceData()["csv_data"].slice().sort(function (a, b) {
                return a['patient_id'] - b['patient_id'];
            });
            if (!temp.length) {
                current_none_custom_data_to_csv(anonymise_flag, selected_tab, [], report_type);
                return;
            }

            patient_ids = temp.map(function (item) {
                return item['patient_id'];
            });
            if (report_type !== 'vf')
            {
                patient_weeks = temp.map(function (item) {
                    return item['weeks'];
                });
            }
        }
        switch (selected_tab) {
            // if current tab is service, send out ajax call to request patient details list
            // by using patient_ids above
            case 'service':
                $.ajax({
                    url: request_url + non_custom_url,
                    type: 'POST',
                    data: {
                        "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                        params: {
                            "ids": JSON.stringify(patient_ids),
                            'report_type': report_type,
                            'from': $datepicker_from.val(),
                            'to': $datepicker_to.val()
                        },
                        'specialty': analytics_toolbox.getCurrentSpecialty()
                    },
                    success: function (response) {
                        // patients array
                        // {age, diagnoses, dob, gender, hos_num, id, name, nhs_num, procedures}
                        const patients = response;

                        // put weeks into patients array
                        if (report_type !== 'vf') {
                            patients['res'].map(function (curr, index) {
                                curr['weeks'] = patient_weeks[index];
                            });
                        }

                        // process data and export as csv
                        current_none_custom_data_to_csv(anonymise_flag, selected_tab, patients['res'], report_type, patients['id_headers'])
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#js-analytics-spinner').hide();
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                    }
                });
                break;
            case 'clinical':
                if (g_custom_flag) {
                    // if Outcomes is selected
                    // custom type depends on current selected specialty
                    const custom_type = analytics_toolbox.getCurrentSpecialty() === 'Medical Retina' ? "CRT" : "IOP";
                    current_custom_data_to_csv(custom_type, analytics_dataCenter.custom.getCustomData()['custom_data']['csv_data'], anonymise_flag);
                } else {
                    $.ajax({
                        url: request_url + non_custom_url,
                        type: 'POST',
                        data: {
                            "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                            params: {
                                "diagnoses_csv": true,
                                'from': $datepicker_from.val(),
                                'to': $datepicker_to.val()
                            },
                            'specialty': analytics_toolbox.getCurrentSpecialty()
                        },
                        success: function (response) {
                            current_none_custom_data_to_csv(anonymise_flag, selected_tab, response.res, null, response.id_headers);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#js-analytics-spinner').hide();
                            analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
                        }
                    });
                }
                break;
        }

    }

    return function () {
        // bind click event on download (csv) and download (csv - anonymised)
        $('button[id^="js-download-csv"]').off('click').on('click', _.throttle(downLoadClick, ajaxThrottleTime));
    };
})();
