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
?>
<?php
$this->beginContent('//patient/event_container', array('no_face' => false));
$assetAliasPath = 'application.modules.OphInBiometry.assets';
$this->moduleNameCssClass .= ' edit';
?>

<div class="data-group">
  <div class="cols-12 column">
      <div class="alert-box issue">
            <?php if (count($imported_events) > 0) { ?>
              The following Biometry reports are available for this patient. <b>Please select a report.</b>
            <?php } else { ?>
              There are no imported events.
            <?php } ?>
      </div>

    <section class="element view full priority">
        <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'biometry-event-select',
            'enableAjaxValidation' => false,
        ));

        $this->displayErrors($errors) ?>

      <div class="element-fields">
        <fieldset>
            <input id="biometry_type" type="hidden" name="SelectBiometry"/>
            <header class="element-header"><h3 class="element-title">Select Biometry Report</h3></header>
            <div class="element-data full-width">
                <table class="js-select-biometry clickable-rows large standard">
                    <thead>
                    <tr>
                        <th>Date and time</th>
                        <th>Machine</th>
                        <th>Instrument</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($imported_events as $imported_event) { ?>
                        <tr class="highlight booking">
                            <td>
                                <?php
                                $eventDateTime = explode(' ', $imported_event->event->event_date);
                                echo date('j M Y', strtotime($eventDateTime[0])) . ' ' . $eventDateTime[1]; ?>
                            </td>
                            <td>
                                <?= $imported_event->device_name . ' (' . $imported_event->device_id . ')' ?>
                            </td>
                            <td>
                                <?= $imported_event->device_manufacturer . ' ' . $imported_event->device_model; ?>
                            </td>
                            <td><i id ="biometry<?php echo $imported_event->id?>" class="oe-i direction-right-circle medium pad"></i></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </fieldset>
      </div>

        <?php $this->displayErrors($errors, true) ?>
        <?php $this->endWidget(); ?>
    </section>
        <?php
        if (!$this->isManualEntryDisabled()) {
            ?>
        <a href="/OphInBiometry/Default/create?patient_id=<?php echo $this->patient->id ?>&force_manual=1"
           style="float:right;margin:10px;">I don't want to select a report let me enter the data manually</a>
        <?php } ?>
  </div>
</div>

<?php $this->endContent(); ?>

<script type="text/javascript">
    $( ".clickable-rows.large.standard tbody tr" ).on( "click", function() {
        $("#biometry_type").attr('value', $(this).find('i').attr('id'));
        $("#biometry-event-select").submit();
    });
</script>