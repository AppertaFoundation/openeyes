<?PHP Yii::app()->getAssetManager()->registerScriptFile('../../../node_modules/jspdf/dist/jspdf.min.js') ?>
<div class="analytics-options">
    <?php $this->renderPartial('analytics_sidebar_header', array('specialty'=>$specialty));?>

    <div class="specialty"><?= $specialty ?></div>
    <div class="specialty-options">
        <div class="view-mode flex-layout">
            <?php $clinical_button_disable = true;
            if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)) {
                $clinical_button_disable = false;
            }?>
            <!-- Service Manager flag -->
            <?php
                $isServiceMgr = false;
            if (Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)) {
                $isServiceMgr = true;
            }
            ?>
            <button class="analytics-section pro-theme cols-12 selected <?=$clinical_button_disable? 'disabled': '';?>" id="js-btn-clinical"
                    data-section="#js-hs-chart-analytics-clinical-main"
                    data-tab="#js-charts-clinical">
                    Clinical
            </button>
        </div>
        <div id="js-charts-clinical">
            <br>
            <ul class="charts">
                <li><a href="#" data-report="PCR" class="js-cataract-report-type selected">PCR Risk</a></li>
                <li><a href="#" data-report="CP" class="js-cataract-report-type">Complication Profile</a></li>
                <li><a href="#" data-report="VA" class="js-cataract-report-type">Visual Acuity</a></li>
                <li><a href="#" data-report="RO" class="js-cataract-report-type">Refractive Outcome</a></li>
                <li><a href="#" data-report="NOD" class="js-cataract-report-type">NOD Audit</a></li>
            </ul>
            <form id="search-form">
                <div id="search-form-report-search-section"></div>
                <h3>Filter by Date</h3>
                <div class="flex-layout">
                    <div id="js-common-filters-service-clinical">
                        <input name="from" type="text" class="pro-theme cols-5"
                               id="analytics_datepicker_from"
                               value=""
                               placeholder="from">
                        <input type="text" class="pro-theme cols-5"
                               id="analytics_datepicker_to"
                               value=""
                               name="to"
                               placeholder="to">
                    </div>
                    <input type="hidden" class="pro-theme cols-5"
                           id="analytics_allsurgeons"
                           value=""
                           name="allsurgeons">
                </div>
                <div class="row">
                    <button id="js-clear-date-range" class="pro-theme" type="button" onclick="viewAllDates()">View all dates</button>
                </div>
                <!-- only the user with service manager role can view all surgeons -->
                <div class="row">
                    <button id="js-all-surgeons" style="<?=$isServiceMgr ? '' : 'display:none;'?>" class="pro-theme" type="button" onclick="viewAllSurgeons()">View all surgeons</button>
                </div>
                <button class="pro-theme green hint cols-full update-chart-btn" type="submit">Update Chart</button>
            </form>
        </div>
        <div class="extra-actions">
            <button id="js-download-csv" class="pro-theme cols-full">Download (CSV)</button>
        </div>
        <div class="extra-actions">
            <button id="js-download-pdf" class="pro-theme cols-full">Download All Plots as PDF</button>
        </div>

    </div><!-- .specialty-options -->
</div>
<script type="text/javascript">
    <?php
    $side_bar_user_list = array();
    if (isset($user_list)) {
        foreach ($user_list as $user) {
            $side_bar_user_list[$user->getFullName()] = $user->id;
        }
    } else {
        $side_bar_user_list = null;
    }
    ?>
    // when the page is initialized with the first plot initialized, #search-form will get submit event in OpenEyes.Dash.js
    // $('#search-form').on('submit', function (e) {
    //     e.preventDefault();
    //     $('.report-search-form').trigger('submit');
    // });

    function viewAllSurgeons() {
        if ($('#analytics_allsurgeons').val() == 'on') {
            $('#analytics_allsurgeons').val('');
            $('#js-all-surgeons').html('View all surgeons');
        } else {
            $('#analytics_allsurgeons').val('on');
            $('#js-all-surgeons').html('View current surgeons');
        }
    }
    function viewAllDates() {
        $('#analytics_datepicker_from').val("");
        $('#analytics_datepicker_to').val("");
    }
    $('#js-btn-clinical').on('click',function () {
        if ($('#pcr-risk-grid').html() == "" && $('#js-chart-CA-selection').val() == '0'){
            OpenEyes.Dash.init('#pcr-risk-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=PcrRisk&template=analytics', null, 10);
            $('.mdl-cell').css('height','600px');
            $('.mdl-cell').css('width','1000px');
        }
    });
    $('.js-cataract-report-type').on('click',function () {
        // everytime switching between cataract report type will bind a submit event on #search-form
        // clear that out at beginning, as plot initialization will bind submit event
        if ($._data(document.getElementById('search-form'), "events").hasOwnProperty('submit')){
            $('#search-form').off('submit')
        }
        $(this).addClass("selected");
        $('.js-cataract-report-type').not(this).removeClass("selected");
        $('#pcr-risk-grid').html("");
        $('#cataract-complication-grid').html("");
        $('#visual-acuity-grid').html("");
        $('#refractive-outcome-grid').html("");
        $('#nod-audit-grid').html("");
        $('#analytics_datepicker_from').val("");
        $('#analytics_datepicker_to').val("");
        $('#analytics_allsurgeons').val("");
        $('.analytics-event-list-row').hide();
        $('.analytics-event-list').hide();
        $('#js-back-to-chart').hide();
        $('#js-all-surgeons').html('View all surgeons');
        var selected_value = $(this).data("report");
        switch (selected_value) {
            case "PCR":
                OpenEyes.Dash.init('#pcr-risk-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=PcrRisk&template=analytics', null, 10);
                break;
            case "CP":
                OpenEyes.Dash.init('#cataract-complication-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=CataractComplications&template=analytics', null,10);
                break;
            case "VA":
                OpenEyes.Dash.init('#visual-acuity-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\VisualOutcome&template=analytics', null, 10);
                break;
            case "RO":
                OpenEyes.Dash.init('#refractive-outcome-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics&procedures[]=all', null, 10);
                break;
            case "NOD":
                OpenEyes.Dash.init('#nod-audit-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=NodAudit&template=analytics', null, 10);
                break;
        }
    });
    // allow one click every two seconds
    // to avoid multi-click on this button
    $('#js-download-pdf').on('click', _.throttle(reportPlotToPDF, 2000, {'trailing': false}));

    // the callback for download pdf click event
    function reportPlotToPDF(){
        // grab dates
        // if from, to not filled, the max / min date from event data will be filled in
        var date = "";
        var from_date = $('#analytics_datepicker_from').val() ? $('#analytics_datepicker_from').val() : "<?php echo $min_event_date?>";
        var to_date = $('#analytics_datepicker_to').val() ? " to " + $('#analytics_datepicker_to').val() : " to " + "<?php echo $max_event_date?>";
        // make sure the entry is logical
        if (new Date(from_date) > new Date(to_date)){
            alert('From date cannot be later than To date')
            return;
        }
        if (new Date(to_date) < new Date(from_date)){
            alert('To date cannot be earlier than From date')
            return;
        }
        date = from_date + to_date
        // prevent click during downloading
        if ($(this).text() === 'Downloading...'){
            return false;
        }

        // for better user experience to let them know it is downloading
        var originalText = $(this).text();
        $(this).text('Downloading...');
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
            if (dict[key][2] === selected){
                // get the plot and set required color
                var currentPlot = document.getElementById(dict[key][0]);
                // set plot color in pdf
                configPlotPDF(currentPlot, config);
                Plotly.toImage(currentPlot)
                    .then((dataURL)=>{
                        pageStampDetails(doc, date);

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
            if (event.target.activeElement.id && event.target.activeElement.id === 'js-download-pdf') {
                // get plot
                var plot = document.getElementById(dict[settings.url][0]);
                // set plot color
                configPlotPDF(plot, config);

                // convert the plot into image
                Plotly.toImage(plot)
                    .then((dataURL)=>{
                        doc.addPage();
                        pageStampDetails(doc, date);
                        // put the image into pdf
                        doc.addImage(dataURL, 'PNG', marginL, marginT, plotWidth, plotHeight);
                        
                        if (counter >= total){
                            doc.save('Cataract_Plots.pdf');
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
                        if (flag){
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
    // to config the colors for the plots
    // config: the config object from the beginning of reportPlotToPDF function
    // or the analytics_layout from analytics_plotly.js
    function configPlotPDF(plot, config){
        // in case the plot is not passed in
        if (plot){
            plot.layout.paper_bgcolor = config.paper_bgcolor;
            plot.layout.plot_bgcolor = config.plot_bgcolor;
            plot.layout.font.color = config.font === undefined ? 'white' : config.font.color;
            plot.layout.yaxis.linecolor = config.yaxis.linecolor;
            plot.layout.xaxis.linecolor = config.xaxis.linecolor;
        }
    }

    function pageStampDetails(doc, date){
        doc.setFontSize(8);
        doc.text(15, 10, 'Surgeon Name: ' + 
        "<?php echo $current_user->contact->first_name . ' ' . $current_user->contact->last_name; ?>");
        doc.text(15, 20, 'Date: ' + date);
    }
</script>
