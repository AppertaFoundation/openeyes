function pcr_risk_to_csv(){
    var data = $('#PcrRiskReport')[0].data[0];
    var file_name = data.name;
    data.x = data.x.concat($('#PcrRiskReport')[0].data[1].x);
    data.y = data.y.concat($('#PcrRiskReport')[0].data[1].y);
    csv_export(file_name,data);
}

function complication_profile_to_csv(){
    var data = $('#CataractComplicationsReport')[0].data[0];
    var file_name = data.name;
    csv_export(file_name,data);
}

function visual_acuity_to_csv(){
    var data = $('#OEModule_OphCiExamination_components_VisualOutcomeReport')[0].data[0];
    var file_name = data.name;
    csv_export(file_name,data);
}

function refractive_outcome_to_csv(){
    var data = $('#OEModule_OphCiExamination_components_RefractiveOutcomeReport')[0].data[0];
    var file_name = data.name;
    csv_export(file_name,data);
}
function csv_export(filename,data){
    var processData = function (x,y) {
        var finalVal = x+','+y+'\n';
        return finalVal;
    };

    var csvFile = 'x,y\n';
    for (var i = 0; i < data.x.length; i++) {
        csvFile += processData(data.x[i],data.y[i]);
    }

    var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(blob, filename);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) {
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
    console.log($('#js-chart-CA-selection').val());
    switch($('#js-chart-CA-selection').val()) {
        case '0':
            pcr_risk_to_csv();
            break;
        case '1':
            complication_profile_to_csv();
            break;
        case '2':
            visual_acuity_to_csv();
            break;
        case '3':
            refractive_outcome_to_csv();
            break;
    }
});