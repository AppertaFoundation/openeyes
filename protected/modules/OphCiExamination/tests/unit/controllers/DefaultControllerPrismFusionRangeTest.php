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

namespace OEModule\OphCiExamination\tests\unit\controllers;

use OEModule\OphCiExamination\models\PrismFusionRange;
use OEModule\OphCiExamination\models\PrismFusionRange_Entry;
use OEModule\OphCiExamination\tests\traits\InteractsWithPrismFusionRange;

/**
 * Class DefaultControllerPrismFusionRangeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\controllers
 * @covers \OEModule\OphCiExamination\controllers\DefaultController
 * @group sample-data
 * @group strabismus
 * @group prism-fusion-range
 * @group pfr
 */
class DefaultControllerPrismFusionRangeTest extends BaseDefaultControllerTest
{
    use \WithFaker;
    use \WithTransactions;
    use InteractsWithPrismFusionRange;

    /** @test */
    public function saving_a_simple_element()
    {
        $data = $this->generatePrismFusionRangeData();
        // prevent error requiring HeadPosture element
        $data['entries'][0]['with_head_posture'] = PrismFusionRange_Entry::$WITHOUT_HEAD_POSTURE;

        $saved_element = $this->createElementWithDataWithController($data);

        $this->assertNotNull($saved_element);
        $this->assertInstanceOf(PrismFusionRange::class, $saved_element);
        $this->assertEquals($data['comments'], $saved_element->comments);

        $this->assertCount(1, $saved_element->entries);
        $entries_data = $data['entries'][0];
        $saved_entry = $saved_element->entries[0];
        foreach ($entries_data as $attr => $value) {
            $this->assertEquals($value, $saved_entry->$attr, "{$attr} should be set to {$value}");
        }
    }

    /** @test */
    public function updating_element()
    {
        $element = $this->generateSavedPrismFusionRangeWithEntries(2);
        $data = $this->generatePrismFusionRangeData(1);
        $data['entries'][0]['with_head_posture'] = PrismFusionRange_Entry::$WITHOUT_HEAD_POSTURE;

        $this->updateElementWithDataWithController($element, $data);
        $element->refresh();

        $this->assertEquals($data['comments'], $element->comments);

        $this->assertCount(1, $element->entries);
        foreach ($data['entries'][0] as $attr => $val) {
            $this->assertEquals($val, $element->entries[0]->$attr);
        }
    }

    protected function createElementWithDataWithController($data)
    {
        $model_name = \CHtml::modelName(PrismFusionRange::class);
        $_POST[$model_name] = $data;

        $event_id = $this->performCreateRequestForRandomPatient();

        return PrismFusionRange::model()->findByAttributes(['event_id' => $event_id]);
    }
}
