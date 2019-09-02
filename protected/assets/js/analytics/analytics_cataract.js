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
    
    var currentPlot = null;

	// to config the colors for the plots
	// config: the config object from the beginning of reportPlotToPDF function
	// or the analytics_layout from analytics_plotly.js
	function configPlotPDF(plot, config) {
		// in case the plot is not passed in
		if (plot) {
			plot.layout.paper_bgcolor = config.paper_bgcolor;
			plot.layout.plot_bgcolor = config.plot_bgcolor;
			plot.layout.font.color = config.font === undefined ? 'white' : config.font.color;
			plot.layout.yaxis.linecolor = config.yaxis.linecolor;
			plot.layout.xaxis.linecolor = config.xaxis.linecolor;
		}
	}

	function pageStampDetails(doc, date, surgeon_name) {
		doc.setFontSize(8);
		doc.text(15, 10, 'Surgeon Name: ' + surgeon_name);
		doc.text(15, 20, 'Date: ' + date);
	}

    // the callback for download pdf click event
    var reportPlotToPDF = function(event_date, current_user){
        $('#js-analytics-spinner').show();
        var surgeon_name = current_user['name'];
        // grab dates
        // if from, to not filled, the max / min date from event data will be filled in
        var date = "";
        var from_date = $('#analytics_datepicker_from').val() ? $('#analytics_datepicker_from').val() : analytics_toolbox.processDate(new Date(event_date['date_from']));
        var to_date = $('#analytics_datepicker_to').val() ? " to " + $('#analytics_datepicker_to').val() : " to " + analytics_toolbox.processDate(new Date(event_date['date_to']));
        // make sure the entry is logical
        // if(new Date(from_date) > new Date(to_date)){
        //     alert('From date cannot be later than To date')
        //     return;
        // }
        // if(new Date(to_date) < new Date(from_date)){
        //     alert('To date cannot be earlier than From date')
        //     return;
        // }
        date = from_date + to_date
        // prevent click during downloading
        if($(this).text() === 'Downloading...'){
            return false;
        }

        // for better user experience to let them know it is downloading
        var originalText = $(this).text();
        $(this).text('Downloading...');

        // plot config
        var config = {
            // transparent plot bg color for not blocking texts
            paper_bgcolor: 'rgba(0, 0, 0, 0)',
            plot_bgcolor: 'rgba(0, 0, 0, 0)',
            font: {
                color: 'black',
            },
            yaxis: {
                linecolor: 'black',
            },
            xaxis: {
                linecolor: 'black',
            }
        };

        // instantiate jsPDF
        var doc = new jsPDF('l', 'pt', 'A4'); 
        // get page size
        var pageW = doc.internal.pageSize.width;
        var pageH = doc.internal.pageSize.height;

        // store total number of reports
        var total = Object.keys(dict).length;

        // initialize the counter for controlling the logic, 
        // because when the page load, there is always one plot is initialized
        var counter = 1;

        // margin top
        var marginT = 15;
        // margin left
        var marginL = 10;

        // fix plot width
        // marginL * 3 means: left, middle, right
        var plotWidth = (pageW - marginL * 3);
        // fix plot width
        // marginL * 3 means: top, middle, bottom
        var plotHeight = (pageH - marginT * 3);

        // get current selected cataract report type
        var selected = $('.js-cataract-report-type.selected').data('report'); 

        for(var key in dict){
            // whichever plot is initialized will be put into pdf first
            if(dict[key][2] === selected){
                // get the plot and set required color
                var currentPlot = document.getElementById(dict[key][0]);
                // set plot color in pdf
                configPlotPDF(currentPlot, config);
                Plotly.toImage(currentPlot)
                    .then((dataURL)=>{
                        pageStampDetails(doc, date, surgeon_name);

                        doc.addImage(dataURL, 'PNG', marginL, marginT, plotWidth, plotHeight);
                        counter++;
                    });
                    // put the color back for update chart function
                    // analytics_layout is from analytics_plotly.js
                    configPlotPDF(currentPlot, analytics_layout);
                    continue;
            }
            // hide all the none current plots to avoid page shake
            $(dict[key][1]).hide();
            // initialize all the none current plots
            OpenEyes.Dash.init(dict[key][1]);
            OpenEyes.Dash.addBespokeReport(key, null, 10);
        }
        // within this ajaxSuccess, the ajax request tirggered by download pdf button will be caught
        // and add generated plot into pdf after the requestcomplete
        $(document).ajaxSuccess(function(event, request, settings){
            // flag for if the pdf is saved
            var saved = false;
            // only the events triggered by js-download-pdf will be captured
            if(event.target.activeElement.id && event.target.activeElement.id === 'js-download-pdf'
            && dict[settings.url]) {
                // get plot
                var plot = document.getElementById(dict[settings.url][0]);
                // set plot color
                configPlotPDF(plot, config);

                // convert the plot into image
                Plotly.toImage(plot)
                    .then((dataURL)=>{
                        doc.addPage();
                        pageStampDetails(doc, date, surgeon_name);
                        // put the image into pdf
                        doc.addImage(dataURL, 'PNG', marginL, marginT, plotWidth, plotHeight);
                        
                        if(counter >= total){
                            doc.save('Cataract_Plots.pdf');
                            $('#js-analytics-spinner').hide();
                            saved = true;
                            return saved;
                        } else {
                            counter++;
                            // See Jira OE-8869 to find the removed code (every four plots add new page)
                        }
                    }).then(function(flag){
                        // once the plot is added into pdf, it will be cleared out
                        // and show it (it is hidden before) to avoid crashing other
                        // functions
                        $(dict[settings.url][1]).html("");
                        $(dict[settings.url][1]).show();

                        // the search form will be affected by initializing all the plots
                        // bring it back at this stage
                        if(flag){
                            // clear the dictionary
                            delete dict;
                            // to reset the search form
                            $('.js-cataract-report-type.selected').click();
                            // without doing so, previous requests will be captured
                            $(document).off('ajaxSuccess');
                            $('#js-download-pdf').text(originalText);
                        }
                    });
            }
        });
        return true;
    }

	function getSelectedReportURL(ele) {
		var selected_ele = ele ? $(ele) : $('.js-cataract-report-type.selected');
		currentPlot = selected_ele;
		// console.log(ele)
		var selected_container = selected_ele.data('container');
		// console.log(selected_container)
		var selected_report = selected_ele.data('report');
		var report_url = '';
		for (var key in dict) {
			if (selected_report === dict[key][2]) {
				report_url = key;
				currentPlot = dict[key][0];
				return {
					selected_container: selected_container,
					report_url: report_url
				};
			}
		}
	}
	var init = function (data, side_bar_user_list) {
        // console.log(data)
        var current_user = data['current_user'];
        var event_date = data['event_date'][0];
        analytics_toolbox.initDatePicker(event_date);
        analytics_csv_cataract();
		if (!$('.analytics-cataract').html()) {
			var selected_item = getSelectedReportURL();
			var init_container = selected_item['selected_container'];
			var init_url = selected_item['report_url'];
			// console.log(init_container)
			// console.log(init_url)
			OpenEyes.Dash.init(init_container);
			OpenEyes.Dash.addBespokeReport(init_url, null, 10);
		}
		$('.js-cataract-report-type').off('click')
		$('.js-cataract-report-type').on('click', function (e) {
            analytics_csv_cataract();
			$('#js-analytics-spinner').show();
			// currentPlot = this;
			e.stopPropagation();
			e.preventDefault();
			if ($('.analytics-patient-list').css('display') === 'block') {
				$('.analytics-patient-list').hide();
			}
			if ($('.analytics-charts').css('display') === 'none') {
				$('.analytics-charts').show()
			}
			var selected_item = getSelectedReportURL(this);
			var selected_container = selected_item['selected_container'];
			var selected_url = selected_item['report_url'];
			$(this).addClass("selected");
			$('.js-cataract-report-type').not(this).removeClass("selected");
            $('.analytics-cataract').not($(selected_container)).html("");
            // console.log($(selected_container).html() ? 'not empty' : 'empty');
            if($(selected_container).html()){
                $(dict[selected_url][1]).html("");
                OpenEyes.Dash.addBespokeReport(selected_url, null, 10);
            } else {
                OpenEyes.Dash.init(selected_container);
                OpenEyes.Dash.addBespokeReport(selected_url, null, 10);
            }
			// console.log(currentPlot);
			// $('#' + currentPlot).on('plotly_click', function(e, data){
			//     console.log('clicked')
			//     console.log(data);
			// })
        });
        $('#js-clear-date-range').off('click').on('click', function(){
            var date_from = analytics_toolbox.processDate(new Date(event_date['date_from']))
            var date_to = analytics_toolbox.processDate(new Date(event_date['date_to']))
            $('#analytics_datepicker_from').val(date_from);
            $('#analytics_datepicker_to').val(date_to);
        });
        $('#js-all-surgeons').off('click').on('click', function(){
            $(this).toggleClass('green hint');
            if ($('#analytics_allsurgeons').val() == 'on') {
                $('#analytics_allsurgeons').val('');
            } else {
                $('#analytics_allsurgeons').val('on');
            }
            $('#search-form').submit();
        });
        $('#search-form').off('submit').on('submit', function (e) {
            e.preventDefault();
            $('.report-search-form').trigger('submit');
        });
        // $('#js-download-pdf').off('click').on('click', _.throttle(reportPlotToPDF, 2000, {'trailing': false}));
        var pdfDownloadBTN = document.getElementById('js-download-pdf');
        pdfDownloadBTN.addEventListener('click', _.throttle(reportPlotToPDF.bind(pdfDownloadBTN, event_date, current_user), 2000, {'trailing': false}))
		// $(document).off('ajaxComplete');
		$(document).ajaxComplete(function (event, request, settings) {
            // console.log($(document).ajaxComplete);
            settings.global = false;
            console.log(settings)
            console.log(event.target.activeElement)
			$('#js-analytics-spinner').hide();
			// flag for if the pdf is saved
			// only the events triggered by js-download-pdf will be captured
            if (settings.url.includes(currentPlot.replace('Report', '').replace(/_/g, '\\'))
            && event.target.activeElement.id !== 'js-download-pdf') {
                // event.target.activeElement.id && event.target.activeElement.id === 'js-download-pdf'
				// console.log(settings.url)
				// console.log('got it')
                var report = document.getElementById(currentPlot);
                console.log($(event.target.activeElement))
				analytics_drill_down(report, null);
				// report.on('plotly_click', function(data){
				//     // console.log(data);
				//     // console.log(data['points'][0]['customdata']);
				//     analytics_drill_down(this)
				// })
			}
			// console.log(request)
			// console.log(event)
			// report.on('plotly_click', function(data){
			//     console.log(data);
			// })
		})
		// console.log(currentPlot);
	}
	return init;
})()