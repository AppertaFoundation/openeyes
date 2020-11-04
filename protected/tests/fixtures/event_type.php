<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
return array(
    'event_type1' => array(
        'id' => 1000,
        'name' => 'Operation note 2',
        'event_group_id' => 5,
        'class_name' => 'OphTrOperationnote',
        'support_services' => 0,
    ),
    'event_type2' => array(
        'id' => 1001,
        'name' => 'Operation booking 2',
        'event_group_id' => 5,
        'class_name' => 'OphTrOperationbooking',
        'support_services' => 0,
    ),
    'event_type3' => array(
        'id' => 1002,
        'name' => 'Examination 2',
        'event_group_id' => 1,
        'class_name' => 'OphCiExamination',
        'support_services' => 0,
    ),
    'event_type4' => array(
        'id' => 1003,
        'name' => 'Consent form 2',
        'event_group_id' => 5,
        'class_name' => 'OphTrConsent',
        'support_services' => 0,
    ),
    'event_type5' => array(
        'id' => 1004,
        'name' => 'Therapy application 2',
        'event_group_id' => 2,
        'class_name' => 'OphCoTherapyapplication',
        'support_services' => 0,
    ),
    'event_type6' => array(
        'id' => 1005,
        'name' => 'Laser 2',
        'event_group_id' => 5,
        'class_name' => 'OphTrLaser',
        'support_services' => 0,
    ),
    'event_type7' => array(
        'id' => 1006,
        'name' => 'Anaesthetic Satisfaction Audit 2',
        'event_group_id' => 9,
        'class_name' => 'OphOuAnaestheticsatisfactionaudit',
        'support_services' => 0,
    ),
    'event_type8' => array(
        'id' => 1007,
        'name' => 'Correspondence 2',
        'event_group_id' => 2,
        'class_name' => 'OphCoCorrespondence',
        'support_services' => 1,
    ),
    'event_type9' => array(
        'id' => 1008,
        'name' => 'Prescription 2',
        'event_group_id' => 6,
        'class_name' => 'OphDrPrescription',
        'support_services' => 0,
    ),
    'event_type10' => array(
        'id' => 1009,
        'name' => 'Ultrasound 2',
        'event_group_id' => 4,
        'class_name' => 'OphImUltrasound',
        'support_services' => 0,
    ),
    'event_type11' => array(
        'id' => 1010,
        'name' => 'Cataract Referral 2',
        'event_group_id' => 2,
        'class_name' => 'OphCoCataractReferral',
        'support_services' => 0,
    ),
    'event_type12' => array(
        'id' => 1011,
        'name' => 'Inheriting Examination',
        'event_group_id' => 1,
        'class_name' => 'OphCiInheritExamination',
        'support_services' => 0,
        'parent_event_type_id' => 1002,
    ),
    'event_type13' => array(
        'id' => 1012,
        'name' => 'Legacy letters',
        'event_group_id' => 1,
        'class_name' => 'OphLeEpatientletter',
        'support_services' => 0,
    ),
    'event_type14' => array(
        'id' => 27,
        'name' => 'Examination',
        'event_group_id' => 1,
        'class_name' => 'OphCiExamination',
        'support_services' => 0,
        'show_attachments' => 1,
    ),
    'event_type15' => array(
        'id' => 44,
        'name' => 'Operation Checklists',
        'event_group_id' => 1,
        'class_name' => 'OphTrOperationchecklists',
        'support_services' => 0,
        'show_attachments' => 1,
    ),
);
