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
              'sql' => "SELECT MAX(ev.last_modified_date) 
                        FROM `event` ev 
                          INNER JOIN episode ep ON ep.id = ev.episode_id
                        WHERE ep.patient_id = " . $this->patient->id
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
                    foreach ($episode->events as $event) {
                        /* @var Event $event */

                        $event_li_css = $event->getEventLiCss();
                        /**
                         * getting variable: 'event_path', 'event_name', 'event_image', 'event_date', 'event_li_css'
                         */
                        extract($event->getEventListDetails());

                        $patientTicketing_API = new \OEModule\PatientTicketing\components\PatientTicketing_API();
                        $virtual_clinic_event = $patientTicketing_API->getTicketForEvent($event);
                        ?>

                        <li id="js-sideEvent<?php echo $event->id ?>"
                            class="<?=implode(' ', $event_li_css)?>"
                            data-event-id="<?= $event->id ?>"
                            data-event-date="<?= $event->event_date ?>"
                            data-created-date="<?= $event->created_date ?>"
                            data-event-year-display="<?= substr($event->NHSDate('event_date'), -4) ?>"
                            data-event-date-display="<?= $event->NHSDate('event_date') ?>"
                            data-event-type="<?= $event_name ?>"
                            data-institution="<?= $event->institution->name ?>"
                            data-subspecialty="<?= $subspecialty_name ?>"
                            data-event-icon='<?= $event->getEventIcon('medium') ?>'
                            <?php if ($event_image !== null) { ?>
                                data-event-image-url="<?= $event_image->getImageUrl() ?>"
                            <?php } ?>
                            style="<?=$event->deleted && $display_deleted_events_in_deleted_category ? 'display:none;' : ''?>"
                        >
                            <div class="tooltip quicklook" style="display: none; ">
                                <div class="event-name"><?php echo $event_name ?></div>
                                <div class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></div>
                                <?php $event_icon_class = '';
                                $event_issue_text = $event->getIssueText();
                                $event_issue_class = 'event-issue';
                                if ($event->hasIssue()) {
                                    $event_issue_class .= ($event->hasIssue('ready') ? ' ready' : ' alert');
                                }
                                /**
                                 * getting variable: 'event_icon_class', 'event_issue_class', 'event_issue_text'
                                 */
                                extract($event->getDetailedIssueText($event_icon_class, $event_issue_text, $event_issue_class));
                                if (!empty($event_issue_text)) { ?>
                                    <div class="<?= $event_issue_class ?>">
                                        <?= $event_issue_text ?>
                                    </div>
                                <?php } ?>
                                <div class="event-name">Institution: <strong><?=$event->institution ?? '-';?></strong></div>
                                <div class="event-name">Site: <strong><?=$event->site ?? '-';?></strong></div>
                            </div>

                            <a href="<?=$event_path?>" data-id="<?php echo $event->id ?>">
                                <?php
                                if ($event->hasIssue()) {
                                    if ($event->hasIssue('ready')) {
                                        $event_icon_class .= ' ready';
                                    } elseif ($eur = EUREventResults::model()->find('event_id=?', array($event->id)) && $event->hasIssue('EUR Failed')) {
                                        $event_icon_class .= ' cancelled';
                                    } elseif ($event->hasIssue('Consent Withdrawn')) {
                                        $event_icon_class .= ' cancelled';
                                    } else {
                                        $event_icon_class .= ' alert';
                                    }
                                    if ($event->hasIssue('draft')) {
                                        $event_icon_class .= ' draft';
                                    }
                                }
                                if ($virtual_clinic_event) {
                                    $event_icon_class .= ' virtual-clinic';
                                }
                                ?>
                                <span class="event-type js-event-a<?= $event_icon_class ?>">
                                    <?= $event->getEventIcon() ?>
                                </span>
                                <span class="event-extra">
                                    <?php
                                    $api = Yii::app()->moduleAPI->get($event->eventType->class_name);
                                    if (method_exists($api, 'getLaterality')) {
                                        $this->widget('EyeLateralityWidget', [
                                            'show_if_both_eyes_are_null' =>
                                              !property_exists($api, 'show_if_both_eyes_are_null') ||
                                              $api->show_if_both_eyes_are_null,
                                            'eye' => $api->getLaterality($event->id),
                                            'pad' => '',
                                        ]);
                                    } ?>
                                </span>
                                <span class="event-date <?= ($event->isEventDateDifferentFromCreated()) ? ' backdated' : '' ?>">
                                    <?=$event->getEventDate()?>
                                </span>
                                <span class="tag"><?= $tag ?></span>
                            </a>
                        </li>
                    <?php }
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
