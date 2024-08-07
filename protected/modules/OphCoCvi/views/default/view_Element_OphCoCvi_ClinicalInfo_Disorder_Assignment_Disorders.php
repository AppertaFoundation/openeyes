<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

foreach (array_merge($this->getDisorderSections($element->patient_type),$element->getInactiveSectionsToDisplay()) as $disorder_section) {
    ?>
        <div class="collapse-data-header-icon collapse"><h3><?php echo \CHtml::encode($disorder_section->name); ?></h3></div>
        <div class="row data-row">
            <div class="large-12 column end">
                <div class="row">
                    <div class="element-eye column">
                        <?php $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders_Side', array(
                            'element' => $element,
                            'disorder_section' => $disorder_section,
                            'inactive_disorders' => ($disorder_section->active == 0 || $disorder_section->deleted == 1)
                                ? []
                                : $element->getInactiveDisorders($disorder_section->name)
                        )) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
}
