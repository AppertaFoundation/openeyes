<?PHP Yii::app()->getAssetManager()->registerScriptFile('../../../node_modules/jspdf/dist/jspdf.min.js') ?>
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


