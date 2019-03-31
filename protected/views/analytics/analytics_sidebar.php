<div class="analytics-options">

    <?php $this->renderPartial('analytics_sidebar_header',array('specialty'=>$specialty));?>

    <div class="specialty"><?= $specialty ?></div>
    <div class="specialty-options">
            <div class="view-mode flex-layout">
                <?php $clinical_button_disable = true;
                if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){
                    $clinical_button_disable = false;
                }?>
                <button class="analytics-section pro-theme cols-6 <?=$clinical_button_disable? 'disabled': '';?>" id="js-btn-clinical"
                        data-section="#js-hs-chart-analytics-clinical-main"
                        data-tab="#js-charts-clinical">
                            Clinical
                </button>
                <button class="analytics-section pro-theme cols-6 selected" id="js-btn-service"
                        data-section="#js-hs-chart-analytics-service"
                        data-tab="#js-charts-service">
                    Service
                </button>
            </div>
            <input type="hidden" id="side-bar-subspecialty-id" value="<?= $specialty == 'Glaucoma'? 1:0; ?>">
            <div id="js-charts-clinical" style="display: none;">
                <ul class="charts">
                    <li>
                        <a href="#"
                           id="js-hs-clinical-diagnoses"
                           data-plotid="#js-hs-chart-analytics-clinical-diagnosis"
                           data-filterid="#js-clinical-data-filter-diagnosis"
                           class="selected clinical-plot-button js-plot-display-label">
                            Diagnoses
                        </a>
                    </li>
                    <?php if ($specialty !== "All"){?>
                        <li>
                            <a href="#"
                               id="js-hs-clinical-custom"
                               data-plotid="#js-hs-chart-analytics-clinical-others"
                               data-filterid="#js-clinical-data-filter-custom"
                               class="clinical-plot-button js-plot-display-label">
                                Change in vision
                            </a>
                        </li>
                    <?php }?>
                </ul>

                <div id="js-clinical-data-filter-diagnosis" class="js-hs-filter-analytics-clinical"  style="display: block">
                    <h3>Filters</h3>
                    <div class="clinical-filters custom-filters flex-layout">
                        <div class="clinical-filters flex-layout cols-9">
                            <ul class="filters-selected cols-9">
                                <?php if(isset($user_list)){?>
                                    <li>User:
                                        <span class="service-selected js-hs-filters js-hs-surgeon"
                                              id="js-chart-filter-clinical-surgeon-diagnosis"
                                              data-name="clinical_surgeon">All</span>
                                    </li>
                                <?php } else { ?>
                                    <li>User:
                                        <span class="service-selected js-hs-filters js-hs-surgeon"
                                              id="js-chart-filter-clinical-surgeon-diagnosis"
                                              data-name="clinical_surgeon">
                                            <?=$current_user->getFullName();?>
                                        </span>
                                    </li>
                                <?php } ?>
                                <li>
                                    <input type="checkbox"
                                           id="js-chart-filter-service-unbooked-only" checked>
                                    <span>Unbooked Only</span>
                                </li>
                            </ul>
                        </div>
                        <?php if (isset($user_list)) { ?>
                        <div class="flex-item" style="position: relative; top: -75px; left: -140px;">
                            <div class="oe-filter-options"
                                 id="oe-filter-options-clinical-filters"
                                 data-filter-id="clinical-filters"><!-- simple button to popup filter options -->
                                <button class="oe-filter-btn green hint" id="oe-filter-btn-clinical-filters">
                                    <i class="oe-i filter pro-theme"></i>
                                </button>
                                <!-- Filter options. Popup is JS positioned: top-left, top-right, bottom-left, bottom-right -->
                                <div class="filter-options-popup" id="filter-options-popup-clinical-filters"
                                     style="display: none;">
                                    <!-- provide close (for touch) -->
                                    <div class="close-icon-btn">
                                        <i class="oe-i remove-circle medium pro-theme"></i>
                                    </div>
                                    <div class="flex-layout flex-top">
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-clinical-surgeon-diagnosis">
                                                <h3>Users</h3>
                                                <ul class="btn-list">
                                                    <li class="selected">All</li>
                                                    <?php foreach ($user_list as $user) { ?>
                                                        <li><?= $user->getFullName(); ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div><!-- options-group -->
                                    </div><!-- .flex -->
                                </div><!-- filter-options-popup -->
                            </div><!-- .oe-filter-options -->
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php if($specialty !== 'All'){?>
                <div id="js-clinical-data-filter-custom"
                     class="js-hs-filter-analytics-clinical"
                     style="display: none; ">
                    <div class="flex-item" style="position: relative; top: -25px; left: 120px;"><!-- OE UI Filter options (id: custom-filters) -->
                        <div class="oe-filter-options" id="oe-filter-options-custom-filters"
                             data-filter-id="custom-filters"><!-- simple button to popup filter options -->
                            <button class="oe-filter-btn green hint"
                                    id="oe-filter-btn-custom-filters">
                                <i class="oe-i filter pro-theme"></i>
                            </button>
                            <!-- Filter options. Popup is JS positioned: top-left, top-right, bottom-left, bottom-right -->
                            <div class="filter-options-popup" id="filter-options-popup-custom-filters"
                                 style="display: none;">
                                <!-- provide close (for touch) -->
                                <div class="close-icon-btn">
                                    <i class="oe-i remove-circle medium pro-theme"></i>
                                </div>
                                <div class="flex-layout flex-top">
                                    <?php if ($specialty !== "Cataract") {
                                        if ($specialty === 'Medical Retina') {
                                            $analytics_treatment = array('Lucentis', 'Elyea', 'Avastin', 'Triamcinolone', 'Ozurdex');
                                            $analytics_diagnoses = array('AMD(wet)', 'BRVO', 'CRVO', 'DMO');
                                            ?>
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-treatment">
                                                <h3>Treatment</h3>
                                                <ul class="btn-list">
                                                    <li class="selected">All</li>
                                                    <?php foreach ($analytics_treatment as $treatment) { ?>
                                                        <li><?= $treatment; ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-plot">
                                                <h3>Plot</h3>
                                                <ul class="btn-list">
                                                    <li>VA (absolute)</li>
                                                    <li class="selected">VA (change)</li>
                                                </ul>
                                            </div><!-- options-group -->
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-protocol">
                                                <h3>Protocol</h3>
                                                <ul class="btn-list">
                                                    <li class="selected">ALL</li>
                                                </ul>
                                            </div><!-- options-group -->
                                        <?php } elseif ($specialty === 'Glaucoma') {
                                            $analytics_diagnoses = array('Glaucoma', 'Open Angle Glaucoma', 'Angle Closure Glaucoma', 'Low Tension Glaucoma', 'Ocular Hypertension');
                                            $analytics_procedures = array('Cataract Extraction','Trabeculectomy', 'Aqueous Shunt','Cypass','SLT','Cyclodiode');
                                            ?>
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-procedure">
                                                <h3>Procedures</h3>
                                                <ul class="btn-list">
                                                    <li class="selected">All</li>
                                                    <?php foreach ($analytics_procedures as $procedure) { ?>
                                                        <li><?= $procedure; ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div><!-- options-group -->
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-plot">
                                                <h3>Plot</h3>
                                                <ul class="btn-list">
                                                    <li>VA (absolute)</li>
                                                    <li  class="selected">VA (change)</li>
                                                </ul>
                                            </div><!-- options-group -->
                                        <?php } ?>

                                        <?php if (isset($user_list)) { ?>
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-clinical-surgeon-custom">
                                                <h3>Users</h3>
                                                <ul class="btn-list">
                                                    <li class="selected">All</li>
                                                    <?php foreach ($user_list as $user) { ?>
                                                        <li><?= $user->getFullName(); ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div><!-- options-group -->
                                        <?php } ?>

                                        <div class="options-group" data-filter-ui-id="js-chart-filter-diagnosis">
                                            <h3>Diagnosis</h3>
                                            <ul class="btn-list js-multi-list">
                                                <li class="selected">All</li>
                                                <?php foreach ($analytics_diagnoses as $diagnosis) { ?>
                                                    <li><?= $diagnosis; ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div><!-- options-group -->
                                    <?php } ?>
                                    <div class="options-group" data-filter-ui-id="js-chart-filter-age">
                                        <h3>Age</h3>
                                        <ul class="btn-list">
                                            <li class="selected">All</li>
                                            <li>Range</li>
                                        </ul>
                                    </div><!-- options-group -->
                                    <div class="options-group" data-filter-ui-id="js-chart-filter-eye-side">
                                        <h3>Eye</h3>
                                        <ul class="btn-list" id="js-btn-selected-eye">
                                            <li class="selected">Right</li>
                                            <li>Left</li>
                                        </ul>
                                    </div>
                                </div><!-- .flex -->
                            </div><!-- filter-options-popup -->
                        </div><!-- .oe-filter-options -->
                    </div>
                    <h3>Filters</h3>
                    <div class="custom-filters">
                        <ul class="filters-selected cols-9">
                            <?php if(isset($user_list)){?>
                                <li>User:
                                    <span class="service-selected js-hs-filters js-hs-surgeon"
                                          id="js-chart-filter-clinical-surgeon-custom"
                                          data-name="custom_surgeon">All</span>
                                </li>
                            <?php }else{?>
                                <li>User:
                                    <span class="service-selected js-hs-filters js-hs-surgeon"
                                          id="js-chart-filter-clinical-surgeon-custom"
                                          data-name="custom_surgeon">
                                        <?= $current_user->getFullName();?>
                                    </span>
                                </li>
                            <?php } ?>
                            <li>Eye: <span id="js-chart-filter-eye-side">Right</span></li>
                            <li id="js-chart-filter-age-all">Ages: <span id="js-chart-filter-age"  data-name="custom_age_all">All</span></li>
                            <li id="js-chart-filter-age-range" style="display: none;">Ages:
                                <select id="js-chart-filter-age-min" style="font-size: 1em; width: inherit" data-name="custom_age_min">
                                    <?php for ($i = 0; $i < 120; $i++) { ?>
                                        <option value="<?= $i; ?>"><?= $i; ?></option>
                                    <?php } ?>
                                </select>
                                to
                                <select id="js-chart-filter-age-max" style="font-size: 1em; width: inherit" data-name="custom_age_max">
                                    <?php for ($i = 0; $i < 120; $i++) { ?>
                                        <option value="<?= $i; ?>"><?= $i; ?></option>
                                    <?php } ?>
                                </select>
                            </li>
                            <?php if ($specialty == "Medical Retina") { ?>
                                <li>Treatment: <span id="js-chart-filter-treatment" class="js-hs-filters js-hs-custom-mr-treatment" data-name="custom_treatment">All</span></li>
                                <li>Diagnosis: <span id="js-chart-filter-diagnosis" class="js-hs-filters js-hs-custom-mr-diagnosis" data-name="custom_diagnosis">All</span></li>
                                <li>Plot: <span id="js-chart-filter-plot" class="js-hs-filters js-hs-custom-mr-plot-type" data-name="custom_plot">VA (change)</span></li>
                                <li>Protocol: <span id="js-chart-filter-protocol" class="js-hs-filters" data-name="custom_protocol">ALL</span></li>
                            <?php }else{ ?>
                                <li>Diagnosis: <span id="js-chart-filter-diagnosis" class="js-hs-filters js-hs-custom-gl-diagnosis" data-name="custom_diagnosis">All</span></li>
                                <li>Plot: <span id="js-chart-filter-plot" class="js-hs-filters js-hs-custom-mr-plot-type" data-name="custom_plot">VA (change)</span></li>
                                <li>Procedure: <span id="js-chart-filter-procedure" class="js-hs-filters js-hs-custom-gl-procedure" data-name="custom_procedure">All</span></li>
                            <?php } ?>
                        </ul>
                    </div><!-- .chart-filters -->
                </div><!-- #js-custom-data-filter -->
            <?php } ?>
            </div>
        <div id="js-charts-service">
            <ul class="charts">
                <li><a href="#" id="js-hs-app-follow-up-coming" class="js-plot-display-label">Followups coming due</a></li>
                <li><a href="#" id="js-hs-app-follow-up-overdue" class="selected js-plot-display-label">Overdue followups</a></li>
                <li><a href="#" id="js-hs-app-follow-up-waiting" class="js-plot-display-label">Waiting time for new patients</a></li>
            </ul>
            <div id="js-service-data-filter" class="" style="display: block">
                <div class="service-filters custom-filters">
                    <div class="flex-item" style="position: relative; top: -25px; left: 200px;"><!-- OE UI Filter options (id: service-filters) -->
                        <div class="oe-filter-options" id="oe-filter-options-service-filters"
                             data-filter-id="service-filters"><!-- simple button to popup filter options -->
                            <button class="oe-filter-btn green hint" id="oe-filter-btn-service-filters">
                                <i class="oe-i filter pro-theme"></i>
                            </button>
                            <!-- Filter options. Popup is JS positioned: top-left, top-right, bottom-left, bottom-right -->
                            <div class="filter-options-popup" id="filter-options-popup-service-filters"
                                 style="display: none;">
                                <!-- provide close (for touch) -->
                                <div class="close-icon-btn">
                                    <i class="oe-i remove-circle medium pro-theme"></i>
                                </div>
                                <div class="flex-layout flex-top">
                                    <div class="options-group" data-filter-ui-id="js-chart-filter-service-diagnosis">
                                        <h3>Diagnosis</h3>
                                        <ul class="btn-list">
                                            <li class="selected">All</li>
                                            <?php foreach ($common_disorders as $id => $diagnosis) { ?>
                                                <li><?= $diagnosis; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div><!-- options-group -->
                                </div><!-- .flex -->
                            </div><!-- filter-options-popup -->
                        </div><!-- .oe-filter-options -->
                    </div>
                    <h3>Filters</h3>
                    <div class="service-filters  flex-layout">
                        <ul class="filters-selected cols-9">
                            <li>
                                <input type="checkbox" id="js-chart-filter-service-unbooked-only" checked>
                                <span>Unbooked Only</span>
                            </li>
                            <li>Diagnosis:
                                <span id="js-chart-filter-service-diagnosis" class="js-hs-filters"
                                                 data-name="service_diagnosis">All</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <form id="search-form">
            <input type="hidden" name="specialty" value="<?= $specialty; ?>">
            <h3>Filter by Date</h3>
            <div class="flex-layout">
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
            <div class="row">
                <button id="js-clear-date-range" class="pro-theme" onclick="viewAllDates()" type="button">View all dates</button>
            </div>
            <button class="pro-theme green hint cols-full update-chart-btn" type="submit">Update Chart</button>
        </form>


        <div class="extra-actions">
            <button id="js-download-csv" data-value="aaa" class="pro-theme cols-full">Download (CSV)</button>
            <button id="js-download-anonymized-csv" class="pro-theme cols-full">Download (CSV - Anonymised)</button>
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

    $('#js-btn-selected-eye').click(function(e){
        $('#js-chart-filter-eye-side').trigger( "changeEyeSide" );
    });
    $('#js-chart-filter-eye-side').bind( "changeEyeSide", function(){
        var side = $('#js-chart-filter-eye-side').text().toLowerCase();
        var opposite_side = side == 'left' ? 'right' : 'left';
        $('#js-hs-chart-analytics-clinical-others-' + side).show();
        $('#js-hs-chart-analytics-clinical-others-' + opposite_side).hide();
    });

    $('#js-chart-filter-age').on('DOMSubtreeModified', function () {
        if ($('#js-chart-filter-age').html() == "Range") {
            $('#js-chart-filter-age-all').hide();
            $('#js-chart-filter-age-min').addClass('js-hs-filters');
            $('#js-chart-filter-age-max').addClass('js-hs-filters');
            $('#js-chart-filter-age-range').show();
        } else {
            $('#js-chart-filter-age-range').hide();
            $('#js-chart-filter-age-min').removeClass('js-hs-filters');
            $('#js-chart-filter-age-max').removeClass('js-hs-filters');
            $('#js-chart-filter-age-all').show();
        }
    });

    function getCurrentShownPlotId(){
        var plot_id;
        $('.js-plotly-plot').each(function () {
            if($(this).is(':visible')){
                plot_id =  $(this)[0].id;
                return false;
            }
        });
        return plot_id;
    }

    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        let current_plot = $("#"+getCurrentShownPlotId());
        current_plot.hide();
        $('#js-analytics-spinner').show();
        $.ajax({
            url: '/analytics/updateData',
            data:$('#search-form').serialize() + getDataFilters(),
            dataType:'json',
            success: function (data, textStatus, jqXHR) {
                $('#js-analytics-spinner').hide();
                current_plot.show();
                plotUpdate(data);
            }
        });
    });

    function getDataFilters(){
        var specialty = "<?=$specialty;?>";
        var side_bar_user_list = <?=CJavaScript::encode($side_bar_user_list);?>;
        var service_common_disorders = <?=CJavaScript::encode($common_disorders);?>;
        var mr_custom_diagnosis = ['AMD(wet)', 'BRVO', 'CRVO', 'DMO'];
        var gl_custom_diagnosis = ['Glaucoma', 'Open Angle Glaucoma', 'Angle Closure Glaucoma', 'Low Tension Glaucoma', 'Ocular Hypertension'];
        var mr_custom_treatment = ['Lucentis', 'Elyea', 'Avastin', 'Triamcinolone', 'Ozurdex'];
        var gl_custom_procedure = ['Cataract Extraction','Trabeculectomy', 'Aqueous Shunt','Cypass','SLT','Cyclodiode'];
        var filters ="specialty="+specialty;
        $('.js-hs-filters').each(function () {
            if($(this).is('span')){
                if ($(this).html() !== 'All'){
                    if ($(this).hasClass('js-hs-surgeon')){
                        filters += '&'+$(this).data('name')+'='+side_bar_user_list[$(this).html()];
                    }else if($(this).data('name') == "service_diagnosis"){
                        filters += '&'+$(this).data('name')+'='+Object.keys(service_common_disorders).find(key => service_common_disorders[key] ===$(this).html());
                    }else if($(this).hasClass('js-hs-custom-mr-diagnosis')){
                        var diagnosis_array = $(this).html().split(",");
                        var diagnoses = "";
                        diagnosis_array.forEach(
                            function (item) {
                                diagnoses += mr_custom_diagnosis.indexOf(item) + ',';
                            }
                        );
                        diagnoses = diagnoses.slice(0,-1);
                        filters += '&'+$(this).data('name')+'='+diagnoses;
                    }else if($(this).hasClass('js-hs-custom-mr-treatment')){
                        var treatment = mr_custom_treatment.indexOf($(this).html());
                        filters += '&'+$(this).data('name')+'='+treatment;
                    }else if($(this).hasClass('js-hs-custom-gl-procedure')){
                        var procedure = gl_custom_procedure.indexOf($(this).html());
                        filters += '&'+$(this).data('name')+'='+procedure;
                    }else if($(this).hasClass('js-hs-custom-gl-diagnosis')){
                        var diagnosis_array = $(this).html().split(",");
                        var diagnoses = "";
                        diagnosis_array.forEach(
                            function (item) {
                                diagnoses += gl_custom_diagnosis.indexOf(item) + ',';
                            }
                        );
                        diagnoses = diagnoses.slice(0,-1);
                        filters += '&'+$(this).data('name')+'='+diagnoses;
                    }else if($(this).hasClass('js-hs-custom-mr-plot-type')){
                        if ($(this).html().includes('change')){
                            filters += '&'+$(this).data('name')+'=change';
                        }
                    }
                    else{
                        filters += '&'+$(this).data('name')+'='+$(this).html();
                    }
                }
            }else if($(this).is('select')){
                filters += '&'+$(this).data('name')+'='+$(this).val();
            }
        });
        return filters;
    }
    function plotUpdate(data){
        <?php
            if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){ ?>
                var clinical_chart = $('#js-hs-chart-analytics-clinical')[0];
                var clinical_data = data[0];
                window.csv_data_for_report['clinical_data'] = clinical_data['csv_data'];
                clinical_chart.data[0]['x'] = clinical_data.x;
                clinical_chart.data[0]['y'] = clinical_data.y;
                clinical_chart.data[0]['customdata'] = clinical_data.customdata;
                clinical_chart.data[0]['text'] = clinical_data.text;
                clinical_chart.layout['yaxis']['tickvals'] = clinical_data['y'];
                clinical_chart.layout['yaxis']['ticktext'] = clinical_data['text'];
                clinical_chart.layout['hovermode'] = 'y';
                Plotly.redraw(clinical_chart);
        <?php
            if ($specialty !== 'All'){?>
        var custom_charts = ['js-hs-chart-analytics-clinical-others-left','js-hs-chart-analytics-clinical-others-right'];
        var custom_data = data[2];
        window.csv_data_for_report['custom_data'] = custom_data['csv_data'];
        for (var i = 0; i < custom_charts.length; i++) {
            var chart = $('#'+custom_charts[i])[0];
            chart.layout['title'] = (i)? 'Clinical Section (Right Eye)': 'Clinical Section (Left Eye)';
            chart.data[0]['x'] = custom_data[i][0]['x'];
            chart.data[0]['y'] = custom_data[i][0]['y'];
            chart.data[0]['customdata'] = custom_data[i][0]['customdata'];
            chart.data[0]['error_y'] = custom_data[i][0]['error_y'];
            chart.data[1]['x'] = custom_data[i][1]['x'];
            chart.data[1]['y'] = custom_data[i][1]['y'];
            chart.data[1]['customdata'] = custom_data[i][1]['customdata'];
            chart.data[1]['error_y'] = custom_data[i][1]['error_y'];
            Plotly.redraw(chart);
        }
        <?php }}?>
        //update the service data
        constructPlotlyData(data[1]['plot_data']);
        window.csv_data_for_report['service_data'] = data[1]['csv_data'];
    }
    function viewAllDates() {
        $('#analytics_datepicker_from').val("");
        $('#analytics_datepicker_to').val("");
    }
</script>
