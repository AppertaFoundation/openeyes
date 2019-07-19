<html lang="en">
<head>
    <script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
    <link href="<?= Yii::app()->assetManager->createUrl('fonts/Roboto/roboto.css')?>" rel="stylesheet">
    <link href="<?= Yii::app()->assetManager->createUrl('fonts/material-design/material-icons.css')?>" rel="stylesheet">
<!--    <link rel="stylesheet" href="--><?php //= Yii::app()-assetManager-createUrl('components/material-design-lite/material.min.css')?><!--">-->
    <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('css/dashboard.css')?>">
    <link rel="stylesheet" href="<?= Yii::app()->assetManager->createUrl('components/jquery-ui/themes/base/minified/jquery.ui.datepicker.min.css')?>">

    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_sidebar.js')?>"></script>
    <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/enhancedPopupFixed.js')?>"></script>
</head>
<body>
    <nav class="analytics-header flex-layout">
        <div class="title">Analytics</div>

        <!-- exit oes and go back to previous page -->
        <div id="js-exit-analytics">
            <i class="oe-i remove-circle"></i>
        </div>
    </nav>
</body>

</html>
