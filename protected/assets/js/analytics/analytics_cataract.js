var analytics_cataract = (function () {
	var dict = {
		'/report/ajaxReport?report=PcrRisk&template=analytics': [
			'PcrRiskReport',
			'#pcr-risk-grid',
			'PCR',
		],
		'/report/ajaxReport?report=CataractComplications&template=analytics': [
			'CataractComplicationsReport',
			'#cataract-complication-grid',
			'CP',
		],
		'/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\VisualOutcome&template=analytics': [
			'OEModule_OphCiExamination_components_VisualOutcomeReport',
			'#visual-acuity-grid',
			'VA',
		],
		'/report/ajaxReport?report=NodAudit&template=analytics': [
			'NodAuditReport',
			'#nod-audit-grid',
			'NOD',
		],
		'/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics&procedures[]=all': [
			'OEModule_OphCiExamination_components_RefractiveOutcomeReport',
			'#refractive-outcome-grid',
			'RO',
		]
    };
    function getSelectedReportURL(ele){
        var selected_ele = ele ? $(ele) : $('.js-cataract-report-type.selected');
        // console.log(ele)
        var selected_container = selected_ele.data('container');
        // console.log(selected_container)
        var selected_report = selected_ele.data('report');
        var report_url = '';
        for(var key in dict){
            if(selected_report === dict[key][2]){
                report_url = key;
                break;
            }
        }
        return [selected_container, report_url];
    }
    var init = function(){
        if(!$('.analytics-cataract').html()){
            var init_container = getSelectedReportURL()[0];
            var init_url = getSelectedReportURL()[1];
            // console.log(init_container)
            // console.log(init_url)
            OpenEyes.Dash.init(init_container);
            OpenEyes.Dash.addBespokeReport(init_url, null, 10);
        }
        $('.js-cataract-report-type').on('click', function(){
            // var selected_container = $(this).data('container');
            // var selected_report = $(this).data('report');
            // var report_url = '';
            // for(var key in dict){
            //     // console.log(dict[key][2])
            //     if(selected_report === dict[key][2]){
            //         report_url = key;
            //         break
            //     }
            // }
            var selected_container = getSelectedReportURL(this)[0];
            var selected_url = getSelectedReportURL(this)[1];
            $(this).addClass("selected");
            $('.js-cataract-report-type').not(this).removeClass("selected");
            $('.analytics-cataract').not($(selected_container)).html("");
            OpenEyes.Dash.init(selected_container);
            OpenEyes.Dash.addBespokeReport(selected_url, null, 10);
        });
        // console.log(Object.values(dict));
    }
    return init;
})()