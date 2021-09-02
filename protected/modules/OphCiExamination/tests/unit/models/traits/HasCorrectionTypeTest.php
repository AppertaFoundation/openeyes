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

namespace OEModule\OphCiExamination\tests\unit\models\traits;

use ModelTestCase;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\traits\HasCorrectionType;

/**
 * Class HasCorrectionTypeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models\traits
 * @covers \OEModule\OphCiExamination\models\traits\HasCorrectionType
 * @group sample-data
 * @group strabismus
 * @group correction-given
 */
class HasCorrectionTypeTest extends ModelTestCase
{
    protected $element_cls = HasCorrectionTypeTest_TestClass::class;

    public function setUp()
    {
        parent::setUp();

        $this->createTestTable('test_correction_type', [
            'correctiontype_id' => 'int(11) not null'
        ], [
            'test_correctiontype_correction_type_fk' => [
                'correctiontype_id',
                'ophciexamination_correctiontype',
                'id'
            ]
        ]);
    }

    /** @test */
    public function cannot_set_random_value_to_correction_type()
    {
        $instance = new HasCorrectionTypeTest_TestClass();
        $instance->correctiontype_id = 'foo';
        $this->assertAttributeInvalid($instance, 'correctiontype_id', 'is invalid');
    }

    /** @test */
    public function relation_definition_works()
    {
        $instance = new HasCorrectionTypeTest_TestClass();
        $criteria = new \CDbCriteria();
        $criteria->limit = 5;
        $all = CorrectionType::model()->findAll($criteria);
        $valid = $all[array_rand($all)];

        $instance->correctiontype_id = $valid->getPrimaryKey();
        $this->assertTrue($instance->validate());

        $this->assertEquals($valid->getPrimaryKey(), $instance->correctiontype->getPrimaryKey());
    }
}

class HasCorrectionTypeTest_TestClass extends \BaseActiveRecord
{
    use HasCorrectionType;

    public $correction_type_attributes = ['correctiontype_id'];

    public function tableName()
    {
        return 'test_correction_type';
    }

    public function rules()
    {
        return $this->rulesForCorrectionType();
    }

    public function relations()
    {
        return $this->relationsForCorrectionType();
    }
}
