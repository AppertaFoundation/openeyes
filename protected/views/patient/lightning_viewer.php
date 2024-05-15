<?php

/**
 * @var string $selectedPreviewType
 * @var array $previewGroups
 * @var array $previewsByYear
 */

$navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/svg/oe-nav-icons.svg';

$previewWidth = @Yii::app()->params['lightning_viewer']['image_width'] ?: 800;

?>

<?php $this->renderPartial('//patient/episodes_sidebar'); ?>

<main class="oe-lightning-viewer">
    <?php $this->renderPartial('//patient/_patient_alerts') ?>
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
              <li class="<?php if ($previewType === $selectedPreviewType) :
                    ?>selected<?php
                         endif; ?>">
                  <?= count($events) > 0 ? reset($events)->getEventIcon() : '<i class="oe-i-e"></i>' ?>
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
                        <?= $event->getEventIcon() ?>
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
          <div class="js-lightning-view-overlay"
               style="width: <?= $previewWidth ?>px; min-height: 512px; position: absolute;"></div>
          <i class="js-preview-image-loader spinner" style="display: none;"></i>
        <?php foreach ($previewsByYear as $year => $events) {
            foreach ($events as $event) {
                $criteria = new CDbCriteria();
                $criteria->compare('event_id', $event->id);
                $criteria->join = 'LEFT JOIN eye ON eye.id = eye_id';
                $criteria->order = 'eye.display_order, page';
                $eventImages = EventImage::model()->findAll($criteria);
                ?>
              <div class="js-lightning-image-preview"
                   data-event-id="<?= $event->id ?>"
                   data-image-count="<?= count($eventImages) ?>"
                   data-paged="<?= isset($eventImages[0]) && $eventImages[0]->page !== null ?>"
                   data-preview-type="<?= $selectedPreviewType ?>"
                   data-date="<?= CHtml::encode(Helper::convertDate2HTML($event->event_date)) ?>"
                   style="display: none"
              >
                  <?php if (count($eventImages) === 0) { ?>
                    <p class="no-lightning-image">No preview is available at this time</p>
                  <?php } else { ?>
                        <?php foreach ($eventImages as $eventImage) { ?>
                      <div class="js-lightning-image-preview-page"
                           data-loaded="0"
                           data-src="<?= $eventImage->getImageUrl() ?>"
                           style="max-width: <?= $previewWidth ?>px; <?php if ($eventImage->page) :
                                ?>display: none;<?php
                                             endif; ?>"
                            <?php if ($eventImage->page !== null) :
                                ?>data-page-number="<?= $eventImage->page ?>"<?php
                            endif; ?>
                      ></div>
                        <?php } ?>
                  <?php } ?>
              </div>
            <?php }
        } ?>
    </div>

  </div>
</main>

<script>
  $(function () {
    $('body').css('overflow-y','hidden');
    var lightningViewer = new OpenEyes.UI.LightningViewer();
    var oe_header_height = $('.oe-header').outerHeight();
    var lightning_timeline_height = $('.lightning-timeline').outerHeight();

    $('.js-lightning-view-overlay').css('height','calc(100% - '+(oe_header_height + lightning_timeline_height)+'px)');
  });
</script>