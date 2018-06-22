<?php

if(Yii::app()->controller->action->getId() == 'ElementForm'){
    $assetManager = \Yii::app()->getAssetManager();
    $asset_folder = $assetManager->publish('protected/widgets/js');
    echo '<script type="text/javascript" src="'.$asset_folder.'/MedicationBrowser.js"></script>';
}

?>

<div id="<?php echo $this->id; ?>" class="oe-add-select-search auto-width" style="display: none; height: 250px; position: absolute; bottom: 0; right: 0">
    <div class="close-icon-btn main-close-btn">
        <i class="oe-i remove-circle medium"></i>
    </div>
    <div class="select-icon-btn">
        <i class="oe-i menu selected"></i>
    </div>
    <button class="button hint green add-icon-btn" type="button">
        <i class="oe-i plus pro-theme"></i>
    </button>
    <table class="select-options" style="width: 100%">
        <tbody>
        <tr>
            <!--
            <td>
                <div class="level fixed" data-level="0">
                    <input type="text" class="column-filter" placeholder="Filter" style="width: 100%" />
                    <ul class="add-options" data-multi="false" data-clickadd="false" style="max-height: 208px">
                        <?php foreach (RefSet::getAvailableSets() as $key=>$set): ?>
                        <li data-id="<?= $set->id ?>" class="listelement">
                            <span class="restrict-width"><?= CHtml::encode($set->name) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </td>
            <td>
                <div class="level" data-level="1">
                    <input type="text" class="column-filter" placeholder="Filter" style="width: 100%" />
                    <p style="display:none" class="loader"><small>Please wait...</small></p>
                    <ul class="add-options" data-multi="false" data-clickadd="false" style="max-height: 208px">

                    </ul>
                </div>
            </td>
            -->
            <td>
                <div class="level fixed" data-level="2">
                    <ul class="add-options" data-multi="false" data-clickadd="false"  style="max-height: 208px">
                        <?php foreach (RefSet::getAvailableSets(null, null, $this->usage_code) as $key=>$set): ?>
                            <li data-id="<?= $set->id ?>" class="listelement">
                                <span class="restrict-width"><?= CHtml::encode($set->name) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </td>
            <td>
                <div class="level" data-level="3" style="width:600px;">
                    <input type="text" class="column-filter" placeholder="Search..." style="width: 100%" />
                    <p style="display:none" class="loader"><small>Please wait...</small></p>
                    <ul class="add-options" data-multi="false" data-clickadd="false" style="max-height: 208px">

                    </ul>
                </div>
            </td>

        </tr>
        </tbody>
    </table>
</div>

<button class="button hint small primary  js-add-select-search" type="button" id="<?php echo $this->id."_open_btn"; ?>">
    Add
</button>
<script type="text/javascript">
    $(function(){
        var browser = new OpenEyes.UI.MedicationBrowser({
           element: $('#<?php echo $this->id; ?>'),
           btn: $('#<?php echo $this->id; ?>_open_btn'),
           onSelected: <?php echo $this->fnOnSelected; ?>
        });
    });
</script>