<?php

/**
 * OpenEyes.
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020 Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$config = array(
    'components' => array(
        /*
        'log' => array(
            'routes' => array(
                 // SQL logging
                'system' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, warning, error',
                    'categories' => 'system.db.CDbCommand',
                    'logFile' => 'sql.log',
                ),
                // System logging
                'system' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, warning, error',
                    'categories' => 'system.*',
                    'logFile' => 'system.log',
                ),
                // Profiling
                'profile' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'profile',
                    'logFile' => 'profile.log',
                ),
                // User activity logging
                'user' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'user',
                    'logfile' => 'user.log',
                    'filter' => array(
                        'class' => 'CLogFilter',
                        'prefixSession' => false,
                        'prefixUser' => true,
                        'logUser' => true,
                        'logVars' => array('_GET','_POST'),
                    ),
                ),
                // Log to browser
                'browser' => array(
                    'class' => 'CWebLogRoute',
                    // 'enabled' => YII_DEBUG,
                    // 'levels' => 'error, warning, trace, notice',
                    // 'categories' => 'application',
                    'showInFireBug' => true,
                ),
            ),
        ),
        */
    ),

    'params' => array(
        //'pseudonymise_patient_details' => false,
        //'ab_testing' => false,
        'local_users' => array('admin','api','docman_user','payload_processor'),
        //'log_events' => true,
        //'default_site_code' => '',
        'OphCoTherapyapplication_sender_email' => array('email@example.com' => 'Test'),
        //// flag to turn on drag and drop sorting for dashboards
        // 'dashboard_sortable' => true
        //// default start time used for automatic worklist definitions
        //'worklist_default_start_time' => 'H:i',
        //// default end time used for automatic worklist definitions
        //'worklist_default_end_time' => 'H:i',
        //// number of patients to show on each worklist dashboard render
        //'worklist_default_pagination_size' => int,
        //// number of days in the future to retrieve worklists for the automatic dashboard render
        //'worklist_dashboard_future_days' => int,
        //// days of the week to be ignored when determining which worklists to render - Mon, Tue etc
        // 'worklist_dashboard_skip_days' => array('NONE'),
        //// how far in advance worklists should be generated for matching
        // 'worklist_default_generation_limit' => interval string (e.g. 3 months)
        //// override edit checks on definitions so they can always be edited (use at own peril)
        //'worklist_always_allow_definition_edit' => bool
        //// whether we should render empty worklists in the dashboard or not
        // 'worklist_show_empty' => bool
        //// allow duplicate entries on an automatic worklist for a patient
        // 'worklist_allow_duplicate_patients' => bool
        //// any appointments sent in before this date will not trigger errors when sent in
        // 'worklist_ignore_date => 'Y-m-d',
        // Set it to true if unidentified roles from SSO token should NOT be ignored

        'correspondence_export_url' => 'localhost',
    ),
);

return $config;
