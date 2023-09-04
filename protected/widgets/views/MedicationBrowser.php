<?php

if (Yii::app()->controller->action->getId() == 'ElementForm') {
    $assetManager = \Yii::app()->getAssetManager();
    $asset_folder = $assetManager->getPublishedPathOfAlias('application.widgets.js');
    echo '<script type="text/javascript" src="' . $asset_folder . '/MedicationBrowser.js"></script>';
}

?>

<button class="button hint small primary  js-add-select-search pull-right" type="button"
        id="<?php echo $this->id . "_open_btn"; ?>">
    Add
</button>
<div class="clearfix"></div>
<script type="text/javascript">
    $(function () {
        var browser = new OpenEyes.UI.MedicationBrowser({
            element: $('#<?php echo $this->id; ?>'),
            btn: $('#<?php echo $this->id; ?>_open_btn'),
            onSelected: <?php echo $this->fnOnSelected; ?>
        });
    });
</script>
