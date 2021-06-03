<main class="main-event <?php echo $this->moduleStateCssClass; ?>" id="event-content">


    <h2 class="event-title">
        <?php echo $this->title ?>
        <?php if ($this->event->is_automated) { ?>
            <span id="automated-event">
                <?php $this->renderPartial('//patient/event_automated'); ?>
            </span>
        <?php } ?>
        <?php if ($this->action->id === 'view') { ?>
            <i id="js-event-audit-trail-btn" class="oe-i audit-trail small pad" ></i>
        <?php } ?>
        <?php $extra_info = $this->getExtraTitleInfo();
        if ($extra_info && $extra_info !== "") : ?>
            <div class="event-title-extra-info">
                <?= $extra_info ?>
            </div>
        <?php endif; ?>
    </h2>

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
            $(document).ready(function() {
                var $date_input = $('.js-event-date-input');
                $('.js-change-event-date').on('click', function() {
                    $date_input.show();
                    $('.js-event-date').hide();
                    $('.js-change-event-date').hide();
                });

                $('.pickmeup.pmu-view-days').on('click', function() {
                    if ($(this).hasClass('pmu-hidden')) {
                        $date_input.hide();
                        $('.js-event-date').html($date_input.val());
                        $('.js-change-event-date').show();
                        $('.js-event-date').show();
                    }
                });
            });
        </script>

            <div class="extra-info">
                <small class="fade">by:</small><small>
                <?php
                    $prescribed_by = $this->event->usermodified;
                    $prescribed_date = Helper::convertDate2NHS($this->event->event_date);

                if (isset($this->event->id)) {
                    $element = Element_OphDrPrescription_Details::model()->find('event_id=?', array($this->event->id));

                    if (isset($element->authorisedByUser)) {
                        $prescribed_by = $element->authorisedByUser;
                        $prescribed_date = $element->NHSDate('authorised_date');
                    }
                }
                ?>
                <?= $prescribed_by->fullname . (isset($prescribed_by->registration_code) && $prescribed_by->registration_code !== "" ? ' ('.$prescribed_by->registration_code.')' : ''). (isset($this->event->episode->firm->cost_code) && $this->event->episode->firm->cost_code !== "" ? ' - ['.$this->event->episode->firm->cost_code.']' : '');?>
                </small>
            </div>

        <span class="extra-info js-event-date"><?= $prescribed_date ?></span>
        <i id="js-change-event-date" class=" oe-i history large pad-left js-has-tooltip js-change-event-date" data-tooltip-content="Change Event date" style="display:<?= $this->action->id === 'view' ? 'none' : 'block' ?>"></i>
    </div>

    <?php $this->renderPartial('//patient/_patient_alerts') ?>
    <?php $this->renderPartial('//base/_messages'); ?>

    <?php if ($this->event->eventType->custom_hint_text && $this->event->eventType->hint_position === 'TOP') { ?>
        <div class="alert-box info">
            <div class="user-tinymce-content">
                <?= $this->event->eventType->custom_hint_text ?>
            </div>
        </div>
    <?php }
    echo $content; ?>
    <?php if ($this->event->eventType->custom_hint_text && $this->event->eventType->hint_position === 'BOTTOM') { ?>
        <div class="alert-box info">
            <?= $this->event->eventType->custom_hint_text ?>
        </div>
    <?php }

    if ($this->action->id === 'view') {
        $this->renderEventMetadata();
    } ?>
    <?php
    $this->renderPartial('//patient/event_footer', array('form_id' => $form_id));
    ?>
</main>

<?php if ($this->action->id === 'view') : ?>
    <script type="text/javascript">
        $(function() {
            // For every eyedraw element
            $('.eyedraw').each(function() {
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
