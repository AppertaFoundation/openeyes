<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
if (!empty($episode)) {
    if ($episode->diagnosis) {
        $eye = $episode->eye ? $episode->eye->name : 'None';
        $diagnosis = $episode->diagnosis ? $episode->diagnosis->term : 'none';
    } else {
        $eye = 'No diagnosis';
        $diagnosis = 'No diagnosis';
    }

    $episode->audit('episode summary', 'view');
    ?>

    <div class="element-data">
        <h2>Summary</h2>
        <h3><?= $episode->getSubspecialtyText() ?></h3>
    </div>

    <?php $this->renderPartial('//base/_messages'); ?>

    <div class="row">
        <div class="large-9 column">
            <?php if (Yii::app()->hasModule('OphCiExamination')) { ?>
                <section class="element element-data">
                    <h3 class="data-title">Previous Management</h3>
                    <div class="data-value">
                        <div class="inline-previous-element"
                             data-element-type-id="<?= ElementType::model()->findByAttributes(array('class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Management'))->id ?>"
                             data-no-results-text="No previous management recorded"
                             data-limit="1"
                             data-template-id="previous-management-template">Loading previous management information ...</div>
                    </div>
                </section>

                <script type="text/html" id="previous-management-template">
                    <strong>{{subspecialty}} {{event_date}} ({{last_modified_user_display}} <span class="has-tooltip fa fa-info-circle" data-tooltip-content="This is the user that last modified the Examination event. It is not necessarily the person that originally added the comment."></span>):</strong> {{comments_or_children}}
                </script>
                <?php Yii::app()->assetManager->registerScriptFile("js/OpenEyes.UI.InlinePreviousElements.js", null, -10); ?>

            <?php } ?>
            <section class="element element-data">
                <h3 class="data-title">Principal diagnosis:</h3>
                <div class="data-value highlight">
                    <?php echo $episode->diagnosis ? $episode->diagnosis->term : 'None' ?>
                </div>
            </section>

            <section class="element element-data">
                <h3 class="data-title">Principal eye:</h3>
                <div class="data-value highlight">
                    <?php echo $episode->eye ? $episode->eye->name : 'None' ?>
                </div>
            </section>
        </div>
    </div>

    <?php
    $summaryItems = array();
    
    if ($episode->subspecialty) {
        $summaryItems = EpisodeSummaryItem::model()->enabled($episode->subspecialty->id)->findAll();
    }
    if (!$summaryItems) {
        $summaryItems = EpisodeSummaryItem::model()->enabled()->findAll();
    }
    ?>

    <?php if (count($summaryItems)) { ?>
        <div class="element element-data event-types">
            <?php foreach ($summaryItems as $summaryItem) {
                Yii::import("{$summaryItem->event_type->class_name}.widgets.{$summaryItem->getClassName()}");
                $widget = $this->createWidget($summaryItem->getClassName(), array(
                    'episode' => $episode,
                    'event_type' => $summaryItem->event_type,
                ));
                $className = '';
                if ($widget->collapsible) {
                    $className .= 'collapsible';
                    if ($widget->openOnPageLoad) {
                        $className .= ' open';
                    }
                }
                ?>
                <div class="<?php echo $className; ?>">
                    <h3 id="<?= $summaryItem->getClassName(); ?>" class="data-title">
                        <?= $summaryItem->name; ?>
                        <?php if ($widget->collapsible) {
                            $text = $widget->openOnPageLoad ? 'hide' : 'show';
                            $toggleClassName = $widget->openOnPageLoad ? 'toggle-hide' : 'toggle-show';
                            ?>
                            <a href="#" class="toggle-trigger toggle-<?php echo $toggleClassName; ?>">
                                <span class="text"><?php echo $text; ?></span>
                                <span class="icon-showhide">
                                    Show/hide
                                </span>
                            </a>
                        <?php } ?>
                    </h3>
                    <div class="summary-content">
                        <?php $widget->run(); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <script>
            $(function () {

                $('.event-types .collapsible').each(function () {

                    var container = $(this);
                    var content = container.find('.summary-content');
                    var toggler = container.find('.toggle-trigger');

                    container
                        .on('open.collapsible', function () {
                            content.show();
                            toggler.find('.text').html('hide');
                            toggler.addClass('toggle-hide');
                        })
                        .on('close.collapsible', function () {
                            content.hide();
                            toggler.find('.text').html('show');
                            toggler.addClass('toggle-show');
                        })
                        .on('click.collapsible', '.data-title', function (e) {
                            e.preventDefault();
                            toggler.removeClass('toggle-hide toggle-show');
                            container.trigger(content.is(':visible') ? 'close' : 'open');
                        });

                    if (!container.hasClass('open')) {
                        container.trigger('close.collapsible');
                    }
                });

                // Open the container on page load if location hash matches id.
                var hash = window.location.hash;
                if (hash) {
                    var elem = $(hash);
                    var container = elem.closest('.collapsible');
                    if (container.length) {
                        container.trigger('open');
                        window.location.hash = hash.replace(/#/, '');
                    }
                }
            });
        </script>
    <?php } ?>

    <section class="element element-data">
        <div class="row">
            <div class="large-6 column">
                <h3 class="data-title">Start Date:</h3>
                <div class="data-value">
                    <?php echo $episode->NHSDate('start_date') ?>
                </div>
            </div>
            <div class="large-6 column">
                <h3 class="data-title">End date:</h3>
                <div
                    class="data-value"><?php echo !empty($episode->end_date) ? $episode->NHSDate('end_date') : '(still open)' ?></div>
            </div>
        </div>
    </section>

    <section class="element element-data">
        <div class="row">
            <div class="large-6 column">
                <h3 class="data-title">Subspecialty:</h3>
                <div class="data-value">
                    <?= $episode->getSubspecialtyText() ?>
                </div>
            </div>
            <div class="large-6 column">
                <h3 class="data-title">Consultant firm:</h3>
                <div class="data-value"><?php echo $episode->firm ? $episode->firm->name : 'None' ?></div>
            </div>
        </div>
    </section>

    <div class="metadata">
        <span class="info">
            <?= $episode->getSubspecialtyText() ?>: created by <span
                class="user"><?php echo $episode->user->fullName ?></span>
            on <?php echo $episode->NHSDate('created_date') ?> at <?php echo substr($episode->created_date, 11, 5) ?>
        </span>
    </div>

    <div class="row">
        <div class="large-9 column">
            <section class="element element-data">
                <h3 class="data-title"><?= Episode::getEpisodeLabel() ?> Status:</h3>
                <div class="data-value highlight">
                    <?php echo $episode->status->name ?>
                </div>
            </section>
        </div>
    </div>

    <div class="metadata">
        <span class="info">
            Status last changed by <span class="user"><?php echo $episode->usermodified->fullName ?></span>
            on <?php echo $episode->NHSDate('last_modified_date') ?> at <?php echo substr($episode->last_modified_date,
                11, 5) ?>
        </span>
    </div>

<?php } ?>
