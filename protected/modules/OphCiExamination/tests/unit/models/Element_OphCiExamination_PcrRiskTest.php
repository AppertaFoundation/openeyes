<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\unit\models;

use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk;
use WithTransactions;

/**
 * @group sample-data
 * @group examination
 * @group pcr-risk
 */
class Element_OphCiExamination_PcrRiskTest extends \ModelTestCase
{
    use WithTransactions;

    protected $element_cls = Element_OphCiExamination_PcrRisk::class;
    protected array $columns_to_skip = ['event_id'];

    /** @test */
    public function right_sided_instance_stringifies_pcr_risk_value()
    {
        $instance = Element_OphCiExamination_PcrRisk::factory()
            ->rightSideOnly()
            ->create([
                'right_pcr_risk' => 12.4
            ]);

        $this->assertEquals('R: 12.40%', (string) $instance);
    }

    /** @test */
    public function left_sided_instance_stringifies_pcr_risk_value()
    {
        $instance = Element_OphCiExamination_PcrRisk::factory()
            ->leftSideOnly()
            ->create([
                'left_pcr_risk' => 10.36
            ]);

        $this->assertEquals('L: 10.36%', (string) $instance);
    }

    /** @test */
    public function both_sided_instance_stringifies_pcr_risk_value()
    {
        $instance = Element_OphCiExamination_PcrRisk::factory()
            ->bothSided()
            ->create([
                'right_pcr_risk' => 9.1,
                'left_pcr_risk' => 19.75
            ]);

        $this->assertEquals('R: 9.10%, L: 19.75%', (string) $instance);
    }
}
