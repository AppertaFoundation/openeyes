<?php
/**
 * (C) Copyright Apperta Foundation 2020
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

return array(
    'q1' => array(
        'id' => 1,
        'element_type_id' => 476,
        'question' => "Have there been any changes in the patient's health since Pre-operative Assessment?",
        'type' => 'RADIO',
        'mandatory' => 1,
        'is_hidden' => 0,
        'requires_answer' => 1,
        'is_comment_field_required' => 1,
        'display_order' => 10,
    ),
    'q2' => array(
        'id' => 2,
        'element_type_id' => 478,
        'question' => 'Identity Bracelet',
        'type' => 'RADIO',
        'mandatory' => null,
        'is_hidden' => 0,
        'requires_answer' => 1,
        'is_comment_field_required' => 1,
        'display_order' => 20,
    ),
    'q3' => array(
        'id' => 3,
        'element_type_id' => 479,
        'question' => 'Has the patient been seen by Anaesthetist prior to escort to Theatre',
        'type' => 'RADIO',
        'mandatory' => null,
        'is_hidden' => 0,
        'requires_answer' => 1,
        'is_comment_field_required' => 1,
        'display_order' => 30,
    ),
    'q4' => array(
        'id' => 4,
        'element_type_id' => 480,
        'question' => 'Oxygen therapy in situ',
        'type' => 'RADIO',
        'mandatory' => null,
        'is_hidden' => 0,
        'requires_answer' => 1,
        'is_comment_field_required' => 1,
        'display_order' => 40,
    ),
    'q5' => array(
        'id' => 5,
        'element_type_id' => 481,
        'question' => 'Interpreter present',
        'type' => 'RADIO',
        'mandatory' => null,
        'is_hidden' => 0,
        'requires_answer' => 1,
        'is_comment_field_required' => 1,
        'display_order' => 50,
    ),
    'q6' => array(
        'id' => 6,
        'element_type_id' => 482,
        'question' => 'Is the patient being transferred to another Ward?',
        'type' => 'RADIO',
        'mandatory' => 1,
        'is_hidden' => 0,
        'requires_answer' => 1,
        'is_comment_field_required' => 1,
        'display_order' => 60,
    ),
);
