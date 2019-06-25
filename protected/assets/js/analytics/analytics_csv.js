
function current_service_data_to_csv(anonymized = false){
    if ($('#js-hs-app-follow-up-overdue').hasClass('selected')){
        var data = window.csv_data_for_report['service_data']['overdue'];
        var file_name = "service_overdue_followups";
    }else if ($('#js-hs-app-follow-up-coming').hasClass('selected')){
        var data = window.csv_data_for_report['service_data']['coming'];
        var file_name = "service_coming_followups";
    } else{
        var data = window.csv_data_for_report['service_data']['waiting'];
        var file_name = "service_waiting_followups";
    }
    if (anonymized){
        var csv_file = "DOB, Age, Diagnoses, Weeks\n";
    } else {
        var csv_file = "First Name, Second Name, Hos Num, DOB, Age, Diagnoses, Weeks\n";
    }
     data.forEach(function (item) {
         let item_patient = $('#'+item['patient_id']);
         let patient_name = item_patient.find('.js-csv-name').html();
         patient_name = patient_name.split(' ');
         item = [
             (patient_name[0] == undefined)? '': patient_name[0],
             (patient_name[1] == undefined)? '': patient_name[1],
             item_patient.find('.js-csv-hos_num').html(),
             item_patient.find('.js-csv-dob').html(),
             item_patient.find('.js-csv-age').html(),
             item_patient.find('.js-csv-diagnoses').html().replace(/,/g,'|'),
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

function current_custom_data_to_csv(additional_type,anonymized=false){
    if (anonymized){
        var csv_file = "DOB, Age, Diagnoses, VA-L, "+additional_type+"-L, VA-R,"+additional_type+"-R\n";
    } else {
        var csv_file = "First Name, Second Name, Hos Num, DOB, Age, Diagnoses, VA-L, "+additional_type+"-L, VA-R,"+additional_type+"-R\n";
    }
    var data = Object.values(window.csv_data_for_report['custom_data']);
    console.log(data);
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
            'second_name':(patient_name[1] == undefined)? '': patient_name[1],
        };
        if (!anonymized){
            csv_file += item['first_name']+","+item['second_name']+","+item['hos_num']+",";
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

function current_clinical_data_to_csv(anonymized = false){
    var data = window.csv_data_for_report['clinical_data'];
    var file_name = "clinical_diagnoses";
    if (anonymized){
        var csv_file = "DOB, Age, Diagnoses\n";
    } else {
        var csv_file = "First Name, Second Name, Hos Num, DOB, Age, Diagnoses\n";
    }
    data.forEach(function (item) {
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
$('#js-download-csv').click(function () {
    if($('#js-btn-service').hasClass('selected')){
        current_service_data_to_csv();
    }else if($('#js-btn-clinical').hasClass('selected')){
        if($('#js-hs-clinical-diagnoses').hasClass('selected')){
            current_clinical_data_to_csv();
        }else if($('#js-hs-clinical-custom').hasClass("selected")) {
            if ($('#js-mr-specialty-tab').hasClass('selected')) {
                current_custom_data_to_csv("CRT");
            } else {
                current_custom_data_to_csv("IOP");
            }
        }
    }
});

$('#js-download-anonymized-csv').click(function () {
    if($('#js-btn-service').hasClass('selected')){
        current_service_data_to_csv(true);
    }else if($('#js-btn-clinical').hasClass('selected')){
        if($('#js-hs-clinical-diagnoses').hasClass('selected')){
            current_clinical_data_to_csv(true);
        }else if($('#js-hs-clinical-custom').hasClass("selected")) {
            if ($('#js-mr-specialty-tab').hasClass('selected')) {
                current_custom_data_to_csv("CRT",true);
            } else {
                current_custom_data_to_csv("IOP",true);
            }
        }
    }
});