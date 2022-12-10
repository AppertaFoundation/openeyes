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

use OEModule\OphCiExamination\models\traits\HasRelationOptions;

/**
 * Class HasRelationOptionsTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models\traits
 * @covers \OEModule\OphCiExamination\models\traits\HasRelationOptions
 * @group sample-data
 * @group strabismus
 */
class HasRelationOptionsTest extends \OEDbTestCase
{
    use \WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->createTestTable('test_has_relation_options_lookup', [
            'name' => 'varchar(63) not null',
            'display_order' => 'tinyint default 1 not null',
            'active' => 'boolean default true'
        ]);

        $this->createTestTable('test_has_relation_options', [
            'lookup_id' => 'int(11)'
        ], [
            'test_has_relation_option_lookup_fk' => [
                'lookup_id',
                'test_has_relation_options_lookup',
                'id'
            ]
        ]);

        HasRelationOptions_TestClass::clearCache();
        ;
    }

    public function createLookups($count = 1)
    {
        $lookups = [];
        for ($i = 1; $i <= $count; $i++) {
            $lookup = new HasRelationOptionsLookup_TestClass();
            $lookup->name = $this->faker->word();
            $lookup->display_order = $i;
            $lookup->save();
            $lookups[] = $lookup;
        }

        return $lookups;
    }

    public function optionRetrievalMethodsProvider()
    {
        return [
            ['instanceOptionsFromProperty', 'as property'],
            ['instanceOptionsFromMethod', 'as method call']
        ];
    }

    /**
     * @test
     * @dataProvider optionRetrievalMethodsProvider
     */
    public function options_are_retrieved_and_cached($access_method, $message)
    {
        $test_options = $this->createLookups(3);

        $instance = new HasRelationOptions_TestClass();
        $options = $this->$access_method($instance);

        $this->assertModelsMatch($test_options, $options, $message);
        $this->assertModelsMatch($test_options,
            HasRelationOptions_TestClass::$relation_options_lookup_cache[HasRelationOptionsLookup_TestClass::class . '__all__'],
            $message);
    }

    /**
     * There's no good means of intercepting the SQL query for this, but
     * this seems like a reasonable test to ensure the cache is used
     *
     * @test
     * @dataProvider optionRetrievalMethodsProvider
     */
    public function all_options_are_retrieved_from_cache($access_method, $message)
    {
        HasRelationOptions_TestClass::$relation_options_lookup_cache[HasRelationOptionsLookup_TestClass::class . '__all__'] = ['foo'];
        $instance = new HasRelationOptions_TestClass();
        $options = $this->$access_method($instance);

        $this->assertEquals(['foo'], $options, $message);
    }

    /**
     * @test
     * @dataProvider optionRetrievalMethodsProvider
     */
    public function inactive_option_not_retrieved_by_property($access_method, $message)
    {
        $test_options = $this->createLookups(3);
        $inactive = $test_options[2];
        $inactive->active = false;
        $inactive->save();

        $instance = new HasRelationOptions_TestClass();
        $options = $this->$access_method($instance);

        $this->assertModelsMatch([$test_options[0], $test_options[1]], $options, $message);
    }

    /**
     * @test
     * @dataProvider optionRetrievalMethodsProvider
     */
    public function inactive_option_is_retrieved_by_property_when_set_to_relation($access_method, $message)
    {
        $test_options = $this->createLookups(3);
        $inactive = $test_options[2];
        $inactive->active = false;
        $inactive->save();

        $instance = new HasRelationOptions_TestClass();
        $instance->lookup_id = $inactive->getPrimaryKey();
        $options = $this->$access_method($instance);

        $this->assertModelsMatch($test_options, $options, $message);
    }

    protected function instanceOptionsFromProperty($instance, $property = 'lookup')
    {
        return $instance->{"{$property}_options"};
    }

    protected function instanceOptionsFromMethod($instance, $property = 'lookup')
    {
        $method_name = "{$property}Options";
        return $instance->$method_name();
    }

    protected function assertModelsMatch($expected, $value, $message = "")
    {
        if (!is_array($expected)) {
            $expected = [$expected];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $this->assertEquals(count($expected), count($value), "{$message}Model counts do not match");

        if (count($expected) === 0) {
            return;
        }

        $expected_cls = get_class($expected[0]);

        $value_pks = array_map(function ($instance) use ($expected_cls) {
            $this->assertInstanceOf($expected_cls, $instance);
            return $instance->getPrimaryKey();
        }, $value);

        $expected_pks = array_map(function ($instance) {
            return $instance->getPrimaryKey();
        }, $expected);

        sort($value_pks);
        sort($expected_pks);

        $this->assertEquals($expected_pks, $value_pks, "{$message}primary keys do not match");
    }
}

class HasRelationOptionsLookup_TestClass extends \BaseActiveRecord
{
    public function tableName()
    {
        return 'test_has_relation_options_lookup';
    }


    public function behaviors()
    {
        return array(
            'LookupTable' => \LookupTable::class,
        );
    }
}

class HasRelationOptions_TestClass extends \BaseEventTypeElement
{
    use HasRelationOptions;

    public function tableName()
    {
        return 'test_has_relation_options';
    }

    public function relations()
    {
        return [
            'lookup' => [self::BELONGS_TO, HasRelationOptionsLookup_TestClass::class, 'lookup_id']
        ];
    }
}
