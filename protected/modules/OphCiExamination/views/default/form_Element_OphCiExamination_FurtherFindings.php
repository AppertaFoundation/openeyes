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
<div class="sub-element-fields">
	<div class="field-row furtherfindings-multi-select">
		<?php echo $form->multiSelectListFreeText($element, CHtml::modelName($element) . '[further_findings_assignment]',
            'further_findings_assignment', 'finding_id', CHtml::encodeArray(CHtml::listData(
                Finding::model()->activeOrPk($element->furtherFindingsAssigned)->bySubspecialty($this->firm->getSubspecialty())->findAll(),
                'id',
                'name'
            )), array(), array(
                'empty' => '-- Add --',
                'label' => 'Findings',
                'nowrapper' => true,
                'requires_description_field' => 'requires_description'
            ), false, true, 'No further findings', true, true, array(), 'Finding') ?>
	</div>
</div>
