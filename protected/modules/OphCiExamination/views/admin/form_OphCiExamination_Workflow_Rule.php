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
<div class="field-row">
	<h2>Edit workflow rule</h2>
</div>
<?php echo $form->errorSummary($model)?>
<?php echo $form->dropDownList($model, 'firm_id', CHtml::listData(Firm::model()->activeOrPk($model->firm_id)->findAll(), 'id', 'nameAndSubspecialty'), array('empty' => '- All -'))?>
<?php echo $form->dropDownList($model, 'episode_status_id', 'EpisodeStatus', array('empty' => '- All -'))?>
<?php echo $form->dropDownList($model, 'subspecialty_id', 'Subspecialty', array('empty' => '- All -'))?>
<?php echo $form->dropDownList($model, 'workflow_id', 'OEModule\OphCiExamination\models\OphCiExamination_Workflow')?>
