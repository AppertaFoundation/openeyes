<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-fields">
    <?= $form->multiSelectList(
        $element,
        'MultiSelect_assessment',
        'assessment',
        'ophinvisualfields_result_assessment_id',
        CHtml::listData(
            OphInVisualfields_Result_Assessment::model()->findAll(array('order' => 'display_order asc')),
            'id',
            'name'
        ),
        $element->ophinvisualfields_result_assessment_defaults,
        array(
            'empty' => 'Select',
            'class' => 'linked-fields',
            'data-linked-fields' => 'other',
            'data-linked-values' => 'Other',
            'nowrapper' => true,
        )
    ) ?>
    <?= $form->textArea(
        $element,
        'other',
        array('rows' => 4, 'no_label' => true),
        !$element->hasMultiSelectValue('assessment', 'Other'),
        array('placeholder' => 'Other - please specify')
    ) ?>
</div>
