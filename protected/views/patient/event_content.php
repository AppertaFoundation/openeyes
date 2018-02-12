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
<script>
    $('.js-remove-element').on('click', function (e) {
        e.preventDefault();
        var parent = $(this).parent().parent();
        removeElement(parent);
    });

    $('.js-add-select-search').on('click', function (e) {
        e.preventDefault();
        $(e.target).parent().find('.oe-add-select-search').show();
    });

    $('.oe-add-select-search').find('.add-icon-btn').on('click', function (e) {
        e.preventDefault();
        $(e.target).parent('.oe-add-select-search').hide();
    });

    //Set the option selecting function
    $('.oe-add-select-search').find('.add-options').find('li').each(function () {
        if ($(this).text() !== "") {
            $(this).on('click', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    if ($(this).parent('.add-options').attr('data-multi') === "false") {
                        $(this).parent('.add-options').find('li').removeClass('selected');
                    }
                    $(this).addClass('selected');
                }
            });
        }
    });

    $(".oe-add-select-search .close-icon-btn").click(function (e) {
        $(e.target).closest('.oe-add-select-search').hide();
    });

    $('.js-add-comments').on('click', function (e) {
        e.preventDefault();
        var container = $(e.target).attr('data-input');
        $(container).show();
        $(e.target).hide();
    });
</script>

