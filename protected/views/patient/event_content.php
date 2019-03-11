<main class="main-event <?php echo $this->moduleStateCssClass; ?>" id="event-content">


  <h2 class="event-title">
      <?php echo $this->title ?>
      <?php if ($this->event->is_automated) {
          echo " - ";
          $this->renderPartial('//patient/event_automated');
      } ?>
      <?php if ($this->action->id === 'view') { ?>
        <i id="js-event-audit-trail-btn" class="oe-i audit-trail small pad"></i>
      <?php } ?>
      <?php if ($this->hasExtraTitleInfo()): ?>
        <div class="event-title-extra-info">
            <?= $this->getExtraTitleInfo(); ?>
        </div>
      <?php endif; ?>
  </h2>
    <?php if ($this->title != 'Please select booking') { ?>
        <div class="event-title-extra-info flex-layout">
            <?php $errors = $this->event->getErrors();
            $error_class = isset($errors['event_date']) ? 'error' : '';
            ?>
            <?php
            $this->widget('application.widgets.DatePicker', array(
                'element' => $this->event,
                'name' => CHtml::modelName($this->event) . "[event_date]",
                'field' => 'event_date',
                'options' => array('maxDate' => 'today'),
                'htmlOptions' => array(
                    'style' => 'display:none;',
                    'form' => $form_id,
                    'nowrapper' => true,
                    'class' => 'js-event-date-input ' . $error_class
                ),
                'layoutColumns' => array(
                    'label' => 2,
                    'field' => 2,
                ),
            ));
            ?>
            <script>
                $(document).ready(function () {
                    var $date_input = $('.js-event-date-input');
                    $('.js-change-event-date').on('click', function () {
                        $date_input.show();
                        $('.js-event-date').hide();
                        $('.js-change-event-date').hide();
                    });

                    $('.pickmeup.pmu-view-days').on('click', function () {
                        if ($(this).hasClass('pmu-hidden')) {
                            $date_input.hide();
                            $('.js-event-date').html($date_input.val());
                            $('.js-change-event-date').show();
                            $('.js-event-date').show();
                        }
                    });
                });
            </script>

            <span class="extra-info js-event-date"><?= Helper::convertDate2NHS($this->event->event_date) ?></span>
            <i class="oe-i history large pad-left js-has-tooltip js-change-event-date"
               data-tooltip-content="Change Event date"
               style="display:<?= $this->action->id === 'view' ? 'none' : 'block' ?>"></i>
        </div>
    <?php } ?>
    <?php $this->renderPartial('//patient/_patient_alerts') ?>
    <?php $this->renderPartial('//base/_messages'); ?>

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
