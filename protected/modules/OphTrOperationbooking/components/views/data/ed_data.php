<div class="oe-wb-widget data-image">
    <h3><?= $this->title ?></h3>
        <?php if ($this->doodles) {
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', $this->data);
        } ?>
</div>
