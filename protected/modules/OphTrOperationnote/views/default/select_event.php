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
    $assetAliasPath = 'application.modules.OphTrOperationbooking.assets';
    $this->moduleNameCssClass .= ' edit';
?>

<?php
$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);
?>

	<div class="row">
		<div class="large-12 column">

			<section class="element">
				<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                        'id' => 'operation-note-select',
                        'enableAjaxValidation' => false,
                    ));

                    // Event actions
                    $this->event_actions[] = EventAction::button('Create Operation Note', 'save', array('level' => 'secondary'), array('form' => 'operation-note-select', 'class' => 'button small'));
                ?>
					<?php  $this->displayErrors($errors)?>

    <?php if ($warnings) { ?>
        <div class="row">
            <div class="large-12 column">
                <div class="alert-box patient with-icon">
                    <?php foreach ($warnings as $warn) {?>
                        <strong><?php echo $warn['long_msg']; ?></strong>
                        - <?php echo $warn['details'];
                    }?>
                </div>
            </div>
        </div>
    <?php }?>

					<header class="element-header">
						<h3 class="element-title">Create Operation Note</h3>
					</header>

					<div class="element-fields">

						<div class="field-row">
							<div class="field-info">
								<?php if (count($operations) > 0) {?>
									Please indicate whether this operation note relates to a booking or an unbooked emergency:
								<?php } else {?>
									There are no open bookings in the current episode so only an emergency operation note can be created.
								<?php }?>
							</div>
						</div>

						<fieldset class="row field-row">
							<legend class="large-2 column">Select:</legend>
							<div class="large-6 column end">
								<?php foreach ($operations as $operation) {?>
									<label class="highlight booking">
										<span class="row">
											<span class="large-1 column">
												<input type="radio" value="booking<?= $operation->event_id ?>" name="SelectBooking" />
											</span>
											<span class="large-1 column">
												<img src="<?php echo Yii::app()->assetManager->createUrl('img/small.png', $assetAliasPath)?>" alt="op" style="height:15px" />
											</span>
											<span class="large-3 column <?php echo $theatre_diary_disabled ? 'hide' : ''?>">
												<?php if(!$theatre_diary_disabled){ 
															if($operation->booking){
																echo $operation->booking->session->NHSDate('date'); 
															}
														} ?>
											</span>

											<span class="large-3 column">
												Operation
											</span>
                                            <span class="large-3 column">
                                              <?= $operation->comments; ?>
											</span>
											<span class="large-4 column">
												<?php

                                                    $procedures = $operation->procedures;
                                                    $eye = $operation->eye;

                                                    foreach ($procedures as $i => $procedure) {
                                                        if ($i > 0) { echo '<br/>'; }
                                                        echo $eye->name.' '.$procedure->term;
                                                    }
                                               ?>
											</span>
										</span>
									</label>
								<?php }?>
								<label class="highlight booking">
									<span class="row">
										<span class="large-1 column">
											<input type="radio" value="emergency" name="SelectBooking" <?php if (count($operations) == 0) {?>checked="checked" <?php }?>/>
										</span>
										<span class="large-11 column">
											Emergency
										</span>
									</span>
								</label>
							</div>
						</fieldset>
					</div>

					<?php $this->displayErrors($errors, true)?>
				<?php $this->endWidget(); ?>
			</section>
		</div>
	</div>

<?php $this->endContent();?>
