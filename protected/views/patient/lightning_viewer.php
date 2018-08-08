<?php

/**
 * @var string $selectedPreviewType
 * @var array $previewGroups
 * @var array $previewsByYear
 */

$navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-nav-icons.svg';

?>

<?php $this->renderPartial('//patient/episodes_sidebar'); ?>

<main class="oe-lightning-viewer">
  <div class="lightning-timeline">
    <div class="timeline-options js-lightning-options">

      <div class="lightning-btn js-lightning-options-btn">
        <svg viewBox="0 0 30 30" class="lightning-icon">
          <use xlink:href="<?= $navIconUrl ?>#lightning-viewer-icon"></use>
        </svg>
        <i class="oe-i small-icon arrow-down-bold"></i>
      </div>

      <div class="change-timeline js-change-timeline" style="display: none;">
        <ul>
            <?php
            $previewTypes = array_keys($previewGroups);
            sort($previewTypes);

            foreach ($previewTypes as $previewType) {
                $events = $previewGroups[$previewType];
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <li class="<?php if ($previewType === $selectedPreviewType): ?>selected<?php endif; ?>">
                <i class="oe-i-e <?= $events[0]->eventType->getEventIconCssClass() ?>"></i>
                <a href="<?= Yii::app()->createUrl('/patient/lightningViewer',
                    array('id' => $this->patient->id, 'preview_type' => $previewType)) ?>"
                >
                    <?= $previewType ?> (<?= count($events) ?>)
                </a>
              </li>
            <?php } ?>
        </ul>
      </div>
    </div>

    <div class="timeline">
      <table>
        <tbody>
        <tr>
            <?php foreach ($previewsByYear as $year => $events) {
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <td class="date-divider js-timeline-date" data-year="<?= $year ?>">
                  <?= $year ?>
                <i class="oe-i small collapse"></i>
              </td>
            <?php } ?>
        </tr>
        <tr>
            <?php
            foreach ($previewsByYear as $year => $events) {
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <td>
                <div class="icon-group js-icon-group" data-year="<?= $year ?>">
                    <?php foreach ($events as $event) { ?>
                      <span class="icon-event js-lightning-view-icon"
                            data-event-id="<?= $event->id ?>"
                      >
                        <i class="oe-i-e <?= $event->eventType->getEventIconCssClass() ?>"></i>
                      </span>
                    <?php } ?>
                </div>
                <div class="js-icon-group-count" data-year="<?= $year ?>" style="display: none;">
                  (<?= count($events) ?>)
                </div>
              </td>
            <?php } ?>
        </tr>
        </tbody>
      </table>
    </div>

  </div>

  <div class="flex-layout flex-left flex-top">
    <div class="oe-lightning-meta js-lightning-meta">
      <div class="letter-type js-lightning-preview-type"></div>
      <div class="date oe-date js-lightning-date"></div>
      <div class="help">
        swipe to scan | click to lock
      </div>
    </div>
    <div class="oe-lightning-quick-view js-lightning-view-image-container">
        <?php foreach ($previewsByYear as $year => $events) {
            foreach ($events as $event) {
                $eventImages = EventImage::model()->findAll('event_id = ?', array($event->id));
                ?>
              <div class="js-lightning-image-preview flex-layout"
                   data-event-id="<?= $event->id ?>"
                   data-image-count="<?= count($eventImages) ?>"
                   data-paged="<?= isset($eventImages[0]) && $eventImages[0]->page !== null ?>"
                   data-preview-type="<?= $selectedPreviewType ?>"
                   data-date="<?= CHtml::encode(Helper::convertDate2HTML($event->event_date)) ?>"
                   style="display: none">
                  <?php

                  foreach ($eventImages as $eventImage) { ?>
                    <img class="js-lightning-image-preview-page"
                         src="<?= $eventImage->getImageUrl() ?>"
                         style="width: 800px; <?php if ($eventImage->page): ?>display: none;<?php endif; ?>"
                         alt="No preview available at this time"
                         <?php if ($eventImage->page !== null): ?>data-page-number="<?= $eventImage->page ?>"<?php endif; ?>
                    />
                  <?php } ?>
              </div>
            <?php }
        } ?>
    </div>

  </div>
</main>

<script>
  $('.js-lightning-image-preview').first().show();

  $(function () {

    var selectedEventId = null;

    function changePreview(event_id) {
      $('.js-lightning-image-preview').hide();
      var $preview = $('.js-lightning-image-preview[data-event-id="' + event_id + '"]');
      $preview.show();

      var $meta = $('.js-lightning-meta');
      $meta.find('.js-lightning-preview-type').text($preview.data('preview-type'));
      $meta.find('.js-lightning-date').html($preview.data('date'));

      if ($preview.data('paged')) {
        $preview.find('.js-lightning-image-preview-page').hide();
        $preview.find('.js-lightning-image-preview-page').first().show();
      } else {
        $preview.find('.js-lightning-image-preview-page').show();
      }
      return $preview;
    }

    function changePreviewPage(event_id, page) {
      var $preview = $('.js-lightning-image-preview[data-event-id="' + event_id + '"]');
      if (page !== null) {
        $preview.find('.js-lightning-image-preview-page').hide();
        $preview.find('.js-lightning-image-preview-page[data-page-number="' + page + '"]').show();
      }
    }

    $(this).on('mouseover', '.js-lightning-view-icon', function () {
      if (selectedEventId === null) {
        changePreview($(this).data('event-id'));
      }
    });

    $(this).on('mouseout', '.js-lightning-view-icon', function () {
      $('.icon-event').removeClass('js-hover');
    });

    $(this).on('click', '.js-lightning-view-icon', function () {
      var event_id = $(this).data('event-id');
      if (selectedEventId === event_id) {
        deselectPreview();
      }
      else {
        deselectPreview();
        selectPreview(event_id);
      }
    });

    function selectPreview(event_id) {
      $('.js-lightning-view-icon[data-event-id="' + event_id + '"]').addClass('selected');
      selectedEventId = event_id;
      changePreview(selectedEventId);
    }

    function deselectPreview() {
      $('.js-lightning-view-icon').removeClass('selected');
      selectedEventId = null;
    }

    $(this).on('mousemove', '.js-lightning-image-preview', function (e) {
      var parentOffset = $(this).parent().offset();
      var relX = e.pageX - parentOffset.left;

      var xPercentage = relX / $(this).width();

      var $icons = $('.js-lightning-view-icon');
      var xIndex = Math.floor($icons.length * xPercentage);
      var $icon = $icons.eq(xIndex);

      var $preview;

      if (selectedEventId === null) {
        $('.icon-event').removeClass('js-hover');
        $icon.addClass('js-hover');
        $preview = changePreview($icon.data('event-id'));
      } else {
        $preview = $('.js-lightning-image-preview[data-event-id="' + selectedEventId + '"]');
      }

      if ($preview.data('paged')) {
        var relY = e.pageY - parentOffset.top;
        var yPercentage = relY / $(this).height();
        var page = Math.floor($(this).data('image-count') * yPercentage);
        changePreviewPage($preview.data('event-id'), page);
      }
    });

    var optionsDisplayed = false;

    $('.js-lightning-options-btn').click(changeOptions);

    $('.js-lightning-options')
      .mouseenter(showOptions)
      .mouseleave(hideOptions);

    function changeOptions() {
      if (!optionsDisplayed) {
        showOptions()
      } else {
        hideOptions()
      }
    }

    function showOptions() {
      $('.js-change-timeline').show();
      $('.js-lightning-options-btn').addClass('active');
      optionsDisplayed = true;
    }

    function hideOptions() {
      $('.js-change-timeline').hide();
      $('.js-lightning-options-btn').removeClass('active');
      optionsDisplayed = false;
    }

    $('.js-timeline-date').click(function (e) {
      $('.js-icon-group[data-year="' + $(this).data('year') + '"]').toggle();
      $('.js-icon-group-count[data-year="' + $(this).data('year') + '"]').toggle();
      $(this).toggleClass('collapse expand');
    });

  });
</script>