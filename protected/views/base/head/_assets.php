<?php $cs = Yii::app()->clientScript; ?>
<?php $cs->registerCoreScript('jquery')?>
<?php $cs->registerCoreScript('jquery.ui')?>
<?php $cs->registerCSSFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css', 'screen')?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.watermark.min.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/mustache.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/libs/uri-1.10.2.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/modernizr.custom.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/polyfills.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.printElement.min.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.hoverIntent.min.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.autosize.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.getUrlParam.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/print.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/buttons.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.Util.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.Util.EventEmitter.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.StickyElement.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Dialog.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Dialog.Alert.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/OpenEyes.UI.Dialog.Confirm.js?busted'))?>
<?php $cs->registerScriptFile(Yii::app()->createUrl('js/script.js?busted'))?>
