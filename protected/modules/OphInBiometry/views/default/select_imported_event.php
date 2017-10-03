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
    $this->beginContent('//patient/event_container');
    $assetAliasPath = 'application.modules.OphInBiometry.assets';
    $this->moduleNameCssClass .= ' edit';
?>

	<div class="row">
		<div class="large-12 column">

			<section class="element">
				<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                        'id' => 'biometry-event-select',
                        'enableAjaxValidation' => false,
                    ));

                    // Event actions
                    $this->event_actions[] = EventAction::button('Continue', 'save', array('level' => 'secondary'), array('form' => 'biometry-event-select', 'class' => 'button small'));
                ?>
					<?php  $this->displayErrors($errors)?>

					<header class="element-header">
						<h3 class="element-title">Select Biometry Report</h3>
					</header>

					<div class="element-fields">

						<div class="field-row">
							<div class="field-info">
								<?php if (count($imported_events) > 0) {?>
									The following Biometry reports are available for this patient:
								<?php } else {?>
									There are no imported events.
								<?php }?>
							</div>
						</div>

						<fieldset class="row field-row">
							<div class="large-12 column end">
								<?php foreach ($imported_events as $imported_event) {?>
									<label class="highlight booking">
										<span class="row">
											<span class="large-1 column">
												<input type="radio" value="biometry<?php echo $imported_event->id?>" name="SelectBiometry" />
											</span>
											<span class="large-1 column">
												<img src="<?php echo Yii::app()->assetManager->createUrl('img/small.png', $assetAliasPath)?>" alt="op" style="height:15px" />
											</span>
											<span class="large-2 column">
												<b>Date and time: </b><br>
												<span id="date_and_time"><?php
                                                                                $eventDateTime = explode(' ', $imported_event->event->event_date);
    echo date('j M Y', strtotime($eventDateTime[0])).' '.$eventDateTime[1];
    ?></span>
											</span>
											<span class="large-1 column">
												<b>Machine:</b>
											</span>
											<span class="large-3 column">
												<?php
                                                    echo $imported_event->device_name.' ('.$imported_event->device_id.')';
    ?>
											</span>
											<span class="large-1 column">
												<b>Instrument:</b>
											</span>
											<span class="large-3 column">
												<?php
                                                echo $imported_event->device_manufacturer.' '.$imported_event->device_model;
    ?>
											</span>

										</span>
									</label>
								<?php }?>
							</div>
						</fieldset>
					</div>

					<?php $this->displayErrors($errors, true)?>
				<?php $this->endWidget(); ?>
			</section>
			<?php
            if (!$this->isManualEntryDisabled()) {
                ?>
			<a href="/OphInBiometry/Default/create?patient_id=<?php echo$this->patient->id ?>&force_manual=1" style="float:right;margin:10px;">I don't want to select a report let me enter the data manually</a>
		<?php } ?>
		</div>
	</div>

<?php $this->endContent();?>
