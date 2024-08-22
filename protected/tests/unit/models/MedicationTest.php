<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class MedicationTest
 * @method drugs($fixture_id)
 */
class MedicationTest extends ActiveRecordTestCase
{
    /**
     *  @var Medication
     */
    protected Medication $model;
    public $fixtures = array(
        'drugs' => 'Medication',
        'medication_use' => EventMedicationUse::class,
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Medication();
    }

    /**
     *  @covers Medication
     *
     */
    public function testModel()
    {
        $this->assertEquals('Medication', get_class(Medication::model()), 'Class name should match model.');
    }

    /**
     *  @covers Medication
     *
     */
    public function testTableName()
    {
        $this->assertEquals('medication', $this->model->tableName());
    }

    /**
     * @covers Medication
     *
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->drugs('drug1')->validate(), var_export($this->drugs('drug1')->errors, true));

        $this->assertEmpty($this->drugs('drug2')->errors);
    }

    /**
     *  @covers Medication
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'default_route_id' => 'Default route',
            'id' => 'ID',
            'source_type' => 'Source Type',
            'source_subtype' => 'Source Subtype',
            'preferred_term' => 'Preferred Term',
            'preferred_code' => 'Preferred Code',
            'vtm_term' => 'VTM Term',
            'vtm_code' => 'VTM Code',
            'vmp_term' => 'VMP Term',
            'vmp_code' => 'VMP Code',
            'amp_term' => 'AMP Term',
            'amp_code' => 'AMP Code',
            'deleted_date' => 'Deleted Date',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'will_copy' => 'Will copy',
            'default_form_id' => 'Default form',
            'default_dose' => 'Default dose',
            'default_dose_unit_term' => 'Default dose unit',
            'default_dose' => 'Default dose'
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers Medication
     *
     */
    public function testGetLabel()
    {
        $result = $this->drugs('drug1')->getLabel();
        $preservative_free = MedicationAttribute::model()->findByAttributes(array('name' => 'PRESERVATIVE_FREE'));

        $preservative_free_attr = array_filter($this->drugs('drug1')->medicationAttributeAssignments, static function ($item) use ($preservative_free) {
            return $item->id === $preservative_free->id;
        });

        if (!empty($preservative_free_attr)) {
            $expected = 'Abidec drops (No Preservative)';
            $this->assertEquals($expected, $result);
        } else {
            $expected = 'Abidec drops';
            $this->assertEquals($expected, $result);
        }
    }
}
