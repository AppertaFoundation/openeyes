<?php $assetManager = Yii::app()->getAssetManager();?>
<?php $assetManager->registerScriptFile('js/modernizr.custom.js')?>
<?php $assetManager->registerCoreScript('jquery')?>
<?php $assetManager->registerCoreScript('jquery.ui')?>
<?php $assetManager->registerScriptFile('mustache/mustache.js', 'application.assets.components')?>
<?php $assetManager->registerScriptFile('eventemitter2/lib/eventemitter2.js', 'application.assets.components')?>
<?php $assetManager->registerScriptFile('js/jquery.printElement.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.hoverIntent.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.autosize.js')?>
<?php $assetManager->registerScriptFile('js/jquery.cookie.js')?>
<?php $assetManager->registerScriptFile('js/jquery.getUrlParam.js')?>
<?php $assetManager->registerScriptFile('js/jquery.waypoints.min.js')?>
<?php $assetManager->registerScriptFile('js/sticky.min.js')?>
<?php $assetManager->registerScriptFile('js/jquery.getUrlParam.js')?>
<?php $assetManager->registerScriptFile('js/libs/uri-1.10.2.js')?>
<?php $assetManager->registerScriptFile('js/print.js')?>
<?php $assetManager->registerScriptFile('js/buttons.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Util.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Util.EventEmitter.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Sidebar.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.StickyElement.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Tooltip.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.Alert.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.Confirm.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Widgets.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.FieldImages.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Window.js'); ?>
<?php $assetManager->registerScriptFile('js/OpenEyes.Form.js')?>
<?php $assetManager->registerScriptFile('js/OpenEyes.UI.Search.js')?>
<?php $assetManager->registerScriptFile('js/script.js')?>
<?php $assetManager->registerScriptFile('components/foundation/js/foundation.min.js');?>
<?php $assetManager->registerScriptFile('components/foundation/js/foundation/foundation.dropdown.js');?>
<?php $assetManager->registerScriptFile('components/jt.timepicker/jquery.timepicker.js');?>
<?php $assetManager->registerScriptFile('js/bootstrap-tour-standalone.min.js');?>
<?php $assetManager->registerScriptFile('js/oelauncher.js');?>
<?php $assetManager->registerScriptFile('js/idg-oe.min.js', 'application.assets.newblue');?>
<?php $newBlue = $assetManager->publish(Yii::getPathOfAlias('application.assets.newblue')); ?>
<script>
    (function(){
        /* IDG demo only. Replace with a more permanent solution. */
        // use localStorage for CSS Themes Switching
        var css = "style_oe3.0_classic.min.css"; // default Classic theme (until they get used to PRO! ;)
        if(localStorage.getItem("oeTheme")){
            var theme = localStorage.getItem("oeTheme");
            if(theme === 'pro'){
                css = "style_oe3.0.min.css";
            }
        }
        // build CSS <link>
        document.write('<link rel="stylesheet" type="text/css" href="<?php echo $newBlue; ?>/css/'+ css + '">');
    })();
</script>
