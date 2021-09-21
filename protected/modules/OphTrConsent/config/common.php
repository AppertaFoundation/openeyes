<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'params' => array(
        'puppeteer_footer_left_OphTrConsent' => '{{DOCREF}}{{BARCODE}}{{PATIENT_NAME}}{{PATIENT_PRIMARY_IDENTIFIER}}{{PATIENT_SECONDARY_IDENTIFIER}}{{PATIENT_DOB}}{{PROCEDURES}}',
        'puppeteer_bottom_margin_OphTrConsent' => '26mm',
        'admin_menu' => array(
            'OphTrConsent' => array(
                'Leaflets' => array('uri' => '/OphTrConsent/oeadmin/Leaflets/list'),
                'Leaflet Subspecialty context_firm_label Assignment' => array('uri' => '/OphTrConsent/oeadmin/LeafletSubspecialtyFirm/list'),
                'Additional Risks' => array('uri' => '/OphTrConsent/oeadmin/AdditionalRisks/list'),
                'Extra Procedures' => array('uri' => '/OphTrConsent/oeadmin/ExtraProcedures/list'),
                'Extra Procedures Subspecialty Assignment' => array('uri' => '/OphTrConsent/oeadmin/ExtraProcedures/EditSubspecialty'),
                'Layouts' => array('uri' => '/OphTrConsent/oeadmin/ConsentLayouts/list'),
                'Template' => array('uri' => '/OphTrConsent/oeadmin/Template/list'),
                'Relationship to patient' => array('module' => 'OphTrConsent', 'uri' => '/OphTrConsent/oeadmin/PatientRelationship/list'),
                'Contact method' => array('module' => 'OphTrConsent', 'uri' => '/OphTrConsent/oeadmin/PatientContactMethod/list'),
            ),
        ),
    ),
);
