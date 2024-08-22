<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers\traits;

use OEModule\OphCiExamination\models\OphCiExamination_AttributeElement;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeOption;

trait AdminForElementAttribute
{
    public function actionManageElementAttributes()
    {
        $institution_id = !empty($_GET['attribute_element_id'])
                        ? OphCiExamination_AttributeElement::model()->findByPk($_GET['attribute_element_id'])->attribute->institution_id
                        : \Yii::app()->session['selected_institution_id'];

        $attribute_elements_for_institution = array_reduce(
            OphCiExamination_AttributeElement::model()->with('attribute')->findAll(
                'institution_id = :institution_id OR institution_id IS NULL',
                [':institution_id' => $institution_id]
            ),
            static function ($choices, $attribute_element) {
                $choices[$attribute_element->id] = $attribute_element->element_type->name . ' - ' . $attribute_element->attribute->name;
                return $choices;
            },
            []
        );

        $this->genericAdmin(
            'Manage Element Attributes',
            OphCiExamination_AttributeOption::class,
            [
                'filter_fields' => [
                    [
                        'field' => 'attribute_element_id',
                        'model' => OphCiExamination_AttributeElement::class,
                        'choices' => $attribute_elements_for_institution,
                        'no_empty' => true
                    ],
                ],
                'extra_fields' => [
                    ['field' => 'delimiter', 'type' => 'text'],
                    ['field' => 'subspecialty_id', 'type' => 'lookup', 'model' => 'Subspecialty'],
                    [
                        'field' => 'excluded_subspecialties',
                        'type' => 'multilookup',
                        'noSelectionsMessage' => 'No Exclusions',
                        'htmlOptions' => [
                            'empty' => 'Select',
                            'nowrapper' => true,
                        ],
                        'options' => \CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name'),
                    ],
                ],
                'div_wrapper_class' => 'cols-10',
                'return_url' => '/oeadmin/examinationElementAttributes/list',
            ]
        );
    }
}
