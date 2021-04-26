const analytics_csv_cataract = (function () {
    function csv_export_cataract(filename, data) {
        filename += '.csv';
        const processData = function (x, y) {
            return x + ',' + y + '\n';
        };

        let csvFile = 'x,y\n';
        for (let i = 0; i < data.x.length; i++) {
            csvFile += processData(data.x[i], data.y[i]);
        }

        const blob = new Blob([csvFile], {
            type: 'text/csv;charset=utf-8;'
        });
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename);
        } else {
            const link = document.getElementById('js-download-csv');
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
        }
    }

    function pcr_risk_to_csv() {
        // get a deep copy of x, y data from plot
        // in case the further modification will affect the original data
        const $pcrRiskReport = $('#PcrRiskReport')[0];
        let data_x =  $pcrRiskReport.data[0].x.slice();
        let data_y =  $pcrRiskReport.data[0].y.slice();
        const file_name = 'PCR Risk';
        data_x = data_x.concat( $pcrRiskReport.data[1].x);
        data_y = data_y.concat( $pcrRiskReport.data[1].y);
        csv_export_cataract(file_name, {x: data_x, y: data_y});
    }

    function complication_profile_to_csv() {
        const data = $('#CataractComplicationsReport')[0].data[0];
        const file_name = data.name;
        csv_export_cataract(file_name, data);
    }

    function visual_acuity_to_csv() {
        const data = $('#OEModule_OphCiExamination_components_VisualOutcomeReport')[0].data[0];
        const file_name = 'VisualOutcomeReport';
        csv_export_cataract(file_name, data);
    }

    function refractive_outcome_to_csv() {
        const data = $('#OEModule_OphCiExamination_components_RefractiveOutcomeReport')[0].data[0];
        const file_name = data.name;
        csv_export_cataract(file_name, data);
    }

    function nod_Audit_to_csv() {
        const data = $('#NodAuditReport')[0].data[0];
        const file_name = 'NODAuditReportCompletion';
        csv_export_cataract(file_name, data);
    }

    function catprom5_to_csv() {
        const data = $('#OEModule_OphOuCatprom5_components_Catprom5Report')[0].data[0];
        const file_name = data.name;
        csv_export_cataract(file_name, data);
    }

    return function () {
        switch ($('.js-cataract-report-type.selected').data('report')) {
            case 'PCR':
                pcr_risk_to_csv();
                break;
            case 'CP':
                complication_profile_to_csv();
                break;
            case 'VA':
                visual_acuity_to_csv();
                break;
            case 'RO':
                refractive_outcome_to_csv();
                break;
            case 'NOD':
                nod_Audit_to_csv();
                break;
            case 'CP5':
                catprom5_to_csv();
                break;
        }
    };
})();
