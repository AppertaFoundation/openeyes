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
<div class="box admin">
	<header class="box-header">
		<h2 class="box-title">Waiting list contact rules</h2>
		<div class="box-actions">
			<?php echo EventAction::button('Add', 'add_letter_contact_rule', null, array('class' => 'button small'))->toHtml()?>
		</div>
	</header>

	<form id="rulestest" class="panel">
		<fieldset class="row field-row">
			<legend class="large-1 column align">
				Test:
			</legend>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_site_id', '', Site::model()->getListForCurrentInstitution('name'), array('empty' => '- Site -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_service_id', '', CHtml::listData(Service::model()->findAll(array('order' => 'name asc')), 'id', 'name'), array('empty' => '- Service -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_firm_id', '', array(), array('empty' => '- Firm -'))?>
			</div>
			<div class="large-2 column end">
				<?php echo CHtml::dropDownList('lcr_is_child', '', array('' => '- Child/adult -', '1' => 'Child', '0' => 'Adult'))?>
			</div>
		</fieldset>
	</form>

	<div id="nomatch" class="alert-box alert hidden">No match</div>

	<form id="rules" class="panel">
		<?php
        $this->widget('CTreeView', array(
            'data' => $data,
        ))?>
	</form>
</div>


