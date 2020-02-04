<html lang="en">

<head>
    <script type="text/javascript">
        var OpenEyes = OpenEyes || {};
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

<body>
    <nav class="analytics-header flex-layout">
        <div class="title">Analytics</div>

        <!-- exit oes and go back to previous page -->
        <div id="js-exit-analytics">
            <i class="oe-i remove-circle"></i>
        </div>
    </nav>
    <main class="oe-analytics flex-layout flex-top cols-full">
        <div class="cols-3" style="position:sticky;top:0;z-index:5;">
            <div class="analytics-options">
                <div class="select-analytics flex-layout">
                    <h3>Select options</h3>
                    <ul class="oescape-icon-btns">
                        <li class="icon-btn">
                            <a href="allsubspecialties" id="js-all-specialty-tab" class="active" data-specialty="All">All</a>
                        </li>
                        <li class="icon-btn">
                            <a href="cataract" id="js-ca-specialty-tab" class="active" data-specialty="Cataract">CA</a>
                        </li>
                        <li class="icon-btn">
                            <a href="glaucoma" id="js-gl-specialty-tab" class="active" data-specialty="Glaucoma">GL</a>
                        </li>
                        <li class="icon-btn">
                            <a href="medicalretina" id="js-mr-specialty-tab" class="active" data-specialty="Medical Retina">MR</a>
                        </li>
                    </ul>
                </div>
                <div class="specialty"></div>
                
                <div class="specialty-options" id="sidebar"></div>
            </div>
        </div>
        <div class="analytics-charts cols-9" id="plot">
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
                    <col style="width: 100px;">
                    <col style="width: 100px">
                    <col style="width: 200px;">
                    <col style="width: 100px;">
                    <col style="width: 50px;">
                </colgroup>
                <thead>
                <tr>
                    <th class="drill_down_patient_list text-left" style="vertical-align: center;">Hospital No</th>
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
    </main>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/jspdf/dist/jspdf.min.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/plotly.js-dist/plotly.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/dashboard/OpenEyes.Dash.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_toolbox.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_dataCenter.js')?>"></script>
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
    <script>
        analytics_init();
    </script>
</body>

</html>