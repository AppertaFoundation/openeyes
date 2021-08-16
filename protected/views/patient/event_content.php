<?php
/**
 * @var $content string
 * @var $form_id string
 */
?>
<main class="main-event <?php echo $this->moduleStateCssClass; ?>" id="event-content" data-has-errors="<?=($has_errors ?? 'false') ?>">

    <h2 class="event-title">
        <?php echo $this->title ?>
        <?php if ($this->event->is_automated) {
            $this->renderPartial('//patient/event_automated');
        } ?>
        <?php if ($this->action->id === 'view') { ?>
        <i id="js-event-audit-trail-btn" class="oe-i audit-trail small pad"></i>
        <?php } ?>

    </h2>
    <?php $extra_info = $this->getExtraTitleInfo(); ?>
    <div class="event-title-extra-info flex-layout">

        <?php if ($this->title !== 'Please select booking') { ?>
            <?php if (isset($this->event->firm)) : ?>
                <div class="extra-info" style="font-size:105%">
                    <small class="fade">Institution: </small> <?=$this->event->institution;?>
                </div>
                <div class="extra-info" style="font-size:105%">
                    <small class="fade">Site: </small> <?=$this->event->site ?? '-';?>
                </div>
                <div class="extra-info" style="font-size:105%">
                    <small class="fade">Subspecialty: </small>
                    <small><?= $this->event->firm->serviceSubspecialtyAssignment->subspecialty->name ?></small>
                </div>
                <div class="extra-info">
                    <small class="fade">&nbsp;Context: </small>
                    <small><?= $this->event->firm->name ?></small>
                </div>
            <?php endif; ?>

            <?php if ($extra_info && $extra_info !== "") : ?>
                <?= $extra_info ?>
            <?php endif; ?>

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
                    const $date_input = $('.js-event-date-input');
                    $('.js-change-event-date').on('click', function() {
                        if (!$(this).hasClass('disabled')) {
                            $date_input.show();
                            $date_input.select();
                            $('.js-event-date').hide();
                            $('.js-change-event-date').hide();
                        }
                    });

                    $('.pickmeup.pmu-view-days').on('click', function() {
                        if ($(this).hasClass('pmu-hidden')) {
                            $date_input.hide();
                            const $event_date = $('.js-event-date')
                            $event_date .html($date_input.val());
                            $('.js-change-event-date').show();
                            $event_date.show();
                        }
                    });
                });
            </script>

          <span class="extra-info js-event-date"><?= Helper::convertDate2NHS($this->event->event_date) ?></span>
        <span class="js-has-tooltip" data-tooltip-content="Change Event date">
            <i class="oe-i history large pad-left js-change-event-date"
               style="display:<?= $this->action->id === 'view' ? 'none' : 'block' ?>"></i>
        </span>
        <?php } ?>
        </div>

    <?php $this->renderPartial('//patient/_patient_alerts') ?>
    <?php $this->renderPartial('//base/_messages'); ?>
    <?php if (
        $this->event->eventType->custom_hint_text
        && $this->event->eventType->hint_position === 'TOP'
        && in_array($this->action->id, array('create', 'update'))
) { ?>
        <div class="alert-box info">
            <div class="user-tinymce-content">
                <?= $this->event->eventType->custom_hint_text ?>
            </div>
        </div>
    <?php }
    echo $content; ?>
    <?php if (
        $this->event->eventType->custom_hint_text
        && $this->event->eventType->hint_position === 'BOTTOM'
        && in_array($this->action->id, array('create', 'update'))
) { ?>
        <div class="alert-box info">
            <div class="user-tinymce-content">
                <?= $this->event->eventType->custom_hint_text ?>
            </div>
        </div>
    <?php }
    if ($this->action->id === 'view') {
        $this->renderEventMetadata();
    }

    $this->renderPartial('//patient/event_footer', array('form_id' => $form_id));
    ?>
</main>

<?php if ($this->action->id === 'view') : ?>
    <script type="text/javascript">
        $(function() {
            // For every eyedraw element
            $('.eyedraw').each(function() {
                // find it's "twin" element
                const $other = $(this).closest('.element').find('.eyedraw').not(this);
                // and scale up this eyedraw element if it is smaller than the twin
                if ($(this).height() < $other.height()) {
                    $(this).css('height', $other.height());
                }
            })
        });
    </script>
<?php endif; ?>
