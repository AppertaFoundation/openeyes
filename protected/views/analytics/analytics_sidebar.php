            <div class="view-mode flex-layout">
                <?php $clinical_button_disable = true;
                if (Yii::app()->authManager->isAssigned('View clinical', Yii::app()->user->id) || Yii::app()->authManager->isAssigned('Service Manager', Yii::app()->user->id)) {
                    $clinical_button_disable = false;
                }?>
                <button class="analytics-section pro-theme cols-6 <?=$clinical_button_disable? 'disabled': '';?>" id="js-btn-clinical"
                        data-section="#js-hs-chart-analytics-clinical-main"
                        data-tab="#js-charts-clinical"
                        data-options="clinical">
                            Clinical
                </button>
                <button class="analytics-section pro-theme cols-6 selected" id="js-btn-service"
                        data-section="#js-hs-chart-analytics-service"
                        data-tab="#js-charts-service"
                        data-options="service">
                    Service
                </button>
            </div>
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
                    <?php if ($specialty !== "All") {?>
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
                                <?php if (isset($user_list)) {?>
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
            <?php if ($specialty !== 'All') {?>
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
                            <?php if (isset($user_list)) {?>
                                <li>User:
                                    <span class="service-selected js-hs-filters js-hs-surgeon"
                                          id="js-chart-filter-clinical-surgeon-custom"
                                          data-name="custom_surgeon">All</span>
                                </li>
                            <?php } else {?>
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
                            <?php if ($specialty == "Medical Retina") { ?>
                                <li>Treatment: <span id="js-chart-filter-treatment" class="js-hs-filters js-hs-custom-mr-treatment" data-name="custom_treatment">All</span></li>
                                <li>Diagnosis: <span id="js-chart-filter-diagnosis" class="js-hs-filters js-hs-custom-mr-diagnosis" data-name="custom_diagnosis">All</span></li>
                                <li>Plot: <span id="js-chart-filter-plot" class="js-hs-filters js-hs-custom-mr-plot-type" data-name="custom_plot">VA (change)</span></li>
                                <li>Protocol: <span id="js-chart-filter-protocol" class="js-hs-filters" data-name="custom_protocol">ALL</span></li>
                            <?php } else { ?>
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
                <li><a href="#" id="js-hs-app-follow-up-coming" class="js-plot-display-label" data-report="coming">Followups coming due</a></li>
                <li><a href="#" id="js-hs-app-follow-up-overdue" class="selected js-plot-display-label" data-report="overdue">Overdue followups</a></li>
                <li><a href="#" id="js-hs-app-follow-up-waiting" class="js-plot-display-label" data-report="waiting">Waiting time for new patients</a></li>
            </ul>
            <div id="js-service-data-filter" class="" style="display: block">
                <div class="service-filters custom-filters">
                    <div class="flex-item" style="position: relative; top: -25px; left: 250px;"><!-- OE UI Filter options (id: service-filters) -->
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
                                            <?php foreach ($common_disorders as $diagnosis => $term) { ?>
                                                <li><?= $term['term']; ?></li>
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
            <button id="js-download-csv" data-anonymised="0" class="pro-theme cols-full">Download (CSV)</button>
            <button id="js-download-anonymized-csv" data-anonymised="1" class="pro-theme cols-full">Download (CSV - Anonymised)</button>
        </div>

