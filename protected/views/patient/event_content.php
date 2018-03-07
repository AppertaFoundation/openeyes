<main class="main-event <?php echo $this->moduleStateCssClass; ?>" id="event-content">

  <h2 class="event-title">
      <?php echo $this->title ?>
      <?php if ($this->event->is_automated) { ?>
        <span id="automated-event">
          <?php $this->renderPartial('//patient/event_automated'); ?>
        </span>
      <?php } ?>
    <?php if ($this->action->id === 'view') { ?>
      <i id="js-event-audit-trail-btn" class="oe-i audit-trail small pad"></i>
    <?php } ?>
  </h2>
    <?php $this->renderPartial('//base/_messages'); ?>

    <?php if ($this->action->id === 'view' && $this->event->isEventDateDifferentFromCreated()) { ?>
      <section class="element view full view-date">
        <header class="element-header">
          <h3 class="element-title"><?php echo $this->event->getAttributeLabel('event_date') ?></h3>
        </header>
        <div class="element-fields full-width">
          <div>
              <?php echo $this->event->NHSDate('event_date') ?>
          </div>
        </div>
      </section>
    <?php } ?>

    <?php echo $content; ?>

    <?php if ($this->action->id === 'view') {
        $this->renderEventMetadata();
    } ?>
</main>

