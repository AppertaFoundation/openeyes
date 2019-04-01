<?php
/**
 * @var $legacyepisodes
 * @var $active_episodes
 * @var $ordered_episodes
 * @var $active_episodes
 * @var Episode[] $specialty_episodes
 **/

// Note, we are ignoring the possibility of additional specialties here and only supporting the first,
// which is expected to be opthalmology.
$active_episodes = array();
if (is_array($ordered_episodes)) {
    foreach ($ordered_episodes as $specialty) {
        $active_episodes = array_merge($active_episodes, $specialty['episodes']);
    }
    //$active_episodes = $ordered_episodes[0]['episodes'];
}

// flatten the data structure to include legacy events into the core navigation. Note here we are
// simply assuming that the first entry will be Ophthalmology specialty (for the purposes of this PoC
// we don't anticipate events from any other specialty)
if (count($legacyepisodes)) {
    if (!is_array($ordered_episodes) || empty($ordered_episodes)) {
        $ordered_episodes = array(
            array(
                'specialty' => 'Ophthalmology',
                'episodes' => array(),
            ),
        );
    }
    foreach ($legacyepisodes as $le) {
        $ordered_episodes[0]['episodes'][] = $le;
    }
}
?>

<?php
$subspecialty_labels = array();
$current_subspecialty = null;
$episodes_list = array(); ?>
<div class="sidebar-eventlist">
<?php if (is_array($ordered_episodes)):
    foreach ($ordered_episodes as $specialty_episodes): ?>         
        <ul class="events" id="js-events-by-date">
          <?php foreach ($specialty_episodes['episodes'] as $i => $episode): ?>
            <!-- Episode events -->
              <?php
              if ($episode->subspecialty) {
                  $tag = $episode->subspecialty ? $episode->subspecialty->ref_spec : 'Ss';
              } else {
                  $tag = "Le";
              }
              $subspecialty_name = $episode->getSubspecialtyText();
              ?>
              <?php foreach ($episode->events as $event):
                  /* @var Event $event */

                  $highlight = false;

                  if (isset($this->event) && $this->event->id == $event->id) {
                      $highlight = true;
                      $current_subspecialty = $episode->subspecialty;
                  }

                  $event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/';
                  $event_name = $event->getEventName();
                  $event_image = EventImage::model()->find('event_id = :event_id', array(':event_id' => $event->id));
                  ?>

              <li id="js-sideEvent<?php echo $event->id ?>"
                  class="event <?php if ($highlight) { ?> selected<?php } ?>"
                  data-event-id="<?= $event->id ?>"
                  data-event-date="<?= $event->event_date ?>" data-created-date="<?= $event->created_date ?>"
                  data-event-year-display="<?= substr($event->NHSDate('event_date'), -4) ?>"
                  data-event-date-display="<?= $event->NHSDate('event_date') ?>"
                  data-event-type="<?= $event_name ?>"
                  data-subspecialty="<?= $subspecialty_name ?>"
                  data-event-icon='<?= $event->getEventIcon('medium') ?>'
                  <?php if ($event_image !== null && $event_image->status->name === 'CREATED'): ?>
                    data-event-image-url="<?= Yii::app()->createUrl('eventImage/view/' . $event_image->event_id) ?>"
                  <?php endif; ?>
              >

                <div class="tooltip quicklook" style="display: none; ">
                  <div class="event-name"><?php echo $event_name ?></div>
                  <div class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></div>
                    <?php if ($event->hasIssue()) { ?>
                      <div
                          class="event-issue<?= $event->hasIssue('ready') ? ' ready' : '' ?>"><?php echo $event->getIssueText() ?></div>
                    <?php } ?>
                </div>

                <a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
                    <span class="event-type js-event-a <?= ($event->hasIssue()) ? ($event->hasIssue('ready') ? 'ready' : 'alert') : '' ?>">
                        <?= $event->getEventIcon() ?>
                    </span>
                    <span class="event-extra">
                        <?php
                        $api = Yii::app()->moduleAPI->get($event->eventType->class_name);
                        if (method_exists($api, 'getLaterality')) {
                            $this->widget('EyeLateralityWidget', ['eye' => $api->getLaterality($event->id), 'pad' => '']);
                        } ?>
                    </span>

                    <span class="event-date <?= ($event->isEventDateDifferentFromCreated()) ? ' ev_date' : '' ?>">
                    <?php echo $event->event_date
                        ? $event->NHSDateAsHTML('event_date')
                        : $event->NHSDateAsHTML('created_date');
                    ?>
                  </span>
                  <span class="tag"><?= $tag ?></span>
                </a>
              </li>
              <?php endforeach; ?>
          <?php endforeach; ?>
      </ul>
    <?php endforeach;
endif; ?>
</div>

<?php

$this->renderPartial('//patient/add_new_event', array(
    'button_selector' => '#add-event',
    'view_subspecialty' => $current_subspecialty,
    'episodes' => $active_episodes,
    'context_firm' => $this->firm,
    'patient_id' => $this->patient->id,
    'event_types' => EventType::model()->getEventTypeModules(),
)); ?>

<?php
$subspecialty_label_list = array();
foreach ($subspecialty_labels as $id => $label) {
    $subspecialty_label_list[] = "{$id}: '{$label}'";
}
?>
<script type="text/javascript">
  $(document).ready(function () {
    new OpenEyes.UI.Sidebar(
      $('.sidebar .oe-scroll-wrapper')
    );

    $('nav.sidebar').each(function () {
      new OpenEyes.UI.EpisodeSidebar(this, {
        patient_id: OE_patient_id,
        user_context: <?= CJSON::encode(NewEventDialogHelper::structureFirm($this->firm)) ?>,
        subspecialty_labels: {
            <?= implode(",", $subspecialty_label_list); ?>
        },
        subspecialties: <?= CJSON::encode(NewEventDialogHelper::structureAllSubspecialties()) ?>
      });
    });
  });
</script>
