<?php $assetManager = Yii::app()->getAssetManager();?>
<?php
if (isset(Yii::app()->params['image_generation']) && Yii::app()->params['image_generation']) {
    $display_theme = 'dark';
} else {
    $user_theme = SettingUser::model()->find('user_id = :user_id AND `key` = "display_theme"', array(":user_id" => Yii::app()->user->id));
    $display_theme = $user_theme ? SettingMetadata::model()->getSetting('display_theme') : Yii::app()->params['image_generation'];
}

$newblue_path = $assetManager->getPublishedPathOfAlias('application.assets.nxblu');
Yii::app()->clientScript->registerCssFile($newblue_path . '/dist/css/style_eyedraw_doodles.css');
?>
<link href="<?= $newblue_path ?>/dist/css/style_openeyes.css" rel="stylesheet" media="screen">
<link href="<?= $newblue_path ?>/dist/css/style_block-browser-print.css" rel="stylesheet" media="print">

<!-- preload common custom fonts -->
<link rel="preload" href="<?= $newblue_path ?>/dist/fonts/roboto-subset/100-thin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= $newblue_path ?>/dist/fonts/roboto-subset/300-light.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= $newblue_path ?>/dist/fonts/roboto-subset/400-latin-greek.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= $newblue_path ?>/dist/fonts/roboto-subset/500-latin-greek.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= $newblue_path ?>/dist/fonts/roboto-subset/700-latin-greek.woff2" as="font" type="font/woff2" crossorigin>

<?php $assetManager->registerScriptFile('js/modernizr.custom.js')?>
<?php $assetManager->registerCoreScript('jquery')?>
<?php $assetManager->registerCoreScript('jquery.ui')?>
<?php $assetManager->registerCoreScript('mustache');?>
<?php $assetManager->registerCoreScript('sortable');?>
<?php $assetManager->registerCoreScript('pickmeup');?>
<?php $assetManager->registerCoreScript('tinymce');?>
<?php $assetManager->registerCoreScript('lodash');?>
<?php $assetManager->registerScriptFile('eventemitter2/lib/eventemitter2.js', 'application.assets.components')?>
<?php $assetManager->registerScriptFile('js/jquery.printElement.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.hoverIntent.min.js')?>
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
<?php $assetManager->registerScriptFile('js/oelauncher.js');?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.PathwayStepPicker.js'); ?>
<?php $assetManager->registerScriptFile('js/worklist/OpenEyes.UI.Dialog.PathwayStepOptions.js'); ?>
<?php $assetManager->registerScriptFile('js/worklist/OpenEyes.UI.Dialog.NewPathwayStep.js'); ?>
