<main class="main-event <?php echo $this->moduleStateCssClass; ?>" id="event-content">
    <?php echo $content; ?>
    <?php if(isset($this->event) && $this->event->deleted) {?>
        <div class="removed-record-watermark">Removed record - Do not use</div>
    <?php }?>
</main>
