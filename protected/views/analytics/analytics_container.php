<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_sidebar.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/enhancedPopupFixed.js')?>"></script>

<?php $this->renderPartial('//analytics/analytics_header', array());?>

<main class="oe-analytics flex-layout flex-top">
    <?php $this->renderPartial('//analytics/analytics_sidebar',
        array('specialty'=>$specialty)
    ); ?>
    <div class="analytics-charts">
        <?php $this->renderPartial('//analytics/analytics_service',
            array('service_data'=>$service_data)); ?>
        <?php $this->renderPartial('//analytics/analytics_clinical',
            array('clinical_data'=>$clinical_data)); ?>
        <?php $this->renderPartial('//analytics/analytics_custom',
            array('custom_data'=> $custom_data)); ?>
    </div>
    <?php $this->renderPartial('//analytics/analytics_drill_down_list'); ?>
</main>