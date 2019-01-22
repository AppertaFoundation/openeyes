

function current_service_data_to_csv(){
    var data = $('#js-hs-chart-analytics-service')[0].data[0];
    var file_name = data.name;
    csv_export(file_name,data);
}
function current_custom_data_to_csv(){
    var side = ($('#js-chart-filter-eye-side-right').is(':checked')) ? 'right': 'left';
    var data = $('#js-hs-chart-analytics-custom-'+side)[0].data[0];
    var file_name = 'custom_data_'+side;
    data.customdata = $('#js-hs-chart-analytics-custom-'+side)[0].data[0].customdata.concat($('#js-hs-chart-analytics-custom-'+side)[0].data[1].customdata);
    csv_export(file_name,data);
}
function current_clinical_data_to_csv(){
    var data = $('#js-hs-chart-analytics-clinical')[0].data[0];
    var file_name = data.name;
    csv_export(file_name,data);
}
function csv_export(filename,data){
    var id_check = [];
    var processData = function (customdata) {
        var finalVal = '';
        if (customdata.length > 0){
            for (var i = 0; i < customdata.length; i++) {
                if (!id_check.includes(customdata[i])){
                    id_check.push(customdata[i]);
                    finalVal += id_check.length+',';
                    var row = document.getElementById(customdata[i]);
                    var cells = row.getElementsByTagName('td');
                    for (j = 0;j < cells.length - 1; j++){
                        finalVal += cells[j].innerHTML + ',';
                    }
                    finalVal = finalVal.slice(0,-1) + '\n';
                }
            }
        }
        return finalVal;
    };

    var csvFile = 'record_num, hos_num, firstname, surname, dob, gender, age\n';
    for (var i = 0; i < data.customdata.length; i++) {
        var current_customdata = null;
        if (data.hasOwnProperty('customdata')){
            current_customdata = data.customdata[i];
        }
        csvFile += processData(current_customdata);
    }

    var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}
$('#js-download-csv').click(function () {
    if($('#js-btn-custom').hasClass('selected')){
        current_custom_data_to_csv();
    }else if($('#js-btn-service').hasClass('selected')){
        current_service_data_to_csv();
    }else if($('#js-btn-clinical').hasClass('selected')){
        current_clinical_data_to_csv();
    }
});