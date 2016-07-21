<?php
/**
* OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

if(file_exists('/etc/openeyes/db.conf')) {
	$db = parse_ini_file('/etc/openeyes/db.conf');
} else {
	$db = array(
		'host' => '127.0.0.1',
		'port'	=> '=3306',
		'dbname'	=> 'openeyes',
		'username'	=> 'openeyes',
		'password'	=> 'oe_test',
	);
}


$config = array(
	'components' => array(
		'db' => array(
			'connectionString' => "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}",
			'username' => $db['username'],
			'password' => $db['password'],
		),
		'session' => array(
			'timeout' => 86400
		),
		'mailer' => array(
			// Setting the mailer mode to null will suppress email
			//'mode' => null
			// Mail can be diverted by setting the divert array
			//'divert' => array('foo@example.org', 'bar@example.org')
		),
		/*
		'cacheBuster' => array(
			'time' => '2013062101',
		),
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
		'OphCiExamination' => array( 'class' => '\OEModule\OphCiExamination\OphCiExaminationModule', ),
		'OphCoCorrespondence',
		'OphCiPhasing',
		'OphCoTherapyapplication',
		'OphDrPrescription',
		'OphOuAnaestheticsatisfactionaudit',
		'OphTrConsent',
		'OphTrOperationnote',
		'OphTrOperationbooking',
		'OphTrIntravitrealinjection',
		'OphTrLaser',
		'PatientTicketing' => array( 'class' => '\OEModule\PatientTicketing\PatientTicketingModule', ),
		'OphInVisualfields',
        'OphInBiometry',
		'OphCoMessaging' => array('class' => '\OEModule\OphCoMessaging\OphCoMessagingModule')
	),

	'params'=>array(
		//'pseudonymise_patient_details' => false,
		//'ab_testing' => false,
		'auth_source' => 'BASIC',	// BASIC or LDAP
		// This is used in contact page
		'ldap_server' => 'ldap.example.com',
		//'ldap_port' => '',
		'ldap_admin_dn' => 'CN=openeyes,CN=Users,dc=example,dc=com',
		'ldap_password' => '',
		'ldap_dn' => 'CN=Users,dc=example,dc=com',
		'environment' => 'dev',
		'google_analytics_account' => '',
		'local_users' => array('admin','username'),
		//'log_events' => true,
		'specialty_codes' => array(130),
		//'default_site_code' => '',
		'specialty_sort' => array(130, 'SUP'),
		'OphCoTherapyapplication_sender_email' => array('email@example.com' => 'Test'),
		// flag to turn on drag and drop sorting for dashboards
		// 'dashboard_sortable' => true
		'event_print_method' => 'pdf',
		'wkhtmltopdf_nice_level' => 19,
		'city_road_satellite_view' => 1,
		// default start time used for automatic worklist definitions
		//'worklist_default_start_time' => 'H:i',
		// default end time used for automatic worklist definitions
		//'worklist_default_end_time' => 'H:i',
		// number of patients to show on each worklist dashboard render
		//'worklist_default_pagination_size' => int,
		// number of days in the future to retrieve worklists for the automatic dashboard render
		//'worklist_dashboard_future_days' => int,
		// days of the week to be ignored when determining which worklists to render - Mon, Tue etc
		//'worklist_dashboard_skip_days' => array()
		//how far in advance worklists should be generated for matching
		//'worklist_default_generation_limit' => interval string (e.g. 3 months)
		// override edit checks on definitions so they can always be edited (use at own peril)
		//'worklist_always_allow_definition_edit' => bool
		// whether we should render empty worklists in the dashboard or not
		//'worklist_show_empty' => bool
		// allow duplicate entries on an automatic worklist for a patient
		//'worklist_allow_duplicate_patients' => bool
		// any appointments sent in before this date will not trigger errors when sent in
		//'worklist_ignore_date => 'Y-m-d',
		'portal' => array(
			'uri' => 'http://api.localhost:8000',
			'endpoints' => array(
				'auth' => '/oauth/access',
				'examinations' => '/examinations/searches'
			),
			'credentials' => array(
				'username' => 'user@example.com',
				'password' => 'apipass',
				'grant_type' => 'password',
				'client_id' => 'f3d259ddd3ed8ff3843839b',
				'client_secret' => '4c7f6f8fa93d59c45502c0ae8c4a95b',
			)
		),
	),
);

return $config;
