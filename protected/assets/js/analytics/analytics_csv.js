var analytics_csv_download = (function () {
    var ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;
    var g_custom_flag = false;
    var request_url = '/analytics/downloadcsv';

    function current_none_custom_data_to_csv(anonymise_flag, selected_tab,  data, additional_file_name = null) {
        var current_specialty = analytics_toolbox.getCurrentSpecialty();

        var csv_file = 'First Name, Last Name, Hos Num, NHS Num, DOB, Age, Diagnoses';

        var file_name = current_specialty + "_clinical_diagnoses";

        if (anonymise_flag) {
            file_name += ' - Anonymised';
            csv_file = "DOB, Age, Diagnoses";
        }

        if(selected_tab && selected_tab === 'service') {
            csv_file += ', Weeks';
            file_name = current_specialty + '_service_' + additional_file_name + '_followups';
        }

        csv_file += '\n';
        data.forEach(function(item){
            var patient_name = item['name'].split(' ');
            var temp = [];
            if (!anonymise_flag) {
                temp = temp.concat([
                    (patient_name[0] == undefined) ? '' : patient_name[0],
                    (patient_name[1] == undefined) ? '' : patient_name[1],
                    item['hos_num'],
                    item['nhs_num'],
                ]);
            }

            temp = temp.concat([
                item['dob'],
                item['age'],
                item['diagnoses'] ? item['diagnoses'].replace(/,/g, '|') : 'N/A',
            ]);

            if(selected_tab && selected_tab === 'service'){
                temp.push(item['weeks']);
            }
            
            csv_file += temp.join(', ');
            csv_file += '\n';
        })
        csv_export(file_name, csv_file);
    }

    function current_custom_data_to_csv(additional_type, anonymized = false, custom_data) {
        var csv_file = "First Name, Last Name, Hos Num, DOB, Age, Diagnoses, VA-L, " + additional_type + "-L, VA-R," + additional_type + "-R\n";
        if (anonymized) {
            csv_file = "DOB, Age, Diagnoses, VA-L, " + additional_type + "-L, VA-R," + additional_type + "-R\n";
        }
        var file_name = analytics_toolbox.getCurrentSpecialty() + "_clinical_data_" + additional_type;
        var data = Object.values(custom_data);
        var patient_ids;
        temp = data.slice().sort(function (a, b) {
            return a['patient_id'] - b['patient_id']
        });
        patient_ids = temp.map(function (item) {
            return item['patient_id']
        })

        if(!patient_ids.length){
            csv_export(file_name, csv_file);
            return;
        }
        $.ajax({
            url: request_url,
            type: 'POST',
            data: {
                "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                params: {
                    "ids": JSON.stringify(patient_ids),
                },
                'specialty': analytics_toolbox.getCurrentSpecialty()
            },
            success: function (response) {
                var patients = JSON.parse(response);
                patients.map(function (curr, index) {
                    curr['left'] = data[index]['left'];
                    curr['right'] = data[index]['right'];
                })
                patients.forEach(function (item) {
                    patient_name = item['name'].split(' ');
                    item = {
                        'left': item['left'],
                        'right': item['right'],
                        'diagnoses': item['diagnoses'] ? item['diagnoses'].replace(/,/g, '|') : 'N/A',
                        'hos_num': item['hos_num'],
                        'nhs_num': item['nhs_num'],
                        'age': item['age'],
                        'dob': item['dob'],
                        'first_name': (patient_name[0] == undefined) ? '' : patient_name[0],
                        'last_name': (patient_name[1] == undefined) ? '' : patient_name[1],
                    }
                    if (!anonymized) {
                        csv_file += item['first_name'] + "," + item['last_name'] + "," + item['hos_num'] + ",";
                    }
                    csv_file += item['dob'] + "," + item['age'] + ",";
                    csv_file += item['diagnoses'] + ",";
                    csv_file = convert_array_to_string_in_csv(csv_file, item['left']['VA']);
                    csv_file = convert_array_to_string_in_csv(csv_file, item['left'][additional_type]);
                    csv_file = convert_array_to_string_in_csv(csv_file, item['right']['VA']);
                    csv_file = convert_array_to_string_in_csv(csv_file, item['right'][additional_type]);
                    csv_file = csv_file.replace(/.$/, "\n");
                })
                csv_export(file_name, csv_file);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#js-analytics-spinner').hide();
                analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
            }
        })
    }

    function convert_array_to_string_in_csv(csv, item) {
        item.forEach(function (element) {
            csv += element + "|";
        });
        if (item.length == 0) {
            csv += "|";
        }
        return csv.replace(/.$/, ",")
    }

    function csv_export(filename, csv_file) {
        var blob = new Blob([csv_file], {
            type: 'text/csv;charset=utf-8;'
        });
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename + '.csv');
        } else {
            var link = document.createElement("a");
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
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

    function downLoadClick(e) {
        e.stopPropagation();

        $('#js-analytics-spinner').show();

        g_custom_flag = false;
        if($('#js-hs-chart-analytics-clinical-others').css('display') !== 'none'){
            $('#js-hs-chart-analytics-clinical-others div.js-plotly-plot').each(function(i, item){
                if($(item).css('display') !== 'none'){
                    g_custom_flag = true;
                }
            })
        }
        // anonymise_flag = 0 || 1
        var anonymise_flag = $(this).data('anonymised')

        // get current selected tab: service / clinical
        var selected_tab = $('.analytics-section.selected').data('options')

        // temp: deep copy from original csv_data
        // patient_ids: store patient ids data extracted from csv_data array
        // patient_weeks: store weeks data extracted from csv_data array
        var temp, patient_ids, patient_weeks;
        // report_type for csv file name
        var report_type = $('#js-charts-service ul a.selected').data('report');
        if (selected_tab === 'service') {
            // sort array to align patient_ids and patient_weeks
            temp = analytics_dataCenter.service.getServiceData()["csv_data"].slice().sort(function (a, b) {
                return a['patient_id'] - b['patient_id']
            });
            if(!temp.length){
                current_none_custom_data_to_csv(anonymise_flag, selected_tab, [], report_type);
                return;
            }
            patient_ids = temp.map(function (item) {
                return item['patient_id']
            })
            patient_weeks = temp.map(function (item) {
                return item['weeks']
            })
        }
        switch (selected_tab) {
            // if current tab is service, send out ajax call to request patient details list
            // by using patient_ids above
            case 'service':
                $.ajax({
                    url: request_url,
                    type: 'POST',
                    data: {
                        "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                        params: {
                            "ids": JSON.stringify(patient_ids),
                            'from': $('#analytics_datepicker_from').val(),
                            'to': $('#analytics_datepicker_to').val()
                        },
                        'specialty': analytics_toolbox.getCurrentSpecialty()
                    },
                    success: function (response) {
                        // patients array
                        // {age, diagnoses, dob, gender, hos_num, id, name, nhs_num, procedures}
                        var patients = JSON.parse(response);

                        // put weeks into patients array
                        patients.map(function (curr, index) {
                            curr['weeks'] = patient_weeks[index];
                        })
                        // process data and export as csv
                        current_none_custom_data_to_csv(anonymise_flag, selected_tab, patients, report_type)
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#js-analytics-spinner').hide();
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
                    }
                })
                break;
            case 'clinical':
                if (g_custom_flag) {
                    // if change in vision is selected
                    // custom type depends on current selected specialty
                    var custom_type = analytics_toolbox.getCurrentSpecialty() === 'Medical Retina' ? "CRT" : "IOP";
                    current_custom_data_to_csv(custom_type, anonymise_flag, analytics_dataCenter.custom.getCustomData()['custom_data']['csv_data']);
                } else {
                    $.ajax({
                        url: request_url,
                        type: 'POST',
                        data: {
                            "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                            params: {
                                "diagnoses_csv": true,
                                'from': $('#analytics_datepicker_from').val(),
                                'to': $('#analytics_datepicker_to').val()
                            },
                            'specialty': analytics_toolbox.getCurrentSpecialty()
                        },
                        success: function (response) {
                            current_none_custom_data_to_csv(anonymise_flag, selected_tab, JSON.parse(response))
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#js-analytics-spinner').hide();
                            analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
                        }
                    })
                }
                break;
        }
        return
    }
    var init = function () {
        // bind click event on download (csv) and download (csv - anonymised)
        $('.extra-actions button').off('click').on('click', _.throttle(downLoadClick, ajaxThrottleTime));
    }
    return init;
})()
