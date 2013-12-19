<?php $cs = Yii::app()->clientScript; ?>
<?php $cs->registerCoreScript('jquery')?>
<?php $cs->registerCoreScript('jquery.ui')?>
<?php $cs->registerCSSFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css', 'screen')?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.watermark.min.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/mustache.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/libs/uri-1.10.2.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/modernizr.custom.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/polyfills.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.printElement.min.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.hoverIntent.min.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.autosize.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.getUrlParam.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/print.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/buttons.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.Util.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.Util.EventEmitter.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.StickyElement.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Dialog.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Dialog.Alert.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Dialog.Confirm.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Widgets.js'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/script.js'))?>
