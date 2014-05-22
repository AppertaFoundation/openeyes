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

Yii::app()->assetManager->registerScriptFile('js/medication.js');
?>
<section id="medication" class="box patient-info associated-data js-toggle-container">

	<header class="box-header">
		<h3 class="box-title"><span class="icon-patient-clinician-hd_flag"></span>Medication</h3>
		<img src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" class="loader hidden"/>
		<a href="#" class="toggle-trigger toggle-hide js-toggle"><span class="icon-showhide">Show/hide this section</span></a>
	</header>

	<div class="js-toggle-body">
		<div id="medication_list">
			<?php $this->renderPartial("/medication/list", array("patient" => $this->patient, "current" => true)); ?>
			<?php $this->renderPartial("/medication/list", array("patient" => $this->patient, "current" => false)); ?>
		</div>

		<?php if ($this->checkAccess('OprnEditMedication')): ?>
			<div class="box-actions">
				<button type="button" id="medication_add" class="secondary small">Add Medication</button>
			</div>
			<div id="medication_form" class="medication_form hidden"></div>
			<div id="medication_stop" class="medication_form hidden"><?php $this->renderPartial("/medication/stop") ?></div>
		<?php endif ?>
	</div>
</section>
