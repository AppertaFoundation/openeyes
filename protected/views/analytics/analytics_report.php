<html lang="en">

<head>
    <script type="text/javascript">
        if(typeof(OpenEyes) === 'undefined'){
            let OpenEyes = OpenEyes || {};
        }
    </script>
    <link href="<?= Yii::app()->assetManager->createUrl('fonts/Roboto/roboto.css')?>" rel="stylesheet">
    <link href="<?= Yii::app()->assetManager->createUrl('fonts/material-design/material-icons.css')?>" rel="stylesheet">
    <!--    <link rel="stylesheet" href="--><?php //= Yii::app()-assetManager-createUrl('components/material-design-lite/material.min.css')?>
    <!--">-->
    <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('css/dashboard.css')?>">
    <link rel="stylesheet"
        href="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/themes/base/minified/jquery.ui.datepicker.min.css')?>">
    <style>
        #js-analytics-spinner{
            position: absolute;
            overflow:hidden;
            background: rgba(0, 0, 0, .3);
            z-index: 9999;
            width: 100%;
            height: 100%;
        }
        html{
            overflow:hidden;
        }
        .oe-err-msg{
            background-color: red;
            color: white;
            font-size: 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            padding: 20px;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body class="open-eyes oe-grid">
    <div class="oe-full-header use-full-screen">
        <div class="title lightcaps">
            <span class="oe-area">Analytics</span>
            <ul class="oescape-icon-btns">
                <li class="icon-btn">
                    <a href="#" data-link="allsubspecialties" id="js-all-specialty-tab" class="inactive" data-specialty="All">All</a>
                </li>
                <li class="icon-btn">
                    <a href="#" data-link="cataract" id="js-ca-specialty-tab" class="inactive" data-specialty="Cataract">CA</a>
                </li>
                <li class="icon-btn">
                    <a href="#" data-link="glaucoma" id="js-gl-specialty-tab" class="inactive" data-specialty="Glaucoma">GL</a>
                </li>
                <li class="icon-btn">
                    <a href="#" data-link="medicalretina" id="js-mr-specialty-tab" class="inactive" data-specialty="Medical Retina">MR</a>
                </li>
            </ul>
            <span id="specialty"></span>
        </div>
    </div>
    <div class="oe-full-content subgrid wide-side-panel analytics-v2 use-full-screen">
        <nav class="oe-full-side-panel analytics-options-v2">
            <div class="specialty-options" id="sidebar">
            </div>
        </nav>
        <main class="oe-full-main">
            <div class="flex-layout flex-top">
                <div class="cols-full" id="plot">
                    <!-- <div class="mdl-layout__container"></div> -->
                </div>
                <!-- if $cataract condition in the div class -->
                <div class="analytics-patient-list" style="display: none;" >
                    <div class="flex-layout">
                        <h3 id="js-list-title">Patient List</h3>
                        <button id="js-back-to-chart" class="selected js-plot-display-label" >Back to chart</button>
                    </div>
                    <table>
                        <colgroup>
                            <col style="width: 7%;">
                            <col style="width: 7%">
                            <col style="width: 14%;">
                            <col style="width: 7%;">
                            <col style="width: 3.5%;">
                            <col style="width: 3.5%;">
                            <col style="width: 24%;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Hospital No</th>
                            <th class="drill_down_patient_list text-left" style="vertical-align: center;">NHS No</th>
                            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Name</th>
                            <th class="text-left" style="vertical-align: center;">DOB</th>
                            <th class="text-left" style="vertical-align: center;">Age</th>
                            <th class="text-left" style="vertical-align: center;">Gender</th>
                            <th class="text-left" style="vertical-align: center;">Diagnoses</th>
                            <th class="text-left patient_procedures" style="vertical-align: center;">Procedures</th>
                        </tr>
                        </thead>
                        <tbody id="p_list">

                        </tbody>
                    </table>
                </div>
                <div id="js-analytics-spinner" style="display: none;"><i class="spinner"></i></div>
            </div>
        </main>
    </div>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/jspdf/dist/jspdf.min.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/plotly.js-dist/plotly.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_toolbox.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_dataCenter.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/dashboard/OpenEyes.Dash.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_sidebar.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_custom.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_service.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_clinical.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv_cataract.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_cataract.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_drill_down.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_init.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/enhancedPopupFixed.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('newblue/plotlyJS/oePlotly_v1.js')?>"></script>
    <script>
        analytics_init();
    </script>
</body>

</html>
