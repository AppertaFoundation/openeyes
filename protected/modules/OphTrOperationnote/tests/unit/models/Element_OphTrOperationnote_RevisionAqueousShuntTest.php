<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use Element_OphTrOperationnote_RevisionAqueousShunt;
class Element_OphTrOperationnote_RevisionAqueousShuntTest extends ActiveRecordTestCase
{

    public $fixtures = array(
        'revision' => 'Element_OphTrOperationnote_RevisionAqueousShunt',
    );

    public function setUp() {
        $this->model = new Element_OphTrOperationnote_RevisionAqueousShunt();
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->model);
    }

    public function getModel() {
        return Element_OphTrOperationnote_RevisionAqueousShunt::model();
    }

    public function testModel()
    {
        $this->assertEquals('Element_OphTrOperationnote_RevisionAqueousShunt', get_class(Element_OphTrOperationnote_RevisionAqueousShunt::model()), 'Class name should match model.');
    }

    /**
     * @covers Procedure
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophtroperationnote_revision_aqueous', $this->model->tableName());
    }

    /**
     * @covers Procedure
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->revision('revision1')->validate());
        $this->assertEmpty($this->revision('revision1')->errors);
    }

    /**
     * @covers Procedure
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'plate_pos_id' => 'Plate position of revised tube',
            'is_shunt_explanted' => 'Shunt explanted',
            'final_tube_position_id' => 'Final tube position',
            'intraluminal_stent_id' => 'Intraluminal Stent',
            'is_visco_in_ac' => 'Visco in AC',
            'is_flow_tested' => 'Flow Tested',
            'comments' => 'Comments'
        );
        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    public function testGetPlatePosById()
    {
        $this->assertEquals('STQ', $this->model->getPlatePosById($this->revision('revision1')->plate_pos_id));
        $this->assertEquals('SNQ', $this->model->getPlatePosById($this->model::PLATE_POS_SNQ));
        $this->assertEquals('INQ', $this->model->getPlatePosById($this->model::PLATE_POS_INQ));
        $this->assertEquals('ITQ', $this->model->getPlatePosById($this->model::PLATE_POS_ITQ));
        $this->assertNull($this->model->getPlatePosById(99));
    }

    public function testGetTubePosById()
    {
        $this->assertEquals('AC', $this->model->getTubePosById($this->revision('revision1')->final_tube_position_id));
        $this->assertEquals('Sulcus', $this->model->getTubePosById($this->model::TUBE_POS_SULCUS));
        $this->assertEquals('Pars Plana', $this->model->getTubePosById($this->model::TUBE_POS_PARS_PLANA));
        $this->assertNull($this->model->getTubePosById(99));
    }

    public function testGetRipcordSutureById()
    {
        $this->assertEquals('Not modified', $this->model->getRipcordSutureById($this->model::RIPCORD_SUTURE_NOT_MOD));
        $this->assertEquals('Newly inserted', $this->model->getRipcordSutureById($this->model::RIPCORD_SUTURE_NEWLY_INS));
        $this->assertEquals('Removed', $this->model->getRipcordSutureById($this->model::RIPCORD_SUTURE_REMOVED));
        $this->assertEquals('Adjusted', $this->model->getRipcordSutureById($this->model::RIPCORD_SUTURE_ADJUSTED));
        $this->assertEquals('No intraluminal stent', $this->model->getRipcordSutureById($this->model::RIPCORD_SUTURE_NO_RIPCORD));
        $this->assertNull($this->model->getRipcordSutureById(99));
    }
}
