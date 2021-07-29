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

class ProcedureTest extends ActiveRecordTestCase
{

    public $fixtures = array(
        'procedure' => 'Procedure',
    );

    public function getModel()
    {
        return Procedure::model();
    }

    public function setUp()
    {
        parent::setUp();
        $this->model = new Procedure();
    }

    public function testModel()
    {
        $this->assertEquals('Procedure', get_class(Procedure::model()), 'Class name should match model.');
    }

    /**
     * @covers Procedure
     */
    public function testTableName()
    {
        $this->assertEquals('proc', $this->model->tableName());
    }

    /**
     * @covers Procedure
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->procedure('procedure1')->validate());
        $this->assertEmpty($this->procedure('procedure1')->errors);
    }

    /**
     * @covers Procedure
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'term' => 'Term',
            'short_format' => 'Short Format',
            'default_duration' => 'Default Duration',
            'opcsCodes.name' => 'OPCS Code',
            'low_complexity_criteria' => 'Low Complexity Criteria',
        );
        $this->assertEquals($expected, $this->model->attributeLabels());
    }
}
