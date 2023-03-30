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

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;

use OEModule\OphCiExamination\models\OphCiExamination_Workflow;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule;
use \Firm;
use \Subspecialty;
use \EpisodeStatus;

class OphCiExamination_Workflow_RuleFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        // Defaults to a workflow rule for a factory generated workflow,
        // null on firm, subspecialty and episode status means it covers all possible
        // values in
        return [
            'workflow_id' => OphCiExamination_Workflow::factory(),
            'firm_id' => null,
            'subspecialty_id' => null,
            'episode_status_id' => null,
        ];
    }

    /**
     * @param OphCiExamination_Workflow|OphCiExamination_WorkflowFactory|int|string $workflow
     * @return OphCiExamination_Workflow_RuleFactory
     */
    public function forWorkflow($workflow): self
    {
        return $this->state([
            'workflow_id' => $workflow,
        ]);
    }

    /**
     * A null value for $firm means the rule applies to across all firms
     *
     * @param Firm|FirmFactory|int|string|null $firm
     * @return OphCiExamination_Workflow_RuleFactory
     */
    public function forFirm($firm = null): self
    {
        return $this->state([
            'firm_id' => $firm,
        ]);
    }

    /**
     * A null value for subspecialty means the rule applies across all subspecialties
     *
     * @param Subspeciality|SubspecialtyFactory|int|string|null $subspecialty
     * @return OphCiExamination_Workflow_RuleFactory
     */
    public function forSubspecialty($subspecialty = null): self
    {
        return $this->state([
            'subspecialty_id' => $subspecialty,
        ]);
    }

    /**
     * A null value for $status means the rule applies across all episode statuses
     *
     * @param EpisodeStatus|int|string|null $status
     * @return OphCiExamination_Workflow_RuleFactory
     */
    public function forEpisodeStatus($status): self
    {
        return $this->state([
            'episode_status_id' => $status,
        ]);
    }
}
