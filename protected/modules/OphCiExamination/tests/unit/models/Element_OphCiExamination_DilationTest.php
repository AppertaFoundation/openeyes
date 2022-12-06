<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models;

class Element_OphCiExamination_DilationTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'ep' => 'Episode',
        'ev' => 'Event',
        'drugs' => ':ophciexamination_dilation_drugs',
    );

    public $delete_element_ids = array();

    public function getModel()
    {
        return models\Element_OphCiExamination_Dilation::model();
    }

    public function tearDown(): void
    {
        foreach ($this->delete_element_ids as $id) {
            foreach (models\OphCiExamination_Dilation_Treatment::model()->findAll('element_id = ?', array($id)) as $t) {
                $t->noVersion()->delete();
            }
            models\Element_OphCiExamination_Dilation::model()->noVersion()->deleteByPk($id);
        }
        $this->delete_element_ids = array();

        parent::tearDown();
    }

    public function getValidTreatmentMock()
    {
        $treatment = $this->getMockBuilder('\OEModule\OphCiExamination\models\OphCiExamination_Dilation_Treatment')
                ->disableOriginalConstructor()
                ->setMethods(array('validate'))
                ->getMock();
        $treatment->expects($this->once())
                ->method('validate')
                ->will($this->returnValue(true));

        return $treatment;
    }

    public function testValidate_treatments()
    {
        $event = $this->ev('event1');

        foreach (array('left' => \Eye::LEFT, 'right' => \Eye::RIGHT, 'both' => \Eye::BOTH) as $side => $eye_id) {
            $el = new models\Element_OphCiExamination_Dilation();
            $el->event_id = $event->id;
            $el->eye_id = $eye_id;

            $this->assertEquals(false, $el->validate());

            $treatment = $this->getValidTreatmentMock();

            if ($side != 'both') {
                $el->{$side.'_treatments'} = array($treatment);
                $this->assertTrue($el->validate(), 'Validation should be successful for {$side} treatment');
            } else {
                $treatment2 = $this->getValidTreatmentMock();

                $el->left_treatments = array($treatment);
                $el->right_treatments = array($treatment2);
                $this->assertTrue($el->validate(), 'Validation should be successful for {$side} treatment');
            }
        }
    }

    public function testUpdateTreatments()
    {
        $event = $this->ev('event1');
        $el = new models\Element_OphCiExamination_Dilation();
        $el->event_id = $event->id;
        $el->eye_id = \Eye::BOTH;

        $el->left_treatments = array($this->getValidTreatmentMock());
        $el->right_treatments = array($this->getValidTreatmentMock());
        $el->save();

        $this->delete_element_ids[] = $el->id;

        $el->updateTreatments(\Eye::LEFT, array(array(
                    'drug_id' => $this->drugs['drug1']['id'],
                    'drops' => 2,
                    'treatment_time' => '11:00',
                )));

        $el->updateTreatments(\Eye::RIGHT, array(array(
                        'drug_id' => $this->drugs['drug2']['id'],
                        'drops' => 2,
                        'treatment_time' => '11:00',
                )));

        $this->assertCount(1, models\OphCiExamination_Dilation_Treatment::model()->findAll('element_id = ? AND side = ?', array($el->id, models\OphCiExamination_Dilation_Treatment::LEFT)));
        $this->assertCount(1, models\OphCiExamination_Dilation_Treatment::model()->findAll('element_id = ? AND side = ?', array($el->id, models\OphCiExamination_Dilation_Treatment::RIGHT)));
    }
}
