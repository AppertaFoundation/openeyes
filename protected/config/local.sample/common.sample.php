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

// If the old db.conf file (pre docker) exists, use it. Else read environment variable, else read docker secrets
// Note, docker secrets are the recommended approach for docker environments

if (file_exists('/etc/openeyes/db.conf')) {
    $db = parse_ini_file('/etc/openeyes/db.conf');
} else {
    $db = array(
        'host' => getenv('DATABASE_HOST') ? getenv('DATABASE_HOST') : 'localhost',
        'port' => getenv('DATABASE_PORT') ? getenv('DATABASE_PORT') : '3306',
        'dbname' => getenv('DATABASE_NAME') ? getenv('DATABASE_NAME') : 'openeyes',
        'username' => getenv('DATABASE_USER') ? getenv('DATABASE_USER') : (rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ? rtrim(file_get_contents("/run/secrets/DATABASE_USER")) : 'openeyes'),
        'password' => getenv('DATABASE_PASS') ? getenv('DATABASE_PASS') : (rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ? rtrim(file_get_contents("/run/secrets/DATABASE_PASS")) : 'openeyes'),
    );
}

$config = array(
    'components' => array(
        'db' => array(
            'connectionString' => "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}",
            'username' => $db['username'],
            'password' => $db['password'],
        ),
        'mailer' => array(
            // Setting the mailer mode to null will suppress email
            //'mode' => null
            // Mail can be diverted by setting the divert array
            //'divert' => array('foo@example.org', 'bar@example.org')
        ),
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
                ),
            ),
        ),
        */
    ),

    'modules' => array(
        'eyedraw',
        'OphCiExamination' => array('class' => '\OEModule\OphCiExamination\OphCiExaminationModule'),
        'OphCoCorrespondence',
        'OphCiPhasing',
        'OphTrIntravitrealinjection',
        'OphCoTherapyapplication',
        'OphDrPrescription',
        'OphTrConsent',
        'OphTrOperationnote',
        'OphTrOperationbooking',
        'OphTrLaser',
        'PatientTicketing' => array('class' => '\OEModule\PatientTicketing\PatientTicketingModule'),
        'OphInVisualfields',
        'OphInBiometry',
        'OphCoMessaging' => array('class' => '\OEModule\OphCoMessaging\OphCoMessagingModule'),
        'PASAPI' => array('class' => '\OEModule\PASAPI\PASAPIModule'),
        'OphInLabResults',
        'OphCoCvi' => array('class' => '\OEModule\OphCoCvi\OphCoCviModule'),
        // Uncomment next section if you want to use the genetics module
        /*'Genetics',
        'OphInDnasample',
        'OphInDnaextraction',
        'OphInGeneticresults',*/
        'OphCoDocument',
        'OphCiDidNotAttend',
        'OphGeneric',
        'OECaseSearch',
        'OETrial',
        'OphOuCatprom5'
    ),

    'params' => array(
        //'pseudonymise_patient_details' => false,
        //'ab_testing' => false,
        'auth_source' => getenv('OE_LDAP_SERVER') ? 'LDAP' : 'BASIC',    // BASIC or LDAP
        // This is used in contact page
        'ldap_admin_dn' => 'CN=openeyes,CN=Users,dc=example,dc=com',
        'ldap_password' => '',
        'ldap_dn' => 'CN=Users,dc=example,dc=com',
        'local_users' => array('admin', 'username'),
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
        
        /**
        * Filename format for the PDF and XML files output by the docman export
        * possible values:
        *
        * format1 => OPENEYES_<eventId>_<randomInteger>.pdf [current format, default if parameter not specified]
        * format2 => <hosnum>_<yyyyMMddhhmm>_<eventId>.pdf
        * format3 => <hosnum>_edtdep-OEY_yyyyMMdd_hhmmss_<eventId>.pdf
        * format4 => <hosnum>_<yyyyMMddhhmmss>_<eventId>__<doctype>_.pdf
        */
        'docman_filename_format' => 'format1',
        // set this to false if you want to suppress XML output
        'docman_generate_xml' => true,
    ),
);

return $config;
