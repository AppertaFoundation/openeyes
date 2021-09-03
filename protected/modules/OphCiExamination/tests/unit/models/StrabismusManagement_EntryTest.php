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

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\StrabismusManagement_Entry;
use OEModule\OphCiExamination\tests\traits\InteractsWithStrabismusManagement;

/**
 * Class StrabismusManagement_EntryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\StrabismusManagement_Entry
 * @group sample-data
 * @group strabismus
 * @group strabismus-management
 */
class StrabismusManagement_EntryTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use InteractsWithStrabismusManagement;

    protected $element_cls = StrabismusManagement_Entry::class;

    /** @test */
    public function attribute_safety()
    {
        $data = $this->generateStrabismusManagementEntryData();
        $instance = $this->getElementInstance();
        $instance->setAttributes($data);

        foreach ($data as $attr => $val) {
            $this->assertEquals($val, $instance->$attr);
        }
    }

    public function stringification_provider()
    {
        return [
            [SidedData::RIGHT, 'Right'],
            [SidedData::LEFT, 'Left'],
            [SidedData::RIGHT | SidedData::LEFT, 'Bilateral']
        ];
    }

    /**
     * @param $laterality
     * @param $expected_prefix
     * @test
     * @dataProvider stringification_provider
     */
    public function stringification($laterality, $expected_prefix)
    {
        $instance = $this->getElementInstance();
        $data = $this->generateStrabismusManagementEntryData([
            'eye_id' => $laterality
        ]);
        $instance->setAttributes($data);
        $expected = "{$expected_prefix} {$data['treatment']}";
        if ($data['treatment_options']) {
            $expected .= " {$data['treatment_options']}";
        }
        if ($data['treatment_reason']) {
            $expected .= " {$data['treatment_reason']}";
        }
        $this->assertEquals($expected, (string) $instance);
    }
}
