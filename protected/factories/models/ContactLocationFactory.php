<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use OE\factories\ModelFactory;

use Institution;
use Site;
use Contact;

class ContactLocationFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'institution_id' => Institution::factory(),
            'site_id' => function ($attributes) {
                return Site::factory()->state(['institution_id' => $attributes['institution_id']]);
            }
        ];
    }

    /**
     * @param Contact|ContactFactory|string|int $contact
     * @return ContactLocationFactory
     */
    public function forContact($contact): self
    {
        return $this->state([
            'contact_id' => $contact
        ]);
    }

    /**
     * @param Institution|InstitutionFactory|string|int $institution
     * @return ContactLocationFactory
     */
    public function forInstitution($institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }

    /**
     * @param Site|SiteFactory|string|int $site
     * @return ContactLocationFactory
     */
    public function forSite($site): self
    {
        return $this->state([
            'site_id' => $site
        ]);
    }
}
