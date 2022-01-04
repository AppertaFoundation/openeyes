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

return array(
    'params' => array(
        // fixed details for admin functionality
        'admin_menu' => array(
            'OphCoTherapyapplication' => [
                'Diagnoses' => ['uri' => '/OphCoTherapyapplication/admin/viewDiagnoses', 'restricted' => array('admin')],
                'Treatments' => ['uri' => '/OphCoTherapyapplication/admin/viewTreatments', 'restricted' => array('admin')],
                'Decision Trees' => '/OphCoTherapyapplication/admin/viewDecisionTrees',
                'File Collections' => '/OphCoTherapyapplication/admin/viewFileCollections',
                'Email Recipients' => '/OphCoTherapyapplication/admin/viewEmailRecipients',
            ]
        ),
        'reports' => array(
            'Therapy applications' => '/OphCoTherapyapplication/report/applications',
            'Pending applications' => '/OphCoTherapyapplication/report/pendingApplications',
        ),
        // The email address that sends therapy applications (key/value pair of address to name)
        // 'OphCoTherapyapplication_sender_email' => array('email@test.com' => 'Test'),
        // The email address displayed in the standard non-compliant form
        // 'OphCoTherapyapplication_applicant_email' => 'armd@nhs.net',
        // postal details of the chief pharmacist (string of name and address)
        // 'OphCoTherapyapplication_chief_pharmacist' => '',
        // contact details of the chief pharmacist (string)
        // 'OphCoTherapyapplication_chief_pharmacist_contact' => '',
        //'OphCoTherapyapplication_email_size_limit' => '10MB',
        // whether the user submitting the application should receive a copy of the submission email
        //'OphCoTherapyapplication_cc_applicant' => true,
    ),
);
