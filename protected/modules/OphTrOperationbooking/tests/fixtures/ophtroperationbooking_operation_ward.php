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
        'ward1' => array(
            'id' => 1,
            'site_id' => 1,
            'institution_id' => 1,
            'code' => 'MW',
            'name' => 'Male Ward',
            'long_name' => 'Male Ward',
            'directions' => 'None',
            'restriction' => OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT,
            'display_order' => 1,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
        'ward2' => array(
            'id' => 2,
            'site_id' => 1,
            'institution_id' => 1,
            'code' => 'FW',
            'name' => 'Female Ward',
            'long_name' => 'Female Ward',
            'directions' => 'None',
            'restriction' => OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT,
            'display_order' => 2,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
        'ward3' => array(
            'id' => 3,
            'site_id' => 1,
            'institution_id' => 1,
            'name' => 'Girl Ward',
            'code' => 'GW',
            'long_name' => 'Girl Ward',
            'directions' => 'None',
            'restriction' => OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_CHILD,
            'display_order' => 3,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
        'ward4' => array(
            'id' => 4,
            'site_id' => 1,
            'institution_id' => 1,
            'code' => 'MIXED',
            'name' => 'Mixed Ward',
            'long_name' => 'Mixed Ward',
            'directions' => 'None',
            'restriction' => OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT,
            'display_order' => 4,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
        'ward5' => array(
            'id' => 5,
            'site_id' => 1,
            'institution_id' => 1,
            'code' => 'BW',
            'name' => 'Boy Ward',
            'long_name' => 'Boy Ward',
            'directions' => 'None',
            'restriction' => OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_CHILD,
            'display_order' => 5,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
        'ward6' => array(
            'id' => 6,
            'site_id' => 1,
            'institution_id' => 1,
            'code' => 'CW',
            'name' => 'Child Ward',
            'long_name' => 'Child Ward',
            'directions' => 'None',
            'restriction' => OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE + OphTrOperationbooking_Operation_Ward::RESTRICTION_CHILD,
            'display_order' => 6,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
        'ward7' => array(
            'id' => 7,
            'site_id' => 2,
            'institution_id' => 1,
            'code' => 'GENERAL',
            'name' => 'Other Site General Ward',
            'long_name' => 'Other Site General Ward',
            'directions' => 'None',
            'restriction' => 31,
            'display_order' => 7,
            'last_modified_user_id' => 1,
            'last_modified_date' => '1901-01-01 00:00:00',
            'created_user_id' => 1,
            'created_date' => '1901-01-01 00:00:00',
        ),
);
