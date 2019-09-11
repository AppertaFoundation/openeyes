
var analytics_csv_download = (function(){

    function current_service_data_to_csv(anonymized = false, service_data, additional_file_name){
        // if ($('#js-hs-app-follow-up-overdue').hasClass('selected')){
        //     var data = window.csv_data_for_report['service_data']['overdue'];
        //     var file_name = "service_overdue_followups";
        // }else if ($('#js-hs-app-follow-up-coming').hasClass('selected')){
        //     var data = window.csv_data_for_report['service_data']['coming'];
        //     var file_name = "service_coming_followups";
        // } else{
        //     var data = window.csv_data_for_report['service_data']['waiting'];
        //     var file_name = "service_waiting_followups";
        // }
        var csv_file = 'First Name, Last Name, Hos Num, DOB, Age, Diagnoses, Weeks\n';
        var file_name = 'service_' + additional_file_name + '_followups';
        var data = service_data
        // console.log(data)
        if (anonymized){
            file_name += ' - Anonymised';
            csv_file = "DOB, Age, Diagnoses, Weeks\n";
        }
         data.forEach(function (item) {
            //  console.log(Object.values(item));
             var patient_name = item['name'].split(' ');
             item = [
                //  (patient_name[0] == undefined)? '': patient_name[0],
                //  (patient_name[1] == undefined)? '': patient_name[1],
                //  item_patient.find('.js-csv-hos_num').html(),
                //  item_patient.find('.js-csv-dob').html(),
                //  item_patient.find('.js-csv-age').html(),
                //  item_patient.find('.js-csv-diagnoses').html().replace(/,/g,'|'),
                //  item['weeks'],
                 (patient_name[0] == undefined) ? '' : patient_name[0],
                 (patient_name[1] == undefined) ? '' : patient_name[1],
                 item['hos_num'],
                 item['dob'],
                 item['age'],
                 item['diagnoses'] ? item['diagnoses'].replace(/,/g,'|') : 'N/A',
                 item['weeks'],
             ];
             if(anonymized){
                 item = item.slice(3);
             }
            item.forEach(function (element) {
                if (Array.isArray(element)){
                    csv_file = convert_array_to_string_in_csv(csv_file,element);
                } else{
                    csv_file += element + ",";
                }
            });
             csv_file = csv_file.replace(/.$/ , "\n");
         });
    
        csv_export(file_name,csv_file);
    }
    
    function current_custom_data_to_csv(additional_type, anonymized=false, custom_data){
        var csv_file = "First Name, Last Name, Hos Num, DOB, Age, Diagnoses, VA-L, "+additional_type+"-L, VA-R,"+additional_type+"-R\n";
        if (anonymized){
            csv_file = "DOB, Age, Diagnoses, VA-L, "+additional_type+"-L, VA-R,"+additional_type+"-R\n";
        }
        // var data = Object.values(window.csv_data_for_report['custom_data']);
        var data = custom_data;
        // console.log(data);
        var file_name = "clinical_data";
        data.forEach(function (item) {
            let item_patient = $('#'+item['patient_id']);
            let patient_name = item_patient.find('.js-csv-name').html();
            patient_name = patient_name.split(' ');
            item = {
                'left':item['left'],
                'right':item['right'],
                'diagnoses':item_patient.find('.js-csv-diagnoses').html().replace(/,/g,'|'),
                'hos_num':item_patient.find('.js-csv-hos_num').html(),
                'age':item_patient.find('.js-csv-age').html(),
                'dob':item_patient.find('.js-csv-dob').html(),
                'first_name': (patient_name[0] == undefined)? '': patient_name[0],
                'last_name':(patient_name[1] == undefined)? '': patient_name[1],
            };
            if (!anonymized){
                csv_file += item['first_name']+","+item['last_name']+","+item['hos_num']+",";
            }
            csv_file += item['dob']+","+item['age']+",";
            csv_file += item['diagnoses']+",";
            csv_file = convert_array_to_string_in_csv(csv_file,item['left']['VA']);
            csv_file = convert_array_to_string_in_csv(csv_file,item['left'][additional_type]);
            csv_file = convert_array_to_string_in_csv(csv_file,item['right']['VA']);
            csv_file = convert_array_to_string_in_csv(csv_file,item['right'][additional_type]);
            csv_file = csv_file.replace(/.$/ , "\n");
        });
    
        csv_export(file_name,csv_file);
    }
    function convert_array_to_string_in_csv(csv,item) {
        item.forEach(function (element) {
            csv += element+"|";
        });
        if (item.length == 0){
            csv += "|";
        }
        return csv.replace(/.$/,",")
    }
    
    function current_clinical_data_to_csv(anonymized = false, clinical_data){
        // var data = window.csv_data_for_report['clinical_data'];
        var data = clinical_data;
        console.log(data)
        var file_name = "clinical_diagnoses";
        var csv_file = "First Name, Second Name, Hos Num, DOB, Age, Diagnoses\n";
        if (anonymized){
            csv_file = "DOB, Age, Diagnoses\n";
        }
        data.forEach(function (item) {
            // console.log(item)
            if (anonymized){
                item = item.slice(3);
            }
            item.forEach(function (element) {
                csv_file += element + ",";
            });
            csv_file = csv_file.replace(/.$/ , "\n");
        });
        csv_export(file_name,csv_file);
    }
    
    function csv_export(filename,csv_file){
        var blob = new Blob([csv_file], { type: 'text/csv;charset=utf-8;' });
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename+'.csv');
        } else {
            var link = document.createElement("a");
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename+'.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
    var init = function(data){
        // console.log(data)
        $('.extra-actions button').off('click');
        $('.extra-actions button').on('click', function(){
            // console.log(data)
            var anonymise_flag = $(this).data('anonymised') 
            var selected_tab = $('.analytics-section.selected').data('options')
            var temp, patient_ids, patient_weeks;
            if(selected_tab === 'service'){
                temp = data.slice().sort(function(a, b){
                    return a['patient_id'] - b['patient_id']
                });
                patient_ids = temp.map(function(item){
                    return item['patient_id']
                })
                patient_weeks = temp.map(function(item){
                    return item['weeks']
                })
            }
            // console.log(patient_ids)
            // console.log(patient_ids.sort())
            // console.log(patient_weeks)
            switch(selected_tab){
                case 'service':
                    // while(patient_ids.length > 0){
                        $.ajax({
                            url:'/analytics/DownLoadCSV',
                            type: 'POST',
                            data: {
                                "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                                "csv_data": JSON.stringify(patient_ids),
                            },
                            success: function(response){

                                // console.log(JSON.parse(response))
                                var report_type = $('#js-charts-service ul a.selected').data('report')
                                var patients = JSON.parse(response);
                                var test = patients.map(function(item){
                                    return item['id']
                                })
                                // console.log(test.sort())
                                patients.map(function(curr, index){
                                    curr['weeks'] = patient_weeks[index];
                                })
                                // console.log(patients)
                                current_service_data_to_csv(anonymise_flag, patients, report_type)
                            }
                        
                    })
                    // }
                    break;
                case 'clinical':
                    current_clinical_data_to_csv(anonymise_flag, data['csv_data'])
                    break;
            }
            // console.log(anonymise_flag == true)
            return
        })
        // $('#js-download-csv').click(function () {
        //     if($('#js-btn-service').hasClass('selected')){
        //         current_service_data_to_csv(data=data);
        //     }else if($('#js-btn-clinical').hasClass('selected')){
        //         if($('#js-hs-clinical-diagnoses').hasClass('selected')){
        //             current_clinical_data_to_csv();
        //         }else if($('#js-hs-clinical-custom').hasClass("selected")) {
        //             if ($('#js-mr-specialty-tab').hasClass('selected')) {
        //                 current_custom_data_to_csv("CRT");
        //             } else {
        //                 current_custom_data_to_csv("IOP");
        //             }
        //         }
        //     }
        // });
        
        // $('#js-download-anonymized-csv').click(function () {
        //     if($('#js-btn-service').hasClass('selected')){
        //         current_service_data_to_csv(true);
        //     }else if($('#js-btn-clinical').hasClass('selected')){
        //         if($('#js-hs-clinical-diagnoses').hasClass('selected')){
        //             current_clinical_data_to_csv(true);
        //         }else if($('#js-hs-clinical-custom').hasClass("selected")) {
        //             if ($('#js-mr-specialty-tab').hasClass('selected')) {
        //                 current_custom_data_to_csv("CRT",true);
        //             } else {
        //                 current_custom_data_to_csv("IOP",true);
        //             }
        //         }
        //     }
        // });
    }
    return init;
})()
