<?php $assetManager = Yii::app()->getAssetManager();?>
<?php
$display_theme = SettingMetadata::model()->getSetting('display_theme');
$newblue_path = Yii::getPathOfAlias('application.assets.newblue');
$basic_assets_path = Yii::getPathOfAlias('application.assets');
Yii::app()->clientScript->registerCssFile($assetManager->getPublishedUrl($newblue_path) . '/css/eyedraw_draw_icons.min.css');
?>
<link rel="stylesheet" type="text/css" data-theme="dark"
      href="<?= $assetManager->getPublishedUrl($newblue_path) . '/css/style_oe3.0.min.css' ?>" media="<?= $display_theme !== 'dark' ? 'none' : '' ?>">
<link rel="stylesheet" type="text/css" data-theme="light"
      href="<?= $assetManager->getPublishedUrl($newblue_path) . '/css/style_oe3.0_classic.min.css' ?>" media="<?= $display_theme === 'dark' ? 'none' : '' ?>">

<link rel="stylesheet" type="text/css" data-theme="dark"
      href="<?= $assetManager->getPublishedUrl($basic_assets_path) . '/css/patient_panel.css' ?>" media="<?= $display_theme !== 'dark' ? 'none' : '' ?>">
<link rel="stylesheet" type="text/css" data-theme="light"
      href="<?= $assetManager->getPublishedUrl($basic_assets_path) . '/css/patient_panel_classic.css' ?>" media="<?= $display_theme !== 'dark' ? '' : 'none' ?>">

<?php $assetManager->registerScriptFile('js/modernizr.custom.js')?>
<?php $assetManager->registerCoreScript('jquery')?>
<?php $assetManager->registerCoreScript('jquery.ui')?>
<?php $assetManager->registerScriptFile('mustache/mustache.js', 'application.assets.components')?>
<?php $assetManager->registerScriptFile('eventemitter2/lib/eventemitter2.js', 'application.assets.components')?>
<?php $assetManager->registerScriptFile('js/jquery.printElement.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.hoverIntent.min.js')?>
<?php $assetManager->registerScriptFile('../../node_modules/autosize/dist/autosize.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.cookie.js')?>
<?php $assetManager->registerScriptFile('js/jquery.getUrlParam.js')?>
<?php $assetManager->registerScriptFile('js/jquery.query-object.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.waypoints.min.js')?>
<?php $assetManager->registerScriptFile('js/sticky.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.getUrlParam.js')?>
<?php $assetManager->registerScriptFile('js/libs/uri-1.10.2.js')?>
<?php $assetManager->registerScriptFile('js/print.js')?>
<?php $assetManager->registerScriptFile('js/buttons.js')?>
<?php $assetManager->registerScriptFile('js/comments.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Util.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Util.EventEmitter.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Sidebar.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.StickyElement.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Tooltip.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.ItemSet.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.PrescriptionDialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.LightningViewer.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.NavBtnPopup.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.NavBtnSidebar.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.NavBtnPopUp.HotList.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.Alert.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.Confirm.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Widgets.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.FieldImages.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Window.js'); ?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Form.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Search.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.CopyToClipboard.js')?>
<?php $assetManager->registerScriptFile('js/script.js')?>
<?php $assetManager->registerScriptFile('components/foundation/js/foundation.min.js');?>
<?php $assetManager->registerScriptFile('components/foundation/js/foundation/foundation.dropdown.js');?>
<?php $assetManager->registerScriptFile('components/jt.timepicker/jquery.timepicker.js');?>
<?php $assetManager->registerScriptFile('js/bootstrap-tour-standalone.min.js');?>
<?php $assetManager->registerScriptFile('js/oelauncher.js');?>
<?php $assetManager->registerScriptFile('../../node_modules/sortablejs/Sortable.min.js', 'application.assets.newblue');?>
<?php $assetManager->registerScriptFile('../../node_modules/pickmeup/js/pickmeup.js', 'application.assets.newblue');?>
<?php $assetManager->registerScriptFile('../../node_modules/tinymce/tinymce.js');?>
<?php $assetManager->registerScriptFile('../../node_modules/plotly.js-dist/plotly.js');?>
<?php $assetManager->registerScriptFile('../../node_modules/lodash/lodash.min.js');?>
