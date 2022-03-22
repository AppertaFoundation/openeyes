<?php $assetManager = Yii::app()->getAssetManager();?>
<?php
if (isset(Yii::app()->params['image_generation']) && Yii::app()->params['image_generation']) {
    $display_theme = 'dark';
} else {
    $user_theme = SettingUser::model()->find('user_id = :user_id AND `key` = "display_theme"', array(":user_id" => Yii::app()->user->id));
    $display_theme = $user_theme ? SettingMetadata::model()->getSetting('display_theme') : Yii::app()->params['image_generation'];
}
$newblue_path = Yii::getPathOfAlias('application.assets.newblue');
$basic_assets_path = Yii::getPathOfAlias('application.assets');
Yii::app()->clientScript->registerCssFile($assetManager->getPublishedUrl($newblue_path, true) . '/dist/css/style_eyedraw_doodles.css');
?>
<link rel="stylesheet" type="text/css" data-theme="dark"
      href="<?= $assetManager->getPublishedUrl($newblue_path, true) . '/dist/css/style_oe_dark.3.css' ?>" media="<?= $display_theme !== 'dark' ? 'none' : '' ?>">
<link rel="stylesheet" type="text/css" data-theme="light"
      href="<?= $assetManager->getPublishedUrl($newblue_path, true) . '/dist/css/style_oe_light.3.css' ?>" media="<?= $display_theme === 'dark' ? 'none' : '' ?>">

<?php $assetManager->registerScriptFile('js/modernizr.custom.js')?>
<?php $assetManager->registerCoreScript('jquery')?>
<?php $assetManager->registerCoreScript('jquery.ui')?>
<?php $assetManager->registerScriptFile('../../node_modules/mustache/mustache.min.js');?>
<?php $assetManager->registerScriptFile('eventemitter2/lib/eventemitter2.js', 'application.assets.components')?>
<?php $assetManager->registerScriptFile('js/jquery.printElement.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.hoverIntent.min.js')?>
<?php $assetManager->registerScriptFile('../../node_modules/autosize/dist/autosize.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.cookie.js')?>
<?php $assetManager->registerScriptFile('js/jquery.getUrlParam.js')?>
<?php $assetManager->registerScriptFile('js/jquery.query-object.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.getUrlParam.js')?>
<?php $assetManager->registerScriptFile('js/libs/uri-1.10.2.js')?>
<?php $assetManager->registerScriptFile('js/print.js')?>
<?php $assetManager->registerScriptFile('js/buttons.js')?>
<?php $assetManager->registerScriptFile('js/comments.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Util.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Util.EventEmitter.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.DOM.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.ImageAnnotator.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Sidebar.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Tooltip.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.LoadingOverlay.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.ItemSet.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.PrescriptionDialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.Util.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.QuerySearchDialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.AdderDialog.MedSearch.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.InputFieldValidation.js');?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.LightningViewer.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.NavBtnPopup.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.NavBtnSidebar.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.NavBtnPopUp.HotList.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.Alert.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.Confirm.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Widgets.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.FieldImages.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Window.js'); ?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.ElementController.js'); ?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.ElementController.MultiRow.js'); ?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Form.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Search.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.CopyToClipboard.js')?>
<?php $assetManager->registerScriptFile('js/script.js')?>
<?php $assetManager->registerScriptFile('components/jt.timepicker/jquery.timepicker.js');?>
<?php $assetManager->registerScriptFile('js/oelauncher.js');?>
<?php $assetManager->registerScriptFile('../../node_modules/sortablejs/Sortable.min.js', 'application.assets.newblue');?>
<?php $assetManager->registerScriptFile('../../node_modules/pickmeup/js/pickmeup.js', 'application.assets.newblue');?>
<?php $assetManager->registerScriptFile('../../node_modules/tinymce/tinymce.js');?>
<?php $assetManager->registerScriptFile('../../node_modules/lodash/lodash.min.js');?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.PathwayStepPicker.js'); ?>
<?php $assetManager->registerScriptFile('js/worklist/OpenEyes.UI.Dialog.PathwayStepOptions.js'); ?>
<?php $assetManager->registerScriptFile('js/worklist/OpenEyes.UI.Dialog.NewPathwayStep.js'); ?>
