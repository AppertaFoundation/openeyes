<?php

namespace OEModule\PASAPI\resources;

use CActiveRecord;
use Exception;

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
 *
 * @property Contact $Contact
 */
class PatientContact extends BaseResource
{
    protected static $resource_type = 'PatientContact';

    /**
     * Assign the PatientContact resource attributes to the given Contact model
     * and save it.
     *
     * @param CActiveRecord|\Contact $contact
     *
     * @throws Exception
     *
     * @return bool|null
     */
    public function saveModel($contact)
    {
        $contact->scenario = "pasapi_import";
        $contact->source = "PASAPI";

        $contact->pas_id = $this->getAssignedProperty('PasId');
        if (isset($this->Contact)) {
            $this->Contact->saveModel($contact);
        } else {
            $this->addError('Contact details required.');
        }

        if (!$this->errors) {
            return true;
        }
        return false;
    }
}
