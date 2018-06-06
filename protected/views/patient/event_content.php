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
      <?php if ($this->hasExtraTitleInfo()): ?>
        <div class="event-title-extra-info">
            <?= $this->getExtraTitleInfo(); ?>
        </div>
      <?php endif; ?>
  </h2>
    <div class="event-title-extra-info">
        <span class="extra-info"><?php
            $months = ['Jan', 'Feb', 'Mar','Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] ;
            echo date("d") .' '. $months[date("m")-1]. ' '.date("Y");?></span>
    </div>
    <?php $this->renderPartial('//patient/_patient_alerts') ?>
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

<?php if ($this->action->id === 'view'): ?>
  <script type="text/javascript">
    $(function () {
      // For every eyedraw element
      $('.eyedraw').each(function () {
        // find it's "twin" element
        var $other = $(this).closest('.element').find('.eyedraw').not(this);
        // and scale up this eyedraw element if it is smaller than the twin
        if ($(this).height() < $other.height()) {
          $(this).css('height', $other.height());
        }
      })
    });
  </script>
<?php endif; ?>
