<main class="main-event <?php echo $this->moduleStateCssClass; ?>">

  <h2 class="event-title <?= ($this->event->is_automated) ? 'auto' : '' ?>">
      <?php echo $this->title ?><?php $this->renderPartial('//patient/event_automated'); ?>
  </h2>
    <?php $this->renderPartial('//base/_messages'); ?>

    <?php if ($this->action->id == 'view' && $this->event->isEventDateDifferentFromCreated()) { ?>
      <div class="row data-row">
        <div class="large-2 column" style="margin-left: 10px;">
          <div class="data-label"><?php echo $this->event->getAttributeLabel('event_date') ?>:</div>
        </div>
        <div class="large-9 column end">
          <div class="data-value"><?php echo $this->event->NHSDate('event_date') ?></div>
        </div>
      </div>
    <?php } ?>

    <?php echo $content; ?>

    <?php if ($this->action->id == 'view') {
        $this->renderEventMetadata();
    } ?>
</main>