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

$config = array(
    'params' => array(
        'reports' => array(
            'Letters' => '/OphCoCorrespondence/report/letters',
        ),
        'populate_clinic_date_from_last_examination' => false,
        'admin_menu' => array(
            'OphCoCorrespondence' => [
                'Letter macros' => '/OphCoCorrespondence/admin/letterMacros',
                'Letter Snippet Groups' => '/OphCoCorrespondence/oeadmin/snippetGroup/list',
                'Letter Snippets' => '/OphCoCorrespondence/oeadmin/snippet/list',
                //'Letter Types' => '/OphCoCorrespondence/oeadmin/letterType/list', //available but have to find out how could work with Internal Referral (re letter type enable, rename)
                'Internal Referral' => ['uri' => '/OphCoCorrespondence/oeadmin/internalReferralSettings/settings', 'restricted' => array('admin')],
                'Internal Referral site mapping' => ['uri' => '/OphCoCorrespondence/oeadmin/internalReferralSettings/siteFirmMapping', 'restricted' => array('admin')],
                'Letter settings' => '/OphCoCorrespondence/admin/letterSettings',
                'Sender Email Addresses' => '/OphCoCorrespondence/admin/senderEmailAddresses',
                'Email Templates' => '/OphCoCorrespondence/admin/emailTemplates',
            ]
        ),
    ),
);

$integration_config_file = __DIR__ . '/integration.php';
if (file_exists($integration_config_file)) {
    $config['components'] = include $integration_config_file;
}

return $config;
