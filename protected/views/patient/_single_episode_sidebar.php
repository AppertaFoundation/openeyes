<?php if ((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && $this->checkAccess('OprnCreateEpisode')) {?>
    <div class="oe-sidebar-top-buttons">
        <button
            class="secondary tiny add-event addEvent enabled"
            type="button"
            id="add-event">Event</button></div>
<?php }?>
<div class="oe-scroll-wrapper" style="height:2000px">
<?php
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
            array('specialty' => 'Ophthalmology',
                'episodes' => array()
            )
        );
    }
    foreach ($legacyepisodes as $le) {
        $ordered_episodes[0]['episodes'][] = $le;
    }
}
?>

<div class="all-panels">
<?php
$subspecialty_labels = array();
$current_subspecialty = null;

if (is_array($ordered_episodes)) {
    foreach ($ordered_episodes as $specialty_episodes) { ?>
        <div class="oe-panel specialty" id="specialty-panel-<?=$specialty_episodes['specialty']?>">
            <section>
            <div class="fixed-section">
                <ol class="subspecialties">
                    <?php foreach ($specialty_episodes['episodes'] as $i => $episode) {
                        // TODO deal with support services possibly?
                        $id = $episode->getSubspecialtyID();
                        $subspecialty_name = $episode->getSubspecialtyText();
                        if (!$id) {
                            if ($episode->support_services) {
                                $id = 'SS';
                                $tag = 'Ss';
                            } else {
                                $id = "Le";
                                $tag = $id;
                            }
                        }
                        else {
                            $tag = $episode->subspecialty->ref_spec;
                        }
                        $selected = '';
                        if ($current_episode && $current_episode->getSubspecialtyID() == $id) {
                            $selected = 'selected';
                            $current_subspecialty = $current_episode->getSubspecialty();
                        }

                        if (!array_key_exists($id, $subspecialty_labels)) {
                            $subspecialty_labels[$id] = $subspecialty_name; ?>

                            <li class="subspecialty <?= $selected ?>"
                                data-subspecialty-id="<?= $id ?>"
                                data-definition='<?= CJSON::encode(NewEventDialogHelper::structureEpisode($episode)) ?>'>
                                <a href="<?= Yii::app()->createUrl('/patient/episode/' . $episode->id) ?>">
                                <?= $subspecialty_name ?><span class="tag"><?= $tag ?></span>
                                </a></li>

                        <?php }
                    } ?>
                </ol>
            </div>
            <ol class="events">
                <?php foreach ($specialty_episodes['episodes'] as $i => $episode) { ?>
                    <!-- Episode events -->
                    <?php
                        if ($episode->subspecialty) {
                            $tag = $episode->subspecialty ? $episode->subspecialty->ref_spec : 'Ss';
                        }
                        else {
                            $tag = "Le";
                        }
                        $subspecialty_name = $episode->getSubspecialtyText();
                    ?>
                    <?php foreach ($episode->events as $event) {
                        $highlight = false;

                        if (isset($this->event) && $this->event->id == $event->id) {
                            $highlight = TRUE;
                            $current_subspecialty = $episode->subspecialty;
                        }

                        $event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/';

                        $icon = $event->getEventIcon();
                        $event_name = $event->getEventName();
                    ?>

                        <li id="eventLi<?php echo $event->id ?>"
                            class="<?php if ($highlight) { ?> selected<?php }?>"
                            data-event-date="<?= $event->event_date ?>" data-created-date="<?= $event->created_date ?>"
                            data-event-year-display="<?= substr($event->NHSDate('event_date'), -4) ?>"
                            data-event-date-display="<?= $event->NHSDate('event_date') ?>"
                            data-event-type="<?= $event_name ?>"
                            data-subspecialty="<?= $subspecialty_name ?>">

                            <!-- Quicklook tooltip -->
                            <div class="tooltip quicklook" style="display: none; white-space: inherit; ">
                                <div class="event-name"><?php echo $event_name ?></div>
                                <div class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></div>
                                <?php if ($event->hasIssue()) { ?>
                                    <div class="event-issue<?= $event->hasIssue('ready') ? ' ready' : ''?>"><?php echo $event->getIssueText() ?></div>
                                <?php } ?>
                            </div>

                            <a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
                                    <span class="event-type<?= ($event->hasIssue()) ? ($event->hasIssue('ready') ? ' ready' : ' alert') : '' ?>">
                                        <img src="<?= $icon ?>" alt="op"
                                             width="19" height="19"/>
                                    </span>
                                <span
                                    class="event-date <?php echo ($event->isEventDateDifferentFromCreated()) ? ' ev_date' : '' ?>"> <?php echo $event->event_date ? $event->NHSDateAsHTML('event_date') : $event->NHSDateAsHTML('created_date'); ?></span>
                                <span class="tag"><?= $tag ?></span>
                            </a>

                        </li>
                    <?php } ?>

                <?php } ?>
            </ol>
            </section>
        </div>
    <?php }
}?>
</div>

<?php

$this->renderPartial('//patient/add_new_event',array(
    'button_selector' => '#add-event',
    'view_subspecialty' => $current_subspecialty,
    'episodes' => $active_episodes,
    'context_firm' => $this->firm,
    'patient_id' => $this->patient->id,
    'eventTypes' => EventType::model()->getEventTypeModules(),
));?>

<?php
    $subspecialty_label_list = array();
    foreach ($subspecialty_labels as $id => $label)
        $subspecialty_label_list[] = "{$id}: '{$label}'";
?>
<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.UI.Sidebar(
            $('.sidebar .oe-scroll-wrapper')
        );

        $('div.specialty').each(function() {
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
</div>
