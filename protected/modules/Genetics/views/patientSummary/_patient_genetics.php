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
?>
<section class="box patient-info js-toggle-container">
	<h3 class="box-title">Genetics:</h3>
	<a href="#" class="toggle-trigger toggle-hide js-toggle">
		<span class="icon-showhide">
			Show/hide this section
		</span>
	</a>
	<div class="js-toggle-body">
		<?php if ($pp = $api->findPatientPedigree($patient->id)) {?>
			<div class="row data-row">
				<div class="large-4 column">
					<div class="data-label">Pedigree ID:</div>
				</div>
				<div class="large-8 column">
					<div class="data-value"><?php echo $pp->pedigree_id?> (<?php echo CHtml::link('View',Yii::app()->createUrl('/Genetics/default/viewPedigree/'.$pp->pedigree_id))?>)</div>
				</div>
			</div>
			<?php if ($genetics_patient = GeneticsPatient::model()->find('patient_id=?',array($patient->id))) {?>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo $genetics_patient->getAttributeLabel('comments')?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo  Yii::app()->format->ntext($genetics_patient->comments)?></div>
					</div>
				</div>
			<?php } ?>
		<?php }else{?>
			<div class="row data-row">
				<div class="large-12 column">
					<div class="data-label">This patient has no recorded pedigree.</div>
				</div>
			</div>
		<?php }?>
		<?php
		if ($api = Yii::app()->moduleAPI->get('OphInBloodsample')) {
			if($events = $api->getEventsByPatient($this->patient)) {?>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label">Blood Sample Events:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value">
							<?php foreach ($events as $event) {
								echo EventNavigation::SmallIcon($event);
								echo ' '.Helper::convertMySQL2NHS($event->event_date);
								?>
								<br />
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
		}
		?>
		<?php
		if ($api = Yii::app()->moduleAPI->get('OphInDnaextraction')) {
			if($events = $api->getEventsByPatient($this->patient)) {?>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label">DNA Extraction Events:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value">
							<?php foreach ($events as $event) {
								echo EventNavigation::SmallIcon($event);
								echo ' '.Helper::convertMySQL2NHS($event->event_date);
								?>
								<br />
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
		}
		?>
		<?php
		if ($api = Yii::app()->moduleAPI->get('OphInGenetictest')) {
			if($events = $api->getEventsByPatient($this->patient)) {?>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label">Genetic Test Events:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value">
							<?php foreach ($events as $event) {
								echo EventNavigation::SmallIcon($event);
								echo ' '.Helper::convertMySQL2NHS($event->event_date);
								?>
								<br />
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
		}
		?>
	</div>
</section>
