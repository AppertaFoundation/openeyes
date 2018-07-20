<?php

/**
 * @var string $selectedDocumentType
 * @var array $documentGroups
 * @var array $documentChunks
 */

$navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-nav-icons.svg';

$documentTypes = array_keys($documentGroups);
sort($documentTypes);
?>

<?php $this->renderPartial('//patient/episodes_sidebar'); ?>

<main class="oe-lightning-viewer">

  <div class="lightning-timeline">

    <div class="timeline-options js-lightning-options">

      <!-- interaction for the 'button' is all handled with JS, not CSS -->
      <div class="lightning-btn">
        <svg viewBox="0 0 30 30" class="lightning-icon">
          <use xlink:href="<?= $navIconUrl ?>#lightning-viewer-icon"></use>
        </svg>
        <!-- indicates functionality -->
        <i class="oe-i small-icon arrow-down-bold"></i>
      </div>

      <div class="change-timeline">
        <ul>
            <?php foreach ($documentTypes as $documentType) {
                $events = $documentGroups[$documentType];
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <li <?php if ($documentType === $selectedDocumentType): ?>class="selected"<?php endif; ?>>
                <i class="oe-i-e <?= $events[0]->eventType->getEventIconCssClass() ?>"></i>
                <a href="<?= Yii::app()->createUrl('/patient/lightningViewer',
                    array('id' => $this->patient->id, 'document_type' => $documentType)) ?>"
                >
                    <?= $documentType ?> (<?= count($events) ?>)
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
            <?php foreach ($documentChunks as $year => $events) {
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <td class="date-divider">
                  <?= $year ?>
                <i class="oe-i small collapse js-timeline-date"></i>
              </td>
            <?php } ?>
        </tr>
        <tr>
            <?php
            foreach ($documentChunks as $year => $events) {
                if (count($events) === 0) {
                    continue;
                }
                ?>
              <td>
                <div class="icon-group">
                    <?php foreach ($events as $event) { ?>
                      <span class="icon-event js-lightning-view-icon"
                            data-event-id="<?= $event->id ?>"
                            data-event-image-url="<?= Yii::app()->createUrl('/eventImage/view/',
                                array('id' => $event->id)) ?>"
                      >
                        <i class="oe-i-e <?= $event->eventType->getEventIconCssClass() ?>"></i>
                      </span>
                    <?php } ?>
                  <div style="display: none;"><?= count($events) ?></div>
              </td>
            <?php } ?>
        </tr>
        </tbody>
      </table>
    </div>

  </div>

  <div class="flex-layout flex-left flex-top">

    <div class="oe-lightning-meta">
      <div class="letter-type">Letter sent</div>
      <div class="date">5 Mar 2016</div>
      <div class="sender">Ms Angela Glasby</div>

      <div class="help">
        swipe to scan | click to lock
      </div>
    </div>

    <div class="oe-lightning-quick-view js-lightning-view-image-container"></div>
  </div>
</main>

<script>
  $(function () {
    $('.js-lightning-view-icon').each(function () {
      var $img = $('<img />', {
        src: $(this).data('event-image-url'),
        class: 'js-lightning-image-preview',
        style: 'display: none;',
        alt: 'No preview available at this time',
        'data-event-id': $(this).data('event-id'),
      });

      $img.appendTo('.js-lightning-view-image-container');
    });

    $(this).on('mouseover', '.js-lightning-view-icon', function () {
      $('.js-lightning-image-preview').hide();
      $('.js-lightning-image-preview[data-event-id="' + $(this).data('event-id') + '"]').show();
    });
  });
</script>