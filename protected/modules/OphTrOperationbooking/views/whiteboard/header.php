<?php
?>
<div class="mdl-layout__header-row">
    <span class="mdl-layout-title">
        <img src="<?= Yii::app()->assetManager->createUrl('img/_elements/graphic/OpenEyes_logo_transparent.png')?>" alt="OpenEyes logo" />
        Cataract WHO summary
    </span>
    <div class="mdl-layout-spacer"></div>
    <div>
        <?php if(is_object($this->getWhiteboard()->booking) && $this->getWhiteboard()->booking->isEditable()):?>
            <a class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored"
               id="refresh-button"
               href="/OphTrOperationbooking/whiteboard/reload/<?=$this->getWhiteboard()->event_id?>"
               title="Valid as at <?=date_create_from_format('Y-m-d H:i:s', $this->getWhiteboard()->last_modified_date)->format('j M Y H:i:s')?>">
                Refresh
                <i class="material-icons right">refresh</i>
            </a>
        <?php endif;?>
    </div>

</div>
