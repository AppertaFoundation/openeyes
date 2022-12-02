<?php

use OEModule\BaseActiveRecordTest\models\BaseActiveRecordTest_NamespaceTestClass;
use OEModule\BaseActiveRecordTest\models\NamespaceTestClass;
use PHPUnit\Framework\MockObject\MockObject;

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
class BaseActiveRecordTest extends OEDbTestCase
{
    /**
     * @var BaseActiveRecord
     */
    public $model;

    public $testattributes = array(
        'name' => 'allergy test',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();

        //using allergy model to test the active record
        $this->model = new Patient();
    }

    public function getShortModelNameDataProvider()
    {
        return array(
            array('BaseActiveRecordTest_NonamespaceTestClass', 'BaseActiveRecordTest_NonamespaceTestClass'),
            array(BaseActiveRecordTest_NamespaceTestClass::class, 'BaseActiveRecordTest.NamespaceTestClass'),
            array(NamespaceTestClass::class, 'BaseActiveRecordTest.NamespaceTestClass'),
        );
    }

    /**
     * @covers BaseActiveRecord
     * @dataProvider getShortModelNameDataProvider
     * @param $class_name string
     * @param $short_name string
     */
    public function testGetShortModelName($class_name, $short_name)
    {
        $class_file = __DIR__.DIRECTORY_SEPARATOR.'BaseActiveRecordTest'.DIRECTORY_SEPARATOR.preg_replace('/^.*\\\\/', '', $class_name).'.php';
        require_once $class_file;
        $this->assertEquals($short_name, $class_name::getShortModelName());
    }

    /**
     * @covers BaseActiveRecord
     */
    public function test_basic_save()
    {
        $testmodel = $this->getMockBuilder(SimpleBaseActiveRecordClass::class)
            ->setMethods(array('getIsNewRecord', 'insert'))
            ->getMock();

        $testmodel->setAttributes(array('test_value' => 'new value'));

        $testmodel
            ->method('getIsNewRecord')
            ->willReturn(true);

        // Basically testing insert gets called to save the data
        $testmodel->expects($this->once())
            ->method('insert')
            ->willReturn(true);

        $testmodel->save();
    }

    /**
     * @covers BaseActiveRecord
     *
     */
    public function testNHSDate()
    {
        $this->model->last_modified_date = '1902-01-01 00:00:00';
        $result = $this->model->NHSDate('last_modified_date', $empty_string = '-');

        $expected = '1 Jan 1902';

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers BaseActiveRecord
     *
     */
    public function testNHSDateAsHTML()
    {
        $this->model->last_modified_date = '1902-01-01 00:00:00';
        $result = $this->model->NHSDateAsHTML('last_modified_date', $empty_string = '-');

        $expected = '<span class="day">1</span><span class="mth">Jan</span><span class="yr">1902</span>';

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function test__set_has_many()
    {
        $without_auto_relations = new WithAutoRelationsDisabled();
        $with_auto_relations = new WithAutoRelationsEnabled();

        $without_auto_relations->has_many = ['test'];
        $this->assertEquals('test', $without_auto_relations->has_many[0], 'should pass through assignment when behaviour turned off');

        $with_auto_relations->has_many = ['test2'];
        $this->assertInstanceOf(RelationTestClass::class, $with_auto_relations->has_many[0], 'should set relation class when behaviour turned on');

        $with_auto_relations->setDefaultRelationProperties([
            'has_many' => ['default_prop' => 'test']
        ]);

        $with_auto_relations->__set('has_many', array(array('test_value' => 'a string')));
        $this->assertInstanceOf('RelationTestClass', $with_auto_relations->has_many[0], 'should set relation class when behaviour turned on');
        $this->assertEquals('a string', $with_auto_relations->has_many[0]->test_value);
        $this->assertEquals('test', $with_auto_relations->has_many[0]->default_prop, 'should have picked up default property value');
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function test__set_many_many()
    {
        $without_auto_relations = new WithAutoRelationsDisabled();

        $without_auto_relations->many_many = array('test');
        $this->assertEquals('test', $without_auto_relations->many_many[0], 'should pass through assignment when behaviour turned off');

        $with_auto_relations = new WithAutoRelationsEnabled();

        $with_auto_relations->many_many = array('test2');
        $this->assertIsArray($with_auto_relations->many_many);
        $this->assertInstanceOf('RelationTestClass', $with_auto_relations->many_many[0], 'should set relation class when behaviour turned on');
    }

    public function getRelationMock($pk)
    {
        $mock = $this->getMockBuilder('RelationTestClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getPrimaryKey'))
            ->getMock();
        $mock
            ->method('getPrimaryKey')
            ->willReturn($pk);

        return $mock;
    }

    public function getRelationMockForSave($pk)
    {
        $mock = $this->getMockBuilder('RelationTestClass')
            ->disableOriginalConstructor()
            ->setMethods(array('save', 'getPrimaryKey'))
            ->getMock();
        $mock->expects($this->once())
            ->method('save')
            ->willReturn(true);
        $mock
            ->method('getPrimaryKey')
            ->willReturn($pk);

        return $mock;
    }

    public function getRelationMockForDelete($pk)
    {
        $mock = $this->getMockBuilder('RelationTestClass')
            ->disableOriginalConstructor()
            ->setMethods(array('delete', 'getPrimaryKey'))
            ->getMock();
        $mock
            ->method('getPrimaryKey')
            ->willReturn($pk);
        $mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        return $mock;
    }

    public function getRelationAssMock($pk, $rel_id)
    {
        $mock = $this->getMockBuilder('RelationTestAssClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getPrimaryKey'))
            ->getMock();
        $mock->rel_id = $rel_id;
        $mock->expects($this->any())
            ->method('getPrimaryKey')
            ->willReturn($pk);

        return $mock;
    }

    public function getRelationAssMockForDelete($pk, $rel_id)
    {
        $mock = $this->getMockBuilder('RelationTestAssClass')
            ->disableOriginalConstructor()
            ->setMethods(array('delete', 'getPrimaryKey'))
            ->getMock();
        $mock->rel_id = $rel_id;
        $mock
            ->method('getPrimaryKey')
            ->willReturn($pk);
        $mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        return $mock;
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function testafterSave_hasMany()
    {
        $test = $this->getMockBuilder('RelationOwnerSaveClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetaData', 'getSafeAttributeNames', 'getRelated', 'getPrimaryKey'))
            ->getMock();

        $hm_cls = new CHasManyRelation('has_many', 'RelationTestClass', 'element_id');

        $meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'the_pk',
            )),
            'relations' => array(
                'has_many' => $hm_cls,
            ),
            'columns' => array(),
        ));

        //   echo "<pre>" . print_r($meta, true) . "</pre>";die;

        $test
            ->method('getMetaData')
            ->willReturn($meta);

        $test->expects($this->once())
            ->method('getSafeAttributeNames')
            ->willReturn(array('has_many'));

        $new_vals = array($this->getRelationMockForSave(5));
        $orig_vals = array($this->getRelationMockForDelete(3));
        // fake the attribute having been set by __set
        $test->has_many = $new_vals;

        // fake the original values for the has_many relation value on the test instance
        $test->expects($this->once())
            ->method('getRelated')
            ->with($this->equalTo('has_many'), $this->equalTo(true))
            ->willReturn($orig_vals);

        $r = new ReflectionClass($test);
        $p = $r->getProperty('auto_update_relations');
        $p->setAccessible(true);
        $p->setValue($test, true);

        $as = $r->getMethod('afterSave');
        $as->setAccessible(true);

        $as->invoke($test);
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function testafterSave_hasManyThru()
    {
        $test = $this->getMockBuilder('RelationOwnerSaveClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetaData', 'getSafeAttributeNames', 'getRelated', 'getPrimaryKey', 'getCommandBuilder'))
            ->getMock();

        $hmt_ass_cls = new CHasManyRelation('has_many_thru_ass', 'RelationTestAssClass', 'element_id');
        $hmt_cls = new CHasManyRelation('has_many_thru', 'RelationTestClass', 'rel_id', array('through' => 'has_many_thru_ass'));

        $meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'the_pk',
            )),
            'relations' => array(
                'has_many_thru' => $hmt_cls,
                'has_many_thru_ass' => $hmt_ass_cls,
            ),
            'columns' => array(),
        ));

        $test
            ->method('getMetaData')
            ->willReturn($meta);

        $test->expects($this->once())
            ->method('getSafeAttributeNames')
            ->willReturn(array('has_many_thru'));

        $hmt = $this->getRelationMock(8);
        $test->has_many_thru = array($hmt);

        $test->expects($this->at(2))
            ->method('getRelated')
            ->with($this->equalTo('has_many_thru'), $this->equalTo(true))
            ->willReturn(array($this->getRelationMock(2), $hmt));

        // consistent assignment objects with the getRelated call above
        $test->expects($this->at(3))
            ->method('getRelated')
            ->with($this->equalTo('has_many_thru_ass'), $this->equalTo(true))
            ->willReturn(array($this->getRelationAssMockForDelete(1, 2), $this->getRelationAssMock(2, 8)));

        $r = new ReflectionClass($test);
        $p = $r->getProperty('auto_update_relations');
        $p->setAccessible(true);
        $p->setValue($test, true);

        $as = $r->getMethod('afterSave');
        $as->setAccessible(true);

        $as->invoke($test);
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function testafterSave_manyMany()
    {
        $test = $this->getMockBuilder('RelationOwnerSaveClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetaData', 'getSafeAttributeNames', 'getRelated', 'getPrimaryKey', 'getCommandBuilder'))
            ->getMock();

        $mm_cls = new CManyManyRelation('many_many', 'RelationTestClass', 'many_many_ass(element_id, related_id)');

        $meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'the_pk',
            )),
            'relations' => array(
                'many_many' => $mm_cls,
            ),
            'columns' => array(),
        ));

        $test
            ->method('getMetaData')
            ->willReturn($meta);

        $test->expects($this->once())
            ->method('getSafeAttributeNames')
            ->willReturn(array('many_many'));

        // many many relations will not use save/delete methods, as they use command builder,
        // so we want a bare bones relation mock
        $mm = $this->getRelationMock(12);
        $test->many_many = array($mm, $this->getRelationMock(13));

        $test->expects($this->once())
            ->method('getRelated')
            ->with('many_many')
            ->willReturn(array($this->getRelationMock(7), $mm));

        // many many uses command builder to update the assignment table
        $ins_cmd = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMock();

        $ins_cmd->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $cmd_builder = $this->getMockBuilder('CDbCommandBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('createInsertCommand', 'createDeleteCommand'))
            ->getMock();

        $cmd_builder
            ->method('createInsertCommand')
            ->willReturn($ins_cmd);

        $del_cmd = $this->getMockBuilder('CDbCommand')
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMock();
        $del_cmd->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $cmd_builder
            ->method('createDeleteCommand')
            ->willReturn($del_cmd);

        $test->expects($this->any())
            ->method('getCommandBuilder')
            ->willReturn($cmd_builder);

        $r = new ReflectionClass($test);
        $p = $r->getProperty('auto_update_relations');
        $p->setAccessible(true);
        $p->setValue($test, true);

        $as = $r->getMethod('afterSave');
        $as->setAccessible(true);

        $as->invoke($test);
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function testafterSave_setNull()
    {
        $test = $this->getMockBuilder('RelationOwnerSaveClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetaData', 'getSafeAttributeNames', 'getRelated', 'getPrimaryKey'))
            ->getMock();

        $hm_cls = new CHasManyRelation('has_many', 'RelationTestClass', 'element_id');

        $meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'the_pk',
            )),
            'relations' => array(
                'has_many' => $hm_cls,
            ),
            'columns' => array(),
        ));

        $test
            ->method('getMetaData')
            ->willReturn($meta);

        $test
            ->method('getSafeAttributeNames')
            ->willReturn(array('has_many'));

        $test->has_many = null;

        // fake the original values for the has_many relation value on the test instance
        $test->expects($this->once())
            ->method('getRelated')
            ->with('has_many')
            ->willReturn(array($this->getRelationMockForDelete(3)));

        $r = new ReflectionClass($test);
        $p = $r->getProperty('auto_update_relations');
        $p->setAccessible(true);
        $p->setValue($test, true);

        $as = $r->getMethod('afterSave');
        $as->setAccessible(true);

        $as->invoke($test);
    }

    /**
     * @covers BaseActiveRecord
     * @throws ReflectionException
     */
    public function testAfterSaveNewValues()
    {
        $test = $this->getMockBuilder('RelationOwnerSaveClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getMetaData', 'getSafeAttributeNames', 'getRelated', 'getPrimaryKey'))
            ->getMock();

        $hm_cls = new CHasManyRelation('has_many', 'RelationTestClass', 'element_id');

        $meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'the_pk',
            )),
            'relations' => array(
                'has_many' => $hm_cls,
            ),
            'columns' => array(),
        ));

        $test
            ->method('getMetaData')
            ->willReturn($meta);

        $test
            ->method('getSafeAttributeNames')
            ->willReturn(array('has_many'));

        // fake the attribute having been set by __set
        $test->has_many = array($this->getRelationMockForSave(5), $this->getRelationMockForSave(6));

        $test->expects($this->once())
            ->method('getRelated')
            ->with('has_many')
            ->willReturn(null);

        $r = new ReflectionClass($test);
        $p = $r->getProperty('auto_update_relations');
        $p->setAccessible(true);
        $p->setValue($test, true);

        $as = $r->getMethod('afterSave');
        $as->setAccessible(true);

        $as->invoke($test);
    }

    /**
     * @covers BaseActiveRecord
     */
    public function testsaveOnlyIfDirty()
    {
        $testmodel = $this->getMockBuilder(SimpleBaseActiveRecordClass::class)
            ->setMethods(array('getIsNewRecord', 'insert'))
            ->getMock();

        $testmodel->setAttributes(array('test_value' => 'new value'));

        $testmodel
            ->method('getIsNewRecord')
            ->willReturn(true);

        // Basically testing insert gets called to save the data
        $testmodel->expects($this->exactly(2))
            ->method('insert')
            ->willReturn(true);

        $this->assertTrue($testmodel->saveOnlyIfDirty()->save());

        // no second save with no changes
        $this->assertFalse($testmodel->saveOnlyIfDirty()->save());

        $testmodel->test_value = 'a different value again';

        // saved again now an attribute was altered
        $this->assertTrue($testmodel->saveOnlyIfDirty()->save());
    }
}

class RelationOwnerSaveClass extends BaseActiveRecord
{
    public $has_many;
    public $has_many_thru;
    public $many_many;
    public $the_pk;
}

class SimpleBaseActiveRecordClass extends BaseActiveRecord
{
    public $test_value;
    public $test_pk;
    public $created_user_id;
    public $created_date;
    public $last_modified_date;
    public $last_modified_user_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function rules()
    {
        return array(
            array('test_value', 'safe'),
        );
    }

    /**
     * @return CActiveRecordMetaData|MockObject
     * @throws ReflectionException
     */
    public function getMetaData()
    {
        $columns = array(
            'test_value' => 'string',
            'created_user_id' => 'int',
            'created_date' => 'string',
            'last_modified_user_id' => 'int',
            'last_modified_date' => 'string');
        return ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'test_pk',
                'columns' => $columns)),
            'columns' => $columns,
        ));
    }
}

class RelationTestClass extends BaseActiveRecord
{
    public $default_prop;
    public $test_value;
    public $element_id;
    public $test_pk;

    public function __construct()
    {
        parent::__construct();
    }

    public function rules()
    {
        return array(
            array('default_prop, test_value, element_id', 'safe'),
        );
    }

    /**
     * @return CActiveRecordMetaData|MockObject
     * @throws ReflectionException
     */
    public function getMetaData()
    {
        $columns = array('test_pk', 'default_prop');
        return ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'test_pk',
                'columns' => $columns)),
            'columns' => $columns
        ));
    }

    /**
     * @param string $condition
     * @param array $params
     * @return CActiveRecord|MockObject|RelationTestClass|null
     * @throws ReflectionException
     */
    public function find($condition = '', $params = array())
    {
        return ComponentStubGenerator::generate(self::class, $params);
    }

    public function findByPk($pk, $condition = '', $params = array())
    {
        $cls = __CLASS__;
        $res = new $cls();
        $res->setPrimaryKey($pk);

        return $res;
    }
}

class RelationTestAssClass extends BaseActiveRecord
{
    public $rel_id;
    public $element_id;

    public function rules()
    {
        return array(
            array('default_prop, test_value, element_id', 'safe'),
        );
    }

    /**
     * @return CActiveRecordMetaData|MockObject
     * @throws ReflectionException
     */
    public function getMetaData()
    {
        return ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'id',
                'columns' => array('id', 'element_id', 'rel_id'), )),
        ));
    }

    /**
     * @param string $condition
     * @param array $params
     * @return CActiveRecord|MockObject|RelationTestAssClass|null
     * @throws ReflectionException
     */
    public function find($condition = '', $params = array())
    {
        return ComponentStubGenerator::generate(self::class, $params);
    }

    public function findByPk($pk, $condition = '', $params = array())
    {
        $cls = __CLASS__;
        $res = new $cls();
        $res->setPrimaryKey($pk);

        return $res;
    }

    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        return true;
    }
}
abstract class ForAutoRelationsTesting extends BaseActiveRecord
{
    public function getMetaData()
    {
        $columns = array('test_pk', 'default_prop');
        return ComponentStubGenerator::generate('CActiveRecordMetaData', array(
            'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
                'primaryKey' => 'test_pk',
                'columns' => $columns)),
            'columns' => $columns,
            'relations' => [
                'has_many' => new CHasManyRelation('has_many', 'RelationTestClass', 'element_id'),
                'many_many' => new CManyManyRelation('many_many', 'RelationTestClass', 'many_many_ass(element_id, related_id)')
            ]
        ));
    }

    public function getPrimaryKey()
    {
        return 'foo';
    }
}
class WithAutoRelationsEnabled extends ForAutoRelationsTesting
{
    protected $auto_update_relations = true;


    /**
     * Convenience accessor for testing purposes
     */
    public function setDefaultRelationProperties(array $relation_defaults = [])
    {
        $this->relation_defaults = $relation_defaults;
    }
}

class WithAutoRelationsDisabled extends ForAutoRelationsTesting
{
    protected $auto_update_relations = false;
}
