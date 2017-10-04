<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$this->renderPartial('//base/_messages');
$model = OEModule\OphCiExamination\models\SocialHistory::model();
?>
<div class="box admin">
	<h2>Social History</h2>
	<ul>
		<li><a href="/OphCiExamination/admin/socialHistoryOccupation"><?= CHtml::encode($model->getAttributeLabel('occupation_id')) ?></a></li>
		<li><a href="/OphCiExamination/admin/socialHistoryDrivingStatus"><?= CHtml::encode($model->getAttributeLabel('driving_statuses')) ?></a></li>
		<li><a href="/OphCiExamination/admin/socialHistorySmokingStatus"><?= CHtml::encode($model->getAttributeLabel('smoking_status_id')) ?></a></li>
		<li><a href="/OphCiExamination/admin/socialHistoryAccommodation"><?= CHtml::encode($model->getAttributeLabel('accommodation_id')) ?></a></li>
	</ul>
</div>
