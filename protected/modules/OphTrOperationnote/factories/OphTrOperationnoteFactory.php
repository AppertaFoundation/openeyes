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

namespace OEModule\OphTrOperationnote\factories;

use Element_OphTrOperationnote_Anaesthetic;
use Element_OphTrOperationnote_Comments;
use Element_OphTrOperationnote_PostOpDrugs;
use Element_OphTrOperationnote_ProcedureList;
use Element_OphTrOperationnote_SiteTheatre;
use Element_OphTrOperationnote_Surgeon;
use OE\factories\models\EventFactory;

class OphTrOperationnoteFactory extends EventFactory
{
    protected static $requiredElements = [
        Element_OphTrOperationnote_SiteTheatre::class,
        Element_OphTrOperationnote_Surgeon::class,
        Element_OphTrOperationnote_ProcedureList::class,
        Element_OphTrOperationnote_Anaesthetic::class,
        Element_OphTrOperationnote_PostOpDrugs::class,
        Element_OphTrOperationnote_Comments::class
    ];

    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'event_type_id' => $this->getEventTypeByName('Operation note')
            ]
        );
    }

    public function make(array $attributes = [], bool $canCreate = false)
    {
        // ensure all required elements have been specified as states to be applied
        $this->withElements(
            array_map(
                function ($element_class)
                {
                    return [$element_class];
                },
                self::$requiredElements
            )
        );

        return parent::make($attributes, $canCreate);
    }

    public function withProcedures($procedures): self
    {
        return $this->withElement(Element_OphTrOperationnote_ProcedureList::class, [
            ['withProcedures', $procedures]
        ]);
    }

    public function forRightEye(): self
    {
        return $this->withElement(Element_OphTrOperationnote_ProcedureList::class, [
            ['forRightEye']
        ]);
    }

    public function forLeftEye(): self
    {
        return $this->withElement(Element_OphTrOperationnote_ProcedureList::class, [
            ['forLeftEye']
        ]);
    }
}
