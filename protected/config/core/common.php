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
    'name' => 'OpenEyes',

    // Preloading 'log' component
    'preload' => array('log'),

    // Autoloading model and component classes
    'import' => array(
        'application.vendors.*',
        'application.modules.*',
        'application.models.*',
        'application.models.elements.*',
        'application.components.*',
        'application.components.reports.*',
        'application.components.worklist.*',
        'application.extensions.tcpdf.*',
        'application.modules.*',
        'application.commands.*',
        'application.commands.shell.*',
        'application.behaviors.*',
        'application.widgets.*',
        'application.controllers.*',
        'application.helpers.*',
        'application.gii.*',
        'system.gii.generators.module.*',
        'application.modules.OphTrOperationnote.components.*',
    ),

    'aliases' => array(
        'services' => 'application.services',
        'OEModule' => 'application.modules',
    ),

    'modules' => array(
        // Gii tool
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'openeyes',
            'ipFilters' => array('127.0.0.1'),
        ),
        'oldadmin',
        'Admin',
    ),

    // Application components
    'components' => array(
        'assetManager' => array(
            'class' => 'AssetManager',
            // Use symbolic links to publish the assets when in debug mode.
            'linkAssets' => defined('YII_DEBUG') && YII_DEBUG,
        ),
        'authManager' => array(
            'class' => 'AuthManager',
            'connectionID' => 'db',
            'assignmentTable' => 'authassignment',
            'itemTable' => 'authitem',
            'itemChildTable' => 'authitemchild',
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
            'directoryLevel' => 1,
        ),
        'cacheBuster' => array(
            'class' => 'CacheBuster',
            'time' => '201903131430',
        ),
        'clientScript' => array(
            'class' => 'ClientScript',
            'packages' => array(
                'jquery' => array(
                    'js' => array('jquery/jquery.min.js'),
                    'basePath' => 'application.assets.components',
                ),
                'jquery.ui' => array(
                    'js' => array('jquery-ui/ui/minified/jquery-ui.min.js'),
                    'css' => array('jquery-ui/themes/base/jquery-ui.css'),
                    'basePath' => 'application.assets.components',
                    'depends' => array('jquery'),
                ),
                'mustache' => array(
                    'js' => array('mustache/mustache.js'),
                    'basePath' => 'application.assets.components',
                ),
                'eventemitter2' => array(
                    'js' => array('eventemitter2/lib/eventemitter2.js'),
                    'basePath' => 'application.assets.components',
                ),
                'flot' => array(
                    'js' => array(
                        'components/flot/jquery.flot.js',
                        'components/flot/jquery.flot.time.js',
                        'components/flot/jquery.flot.navigate.js',
                        'js/jquery.flot.dashes.js',
                    ),
                    'basePath' => 'application.assets',
                    'depends' => array('jquery'),
                ),
                'rrule' => array(
                    'js' => array(
                        'components/rrule/lib/rrule.js',
                        'components/rrule/lib/nlp.js',
                    ),
                    'basePath' => 'application.assets',
                ),
                'tagsinput' => array(
                    'css' => array(
                        'components/jquery.tagsinput/src/jquery.tagsinput.css',
                    ),
                    'js' => array(
                        'components/jquery.tagsinput/src/jquery.tagsinput.js',
                    ),
                    'basePath' => 'application.assets',
                    'depends' => array('jquery'),
                ),
            ),
        ),
        'db' => array(
            'class' => 'OEDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=openeyes',
            'emulatePrepare' => true,
            'username' => 'oe',
            'password' => '_OE_PASSWORD_',
            'charset' => 'utf8',
            'schemaCachingDuration' => 300,
        ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => YII_DEBUG ? null : 'site/error',
        ),
        'event' => array(
            'class' => 'OEEventManager',
            'observers' => array(),
        ),
        'fhirClient' => array('class' => 'FhirClient'),
        'fhirMarshal' => array('class' => 'FhirMarshal'),
        'log' => array(
            'class' => 'CLogRouter',
            // 'autoFlush' => 1,
            'routes' => array(
                // Normal logging
                'application' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info, warning, error',
                    'logFile' => 'application.log',
                    'maxLogFiles' => 30,
                ),
                // Action log
                'action' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info, warning, error',
                    'categories' => 'application.action.*',
                    'logFile' => 'action.log',
                    'maxLogFiles' => 30,
                ),
                // Development logging (application only)
                'debug' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, warning, error',
                    'categories' => 'application.*',
                    'logFile' => 'debug.log',
                    'maxLogFiles' => 30,
                ),
            ),
        ),
        'mailer' => array(
            'class' => 'Mailer',
            'mode' => 'sendmail',
        ),
        'moduleAPI' => array(
            'class' => 'ModuleAPI',
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'class' => 'HttpRequest',
            'noCsrfValidationRoutes' => array(
                'site/login', //disabled csrf check on login form
                'api/',
                //If the user uploads a too large file (php.ini) then CSRF validation error comes back
                //instead of the proper error message
                'OphCoDocument/Default/create',
                'OphCoDocument/Default/update',
                'OphCoDocument/Default/fileUpload',
            ),
        ),
        'service' => array(
            'class' => 'services\ServiceManager',
            'internal_services' => array(
                'services\CommissioningBodyService',
                'services\GpService',
                'services\PracticeService',
                'services\PatientService',
            ),
        ),
        'session' => array(
            'class' => 'OESession',
            'connectionID' => 'db',
            'sessionTableName' => 'user_session',
            'autoCreateSessionTable' => false,
            /*'cookieParams' => array(
                'lifetime' => 300,
            ),*/
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '' => 'site/index',
                'patient/viewpas/<pas_key:\d+>' => 'patient/viewpas',
                'file/view/<id:\d+>/<dimensions:\d+(x\d+)?>/<name:\w+\.\w+>' => 'protectedFile/thumbnail',
                'file/view/<id:\d+>/<name:\w+\.\w+>' => 'protectedFile/view',

                // API
                array('api/conformance', 'pattern' => 'api/metadata', 'verb' => 'GET'),
                array('api/conformance', 'pattern' => 'api', 'verb' => 'OPTIONS'),
                array('api/read', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>', 'verb' => 'GET'),
                array('api/vread', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>/_history/<vid:\d+>', 'verb' => 'GET'),
                array('api/update', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>', 'verb' => 'PUT'),
                array('api/delete', 'pattern' => 'api/<resource_type:\w+>/<id:[a-z0-9\-\.]{1,36}>', 'verb' => 'DELETE'),
                array('api/create', 'pattern' => 'api/<resource_type:\w+>', 'verb' => 'POST'),
                array('api/search', 'pattern' => 'api/<resource_type:\w+>', 'verb' => 'GET'),
                array('api/search', 'pattern' => 'api/<resource_type:\w+>/_search', 'verb' => 'GET,POST'),
                array('api/badrequest', 'pattern' => 'api/(.*)'),

                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:\w+>/oeadmin/<controller:\w+>/<action:\w+>' => '<module>/oeadmin/<controller>/<action>',
                '<module:\w+>/oeadmin/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/oeadmin/<controller>/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<hospital_num:\d+>' => 'patient/results',
            ),
        ),
        'user' => array(
            'class' => 'OEWebUser',
            'loginRequiredAjaxResponse' => 'Login required.',
            // Enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'version' => array(
            'class' => 'Version',
        ),
        'widgetFactory' => array(
            'class' => 'WidgetFactory',
        ),
    ),

    'params' => array(
        'utf8_decode_required' => true,
        'pseudonymise_patient_details' => false,
        'ab_testing' => false,
        'auth_source' => 'BASIC', // Options are BASIC or LDAP.
        // This is used in contact page
        'ldap_server' => '',
        'ldap_port' => '',
        'ldap_admin_dn' => '',
        'ldap_password' => '',
        'ldap_dn' => '',
        'ldap_method' => 'native', // use 'zend' for the Zend_Ldap vendor module
        // set to integer value of 2 or 3 to force specific ldap protocol
        'ldap_protocol_version' => 3,
        // alters the prefix used when binding to a user in native ldap connections
        'ldap_username_prefix' => 'cn',
        'ldap_native_timeout' => 3,
        'ldap_info_retries' => 3,
        'ldap_info_retry_delay' => 1,
        'ldap_update_name' => false,
        'ldap_update_email' => true,
        'environment' => 'dev',
        //'watermark' => '',
        'google_analytics_account' => '',
        'local_users' => array(),
        'log_events' => true,
        'default_site_code' => '',
        'institution_code' => '',
        'institution_specialty' => 130,
        'erod_lead_time_weeks' => 3,
        // specifies which specialties are available in patient summary for diagnoses etc (use specialty codes)
        'specialty_codes' => array(),
        // specifies the order in which different specialties are laid out (use specialty codes)
        'specialty_sort' => array(),
        'hos_num_regex' => '/^([0-9]{1,9})$/',
        'pad_hos_num' => '%07s',
        'profile_user_can_edit' => true,
        'profile_user_show_menu' => true,
        'profile_user_can_change_password' => true,
        'tinymce_default_options' => array(
            'plugins' => 'lists table paste code',
            'branding' => false,
            'visual' => false,
            'min_height' => 400,
            'toolbar' => "undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | subtitle | labelitem | label-r-l | code",
            'valid_children' => '+body[style]',
            'custom_undo_redo_levels' => 10,
            'object_resizing' => false,
            'menubar' => false,
            'paste_as_text' => true,
            'table_toolbar' => "tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol",
            'browser_spellcheck' => true,
            'extended_valid_elements' => 'i[*]',
            'valid_elements' => '*[*]',
        ),
        'menu_bar_items' => array(
                'admin' => array(
                    'title' => 'Admin',
                    'uri' => 'admin',
                    'position' => 1,
                    'restricted' => array('admin'),
                ),
                'audit' => array(
                    'title' => 'Audit',
                    'uri' => 'audit',
                    'position' => 2,
                    'restricted' => array('TaskViewAudit'),
                ),
                'reports' => array(
                    'title' => 'Reports',
                    'uri' => 'report',
                    'position' => 3,
                    'restricted' => array('Report'),
                    'userrule' => 'isSurgeon',
                ),
                'cataract' => array(
                    'title' => 'Cataract Audit',
                    'uri' => 'dashboard/cataract',
                    'position' => 4,
                    'userrule' => 'isSurgeon',
                    'restricted' => array('admin'),
                    'options' => array('target' => '_blank'), ),
                'nodexport' => array(
                    'title' => 'NOD Export',
                    'uri' => 'NodExport',
                    'position' => 5,
                    'restricted' => array('NOD Export'),
                ),
                'cxldataset' => array(
                    'title' => 'CXL Dataset',
                    'uri' => 'CxlDataset',
                    'position' => 6,
                    'restricted' => array('CXL Dataset'),
                ),

                'patientmergerequest' => array(
                    'title' => 'Patient Merge',
                    'uri' => 'patientMergeRequest/index',
                    'position' => 17,
                    'restricted' => array('Patient Merge', 'Patient Merge Request'),
                ),
                'patient' => array(
                    'title' => 'Add Patient',
                    'uri' => 'patient/create',
                    'position' => 46,
                    'restricted' => array('TaskAddPatient'),
                ),
                'practices' => array(
                    'title' => 'Practices',
                    'uri' => 'practice/index',
                    'position' => 11,
                    'restricted' => array('TaskViewPractice', 'TaskCreatePractice'),
                ),
                'forum' => array(
                    'title' => 'FORUM',
                    'uri' => "javascript:oelauncher('forum');",
                    'requires_setting' => array('setting_key'=>'enable_forum_integration', 'required_value'=>'on'),
                    'position' => 90,
                ),
                'disorder' => array(
                    'title' => 'Manage Disorders',
                    'uri' => "/disorder/index",
                    'requires_setting' => array('setting_key'=>'user_add_disorder', 'required_value'=>'on'),
                    'position' => 91,
            ),
                'gps' => array(
                    'title' => 'Practitioners',
                    'uri' => 'gp/index',
                    'position' => 10,
                    'restricted' => array('TaskViewGp', 'TaskCreateGp'),
                ),
                'analytics' => array(
                  'title' => 'Analytics',
                  'uri' => '/Analytics/medicalRetina',
                  'position' => 11,
                ),
                'worklist' => array(
                  'title' => 'Worklists',
                  'uri' => '/worklist',
                  'position' => 3,
                ),
        ),
        'admin_menu' => array(
        ),
        'dashboard_items' => array(
        ),
        'admin_email' => '',
        'enable_transactions' => true,
        'event_lock_days' => 0,
        'event_lock_disable' => false,
        'reports' => array(
        ),
        'opbooking_disable_both_eyes' => true,
        //'html_autocomplete' => 'off',
        // html|pdf, pdf requires wkhtmltopdf with patched QT
        'event_print_method' => 'pdf',
        // use this to set a specific path to the wkhtmltopdf binary. if this is not set it will search the current path.
        'wkhtmltoimage_path' => '/usr/local/bin/wkhtmltoimage',
        'wkhtmltopdf_path' => '/usr/local/bin/wkhtmltopdf',
        'wkhtmltopdf_footer_left' => '{{DOCREF}}{{BARCODE}}{{PATIENT_NAME}}{{PATIENT_HOSNUM}}{{PATIENT_NHSNUM}}{{PATIENT_DOB}}',
        'wkhtmltopdf_footer_middle' => 'Page {{PAGE}} of {{PAGES}}',
        'wkhtmltopdf_footer_right' => 'OpenEyes',
        'wkhtmltopdf_top_margin' => '10mm',
        'wkhtmltopdf_bottom_margin' => '20mm',
        'wkhtmltopdf_left_margin' => '5mm',
        'wkhtmltopdf_right_margin' => '5mm',
        'wkhtmltopdf_nice_level' => false,
        'curl_proxy' => null,
        'hscic' => array(
            'data' => array(
                // to store processed zip files
                'path' => realpath(dirname(__FILE__).'/../..').'/data/hscic',

                // to store downloaded zip files which will be processed if they are different from the already processed ones
                // otherwise ignored and will be overwritten on then next download
                'temp_path' => realpath(dirname(__FILE__).'/../..').'/data/hscic/temp',
            ),
        ),

        //'docman_export_dir' => '/tmp/docman_delievery',
        //'docman_login_url' => 'http://{youropeneyeshost}/site/login',
        //'docman_user' => '',
        //'docman_password' => '',
        //'docman_print_url' => 'http://{youropeneyeshost}/OphCoCorrespondence/default/PDFprint/',

        /* injecting autoprint JS into generated PDF */
        //'docman_inject_autoprint_js' => false,

        //'docman_generate_csv' => true,

        /*Docman ConsoleCommand can generate Internal referral XML/PDF along with it's own(Docman) XML/PDF
          In case a trust integrated engine can use the same XML to decide where to forward the document to */
        //'docman_with_internal_referral' => false,

        // xml template
        //'docman_xml_template' => 'template_default',
        // set this to false if you want to suppress XML output

        /**
         * defaults to format1
         * possible values:
         * format1 => OPENEYES_<eventId>_<randomInteger>.pdf [current format, default if parameter not specified]
         * format2 => <hosnum>_<yyyyMMddhhmm>_<eventId>.pdf
         * format3 => <hosnum>_edtdep-OEY_yyyyMMdd_hhmmss_<eventId>.pdf
         * format4 => <hosnum>_<yyyyMMddhhmmss>_<eventId>__<doctype>_.pdf
         */
        //'docman_filename_format' => 'format1',

        /**
         *  Set to false to suppress XML generation for electronic correspondence
         */
        'docman_generate_xml' => false,


        /**
         * Text to be displayed for sending correspondence electronically e.g.: 'Electronic (DocMan)'
         * To be overriden in local config
         */
        'electronic_sending_method_label' => 'Electronic',

        /**
         * Action buttons to be displayed when create/update a correspondence letter
         * Available actions
         *      - 'savedraft' => 'Save draft',
         *      - 'save' => 'Save',
         *      - 'saveprint' => 'Save and print'
         * To remove an option set it to NULL
         * e.g: saveprint' => null,
         */
        'OphCoCorrespondence_event_actions' => array(
                'create' => array(
                    'savedraft' => 'Save draft',
                    'save' => null,
                    'saveprint' => 'Save and print'
            )
        ),

        /**
         * Enable or disable the draft printouts DRAFT background
         * Without this, lightning images and event view will not show draft watermark
         */
        'OphCoCorrespondence_printout_draft_background' => true,

        'OphCoCorrespondence_Internalreferral' => array(
            'generate_csv' => false,
            'export_dir' => '/tmp/internalreferral_delievery',
            'filename_format' => 'format1',
        ),

        /**
         *  Operation bookings will be automatically scheduled to the next available slot (regardless of the firm)
         */
        "auto_schedule_operation" => false,
        'docman_generate_csv' => false,
        'element_sidebar' => true,
        // flag to enable editing of clinical data at the patient summary level - editing is not fully implemented
        // in v2.0.0, so this should only be turned on if you really know what you are doing.
        'allow_patient_summary_clinic_changes' => false,
        'patient_summary_id_widgets' => array(
            array(
                'class' => 'application.widgets.PatientSummaryPopup',
                'order' => PHP_INT_MAX
            )
        ),
        /**
         * Enables the admin->Settings->Logo screen */
        'letter_logo_upload' => true,
        /* ID of the Tag that indicates "preservative free" */
        'preservative_free_tag_id' => 1,

        /**
         * If 'disable_auto_feature_tours' is true than no tour will be start on page load
         * (this overrides the setting in admin > system > settings)
         */
        //'disable_auto_feature_tours' => true,

        'whiteboard' => array(
            // whiteboard will be refresh-able after operation booking is completed
            // overrides admin > Opbooking > whiteboard settings
            //'refresh_after_opbooking_completed' => 24, //hours or false
        ),

        /**
         * Lightning Viewer
         */

        'lightning_viewer' => array(
            'image_width' => 800,
            'viewport_width' => 1280,
            'keep_temp_files' => false,
            'compression_quality' => 50,
            'blank_image_template' => array(
                'height' => 912,
                'width' => 800
            ),
            'debug_logging' => false,
            'event_specific' => array(
                'Correspondence' => array(
                    'image_width' => 1000
                ),
            ),
        ),

        'event_image' => [
            'base_url' => 'http://localhost/'
        ],

        /**
         * Patient Identifiers
         * Used to have installation specific identifiers for every patient (in addition to the Hospital Number and NHS Number)
         *
         * 'label' is the text that will be used to label this identifier (defaults to a human friendly version of the code if not set)
         * 'placeholder' is what appears as the placeholder in the text field (defaults to the label if not set)
         * 'required' is whether the field needs to be entered or not (defaults to false)
         * If 'validate_pattern' is set, then the value must match that regex (unless the value is empty and required is false)
         * 'validate_msg' is the message displayed if the regex match fails (defaults to 'Invalid format')
         * If 'auto_increment' is true, then a blank value will be replaced with the 1 plus the highest value of other patients
         * If 'unique' is true, then the identifier must be unique for that patient
         * If 'display_if_empty' is true, then identifier will be shown in the patient summary panel even if it is null
         */
        /*'patient_identifiers' => array(
            'SOME_NUMBER' => array(
                'label' => 'Some Number',
                // 'placeholder' => 'Some number placeholder',
                // 'required' => true,
                // 'validate_pattern' => '/^\d{8,}$/',
                // 'validate_msg' => ',
                // 'editable' => true,
                // 'auto_increment' => false,
                // 'unique' => false,
                // 'display_if_empty' => false,
            ),
        ),*/
        'hos_num_label' => 'Hospital',
        'nhs_num_label' => 'NHS',
      'ethnic_group_filters' => array(
        'Indigenous Australian',
        'Greek',
        'Italian'
      ),
      'oe_version' => '3.2a',
      'gp_label' => 'GP'
    ),
);
