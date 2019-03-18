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
<section class="element view full  view-anaesthetic">
    <header class="element-header">
        <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
    </header>
    <div class="element-data full-width">
        <div class="data-value listview-expand-collapse">
            <div class="cols-11">
                <?php if (count($element->anaesthetic_type) > 1 ||
                    (count($element->anaesthetic_type) == 1 && !$element->hasAnaestheticType("GA") && !$element->hasAnaestheticType("NoA"))) {
                    echo $this->renderPartial(
                        'view_Element_OphTrOperationnote_Anaesthetic_full',
                        array('element' => $element)
                    );
                } else {
                    echo $this->renderPartial(
                        'view_Element_OphTrOperationnote_Anaesthetic_partial',
                        array('element' => $element)
                    );
                } ?>
            </div>
        </div>
    </div>
</section>