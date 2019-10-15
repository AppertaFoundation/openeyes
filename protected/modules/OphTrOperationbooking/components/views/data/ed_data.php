<?php
?>
<div class="oe-wb-widget data-image">
    <h3><?= $this->title ?></h3>
    <div class="wb-data image-fill">
        <?php if ($this->doodles) {
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', $this->data);
        } ?>
    </div>
</div>
