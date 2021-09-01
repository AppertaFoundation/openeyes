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

<main class="main-event view event-types">

  <h2 class="event-title">Summary: <?= $episode->getSubspecialtyText() ?>
    <i id="js-event-audit-trail-btn" class="oe-i audit-trail small pad"></i></h2>


    <?php $this->renderPartial('//base/_messages'); ?>


  <div class="flex-layout flex-left flex-stretch">
      <?php if (Yii::app()->hasModule('OphCiExamination')) { ?>
        <section class="element tile">
          <header class="element-header">
            <h3 class="element-title">Previous Management</h3>
          </header>
          <div class="element-data full-width">
            <div class="tile-data-overflow">
              <?php
                $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
              // Get the latest summary from the array although the method seems
              // to currently only return the latest summary.
                $summary = $exam_api->getManagementSummaries($this->patient);
                $summary = $summary ? $summary[0] : null;
                ?>
              <strong>
                <?php if ($summary) : ?>
                    <?= $summary->service ?> <?= implode(" ", $summary->date) ?> (<?= $summary->user ?> <span
                class="js-has-tooltip fa oe-i info small"
                data-tooltip-content="This is the user that last modified the Examination event. It is not necessarily the person that originally added the comment."></span>):</strong> <?= $summary->comments ?>
                <?php else : ?>
                No previous managements recorded.
                <?php endif; ?>
              </strong>
            </div>
          </div>
        </section>
        <script type="text/html" id="previous-management-template">
          <strong>{{subspecialty}} {{event_date}} ({{last_modified_user_display}} <span
                class="js-has-tooltip fa oe-i info small"
                data-tooltip-content="This is the user that last modified the Examination event. It is not necessarily the person that originally added the comment."></span>):</strong> {{comments}}
        </script>
            <?php Yii::app()->assetManager->registerScriptFile("js/OpenEyes.UI.InlinePreviousElements.js", null, -10); ?>

      <?php } ?>
    <section class="element tile">
      <header class="element-header">
        <h3 class="element-title">Principal diagnosis:</h3>
      </header>
      <div class="element-data full-width">
        <div class="data-value highlight">
            <?php echo $episode->diagnosis ? $episode->diagnosis->term : 'None' ?>
        </div>
      </div>
    </section>

    <section class="element tile">
      <header class="element-header">
        <h3 class="element-title">Principal eye:</h3>
      </header>
      <div class="element-data full-width">
        <div class="data-value highlight">
            <?php echo $episode->eye ? $episode->eye->name : 'None' ?>
        </div>
      </div>
    </section>
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
        <?php foreach ($summaryItems

        as $summaryItem) {
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
      <div class="element full <?php echo $className; ?>">
        <header class="element-header">
          <h3 id="<?= $summaryItem->getClassName(); ?>" class="element-title">
              <?= $summaryItem->name; ?>
              <?php if ($widget->collapsible) { ?>
              <?php } ?>
          </h3>
        </header>
          <?php if ($widget->collapsible) : ?>
            <div class="element-data full-width">
                <div class="data-value flex-layout flex-top">
                  <div class="cols-11"></div>
                  <div>
                    <i class="oe-i small pad toggle-trigger <?php echo $widget->openOnPageLoad ? 'collapse' : 'expand'; ?>"
                       data-list="meds-current"></i>
                  </div>
                </div>
            </div>
          <?php endif; ?>
        <div class="full-width summary-content">
            <?php $widget->run(); ?>
        </div>
      </div>
        <?php } ?>
      <script>
        $(function () {

          $('.event-types .collapsible').each(function () {

            var container = $(this);
            var content = container.find('.summary-content');
            var toggler = container.find('.toggle-trigger');

            container
              .on('open.collapsible', function () {
                content.show();
                toggler.addClass('collapse');
              })
              .on('close.collapsible', function () {
                content.hide();
                toggler.addClass('expand');
              })
              .on('click.collapsible', '.toggle-trigger', function (e) {
                e.preventDefault();
                toggler.removeClass('expand collapse');
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

  <div class="flex-layout flex-left flex-stretch">
    <section class="element tile">
      <header class="element-header">
        <h3 class="element-title">Start Date</h3>
      </header>
      <div class="element-data full-width">
        <div class="tile-data-overflow">
          <div class="data-value">
              <?php echo $episode->NHSDate('start_date') ?>
          </div>
        </div>
      </div>
    </section>
    <section class="element tile">
      <header class="element-header">
        <h3 class="element-title">End Date</h3>
      </header>
      <div class="element-data full-width">
        <div class="data-value">
            <?php echo !empty($episode->end_date) ? $episode->NHSDate('end_date') : '(still open)' ?>
        </div>
      </div>
    </section>
  </div>

  <div class="flex-layout flex-left flex-stretch">
    <section class="element tile">
      <header class="element-header">
        <h3 class="element-title">Subspecialty:</h3>
      </header>
      <div class="element-data full-width">
        <div class="data-value">
            <?= $episode->getSubspecialtyText() ?>
        </div>
      </div>
    </section>
    <section class="element tile">
      <header class="element-header">
        <h3 class="element-title">Consultant firm:</h3>
      </header>
      <div class="element-data full-width">
        <div class="data-value"><?php echo $episode->firm ? $episode->firm->name : 'None' ?></div>
      </div>
    </section>
  </div>

  <div id="js-event-audit-trail" class="oe-popup-event-audit-trail" style="display: none;">
    <table>
      <tbody>
      <tr>
        <td class="title">Created by</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td><?php echo $episode->user->fullName ?></td>
        <td><?php echo $episode->NHSDate('created_date') ?></td>
        <td><?php echo substr($episode->created_date, 11, 5) ?></td>
      </tr>
      <tr>
        <td class="title">Last Modified by</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td><?php echo $episode->usermodified->fullName ?></td>
        <td><?php echo $episode->NHSDate('last_modified_date') ?></td>
        <td><?php echo substr(
            $episode->last_modified_date,
            11,
            5
            ) ?></td>
      </tr>
      </tbody>
    </table>
  </div>

  <div class="element full ">
    <header class="element-header">
      <h3 class="element-title"><?= Episode::getEpisodeLabel() ?> Status:</h3>
    </header>
    <div class="element-data full-width">
      <div class="data-value"><?php echo $episode->status->name ?></div>
    </div>
  </div>

<?php } ?>
</main>
