<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

// Defines the component to be used for integration with a 3rd party referral management
// system. For WinDip, the hashing function is not available publicly and must be implemented
// for the specific location. The component class indicates the functionality required,
// and will be straight forward to implement for users with a genuine need and appropriate
// documentation from WinDip themselves.
return array(
    'internalReferralIntegration' => array(
        'class' => '\OEModule\OphCoCorrespondence\components\WinDipIntegration',
        'launch_uri' => 'http://172.20.10.3:9001',
        'application_id' => 'OpenEyes',
        // form id is specific to the instance of WinDIP being integrated with.
        'form_id' => '',
        // private function to be implemented for specific installations
        'hashing_function' => null
    )
);