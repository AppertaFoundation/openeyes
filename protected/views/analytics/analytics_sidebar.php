<div class="analytics-options">

    <div class="select-analytics flex-layout">

        <h3>Select options</h3>
        <ul class="oescape-icon-btns">
            <li class="icon-btn">
                <a href="/analytics/allSubspecialties" class="active <?= $specialty == 'All' ? 'selected' : '' ?>">All</a>
            </li>
            <li class="icon-btn">
                <a href="/analytics/cataract" class="active <?= $specialty == 'Cataract' ? 'selected' : '' ?>">CA</a>
            </li>
            <li class="icon-btn">
                <a href="/analytics/glaucoma" class="active <?= $specialty == 'Glaucoma' ? 'selected' : '' ?>">GL</a>
            </li>
            <li class="icon-btn analytics-btn" data-specialty="Medical Retina">
                <a href="/analytics/medicalRetina"
                   class="active <?= $specialty == 'Medical Retina' ? 'selected' : '' ?>">MR</a>
            </li>
        </ul>
        <!-- icon-btns -->
    </div>

    <div class="specialty"><?= $specialty ?></div>
    <div>
        <input type="checkbox" id="js-chart-filter-global-anonymise">Anonymise
    </div>
    <div class="service flex-layout">
        <?php if(isset($user_list)){?>
            <div class="service-selected" id="js-service-selected-filter">All</div>
        <?php }else{?>
            <div class="service-selected" id="js-service-selected-filter"><?=$current_user->getFullName();?></div>
        <?php }?>
        <!-- OE UI Filter options (id: select-service) -->
        <?php if (isset($user_list)){?>
        <div class="oe-filter-options" id="oe-filter-options-select-service" data-filter-id="select-service">
            <!-- simple button to popup filter options -->
            <button class="oe-filter-btn green hint" id="oe-filter-btn-select-service">
                <i class="oe-i filter pro-theme"></i>
            </button><!-- Filter options. Popup is JS positioned: top-left, top-right, bottom-left, bottom-right -->
            <div class="filter-options-popup" id="filter-options-popup-select-service" style="display: none;">
                <!-- provide close (for touch) -->
                <div class="close-icon-btn">
                    <i class="oe-i remove-circle medium pro-theme"></i>
                </div>
                <div class="flex-layout flex-top">
                    <div class="options-group" data-filter-ui-id="js-service-selected-filter">
                        <!-- <h3>Title (optional)</h3> -->
                        <ul class="btn-list">
                            <li>All</li>
                            <?php foreach ($user_list as $user){?>
                            <li><?=$user->getFullName();?></li>
                            <?php }?>
                        </ul>
                    </div><!-- options-group -->
                </div><!-- .flex -->
            </div><!-- filter-options-popup -->
        </div><!-- .oe-filter-options -->
        <?php }?>
    </div>
    <div class="specialty-options">
            <div class="view-mode flex-layout">
                <?php $clinical_button_disable = true;
                if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){
                    $clinical_button_disable = false;
                }?>
                <?php if ($specialty === 'All'){?>
                    <button class="analytics-section pro-theme cols-4 <?=$clinical_button_disable? 'disabled': '';?>" id="js-btn-clinical"
                        data-section="#js-hs-chart-analytics-clinical-main"
                        data-tab="#js-charts-clinical">
                            Clinical
                    </button>
                <?php }else{?>
                    <button class="analytics-section pro-theme cols-4" id="js-btn-custom"
                            data-section="#js-hs-chart-analytics-clinical-others"
                            data-tab="#js-custom-data-filter">
                        Clinical
                    </button>
                <?php }?>
                <button class="analytics-section pro-theme cols-4 selected" id="js-btn-service"
                        data-section="#js-hs-chart-analytics-service"
                        data-tab="#js-charts-service">
                    Service
                </button>

                <button class="analytics-section pro-theme cols-4 disabled" id="js-btn-research">
                    Research
                </button>
            </div>
            <input type="hidden" id="side-bar-subspecialty-id" value="<?= $specialty == 'Glaucoma'? 1:0; ?>">
            <div id="js-charts-clinical" style="display: none;">
                <ul class="charts">
                    <li>
                        <a href="#" id="js-hs-diagnoses">Diagnoses ()</a>
                    </li>
                </ul>
            </div>

            <div id="js-charts-service">
                <ul class="charts">
                    <li><a href="#" id="js-hs-app-follow-up-coming">Follow-Up</a></li>
                    <li><a href="#" id="js-hs-app-follow-up-overdue" class="selected">Delayed</a></li>
                    <li><a href="#" id="js-hs-app-follow-up-waiting" >Waiting Time</a></li>
                </ul>
                <div id="js-service-data-filter" class=""  style="display: block">
                    <h3>Service Data Filters</h3>
                    <div class="service-filters custom-filters flex-layout">
                    <div class="service-filters flex-layout cols-9">
                        <ul class="filters-selected cols-9">
                            <li><input type="checkbox" id="js-chart-filter-service-unbooked-only" checked><span>Unbooked Only</span></li>
                            <li>Diagnosis: <span id="js-chart-filter-service-diagnosis">All</span></li>
                        </ul>
                    </div>
                    <div class="flex-item-bottom"><!-- OE UI Filter options (id: service-filters) -->
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
                                                <?php foreach ($common_disorders as $id=>$diagnosis) { ?>
                                                    <li><?= $diagnosis; ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div><!-- options-group -->
                                    </div><!-- .flex -->
                                </div><!-- filter-options-popup -->
                            </div><!-- .oe-filter-options -->
                        </div>
                    </div>
                </div>
             </div>
            <?php if($specialty !== 'All'){?>
            <div id="js-custom-data-filter" style="display: none;">
                <h3>Clinical Data Filters</h3>
                <div class="custom-filters flex-layout">
                    <ul class="filters-selected cols-9">
                        <li id="js-chart-filter-eye-side">Eyes:
                            <input type="checkbox" id="js-chart-filter-eye-side-right" checked>Right
                            <input type="checkbox" id="js-chart-filter-eye-side-left">Left
                        </li>
                        <li id="js-chart-filter-age-all">Ages: <span id="js-chart-filter-age">All</span></li>
                        <li id="js-chart-filter-age-range" style="display: none;">Ages:
                            <select id="js-chart-filter-age-min" style="font-size: 1em; width: inherit">
                                <?php for ($i = 0; $i < 120; $i++) { ?>
                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php } ?>
                            </select>
                            to
                            <select id="js-chart-filter-age-max" style="font-size: 1em; width: inherit">
                                <?php for ($i = 0; $i < 120; $i++) { ?>
                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </li>
                        <?php if ($specialty == "Medical Retina") { ?>
                            <li>Treatment: <span id="js-chart-filter-treatment">All</span></li>
                        <?php } ?>
                        <li>Diagnosis: <span id="js-chart-filter-diagnosis">All</span></li>
                        <li>Plot: <span id="js-chart-filter-plot">VA (absolute)</span></li>
                        <li>Protocol: <span id="js-chart-filter-protocol">ALL</span></li>
                    </ul>

                    <div class="flex-item-bottom"><!-- OE UI Filter options (id: custom-filters) -->
                        <div class="oe-filter-options" id="oe-filter-options-custom-filters"
                             data-filter-id="custom-filters"><!-- simple button to popup filter options -->
                            <button class="oe-filter-btn green hint" id="oe-filter-btn-custom-filters">
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
                                    <div class="options-group" data-filter-ui-id="js-chart-filter-age">
                                        <h3>Age</h3>
                                        <ul class="btn-list">
                                            <li class="selected">All</li>
                                            <li>Range</li>
                                        </ul>
                                    </div><!-- options-group -->
                                    <?php if ($specialty !== "Cataract") { ?>
                                        <?php
                                        if ($specialty === 'Medical Retina') {
                                            $analytics_treatment = array('Lucentis', 'Elyea', 'Avastin', 'Triamcinolone', 'Ozurdex');
                                            $analytics_diagnoses = array('AMD(wet)', 'BRVO', 'CRVO', 'DMO');
                                            ?>
                                            <div class="options-group" data-filter-ui-id="js-chart-filter-treatment">
                                                <h3>Treatment</h3>
                                                <ul class="btn-list">
                                                    <li class="selected">ALL</li>
                                                    <?php foreach ($analytics_treatment as $treatment) { ?>
                                                        <li><?= $treatment; ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div><!-- options-group -->
                                        <?php } elseif ($specialty === 'Glaucoma') {
                                            $analytics_diagnoses = array('Macular degeneration', 'Diabetic Macular Oedema', 'BRVO', 'CRVO', 'Hemivein');
                                        } ?>

                                        <div class="options-group" data-filter-ui-id="js-chart-filter-diagnosis">
                                            <h3>Diagnosis</h3>
                                            <ul class="btn-list">
                                                <li class="selected">All</li>
                                                <?php foreach ($analytics_diagnoses as $diagnosis) { ?>
                                                    <li><?= $diagnosis; ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div><!-- options-group -->
                                    <?php } ?>
                                    <div class="options-group" data-filter-ui-id="js-chart-filter-plot">
                                        <h3>Plot</h3>
                                        <ul class="btn-list">
                                            <li class="selected">VA (absolute)</li>
                                            <li>VA (change)</li>
                                            <li>SFT</li>
                                        </ul>
                                    </div><!-- options-group -->
                                    <div class="options-group" data-filter-ui-id="js-chart-filter-protocol">
                                        <h3>Protocol</h3>
                                        <ul class="btn-list">
                                            <li class="selected">ALL</li>
                                        </ul>
                                    </div><!-- options-group -->
                                </div><!-- .flex -->
                            </div><!-- filter-options-popup -->
                        </div><!-- .oe-filter-options -->
                    </div>
                </div><!-- .chart-filters -->
            </div><!-- #js-custom-data-filter -->
        <?php } ?>

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
                <input type="hidden" class="pro-theme cols-5"
                       id="analytics_allsurgeons"
                       value=""
                       name="allsurgeons">
            </div>
            <div class="row">
                <button id="js-clear-date-range" class="pro-theme" onclick="viewAllDates()">View all dates</button>
            </div>
            <button class="pro-theme green hint cols-full update-chart-btn" type="submit">Update Chart</button>
        </form>


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
    <?php $filter_eye_side = array('left' => 'right', 'right' => 'left');
    foreach(array_keys($filter_eye_side) as $side){?>
    $('#js-chart-filter-eye-side-' + '<?=$side;?>').click(function () {
        if (this.checked) {
            $('#js-chart-filter-eye-side-' + '<?=$filter_eye_side[$side];?>').attr('checked', false);
            $('#js-hs-chart-analytics-clinical-others-' + '<?=$side;?>').show();
            $('#js-hs-chart-analytics-clinical-others-' + '<?=$filter_eye_side[$side];?>').hide();
        }
    });
    <?php }?>


    $('#js-chart-filter-age').on('DOMSubtreeModified', function () {
        if ($('#js-chart-filter-age').html() == "Range") {
            $('#js-chart-filter-age-all').hide();
            $('#js-chart-filter-age-range').show();
        } else {
            $('#js-chart-filter-age-range').hide();
            $('#js-chart-filter-age-all').show();
        }
    });

    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({

            url: '/analytics/updateData',
            data:$('#search-form').serialize() + getDataFilters(),
            dataType:'json',

            success: function (data, textStatus, jqXHR) {
                plotUpdate(data);
            }
        });
    });

    function getDataFilters(){
        var specialty = "<?=$specialty;?>";
        var side_bar_user_list = <?=CJavaScript::encode($side_bar_user_list);?>;
        var service_common_disorders = <?=CJavaScript::encode($common_disorders);?>;
        var side_bar_user_filter_content = $('#js-service-selected-filter').html();
        var filters ="";
        if (side_bar_user_list !== null && side_bar_user_filter_content !== "All"){
            filters += "&surgeon_id="+side_bar_user_list[side_bar_user_filter_content];
        }
        if ($('#js-chart-filter-diagnosis').html() !== "All") {

            if (specialty == "Medical Retina") {
                if ($('#js-chart-filter-diagnosis').html().includes("AMD")) {
                    filters += "&diagnosis=" + 0;
                } else if ($('#js-chart-filter-diagnosis').html().includes("BRVO")) {
                    filters += "&diagnosis=" + 1;
                } else if ($('#js-chart-filter-diagnosis').html().includes("CRVO")) {
                    filters += "&diagnosis=" + 2;
                } else if ($('#js-chart-filter-diagnosis').html().includes("DMO")) {
                    filters += "&diagnosis=" + 3;
                }
            }
        }
        if ($('#js-chart-filter-service-diagnosis').html() !== "All"){
            filters += "&serviceDiagnosis="+ Object.keys(service_common_disorders).find(key => service_common_disorders[key] ===$('#js-chart-filter-service-diagnosis').html());
        }

        if ($('#js-chart-filter-protocol').html() !== "ALL") {
            filters += "&protocol=" + $('#js-chart-filter-protocol').html()
        }

        var plot_va = "&plot-va=" + $('#js-chart-filter-plot').html();


        if (specialty == "Medical Retina") {
            if ($('#js-chart-filter-treatment').html() !== "All") {
                filters += "&treatment=" + $('#js-chart-filter-treatment').html();
            }
        }

        if ($('#js-chart-filter-age').html() == "Range") {
            filters += "&age-min=" + $('#js-chart-filter-age-min').val() + "&age-max=" + $('#js-chart-filter-age-max').val()
        }
        return filters;
    }
    function plotUpdate(data){
        <?php if ($specialty === 'All'){
            if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){ ?>
                var clinical_chart = $('#js-hs-chart-analytics-clinical')[0];
                var clinical_data = data[0];
                clinical_chart.data[0]['x'] = clinical_data.x;
            clinical_chart.data[0]['y'] = clinical_data.y;
            clinical_chart.data[0]['customdata'] = clinical_data.customdata;
            clinical_chart.data[0]['text'] = clinical_data.text;
            Plotly.redraw(clinical_chart);
        <?php }}else{?>
        var custom_charts = ['js-hs-chart-analytics-clinical-others-left','js-hs-chart-analytics-clinical-others-right'];
        var custom_data = data[0];
        for (var i = 0; i < custom_charts.length; i++) {
            var chart = $('#'+custom_charts[i])[0];
            chart.layout['title'] = (i)? 'Clinical Section (Right Eye)': 'Clinical Section (Left Eye)';
            chart.data[0]['x'] = custom_data[i][0]['x'];
            chart.data[0]['y'] = custom_data[i][0]['y'];
            chart.data[0]['customdata'] = custom_data[i][0]['customdata'];
            chart.data[1]['x'] = custom_data[i][1]['x'];
            chart.data[1]['y'] = custom_data[i][1]['y'];
            chart.data[1]['customdata'] = custom_data[i][1]['customdata'];
            Plotly.redraw(chart);
        }
        <?php }?>
        //update the service data
        constructPlotlyData(data[1]);
    }
    function viewAllDates() {
        $('#analytics_datepicker_from').val("");
        $('#analytics_datepicker_to').val("");
    }
</script>
