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

namespace OEModule\OphTrConsent\models;

/**
 * Elements implementing this interface can require
 * the E-sign element to collect a signature and then
 * call back the element to update
 */
interface RequiresSignature
{
    /**
     * An array of empty signatures that need to be signed
     *
     * @return \OphTrConsent_Signature[]
     */
    public function getRequiredSignatures() : array;

    /**
     * This method will be called when the signature is captured
     *
     * @param int $row_id The id of the row (within this element) that initiated the signature
     * @param int $signature_id The id of the collected signature in database
     * @return void
     */
    public function afterSignedCallback(int $row_id, int $signature_id) : void;
}
