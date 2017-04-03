<?php if ((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && $this->checkAccess('OprnCreateEpisode')) {?>
    <div class="oe-sidebar-top-buttons">
        <?php
        $enabled = false;
        $change_firm = false;
        if ($current_episode) {
            $enabled = true;
            if ($current_episode->getSubspecialtyID() != $this->firm->getSubspecialtyID()) {
                $change_firm = true;
            }
        }
        $class = "disabled";
        if ($enabled) {
            $class = $change_firm ? "change-firm" : "enabled";
        }
        ?>
        <button class="secondary tiny add-episode" type="button" id="add-episode"><?= Episode::getEpisodeLabel(); ?></button><button
                class="secondary tiny add-event addEvent <?= $class ?>"
                type="button"
                id="add-event"
                data-attr-subspecialty-id="<?= $this->firm->getSubspecialtyID();?>"
            <?= $change_firm ? 'data-window-title="Please switch to a ' . $current_episode->getSubspecialtyText() . ' Firm"' : ''; ?>
        >Event</button></div>
<?php }?>
<div class="oe-scroll-wrapper" style="height:300px">
<?php
$subspecialty_colour_codes = array(
    'AE' => '#916865',
    'AD' => '#D4AA7D',
    'AN' => '#D2D8B3',
    'CA' => '#90A9B7',
    'CO' => '#006992',
    'EX' => '#ECA400',
    'GL' => '#D5C4BB',
    'MR' => '#D46A6A',
    'PH' => '#BBA698',
    'ON' => '#D4B483',
    'PE' => '#E4DFDA',
    'PC' => '#4281A4',
    'RF' => '#957186',
    'SP' => '#B3B749',
    'UV' => '#A6AA7B',
    'VR' => '#F4B4A6',
    'Le' => '#cccccc'
);

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
                            $id = "Le";
                            $tag = $id;
                        }
                        else {
                            $tag = $episode->subspecialty ? $episode->subspecialty->ref_spec : 'Ss';
                        }

                        if (!array_key_exists($id, $subspecialty_labels)) {
                            $subspecialty_labels[$id] = $subspecialty_name; ?>

                            <li class="subspecialty <?= $current_episode && $current_episode->getSubspecialtyID() == $id ? "selected" : ""; ?>"
                                data-subspecialty-id="<?= $id ?>">
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
                            data-event-type="<?= $event->eventType->name ?>"
                            data-subspecialty="<?= $subspecialty_name ?>">

                            <!-- Quicklook tooltip -->
                            <div class="tooltip quicklook" style="display: none; ">
                                <div class="event-name"><?php echo $event->eventType->name ?></div>
                                <div class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></div>
                                <?php if ($event->hasIssue()) { ?>
                                    <div class="event-issue<?= $event->hasIssue('ready') ? ' ready' : ''?>"><?php echo $event->getIssueText() ?></div>
                                <?php } ?>
                            </div>

                            <a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
                                    <span class="event-type<?= ($event->hasIssue()) ? ($event->hasIssue('ready') ? ' ready' : ' alert') : '' ?>">
                                        <?php
                                        if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
                                            $assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
                                        } else {
                                            $assetpath = '/assets/';
                                        }
                                        ?>
                                        <img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op"
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

<script type="text/html" id="add-new-event-template">
    <?php $this->renderPartial('//patient/add_new_event',array(
        'episode' => "{{episode}}",
        'subspecialty' => "{{subspecialty}}",
        'patient' => $this->patient,
        'eventTypes' => EventType::model()->getEventTypeModules(),
    ));?>
</script>

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
                user_subspecialty: <?= $this->firm->getSubspecialtyID() ?>,
                subspecialty_labels: {
                    <?= implode(",", $subspecialty_label_list); ?>
                }
            });
        });

        if (window.location.search.replace("?", "") == "show-new-event=1") {
            // quick fix to trigger the new event window after the user is asked to select
            // a new firm.
            $('button.add-event').trigger('click');
        }
    });
</script>
</div>
<div class="show-scroll-tip">scroll down</div>
<div class="scroll-blue-top" style="display:none;"></div>