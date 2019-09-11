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
        <div class="analytics-patient-list" style="display: none;">
            <!-- drill down -->
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
                    <!-- if $cataract condition here -->
                </colgroup>
                <thead>
                    <tr>
                        <th class="drill_down_patient_list text-left" style="vertical-align: center;">Hospital No</th>
                        <th class="drill_down_patient_list text-left" style="vertical-align: center;">Name</th>
                        <th class="text-left" style="vertical-align: center;">DOB</th>
                        <th class="text-left" style="vertical-align: center;">Age</th>
                        <th class="text-left" style="vertical-align: center;">Gender</th>
                        <th class="text-left" style="vertical-align: center;">Diagnoses</th>
                        <!-- if $cataract condition here -->
                        <th class="text-left" style="vertical-align: center;">Procedures</th>
                        <!-- if $cataract condition here -->
                    </tr>
                </thead>
                <tbody id="p_list">

                </tbody>
            </table>
        </div>
        <div id="js-analytics-spinner" style="display: none;"><i class="spinner"></i></div>
    </main>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/plotly.js-dist/plotly.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_toolbox.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_plotly.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_sidebar.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_service.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_clinical.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_drill_down.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/enhancedPopupFixed.js')?>"></script>
    <script>
        window.csv_data_for_report = {};
    </script>
    <script>
        $('.oescape-icon-btns a').on('click', function(e){
            // mute a tag default behavior
            e.preventDefault();
            $('#js-analytics-spinner').show();
            $(this).addClass('selected');
            $('.icon-btn a').not(this).removeClass('selected');
            var target = this.href;
            var specialty = $(this).data('specialty');
            $('.specialty').html(specialty);
            
            $.ajax({
                url: target,
                type: "POST",
                data: {
                    "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                    "specialty": specialty
                },
                // async: false,
                success: function(response){
                    var data = JSON.parse(response);
                    // console.log(data)
                    // var data = getPHPData;
                    
                    var php_data = (function(){
                        var side_bar_user_list = JSON.parse(<?=json_encode($user_list);?>)
                        return {
                            // 'event_list': event_list,
                            'sb_user_list': side_bar_user_list,
                            // 'service_data': service_data,
                            // 'clinical_data': clinical_data,
                            // 'data_sum': data_sum,
                        }
                    })();
                    // console.log(data);
                    $('#sidebar').html(data['dom']['sidebar']);
                    // $('#plot').html(dom['dom']['plot']['service']+dom['dom']['plot']['clinical']);
                    $('#plot').html(data['dom']['plot']);
                    $('#plot').html(data['dom']['drill'])
                    // analytics_service(data['service_data'], data['data_sum'], target);
                    // analytics_clinical('', data['clinical_data'])
                    // analytics_drill_down(target);
                    analytics_sidebar(data['data'], target, php_data['sb_user_list']);
                    // var csv_data = dom['data'] ? dom['data']
                    analytics_drill_down();
                    $('#js-analytics-spinner').hide();
                }
            });
        })

        $('#js-all-specialty-tab').click();
    </script>
</body>

</html>