<?PHP Yii::app()->getAssetManager()->registerScriptFile('../../../node_modules/jspdf/dist/jspdf.min.js') ?>
    <style>
        .download-csv-container{
            position: relative;
            padding: 0;
        }a
        #js-download-csv, #js-download-anonymized-csv{
            position: absolute;
            height: 100%;
            width: 100%;
            line-height: 24px;
        }
        #js-download-csv:hover, #js-download-anonymized-csv:hover{
            color: white;
        }
    </style>
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
                <li><a href="#" data-container="#pcr-risk-grid" data-report="PCR" class="js-cataract-report-type selected">PCR Risk</a></li>
                <li><a href="#" data-container="#cataract-complication-grid" data-report="CP" class="js-cataract-report-type">Complication Profile</a></li>
                <li><a href="#" data-container="#visual-acuity-grid" data-report="VA" class="js-cataract-report-type">Visual Acuity</a></li>
                <li><a href="#" data-container="#refractive-outcome-grid" data-report="RO" class="js-cataract-report-type">Refractive Outcome</a></li>
                <li><a href="#" data-container="#nod-audit-grid" data-report="NOD" class="js-cataract-report-type">NOD Audit</a></li>
                <li><a href="#" data-container="#catprom5-preop-grid" data-report="C5A" class="js-cataract-report-type">Cat-PROM5 pre-op</a></li>
                <li><a href="#" data-container="#catprom5-postop-grid" data-report="C5B" class="js-cataract-report-type">Cat-PROM5 post-op</a></li>
                <li><a href="#" data-container="#catprom5-diff-grid" data-report="C5C" class="js-cataract-report-type">Cat-PROM5 difference </a></li>
            </ul>
            <form id="search-form" autocomplete="off">
                <div id="search-form-report-search-section"></div>
                <h3>Filter by Date</h3>
                <div class="flex-layout">
                    <div id="js-common-filters-service-clinical">
                        <input name="from" type="text" class="pro-theme cols-5"
                               id="analytics_datepicker_from"
                               value=""
                               placeholder="from" autocomplete="off">
                        <input type="text" class="pro-theme cols-5"
                               id="analytics_datepicker_to"
                               value=""
                               name="to"
                               placeholder="to" autocomplete="off">
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
                    <button id="js-all-surgeons" <?=$isServiceMgr ? '' : 'style="display:none;"'?> class="pro-theme" type="button" onclick="viewAllSurgeons()">View all surgeons</button>
                </div>
                <button class="pro-theme green hint cols-full update-chart-btn" type="submit">Update Chart</button>
            </form>
        </div>
        <div class="extra-actions">
            <button id="js-download-csv" class="pro-theme cols-full download-csv-container">
                <a id="js-download-csv">Download (CSV)</a>
            </button>
        </div>
        <div class="extra-actions">
            <button id="js-download-pdf" class="pro-theme cols-full">Download All Plots as PDF</button>
        </div>
