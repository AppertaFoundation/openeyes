<?php
/**
 * (C) Copyright Apperta Foundation 2023
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

class LetterStringFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'body' => $this->faker->paragraphs(2, true),
            'letter_string_group_id' => LetterStringGroup::factory(),
        ];
    }

    /**
     * @param array|Site $site
     */
    public function withSite($site = null)
    {
        return $this->afterCreating(function (LetterString $letter_string) use ($site) {
            $site ??= Site::factory()->create();
            if (!is_array($site)) {
                $site = [$site];
            }

            $letter_string->createMappings(
                ReferenceData::LEVEL_SITE,
                array_map(
                    function ($site) {
                        return $site->id;
                    },
                    $site
                )
            );
        });
    }

    /**
     * @param array|Firm $firm
     */
    public function withFirm($firm = null)
    {
        return $this->afterCreating(function (LetterString $letter_string) use ($firm) {
            $firm ??= Firm::factory()->create();
            if (!is_array($firm)) {
                $firm = [$firm];
            }

            $letter_string->createMappings(
                ReferenceData::LEVEL_FIRM,
                array_map(
                    function ($firm) {
                        return $firm->id;
                    },
                    $firm
                )
            );
        });
    }
}
