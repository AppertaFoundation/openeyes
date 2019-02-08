

function current_service_data_to_csv(anonymize){
    var data = $('#js-hs-chart-analytics-service')[0].data[0];
    data.y = Array(data.y.length).fill('');
    var file_name = data.name;
    csv_export(file_name,data,',weeks', anonymize);
}
function current_custom_data_to_csv(anonymize){
    var side = ($('#js-chart-filter-eye-side-right').is(':checked')) ? 'right': 'left';
    var data = $('#js-hs-chart-analytics-custom-'+side)[0].data;
    var file_name = 'custom_data_'+side;
    if($('#side-bar-subspecialty-id').val() == 0) {
        csv_export('va', data[0], ',week,va', anonymize);
        csv_export('crt', data[1], ',week,crt', anonymize);
    }else {
        csv_export('va', data[0], ',week,va', anonymize);
        csv_export('iop', data[1], ',week,iop', anonymize);
    }
}
function current_clinical_data_to_csv(anonymize){
    var data = $('#js-hs-chart-analytics-clinical')[0].data[0];
    var yaxis_text = $('#js-hs-chart-analytics-clinical')[0].layout.yaxis.ticktext;
    data.x = Array(data.x.length).fill('');
    data.y = yaxis_text;
    var file_name = data.name;
    csv_export(file_name,data,',diagnosis', anonymize);
}
function csv_export(filename,data,csv_extra_column, anonymize){
    var id_check = [];
    var processData = function (x, y, customdata) {
        var finalVal = '';
        if (customdata.length > 0){
            for (var i = 0; i < customdata.length; i++) {
                id_check.push(customdata[i]);
                finalVal += id_check.length+',';
                var row = document.getElementById(customdata[i]);
                var cells = anonymize ? row.getElementsByClassName('js-anonymise'):row.getElementsByClassName('js-csv-data');
                for (j = 0;j < cells.length; j++){
                    finalVal += cells[j].innerHTML + ',';
                }
                if (x !== ''){
                    finalVal += x + ',';
                }
                if (y !== ''){
                    finalVal += y;
                }
                finalVal += '\n';
            }
        }
        return finalVal;
    };

    var csvFile = anonymize ? 'record_num, gender, age'+csv_extra_column+'\n' : 'record_num, hos_num, firstname, surname, dob, gender, age'+csv_extra_column+'\n';
    for (var i = 0; i < data.customdata.length; i++) {
        var current_customdata = null;
        if (data.hasOwnProperty('customdata')){
            current_customdata = data.customdata[i];
        }
        csvFile +=  processData(data.x[i],data.y[i],current_customdata);
    }

    var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
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

function csv_download(anonymize = false) {
     if ($('#js-btn-service').hasClass('selected')) {
        current_service_data_to_csv(anonymize);
    } else if ($('#js-btn-clinical').hasClass('selected')) {
        current_clinical_data_to_csv(anonymize);
    }
}

$('#js-download-csv').click(function () {
    csv_download();
});

$('#js-download-anonymized-csv').click(function () {
    csv_download(true);
});