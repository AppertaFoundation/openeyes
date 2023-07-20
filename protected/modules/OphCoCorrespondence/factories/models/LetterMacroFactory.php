<?php
/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;

class LetterMacroFactory extends ModelFactory
{
    //
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'body' => $this->faker->paragraphs(2, true)
        ];
    }

    public function forPatientRecipient(): self
    {
        return $this->state([
            'recipient_id' => LetterRecipient::factory()->forPatient()
        ]);
    }

    /**
     * @param array|Site $site
     */
    public function withSite(Site $site = null)
    {
        return $this->afterCreating(function (LetterMacro $letter_macro) use ($site) {
            $site ??= ModelFactory::factoryFor(Site::class)->create();
            if (!is_array($site)) {
                $site = [$site];
            }

            $letter_macro->createMappings(
                ReferenceData::LEVEL_SITE,
                array_map(
                    function ($site) {
                        return $site->id;
                    },
                    $site
                ));
        });
    }

    /**
     * @param array|Firm $site
     */
    public function withFirm(Firm $firm = null)
    {
        return $this->afterCreating(function (LetterMacro $letter_macro) use ($firm) {
            $firm ??= ModelFactory::factoryFor(Firm::class)->create();
            if (!is_array($firm)) {
                $firm = [$firm];
            }

            $letter_macro->createMappings(
                ReferenceData::LEVEL_FIRM,
                array_map(
                    function ($firm) {
                        return $firm->id;
                    },
                    $firm
                ));
        });
    }
}
