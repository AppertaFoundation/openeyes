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

    <!-- timeline -->
    <div class="timeline">
      <table>
        <tbody>
        <tr>
            <?php for ($i = count($documentChunks) - 1; $i >= 0; --$i) {
                $chunk = $documentChunks[$i];
                if (count($chunk) === 0) {
                    continue;
                }

                $latestEvent = end($chunk);
                $earliestEvent = reset($chunk);

                $earliestYear = (new DateTime($earliestEvent->event_date))->format('Y');
                $latestYear = (new DateTime($latestEvent->event_date))->format('Y');
                ?>
              <td class="date-divider">
                  <?= $earliestYear === $latestYear ? $earliestYear : $latestYear . '-' . $earliestYear ?>
                <i class="oe-i small collapse js-timeline-date"></i>
              </td>
            <?php } ?>
        </tr>
        <tr>
            <?php
            for ($i = count($documentChunks) - 1; $i >= 0; --$i) {
                $chunk = $documentChunks[$i];
                if (count($chunk) === 0) {
                    continue;
                }
                ?>
              <td>
                <div id="js-icon-1" class="icon-group">
                    <?php foreach ($chunk as $event) { ?>
                      <span id="lqv_0" class="icon-event" data-lightning="sent,23 Nov 2016,Ms Angela Glasby,2016-11-23"><i
                            class="oe-i-e <?= $event->eventType->getEventIconCssClass() ?>"></i></span>
                    <?php } ?>
                  <div style="display: none;"><?= count($chunk) ?></div>
              </td>
            <?php } ?>
        </tr>
        </tbody>
      </table>
    </div><!-- timeline -->

  </div><!-- lightning-timeline -->

  <div class="flex-layout flex-left flex-top">

    <!-- js generated content -->
    <div class="oe-lightning-meta">
      <div class="letter-type">Letter sent</div>
      <div class="date">5 Mar 2016</div>
      <div class="sender">Ms Angela Glasby</div>

      <div class="help">
        swipe to scan | click to lock
      </div>
    </div>


    <div class="oe-lightning-quick-view">
      <img src="../assets/img/_letters/2016-3-5.png" alt="_demo_letter">
    </div>

  </div>
</main>