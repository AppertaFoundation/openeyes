<div class="analytics-options">

    <?php $this->renderPartial('analytics_sidebar_header',array('specialty'=>$specialty));?>

    <div class="specialty"><?= $specialty ?></div>
    <div class="specialty-options">
        <div class="view-mode flex-layout">
            <?php $clinical_button_disable = true;
            if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){
                $clinical_button_disable = false;
            }?>
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
                <div class="row">
                    <button id="js-all-surgeons" class="pro-theme" type="button" onclick="viewAllSurgeons()">View all surgeons</button>
                </div>
                <button class="pro-theme green hint cols-full update-chart-btn" type="submit">Update Chart</button>
            </form>
        </div>
        <div class="extra-actions">
            <button id="js-download-csv" class="pro-theme cols-full">Download (CSV)</button>
        </div>

    </div><!-- .specialty-options -->
</div>
<script type="text/javascript">
    <?php
    $side_bar_user_list = array();
    if (isset($user_list)){
        foreach ($user_list as $user){
            $side_bar_user_list[$user->getFullName()] = $user->id;
        }
    }else{
        $side_bar_user_list = null;
    }
    ?>
    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        $('.report-search-form').trigger('submit');
    });

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
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics', null, 10);
                break;
            case "NOD":
                OpenEyes.Dash.init('#nod-audit-grid');
                OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=NodAudit&template=analytics', null, 10);
                break;
        }
    });
</script>
