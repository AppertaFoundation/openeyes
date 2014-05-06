<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
if ($legacyepisodes) {
    $episode = $legacyepisodes[0];
    ?>
    <section class="panel episode open legacy"> 
        <!-- Show/hide toggle icon -->
        <a href="#" class="toggle-trigger toggle-<?php if ((!$this->event || $this->event->eventType->class_name != 'OphLeEpatientletter') && !@Yii::app()->session['episode_hide_status']['legacy']) { ?>show<?php } else { ?>hide<?php } ?>">
            <span class="icon-showhide">
                Show/hide events for this episode
            </span>
        </a>
        <!-- Episode title -->
        <h4 class="episode-title legacy">
            Legacy events
        </h4>
        <div class="events-container <?php if ((!$this->event) && !@Yii::app()->session['episode_hide_status']['legacy']) { ?>hide<?php } else { ?>show<?php } ?>">
            <ol class="events">
                <?php
                ?>
                <?php
                foreach ($episode->events as $event) {
                    $highlight = false;

                    if (isset($this->event) && $this->event->id == $event->id) {
                        $highlight = TRUE;
                    }

                    $event_path = Yii::app()->createUrl($event->eventType->class_name . '/Default/view') . '/';
                    ?>
                    <li id="eventLi<?php echo $event->id ?>"<?php if ($highlight) { ?> class="selected"<?php } ?>>

                        <!-- Quicklook tooltip -->
                        <div class="quicklook" style="display: none; ">
                            <span class="event-name"><?php echo $event->eventType->name ?></span>
                            <span class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></span>
                            <?php if ($event->hasIssue()) { ?>
                                <span class="event-issue"><?php echo $event->getIssueText() ?></span>
                            <?php } ?>
                        </div>

                        <a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
                            <span class="event-type<?php if ($event->hasIssue()) { ?> alert<?php } ?>">
                                <?php $assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/'; ?>
                                <img src="<?php echo Yii::app()->createUrl($assetpath . 'img/small.png') ?>" alt="op" width="19" height="19" />
                            </span>
                            <span class="event-date"> <?php echo $event->NHSDateAsHTML('created_date'); ?></span>
                        </a>

                    </li>
                <?php } ?>
                <?php
                //      }
//}
                ?>

            </ol>
        </div>
    </section>

    <?php
}
?>
