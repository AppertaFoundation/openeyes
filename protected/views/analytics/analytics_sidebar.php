<div class="analytics-options">

    <div class="select-analytics flex-layout">

        <h3>Select options</h3>
        <ul class="oescape-icon-btns">
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
    <?php if ($specialty !== 'Cataract'){?>
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
    <?php }?>
    <div class="specialty-options">
        <?php if ($specialty === 'Cataract') { ?>
            <div style="<?= $specialty !== 'Cataract' ? 'display: none' : '' ?>">
                <?= CHtml::dropDownList(
                    'js-chart-CA-selection', null,
                    array('PCR Risk', 'Complication Profile', 'Visual Acuity', 'Refractive Outcome'),
                    array('style' => 'font-size: 1em; width: inherit')
                ); ?>
            </div>
        <?php } else { ?>
            <div class="view-mode flex-layout">
                <?php $clinical_button_disable = true;
                if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){
                    $clinical_button_disable = false;
                }?>
                <button class="analytics-section pro-theme cols-3 <?=$clinical_button_disable? 'disabled': '';?>" id="js-btn-clinical"
                        data-section="#js-hs-chart-analytics-clinical-main"
                        data-tab="#js-charts-clinical">
                    Clinical
                </button>
                <button class="analytics-section pro-theme cols-3 selected" id="js-btn-service"
                        data-section="#js-hs-chart-analytics-service"
                        data-tab="#js-charts-service">
                    Service
                </button>
                <button class="analytics-section pro-theme cols-3" id="js-btn-custom"
                        data-section="#js-hs-chart-analytics-custom"
                        data-tab="#js-custom-data-filter">
                    Custom
                </button>
                <button class="analytics-section pro-theme cols-3 disabled" id="js-btn-research">
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
                    <li><a href="#" id="js-hs-app-new">Appointments: New</a></li>
                    <li><a href="#" id="js-hs-app-follow-up-coming">Appointments: Follow Up</a></li>
                    <li><a href="#" id="js-hs-app-follow-up-overdue" class="selected">Appointments: Delayed</a></li>
                </ul>
                <div id="js-service-data-filter"  style="display: none">
                    <h3>Service Data Filters</h3>
                    <div class="service-filters flex-layout cols-9">
                        <input type="checkbox" id="js-chart-filter-service-unbooked-only" checked>Unbooked Only
                    </div>
                </div>
            </div>

            <div id="js-custom-data-filter" style="display: none;">
                <h3>Custom Data Filters</h3>
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
            <?php if ($specialty === "Cataract") { ?>
                <div id="search-form-report-search-section"></div>
            <?php } else { ?>
                <input type="hidden" name="specialty" value="<?= $specialty; ?>">
            <?php } ?>
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
            <?php if ($specialty === "Cataract") { ?>
                <div class="row">
                    <button id="js-all-surgeons" class="pro-theme" onclick="viewAllSurgeons()">View all surgeons
                    </button>
                </div>
            <?php } ?>
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
    <?php if ($specialty === 'Cataract'){?>
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
    <?php } else{
    $filter_eye_side = array('left' => 'right', 'right' => 'left');
    foreach(array_keys($filter_eye_side) as $side){?>
    $('#js-chart-filter-eye-side-' + '<?=$side;?>').click(function () {
        if (this.checked) {
            $('#js-chart-filter-eye-side-' + '<?=$filter_eye_side[$side];?>').attr('checked', false);
            $('#js-hs-chart-analytics-custom-' + '<?=$side;?>').show();
            $('#js-hs-chart-analytics-custom-' + '<?=$filter_eye_side[$side];?>').hide();
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
        var side_bar_user_filter_content = $('#js-service-selected-filter').html();
        var filters = "&specialty=" + specialty;
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

        if ($('#js-chart-filter-protocol').html() !== "ALL") {
            filters = +"&protocol=" + $('#js-chart-filter-protocol').html()
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
        var custom_charts = ['js-hs-chart-analytics-custom-left','js-hs-chart-analytics-custom-right'];
        var custom_data = data[0];
        for (var i = 0; i < custom_charts.length; i++) {
            var chart = $('#'+custom_charts[i])[0];
            chart.data[0]['x'] = custom_data[i][0]['x'];
            chart.data[0]['y'] = custom_data[i][0]['y'];
            chart.data[0]['customdata'] = custom_data[i][0]['customdata'];
            chart.data[1]['x'] = custom_data[i][1]['x'];
            chart.data[1]['y'] = custom_data[i][1]['y'];
            chart.data[1]['customdata'] = custom_data[i][1]['customdata'];
            Plotly.redraw(chart);
        }
        <?php if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id)
            || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)){ ?>
        var clinical_chart = $('#js-hs-chart-analytics-clinical')[0];
        var clinical_data = data[1];
        clinical_chart.data[0]['x'] = clinical_data.x;
        clinical_chart.data[0]['y'] = clinical_data.y;
        clinical_chart.data[0]['customdata'] = clinical_data.customdata;
        clinical_chart.data[0]['text'] = clinical_data.text;
        Plotly.redraw(clinical_chart);
        <?php } ?>
        //update the service data
        constructPlotlyData(data[2]);
}
<?php }?>
    function viewAllDates() {
        $('#analytics_datepicker_from').val("");
        $('#analytics_datepicker_to').val("");
    }
</script>
