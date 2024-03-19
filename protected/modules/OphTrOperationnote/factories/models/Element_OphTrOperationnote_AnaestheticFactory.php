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

use OE\factories\ModelFactory;

class Element_OphTrOperationnote_AnaestheticFactory extends FactoryForOperationnoteElement
{
    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'anaesthetist_id' => Anaesthetist::factory()
            ]
        );
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Element_OphTrOperationnote_Anaesthetic $element) {
            $element->updateComplications(
                array_map(
                    fn ($complication) => $complication->id,
                    $element->anaesthetic_complications ?? []
                )
            );

            $element->updateAnaestheticAgents(
                array_map(
                    fn ($agent) => $agent->id,
                    $element->anaesthetic_agents ?? []
                )
            );

            $element->updateAnaestheticType(
                array_map(
                    fn ($anaesthetic_type) => $anaesthetic_type->id,
                    $element->anaesthetic_type ?? []
                )
            );

            $element->updateAnaestheticDelivery(
                array_map(
                    fn ($delivery) => $delivery->id,
                    $element->anaesthetic_delivery ?? []
                )
            );
        });
    }

    public function withAnaestheticAgent($anaesthetic_agent = 1): self
    {
        return $this->afterMaking(
            function (Element_OphTrOperationnote_Anaesthetic $element) use ($anaesthetic_agent) {
                $element->anaesthetic_agents = array_merge(
                    $element->anaesthetic_agents ?? [],
                    AnaestheticAgent::factory()->count($anaesthetic_agent)->useExisting()->make()
                );
            }
        );
    }

    public function withAnaestheticDelivery($anaesthetic_delivery = 1): self
    {
        return $this->afterMaking(
            function (Element_OphTrOperationnote_Anaesthetic $element) use ($anaesthetic_delivery) {
                $element->anaesthetic_delivery = array_merge(
                    $element->anaesthetic_delivery ?? [],
                    AnaestheticDelivery::factory()->count($anaesthetic_delivery)->useExisting()->make()
                );
            }
        );
    }

    public function withAnaestheticType($anaesthetic_type = 1): self
    {
        return $this->afterMaking(
            function (Element_OphTrOperationnote_Anaesthetic $element) use ($anaesthetic_type) {
                $element->anaesthetic_type = array_merge(
                    $element->anaesthetic_type ?? [],
                    AnaestheticType::factory()->count($anaesthetic_type)->useExisting()->make()
                );
            }
        );
    }

    public function withComplications($complications = 1)
    {
        return $this->afterMaking(
            function (Element_OphTrOperationnote_Anaesthetic $element) use ($complications) {
                $element->anaesthetic_complications = array_merge(
                    $element->anaesthetic_complications ?? [],
                    OphTrOperationnote_AnaestheticComplications::factory()->count($complications)->useExisting()->make()
                );
            }
        );
    }

    protected static function resolveModelFormFieldName($models): ?string
    {
        return null;
    }

    public static function mapInstanceToFormData($instance): array
    {
        return [
            static::formFieldName($instance) => [
                'anaesthetist_id' => $instance->anaesthetist_id,
                'anaesthetic_comment' => $instance->anaesthetic_comment ?? '',
                'anaesthetic_witness_id' => $instance->anaesthetic_witness_id,
            ],
            'AnaestheticAgent' => array_map(fn ($agent) => $agent->id, $instance->anaesthetic_agents),
            'AnaestheticDelivery' => array_map(fn ($delivery) => $delivery->id, $instance->anaesthetic_delivery),
            'AnaestheticType' => array_map(fn ($anaesthetic_type) => $anaesthetic_type->id, $instance->anaesthetic_type),
            'OphTrOperationnote_AnaestheticComplications' => array_map(fn ($complication) => $complication->id, $instance->anaesthetic_complications)
        ];
    }
}
