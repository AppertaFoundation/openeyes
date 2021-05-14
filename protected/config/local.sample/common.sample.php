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

// The base url for single-sign-on using SAML Authentication
$ssoBaseurl = getenv('SSO_BASE_URL') ?: 'http://localhost';
$ssoEntityId = getenv('SSO_ENTITY_ID') ?: '';
$ssoAppEmbedLink = getenv('SSO_APP_EMBED_LINK') ?: '';

// Credentials necessary for single-sign-on using OpenID-Connect
$ssoProviderURL = getenv('SSO_PROVIDER_URL') ?: '';
$ssoClientID = getenv('SSO_CLIENT_ID') ?: '';
$ssoClientSecret = getenv('SSO_CLIENT_SECRET') ?: (rtrim(@file_get_contents("/run/secrets/SSO_CLIENT_SECRET")) ?: '');
$ssoIssuerURL = getenv('SSO_ISSUER_URL') ?: null;
$ssoRedirectURL = getenv('SSO_REDIRECT_URL') ?: 'http://localhost';
$ssoResponseType = array(getenv('SSO_RESPONSE_TYPE')) ?: array('code');
$ssoImplicitFLow = strtolower(getenv('SSO_IMPLICIT_FLOW')) === 'true';
$ssoUserAttributes = getenv('SSO_USER_ATTRIBUTES') ?: '';
$ssoCustomClaims = getenv('SSO_CUSTOM_CLAIMS') ?: '';

$encryptionKey = getenv('SSO_CLIENT_SECRET') ?: (rtrim(@file_get_contents("/run/secrets/SSO_CLIENT_SECRET")) ?: '');

$ssoMappingsCheck = strtolower(getenv('STRICT_SSO_ROLES_CHECK')) === 'true';
$authSource = getenv('AUTH_SOURCE') ?: 'BASIC';

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
        'SSO',
        'OphOuCatprom5',
        'OphTrOperationchecklists'
    ),

    'params' => array(
        //'pseudonymise_patient_details' => false,
        //'ab_testing' => false,
        'auth_source' => $authSource,
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
        'strict_SSO_roles_check' => $ssoMappingsCheck,
        // Settings for OneLogin PHP-SAML toolkit
        'SAML_settings' => array(
            // BaseURL of the view that will process the SAML message
            'baseurl' => '',// baseurl is set to null at the moment because of the OneLogin known issue in Github(Link: https://github.com/onelogin/php-saml/issues/249).
            // If true then all unsigned and unencrypted messages will be rejected and should also follow set standards and rules
            'strict' => true,
            // Debug mode to print errors
            'debug' => false,
            // Service Provider data - OpenEyes
            'sp' => array(
                'entityId' => $ssoBaseurl.'/sso/login',   // Identifier of SP (URI)
                'assertionConsumerService' => array(
                  'url' => $ssoBaseurl.'/sso/login',    // URL where SAMLResponse will be sent
                  'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',  // HTTP-POST binding only
                ),
                'NameIDFormat' => 'emailAddress', // Constraints on name identifier to be used
            ),
            'idp' => array(
                'entityId' => $ssoEntityId,    //Metadata Source
                'singleSignOnService' => array( //SSO endpoint information
                  'url' => $ssoAppEmbedLink, // URL Target where the IdP will send the authentication request
                  'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'singleLogoutService' => array(
                  'url' => '', //URL where the SP will send the logout request
                  'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect', // HTTP-Redirect binding only
                ),
                'x509cert' => '',
            )
        ),
        'OIDC_settings' => array(
            // OpenID Party (OP) that will send the token
            'provider_url' => $ssoProviderURL,
            // Client ID given by the portal
            'client_id' => $ssoClientID,
            // CLient Secret given by the portal
            'client_secret' => $ssoClientSecret,
            // URL for the custom Authorization Server (Optional)
            'issuer' => $ssoIssuerURL,
            // Absolute URL where the OIDC token will be sent
            'redirect_url' => $ssoRedirectURL.'/sso/login',      // Remove trailing slashes
            // Response type - can be [code id_token token]
            'response_type' => $ssoResponseType,
            // Implicit flows
            'implicit_flow' => $ssoImplicitFLow,
            // Scopes - These are all the necessary and sufficient scopes at this stage
            'scopes' => array('openid', 'email', 'profile'),
            // Method used to send authorization code.
            'authParams' => array('response_mode' => 'form_post'),
            // Generates random encryption key for openssl
            'encryptionKey' => $encryptionKey,
            // Configure custom claims with the user attributes that the claims are for
            'custom_claims' => array_combine(explode(",", $ssoCustomClaims), explode(",", $ssoUserAttributes)),
        ),
        'correspondence_export_url' => 'localhost',
    ),
);

return $config;
