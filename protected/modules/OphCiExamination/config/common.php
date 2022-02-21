<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return [
    'components' => [
        'event' => [
            'observers' => [
                'after_medications_save' => [
                    'update_patient_risks' => [
                        'class' => 'OEModule\OphCiExamination\components\HistoryRisksManager',
                        'method' => 'addPatientMedicationRisks'
                    ]
                ],
                'step_started' => [
                    'create_or_update_event' => [
                        'class' => 'OEModule\OphCiExamination\components\PathstepObserver',
                        'method' => 'createOrUpdateEvent',
                    ]
                ],
            ]
        ]
    ],
    'params' => [
        'ophciexamination_drgrading_type_required' => false,
        'ophciexamination_visualacuity_correspondence_unit' => 'Snellen Metre',

        'reports' => [
            'Ready for second eye (unbooked)' => '/OphCiExamination/report/readyForSecondEyeUnbooked',
            'A&E Patient List' => '/OphCiExamination/report/AE'
        ],
    ],

    'aliases' => [
        'ExaminationAdmin' => 'OEModule.OphCiExamination.modules.ExaminationAdmin',
    ],
    'modules' => ['ExaminationAdmin'],
];
