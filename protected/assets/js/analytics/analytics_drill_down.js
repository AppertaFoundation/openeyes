const analytics_drill_down = (function () {
    const ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;
    // for pagination
    let start = 0;
    const limit = 300;

    // post data
    let params = {};

    // flag to indicate if reach max
    let reachedMax = false;

    // for deep copy of data
    let g_data = [];

    // DB query offset multiplier
    let offset = 0;

    // diagnosis type
    let diagnosis = '';

    const request_url = '/analytics/getdrilldown';

    function patientDetails() {
        const link = $(this).data('link');
        $(this).css('background-color', '#28303b');
        window.open(link, '_blank');
    }

    function getPatient() {
        $('#js-analytics-spinner').show();
        $.ajax({
            url: request_url,
            type: "POST",
            data: {
                drill: true,
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                params: params,
                specialty: analytics_toolbox.getCurrentSpecialty(),
            },
            dataType: 'json',
            success: function (response) {
                if (response === "reachedMax" || response['count'] < limit) {
                    reachedMax = true;
                }
                if (response['dom']) {
                    $("#p_list").append(response['dom']);
                }
                $('#p_list tr.analytics-patient-list-row.clickable').on("click", _.throttle(patientDetails, ajaxThrottleTime));
            },
            complete: function () {
                $('#js-analytics-spinner').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown);
            }
        });
    }

    function scrollPatientList() {
        // get screen hight (header not included)
        const scroll_h = document.querySelector("main").scrollHeight;
        if (reachedMax) {
            return;
        }
        // if scroll bar reach 2/3 height of the screen, send out request
        if ($(this).scrollTop() > scroll_h - scroll_h / 3) {
            if (params['ids']) {
                params['ids'] = JSON.stringify(g_data.splice(start, limit));
            } else {
                offset += 1;
                params['offset'] = offset * limit;
                params['limit'] = limit;
            }
            getPatient();
        }
    }

    let prev_leavel_name = '';

    function processDrillDownListTitle(data, specialty, selected_cataract) {
        let hover_text;
        let list_title = 'Patient List -';
        let additional_info = '';
        let patient_total = data.customdata.length;
        const patient_str = patient_total > 1 ? 'patients are at ' : 'patient is at ';
        const plot_type = data.data['name'];
        const selected_tab = $('.analytics-section.selected').data('options');
        if (specialty !== 'Cataract') {
            switch (selected_tab.toLowerCase()) {
                case 'service':
                    const week_num = data.pointIndex;
                    const week_str = week_num > 1 ? 'Weeks' : 'Week';
                    additional_info = patient_total + ' ' + patient_str + week_num + ' ' + week_str + ' for ' + plot_type;
                    break;
                case 'clinical':
                    const selected_clinical = $('.clinical-plot-button.selected').text().trim();
                    switch (selected_clinical.toLowerCase()) {
                        case 'diagnoses':
                            // if the plot has further drill, the data will be an object
                            const further_drill = !Array.isArray(data.customdata);
                            const diagnoses_item = data.yaxis.ticktext[data.y];
                            if (further_drill) {
                                prev_leavel_name = diagnoses_item;
                            }
                            const str_ending = data.data['name'] ? data.data['name'] : prev_leavel_name;
                            patient_total = data.x;
                            additional_info = patient_total + ' ' + patient_str + diagnoses_item + ' for ' + str_ending;
                            break;
                        case 'outcomes':
                            hover_text = data.hovertext.trim();
                            hover_text = hover_text.slice(0, hover_text.indexOf('<'));
                            additional_info = patient_total + ' ' + patient_str + hover_text + ' for ' + data.data['name'] + ' in Outcomes';
                            break;
                    }
                    break;
            }
        } else {
            // for cataract
            if (data.hovertext) {
                // the hover text process here is not quite a generic way, as the hovertext formats are not exactly the same.

                // current  format is close to
                // '<b>xxx</b><br><i>xxx</i>xxxx<br><i>xxx</i>xxx'
                hover_text = data.hovertext;

                const text_match = hover_text.match(/<br>(.*)<br>/);

                hover_text = text_match ? text_match[text_match.length - 1].replace(/<i>|<\/i>/g, '') : ("x: " + data.xaxis.ticktext[data.x].toString() + " y: " + data.yaxis.ticktext[data.y].toString());

                additional_info = patient_total + ' ' + patient_str + hover_text + ' for ' + data.data.name;

            } else {
                additional_info = patient_total + ' ' + patient_str + data.x + ': ' + (data.y === 1 ? '100%' : data.y.toFixed(2) * 100 + '%') + ' for ' + selected_cataract.text().trim() + ' ' + data.data.name;

            }
        }
        list_title = list_title + ' ' + additional_info;
        $('.analytics-patient-list #js-list-title').html(list_title);
    }

    return function (ele) {
        // if no parameter ele passed in, use service screen. only for initialization
        ele = typeof (ele) === 'undefined' ? $('.analytics-section.selected').data('section') : ele;
        const plot_patient = typeof (ele) === 'object' ? ele : document.getElementById(ele.replace('#', ''));

        let custom_data = null;
        $(plot_patient).off('plotly_click').on('plotly_click', function (e, data) {
            params = {};
            const selected_cataract = $('.js-cataract-report-type.selected');
            if (selected_cataract.text().trim() === 'PCR Risk') {
                return;
            }
            // data structure: {event, points}
            /*
                points[0] = {
                    curveNumber,
                    customdata, <- id list for clicked bar or point
                    data,       <- plot info/settings
                    fullData,   <- all plot data/settings
                    pointIndex,
                    pointNumber,
                    x,
                    xaxis,
                    y,
                    yaxis,
                }
            */
            custom_data = data.points[0].customdata;

            const specialty = analytics_toolbox.getCurrentSpecialty();

            processDrillDownListTitle(data.points[0], specialty, selected_cataract);

            // get patient list dom
            const patient_list_container = $('.analytics-patient-list');
            // reset patient list dom layout
            $(patient_list_container).find('table').html(analytics_toolbox.getCleanDrillDownList());
            const colGroup = $('.analytics-patient-list table colgroup');

            if (Array.isArray(custom_data)) {

                $('#plot').hide();
                patient_list_container.show();

                // set up patient list dom layout for cataract
                if (specialty === 'Cataract') {
                    patient_list_container.addClass('analytics-event-list');
                    $('<th class="text-left" style="vertical-align: center;">Eye</th>').insertBefore('.analytics-patient-list .patient_procedures');
                    $('<th style="vertical-align: center;">Date</th>').insertAfter('.analytics-patient-list .patient_procedures');
                    // the width for eye, procedures, date columns
                    colGroup.append('<col style="width: 3.5%;"><col style="width: 24%;"><col style="width: 7%;">');
                } else {
                    // set up patient list dom layout for non cataract
                    // for procedures column
                    colGroup.append('<col style="width: 24%;">');
                }
                // deep copy from passed in data
                g_data = custom_data.slice();
                if (!parseInt(g_data[0])) {
                    diagnosis = g_data[0];
                    const user_list = analytics_dataCenter.user.getSidebarUser();
                    if (user_list) {
                        const user = $('#js-chart-filter-clinical-surgeon-diagnosis').text().trim();
                        if (user !== 'All' && user !== 'Admin Admin') {
                            params['clinical_surgeon'] = user_list[user];
                        }
                    }
                    params['diagnosis'] = diagnosis;
                    params['offset'] = offset;
                    params['limit'] = limit;
                } else {
                    params['ids'] = JSON.stringify(g_data.splice(start, limit));
                }
                params['from'] = $('#analytics_datepicker_from').val();
                params['to'] = $('#analytics_datepicker_to').val();
                getPatient();
            } else {
                // clicked item has further plot
                analytics_clinical('update', custom_data);
            }
        });
        $('main').off('scroll').on('scroll', _.throttle(scrollPatientList, ajaxThrottleTime));

        $('#js-back-to-chart').off('click').on('click', function () {
            // reset
            reachedMax = false;
            start = 0;
            offset = 0;
            analytics_toolbox.hideDrillDownShowChart();
        });

    };
})();
