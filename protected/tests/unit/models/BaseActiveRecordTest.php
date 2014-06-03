<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class BaseActiveRecordTest extends CDbTestCase
{

	/**
	 * @var AddressType
	 */
	public $model;
	/*   public $fixtures = array(
		 'alllergies' => 'Allergy',
		 ); */
	public $testattributes = array(
		'name' => 'allergy test'
	);

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		//using allergy model to test the active record
		$this->model = new Allergy;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{

	}

	/**
	 * @covers BaseActiveRecord::save
	 * @todo   Implement testSave().
	 */
	public function testSave()
	{

		//using allergy model to test the active record

		$testmodel = new Allergy;
		$testmodel->setAttributes($this->testattributes);

		$testmodel->save();

		$result = Allergy::model()->findByAttributes(array('name' => 'allergy test'))->getAttributes();
		$expected = $this->testattributes;

		$this->assertEquals($expected['name'], $result['name'], 'attribute match');
	}

	/**
	 * @covers BaseActiveRecord::NHSDate
	 * @todo   Implement testNHSDate().
	 */
	public function testNHSDate()
	{

		$this->model->last_modified_date = '1902-01-01 00:00:00';
		$result = $this->model->NHSDate('last_modified_date', $empty_string = '-');

		$expected = '1 Jan 1902';

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers BaseActiveRecord::NHSDateAsHTML
	 * @todo   Implement testNHSDateAsHTML().
	 */
	public function testNHSDateAsHTML()
	{

		$this->model->last_modified_date = '1902-01-01 00:00:00';
		$result = $this->model->NHSDateAsHTML('last_modified_date', $empty_string = '-');

		$expected = '<span class="day">1</span><span class="mth">Jan</span><span class="yr">1902</span>';

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers BaseActiveRecord::audit
	 * @todo   Implement testAudit().
	 */
	public function testAudit()
	{
		$this->markTestSkipped('this has been already implemented in the audittest model');
	}

	public function test__set_has_many()
	{
		$test = $this->getMockBuilder('BaseActiveRecord')
				->disableOriginalConstructor()
				->setMethods(array('getMetaData', 'getPrimaryKey'))
				->getMock();

		$hm_cls = new CHasManyRelation('has_many', 'RelationTestClass', 'element_id');

		$meta = $this->getMockBuilder('CActiveRecordMetaData')
				->disableOriginalConstructor()
				->getMock();

		$meta->relations = array(
				'has_many' => $hm_cls,
		);

		$test->expects($this->any())
			->method('getMetaData')
			->will($this->returnValue($meta));
		$test->expects($this->any())
			->method('getPrimaryKey')
			->will($this->returnValue(1));

		$test->__set('has_many',array('test'));
		$this->assertTrue(is_array($test->has_many));
		$this->assertEquals('test', $test->has_many[0], 'should pass through assignment when behaviour turned off');

		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$test->__set('has_many', array('test2'));
		$this->assertTrue(is_array($test->has_many));
		$this->assertInstanceOf('RelationTestClass', $test->has_many[0], 'should set relation class when behaviour turned on');

		$rdp = $r->getProperty('relation_defaults');
		$rdp->setAccessible(true);
		$rdp->setValue($test, array('has_many' => array('default_prop' => 'test')));

		$test->__set('has_many',array(array('test_value' => 'a string')));
		$this->assertTrue(is_array($test->has_many));
		$this->assertInstanceOf('RelationTestClass', $test->has_many[0], 'should set relation class when behaviour turned on');
		$this->assertEquals('a string', $test->has_many[0]->test_value);
		$this->assertEquals('test', $test->has_many[0]->default_prop, 'should have picked up default property value');
	}

	public function test__set_many_many()
	{
		$mm_cls = new CManyManyRelation('many_many', 'RelationTestClass', 'many_many_ass(element_id, related_id)');

		$meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
						'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
												'primaryKey' => 'the_pk',
												)),
						'relations' => array(
								'many_many' => $mm_cls,
						)
				));

		$test = new ManyManyOwnerTestClass();
		$test->md = $meta;

		$test->many_many = array('test');
		$this->assertTrue(is_array($test->many_many));
		$this->assertEquals('test', $test->many_many[0], 'should pass through assignment when behaviour turned off');

		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$test->many_many = array('test2');
		$this->assertTrue(is_array($test->many_many));
		$this->assertInstanceOf('RelationTestClass', $test->many_many[0], 'should set relation class when behaviour turned on');
		$this->assertEquals('test2', $test->many_many[0]->getPrimaryKey());
	}

	public function getRelationMock($pk)
	{
		$mock = $this->getMockBuilder('RelationTestClass')
				->disableOriginalConstructor()
				->setMethods(array('getPrimaryKey'))
				->getMock();
		$mock->expects($this->any())
				->method('getPrimaryKey')
				->will($this->returnValue($pk));

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
				->will($this->returnValue(true));
		$mock->expects($this->any())
				->method('getPrimaryKey')
				->will($this->returnValue($pk));

		return $mock;
	}

	public function getRelationMockForDelete($pk)
	{
		$mock = $this->getMockBuilder('RelationTestClass')
				->disableOriginalConstructor()
				->setMethods(array('delete', 'getPrimaryKey'))
				->getMock();
		$mock->expects($this->any())
				->method('getPrimaryKey')
				->will($this->returnValue($pk));
		$mock->expects($this->once())
				->method('delete')
				->will($this->returnValue(true));

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
			->will($this->returnValue($pk));

		return $mock;
	}

	public function getRelationAssMockForDelete($pk, $rel_id)
	{
		$mock = $this->getMockBuilder('RelationTestAssClass')
				->disableOriginalConstructor()
				->setMethods(array('delete'))
				->getMock();
		$mock->rel_id = $rel_id;
		$mock->expects($this->any())
				->method('getPrimaryKey')
				->will($this->returnValue($pk));
		$mock->expects($this->once())
				->method('delete')
				->will($this->returnValue(true));

		return $mock;
	}

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
						)
				));
		$test->expects($this->once())
				->method('getMetaData')
				->will($this->returnValue($meta));

		$test->expects($this->once())
				->method('getSafeAttributeNames')
				->will($this->returnValue(array('has_many')));

		$new_vals = array($this->getRelationMockForSave(5));
		$orig_vals = array($this->getRelationMockForDelete(3));
		// fake the attribute having been set by __set
		$test->has_many = $new_vals;

		// fake the original values for the has_many relation value on the test instance
		$test->expects($this->once())
				->method('getRelated')
				->with($this->equalTo('has_many'), $this->equalTo(true))
				->will($this->returnValue($orig_vals));


		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$as = $r->getMethod('afterSave');
		$as->setAccessible(true);

		$as->invoke($test);
	}

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
						)
				));

		$test->expects($this->once())
				->method('getMetaData')
				->will($this->returnValue($meta));

		$test->expects($this->once())
				->method('getSafeAttributeNames')
				->will($this->returnValue(array('has_many_thru')));

		$hmt = $this->getRelationMock(8);
		$test->has_many_thru = array($hmt);

		$test->expects($this->at(2))
				->method('getRelated')
				->with($this->equalTo('has_many_thru'), $this->equalTo(true))
				->will($this->returnValue(array($this->getRelationMock(2), $hmt)));

		// consistent assignment objects with the getRelated call above
		$test->expects($this->at(3))
				->method('getRelated')
				->with($this->equalTo('has_many_thru_ass'), $this->equalTo(true))
				->will($this->returnValue(array($this->getRelationAssMockForDelete(1,2), $this->getRelationAssMock(2,8))));

		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$as = $r->getMethod('afterSave');
		$as->setAccessible(true);

		$as->invoke($test);
	}

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
						)
				));

		$test->expects($this->once())
				->method('getMetaData')
				->will($this->returnValue($meta));

		$test->expects($this->once())
			->method('getSafeAttributeNames')
			->will($this->returnValue(array('many_many')));

		// many many relations will not use save/delete methods, as they use command builder,
		// so we want a bare bones relation mock
		$mm = $this->getRelationMock(12);
		$test->many_many = array($mm, $this->getRelationMock(13));

		$test->expects($this->once())
				->method('getRelated')
				->with('many_many')
				->will($this->returnValue(array($this->getRelationMock(7), $mm)));

		// many many uses command builder to update the assignment table
		$ins_cmd = $this->getMockBuilder('CDbCommand')
				->disableOriginalConstructor()
				->setMethods(array('execute'))
				->getMock();

		$ins_cmd->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));

		$cmd_builder = $this->getMockBuilder('CDbCommandBuilder')
				->disableOriginalConstructor()
				->setMethods(array('createInsertCommand', 'createDeleteCommand'))
				->getMock();

		$cmd_builder->expects($this->any())
			->method('createInsertCommand')
			->will($this->returnValue($ins_cmd));

		$del_cmd = $this->getMockBuilder('CDbCommand')
				->disableOriginalConstructor()
				->setMethods(array('execute'))
				->getMock();
		$del_cmd->expects($this->once())
				->method('execute')
				->will($this->returnValue(true));

		$cmd_builder->expects($this->any())
				->method('createDeleteCommand')
				->will($this->returnValue($del_cmd));

		$test->expects($this->any())
			->method('getCommandBuilder')
			->will($this->returnValue($cmd_builder));

		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$as = $r->getMethod('afterSave');
		$as->setAccessible(true);

		$as->invoke($test);
	}
	
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
						)
				));

		$test->expects($this->any())
				->method('getMetaData')
				->will($this->returnValue($meta));

		$test->expects($this->any())
			->method('getSafeAttributeNames')
			->will($this->returnValue(array('has_many')));

		$test->has_many = null;

		// fake the original values for the has_many relation value on the test instance
		$test->expects($this->once())
				->method('getRelated')
				->with('has_many')
				->will($this->returnValue(array($this->getRelationMockForDelete(3))));

		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$as = $r->getMethod('afterSave');
		$as->setAccessible(true);

		$as->invoke($test);
	}

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
						)
				));

		$test->expects($this->any())
				->method('getMetaData')
				->will($this->returnValue($meta));

		$test->expects($this->any())
				->method('getSafeAttributeNames')
				->will($this->returnValue(array('has_many')));

		// fake the attribute having been set by __set
		$test->has_many = array($this->getRelationMockForSave(5), $this->getRelationMockForSave(6));

		$test->expects($this->once())
				->method('getRelated')
				->with('has_many')
				->will($this->returnValue(null));

		$r = new ReflectionClass($test);
		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$as = $r->getMethod('afterSave');
		$as->setAccessible(true);

		$as->invoke($test);
	}

	public function testbeforeDelete()
	{
		$test = $this->getMockBuilder('RelationOwnerSaveClass')
				->disableOriginalConstructor()
				->setMethods(array('getMetaData', 'getRelated', 'getPrimaryKey', 'getCommandBuilder'))
				->getMock();

		$hm_cls = new CHasManyRelation('has_many', 'RelationTestClass', 'element_id');
		$hmt_cls = new CHasManyRelation('has_many_thru', 'RelationTestClass', 'element_id', array('through' => 'has_many'));
		$mm_cls = new CManyManyRelation('many_many', 'RelationTestClass', 'many_many_ass(element_id, related_id)');

		$meta = ComponentStubGenerator::generate('CActiveRecordMetaData', array(
						'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
												'primaryKey' => 'the_pk',
										)),
						'relations' => array(
								//'has_many' => $hm_cls,
								//'has_many_thru' => $hmt_cls,
								'many_many' => $mm_cls,
						)
				));

		$test->expects($this->any())
				->method('getMetaData')
				->will($this->returnValue($meta));

		$test->expects($this->any())
			->method('getPrimaryKey')
			->will($this->returnValue('TestPK'));

		// many many uses command builder behaviour to delete assignment table entries
		$del_cmd = $this->getMockBuilder('CDbCommand')
				->disableOriginalConstructor()
				->setMethods(array('execute'))
				->getMock();

		$del_cmd->expects($this->once())
				->method('execute')
				->will($this->returnValue(true));

		$cmd_builder = $this->getMockBuilder('CDbCommandBuilder')
				->disableOriginalConstructor()
				->setMethods(array('createDeleteCommand'))
				->getMock();

		$cmd_builder->expects($this->any())
				->method('createDeleteCommand')
				->with($this->equalTo('many_many_ass'))
				->will($this->returnValue($del_cmd));

		$test->expects($this->any())
				->method('getCommandBuilder')
				->will($this->returnValue($cmd_builder));

		$r = new ReflectionClass($test);
		$m = $r->getMethod('beforeDelete');
		$m->setAccessible(true);

		$p = $r->getProperty('auto_update_relations');
		$p->setAccessible(true);
		$p->setValue($test, true);

		$m->invoke($test);

		$this->markTestIncomplete('has many uses static model method so cannot complete the test.');
	}
}

class RelationOwnerSaveClass extends BaseActiveRecord
{
	public $has_many;
	public $has_many_thru;
	public $many_many;
	public $the_pk;

}

class ManyManyOwnerTestClass extends BaseActiveRecord
{
	public $the_pk;
	public $md;

	public function __construct()
	{}

	public function getMetaData()
	{
		return $this->md;
	}
}

class RelationTestClass extends BaseActiveRecord
{
	public $default_prop;
	public $test_value;
	public $element_id;
	public $test_pk;

	public function __construct()
	{}

	public function rules()
	{
		return array(
			array('default_prop, test_value, element_id', 'safe')
		);
	}

	public function getMetaData()
	{
		return ComponentStubGenerator::generate('CActiveRecordMetaData', array(
					'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
								'primaryKey' => 'test_pk',
								'columns' => array('test_pk', 'default_prop')))
				));
	}


	public function find($condition='',$params=array())
	{
		return ComponentStubGenerator::generate(get_class(self), $params);
	}

	public function findByPk($pk,$condition='',$params=array())
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

	public function __construct()
	{}

	public function rules()
	{
		return array(
				array('default_prop, test_value, element_id', 'safe')
		);
	}

	public function getMetaData()
	{
		return ComponentStubGenerator::generate('CActiveRecordMetaData', array(
						'tableSchema' => ComponentStubGenerator::generate('CDbTableSchema', array(
												'primaryKey' => 'id',
												'columns' => array('id', 'element_id', 'rel_id')))
				));
	}


	public function find($condition='',$params=array())
	{
		return ComponentStubGenerator::generate(get_class(self), $params);
	}

	public function findByPk($pk,$condition='',$params=array())
	{
		$cls = __CLASS__;
		$res = new $cls();
		$res->setPrimaryKey($pk);
		return $res;
	}

	public function save($runValidation=true,$attributes=null, $allow_overriding=false)
	{
		return true;
	}

}
