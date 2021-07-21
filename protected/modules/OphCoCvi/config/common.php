<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

return array(
    'params' => array(
        'admin_structure' => array(
            'CVI' => array(
                'Clinical Disorder Section' => '/OphCoCvi/admin/clinicalDisorderSection',
                'Clinical Disorder' => '/OphCoCvi/admin/clinicalDisorders',
                'Patient Factor' => '/OphCoCvi/admin/patientFactor',
                'Employment Status' => '/OphCoCvi/admin/employmentStatus',
                'Contact Urgency' => '/OphCoCvi/admin/contactUrgency',
                'Field of Vision' => '/OphCoCvi/admin/fieldOfVision',
                'Low Vision Status' => '/OphCoCvi/admin/lowVisionStatus',
                'Preferred Info Format' => '/OphCoCvi/admin/preferredInfoFormat',
                'Local Authorities' => '/OphCoCvi/localAuthoritiesAdmin/list',
            ),
        ),
        'menu_bar_items' => array(
            'admin' => array(
                'restricted' => array(array('OprnCreateCvi', 'user_id')),
                'sub' => array(
                    'cvi' => array(
                        'title' => 'CVI',
                        'position' => 7,
                        'restricted' => array(array('OprnCreateCvi', 'user_id')),
                        'uri' => '/OphCoCvi/Default/list',
                    ),
                    'la' => array(
                        'title' => 'LA Admin',
                        'uri' => '/OphCoCvi/LocalAuthoritiesAdmin/list',
                        'position' => 8,
                        'restricted' => array(array('OprnCreateCvi', 'user_id')),
                    ),
                )
            ),
        ),
        'patient_summary_render' => array(
            'cvi_status' => array(
                'module' => 'OphCoCvi',
                'method' => 'patientSummaryRender'
            )
        ),
        'additional_rulesets' => array(
            array(
                'namespace' => 'OphCoCvi',
                'class' => 'OEModule\OphCoCvi\components\OphCoCvi_AuthRules'
            ),
        ),
        'ophcocvi_allow_all_consultants' => false,
        'thresholds' => array(
            'visualAcuity' => array(
                'alert_base_value' => 81
            )
        ),
    )
);
