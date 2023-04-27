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

$subspecialty_labels = array();
$current_subspecialty = null;
$episodes_list = array();

$display_deleted_in = $this->displayDeletedEventsIn();
// 1 for "Deleted Events" category, 2 for "Timeline"
$display_deleted_events_in_deleted_category = $display_deleted_in && $display_deleted_in === 1;

// Cache the sidebar for each patient. Refresh whenever a new event is created for the patient
// Currently doesn't filter out change-tracker events, as that would likely add additional time to the SQL query
$epsidebarkey = "_single_episode_sidebar_patient:" . $this->patient->id . "display_deleted:" . $display_deleted_in;

if (
        $this->beginCache(
            $epsidebarkey,
            array(
            'dependency' => array(
              'class' => 'system.caching.dependencies.CDbCacheDependency',
              'sql' => 'SELECT MAX(date) FROM (
                          SELECT MAX(ev.last_modified_date) AS date
                          FROM `event` ev
                            INNER JOIN episode ep ON ep.id = ev.episode_id
                          WHERE ep.patient_id = ' . $this->patient->id . '
                        UNION
                          SELECT MAX(ed.last_modified_date) AS date
                          FROM `event_draft` ed
                            INNER JOIN episode ep ON ep.id = ed.episode_id
                          WHERE ep.patient_id = ' . $this->patient->id .'
                        ) AS cache_dates'
              )
            )
        )
) {?>
<div class="sidebar-eventlist">
    <?php
    if (is_array($ordered_episodes)) {
        $existing_modules = array();
        $missing_modules = array();
        ?>
        <div class="sidebar-grouping">
            <select name="grouping-picker" class="grouping-picker">
                <option value="none" selected="">Events by date</option>
                <option id="institution" value="institution">Events by institution</option>
                <option id="event-year-display" value="event-year-display">Events by year</option>
                <option id="event-type" value="event-type">Events by type</option>
                <option id="subspecialty" value="subspecialty">Specialty</option>
                <?php if ($display_deleted_events_in_deleted_category) { ?>
                    <option id="deleted" value="deleted">Deleted</option>
                <?php } ?>
            </select>
        </div>
        <div class="sidebar-list-controls">
            <button type="button" class="sorting-order asc">
                <i class="oe-i small direction-up"></i>
    </button>
            <button type="button" class="sorting-order desc">
                <i class="oe-i small direction-down"></i>
    </button>

                <button type="button" class="expand-all">
                    <i class="oe-i small increase-height"></i>
    </button>
                <button type="button" class="collapse-all">
                    <i class="oe-i small reduce-height"></i>
    </button>

        </div>
        <ul class="events" id="js-events-by-date">
            <?php foreach ($ordered_episodes as $specialty_episodes) {
                foreach ($specialty_episodes['episodes'] as $i => $episode) {
                    // Episode events
                    if ($episode->subspecialty) {
                        $tag = $episode->subspecialty ? $episode->subspecialty->ref_spec : 'Ss';
                    } else {
                        $tag = "Le";
                    }

                    $subspecialty_name = $episode->getSubspecialtyText();

                    foreach ($episode->draft_events as $draft) {
                        $this->renderPartial(
                            '//patient/_single_episode_sidebar_draft_entry',
                            [
                                'draft' => $draft,
                                'subspecialty_name' => $subspecialty_name,
                                'tag' => $tag,
                            ]
                        );
                    }

                    foreach ($episode->events as $event) {
                        /* @var Event $event */

                        $patientTicketing_API = new \OEModule\PatientTicketing\components\PatientTicketing_API();

                        $this->renderPartial(
                            '//patient/_single_episode_sidebar_event_entry',
                            array_merge(
                                [
                                    'event' => $event,
                                    'event_li_css' => $event->getEventLiCss(),
                                    'subspecialty_name' => $subspecialty_name,
                                    'tag' => $tag,
                                    'patientTicketing_API' => $patientTicketing_API,
                                    'virtual_clinic_event' => $patientTicketing_API->getTicketForEvent($event),
                                ],
                                $event->getEventListDetails()
                            )
                        );
                    }
                }
            } ?>
        </ul>
    <?php } ?>
</div>

    <?php

    $this->endCache($epsidebarkey);
}
?>

<script>
    const eventId = <?= CJSON::encode($this->event ? $this->event->id : '') ?>;
    const sidebarEventItem = document.getElementById('js-sideEvent' + eventId);

    if (sidebarEventItem) {
        sidebarEventItem.classList.add('selected');
    }
</script>

<?php
$current_subspecialty = isset($this->event->episode->subspecialty) ? $this->event->episode->subspecialty : null;

$this->renderPartial('//patient/add_new_event', array(
    'button_selector' => '#add-event',
    'view_subspecialty' => $current_subspecialty,
    'episodes' => $active_episodes,
    'context_firm' => $this->firm,
    'patient_id' => $this->patient->id,
    'event_types' => EventType::model()->getEventTypeModules(),
    'drafts' => EventDraft::model()->with('episode')->findAll('patient_id = ?', [$this->patient->id])
));
if ($this->editable) {
    $this->renderPartial('//patient/change_event_context', array(
        'button_selector' => '.js-change_context',
        'view_subspecialty' => $current_subspecialty,
        'episodes' => $active_episodes,
        'context_firm' => $this->firm,
        'patient_id' => $this->patient->id,
        'currentStep' => (isset($this->event->eventType->class_name) && $this->event->eventType->class_name == 'OphCiExamination' ? $this->getCurrentStep() : ''),
        'currentFirm' => (isset($this->event->firm_id) ? $this->event->firm_id : '""'),
        // for some strange reason '' doesn't reslove to an empty str
        'event_types' => $this->event->eventType->name
    ));
}
?>
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
                subspecialties: <?= CJSON::encode(NewEventDialogHelper::structureAllSubspecialties()) ?>,
                deleted_event_category: <?=json_encode($display_deleted_events_in_deleted_category)?>,
            });
        });
    });
</script>
