<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_sidebar.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/enhancedPopupFixed.js')?>"></script>

<?php $this->renderPartial('//analytics/analytics_header', array()); ?>

<main class="oe-analytics flex-layout flex-top">
    <?php $this->renderPartial('//analytics/analytics_sidebar', array()); ?>
    <div class="analytics-charts">
        <?php $this->renderPartial('//analytics/analytics_clinical'); ?>
        <?php $this->renderPartial('//analytics/analytics_service'); ?>
        <?php $this->renderPartial('//analytics/analytics_custom'); ?>
    </div>
</main>