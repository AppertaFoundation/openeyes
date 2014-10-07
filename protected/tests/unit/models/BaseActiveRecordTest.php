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
class BaseActiveRecordTest extends OEDbTestCase
{
	public $model;

	public $testattributes = array(
		'name' => 'allergy test',
	);

	public $fixtures = array(
		'items' => 'RelationTest_Item',
		'item_assignments' => 'RelationTest_Item_Assignment',
	);

	public static function setUpBeforeClass()
	{
		self::createTestTable('relationtest_item',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(64) not null',
		));

		self::createTestTable('relationtest_element',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
		));

		self::createTestTable('relationtest_item_assignment',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_id' => 'int(10) unsigned NOT NULL',
				'item_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'tinyint(1) unsigned not null',
			),
			array(
				'relationtest_item_assignment_element_id_fk' => array('element_id','relationtest_element','id'),
				'relationtest_item_assignment_item_id_fk' => array('item_id','relationtest_item','id'),
			));
	}

	public static function tearDownAfterClass()
	{
		self::dropTable('relationtest_item_assignment');
		self::dropTable('relationtest_element');
		self::dropTable('relationtest_item');
	}

	protected function setUp()
	{
		parent::setUp();

		//using allergy model to test the active record
		$this->model = new Allergy;
	}

	/**
	 * @covers BaseActiveRecord::save
	 * @todo	 Implement testSave().
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
	 * @todo	 Implement testNHSDate().
	 */
	public function testNHSDate()
	{ $this->model->last_modified_date = '1902-01-01 00:00:00'; $result = $this->model->NHSDate('last_modified_date', $empty_string = '-'); $expected = '1 Jan 1902'; 
		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers BaseActiveRecord::NHSDateAsHTML
	 * @todo	 Implement testNHSDateAsHTML().
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
	 * @todo	 Implement testAudit().
	 */
	public function testAudit()
	{
		$this->markTestSkipped('this has been already implemented in the audittest model');
	}

	public function test__setHasMany_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = true;

		$element->attributes = array('items' => array(
			array('item_id' => 1),
			array('item_id' => 3),
			array('item_id' => 4)
		));

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[0]);
		$this->assertEquals(1, $element->items[0]->item_id);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[1]);
		$this->assertEquals(3, $element->items[1]->item_id);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[2]);
		$this->assertEquals(4, $element->items[2]->item_id);
	}

	public function test__setHasMany_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = false;
		
		$data = array('items' => array(2,4,1));
		
		$element->attributes = $data;

		$this->assertEquals(array(2,4,1), $element->items);
	}

	public function test__setManyMany_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->id);
		$this->assertEquals('item 2', $element->items[0]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[1]);
		$this->assertEquals(4, $element->items[1]->id);
		$this->assertEquals('item 4', $element->items[1]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[2]);
		$this->assertEquals(1, $element->items[2]->id);
		$this->assertEquals('item 1', $element->items[2]->name);
	}

	public function test__setManyMany_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = false;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;

		$this->assertEquals(array(2,4,1), $element->items);
	}

	public function testAfterSave_HasMany_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = true;

		$element->attributes = array('items' => array(
			array('item_id' => 2),
			array('item_id' => 1),
			array('item_id' => 4),
		));

		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->item_id);
		$this->assertEquals(1, $element->items[0]->display_order);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[1]);
		$this->assertEquals(1, $element->items[1]->item_id);
		$this->assertEquals(2, $element->items[1]->display_order);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[2]);
		$this->assertEquals(4, $element->items[2]->item_id);
		$this->assertEquals(3, $element->items[2]->display_order);

		$element->auto_update_relations = true;
		$element->attributes = array('items' => array(
			array('item_id' => 2),
			array('item_id' => 4),
		));

		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertCount(2, $element->items);

		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->item_id);
		$this->assertEquals(1, $element->items[0]->display_order);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[1]);
		$this->assertEquals(4, $element->items[1]->item_id);
		$this->assertEquals(2, $element->items[1]->display_order);
	}

	public function testAfterSave_HasMany_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = false;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertEquals(array(), $element->items);
	}

	public function testAfterSave_HasManyThrough_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasManyThrough;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->id);
		$this->assertEquals('item 2', $element->items[0]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[1]);
		$this->assertEquals(4, $element->items[1]->id);
		$this->assertEquals('item 4', $element->items[1]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[2]);
		$this->assertEquals(1, $element->items[2]->id);
		$this->assertEquals('item 1', $element->items[2]->name);

		$element->auto_update_relations = true;

		$data = array('items' => array(2));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(1, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->id);
		$this->assertEquals('item 2', $element->items[0]->name);
	}

	public function testAfterSave_HasManyThrough_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasManyThrough;
		$element->auto_update_relations = false;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(0, $element->items);
	}

	public function testAfterSave_ManyMany_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->id);
		$this->assertEquals('item 2', $element->items[0]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[1]);
		$this->assertEquals(4, $element->items[1]->id);
		$this->assertEquals('item 4', $element->items[1]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[2]);
		$this->assertEquals(1, $element->items[2]->id);
		$this->assertEquals('item 1', $element->items[2]->name);

		$element->auto_update_relations = true;

		$data = array('items' => array(4));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(1, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(4, $element->items[0]->id);
		$this->assertEquals('item 4', $element->items[0]->name);
	}

	public function testAfterSave_ManyMany_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = false;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(0, $element->items);
	}

	public function testAfterSave_HasMany_AutoUpdateRelations_SetNull()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = true;

		$element->attributes = array('items' => array(
			array('item_id' => 2),
			array('item_id' => 1),
			array('item_id' => 4),
		));

		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->item_id);
		$this->assertEquals(1, $element->items[0]->display_order);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[1]);
		$this->assertEquals(1, $element->items[1]->item_id);
		$this->assertEquals(2, $element->items[1]->display_order);
		$this->assertInstanceOf('RelationTest_Item_Assignment', $element->items[2]);
		$this->assertEquals(4, $element->items[2]->item_id);
		$this->assertEquals(3, $element->items[2]->display_order);

		$element->auto_update_relations = true;

		$element->attributes = array('items' => null);

		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertCount(0, $element->items);
	}

	public function testAfterSave_HasManyThrough_AutoUpdateRelations_SetNull()
	{
		$element = new RelationTest_Element_HasManyThrough;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->id);
		$this->assertEquals('item 2', $element->items[0]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[1]);
		$this->assertEquals(4, $element->items[1]->id);
		$this->assertEquals('item 4', $element->items[1]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[2]);
		$this->assertEquals(1, $element->items[2]->id);
		$this->assertEquals('item 1', $element->items[2]->name);

		$element->auto_update_relations = true;

		$data = array('items' => null);

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(0, $element->items);
	}

	public function testAfterSave_ManyMany_AutoUpdateRelations_SetNull()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$this->assertInstanceOf('RelationTest_Item', $element->items[0]);
		$this->assertEquals(2, $element->items[0]->id);
		$this->assertEquals('item 2', $element->items[0]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[1]);
		$this->assertEquals(4, $element->items[1]->id);
		$this->assertEquals('item 4', $element->items[1]->name);

		$this->assertInstanceOf('RelationTest_Item', $element->items[2]);
		$this->assertEquals(1, $element->items[2]->id);
		$this->assertEquals('item 1', $element->items[2]->name);

		$element->auto_update_relations = true;

		$data = array('items' => null);

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(0, $element->items);
	}

	public function testBeforeDelete_HasMany_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = true;

		$element->attributes = array('items' => array(
			array('item_id' => 2),
			array('item_id' => 1),
			array('item_id' => 4),
		));

		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$count = RelationTest_Item_Assignment::model()->count();

		$element->auto_update_relations = true;
		$element->delete();

		$this->assertEquals($count-3, RelationTest_Item_Assignment::model()->count());
	}

	public function testBeforeDelete_HasMany_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasMany;
		$element->auto_update_relations = true;

		$element->attributes = array('items' => array(
			array('item_id' => 2),
			array('item_id' => 1),
			array('item_id' => 4),
		));

		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$count = RelationTest_Item_Assignment::model()->count();

		$this->setExpectedException('CDbException');

		$element->auto_update_relations = false;
		$element->delete();
	}

	public function testBeforeDelete_HasManyThrough_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasManyThrough;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$count = RelationTest_Item_Assignment::model()->count();

		$element->auto_update_relations = true;
		$element->delete();

		$this->assertEquals($count-3, RelationTest_Item_Assignment::model()->count());
	}

	public function testBeforeDelete_HasManyThrough_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_HasManyThrough;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_HasManyThrough::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$count = RelationTest_Item_Assignment::model()->count();

		$this->setExpectedException('CDbException');

		$element->auto_update_relations = false;
		$element->delete();
	}

	public function testBeforeDelete_ManyMany_AutoUpdateRelations()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$count = RelationTest_Item_Assignment::model()->count();

		$element->auto_update_relations = true;
		$element->delete();

		$this->assertEquals($count-3, RelationTest_Item_Assignment::model()->count());
	}

	public function testBeforeDelete_ManyMany_NoAutoUpdateRelations()
	{
		$element = new RelationTest_Element_ManyMany;
		$element->auto_update_relations = true;

		$data = array('items' => array(2,4,1));

		$element->attributes = $data;
		$result = $element->save();

		$this->assertTrue($result);

		$element = RelationTest_Element_ManyMany::model()->findByPk($element->id);

		$this->assertCount(3, $element->items);

		$count = RelationTest_Item_Assignment::model()->count();

		$this->setExpectedException('CDbException');

		$element->auto_update_relations = false;
		$element->delete();
	}
}

class RelationTest_Item extends BaseActiveRecord
{
	public function rules()
	{
		return array(
			array('name', 'safe'),
		);
	}

	public function tableName()
	{
		return 'relationtest_item';
	}
}

class RelationTest_Element_HasMany extends BaseActiveRecord
{
	public $auto_update_relations;

	public function rules()
	{
		return array(
			array('items', 'safe'),
		);
	}

	public function tableName()
	{
		return 'relationtest_element';
	}

	public function relations()
	{
		return array(
			'items' => array(self::HAS_MANY, 'RelationTest_Item_Assignment', 'element_id', 'order' => 'display_order asc'),
		);
	}
}

class RelationTest_Element_HasManyThrough extends BaseActiveRecord
{
	public $auto_update_relations;

	public function rules()
	{
		return array(
			array('items', 'safe'),
		);
	}

	public function tableName()
	{
		return 'relationtest_element';
	}

	public function relations()
	{
		return array(
			'item_assignments' => array(self::HAS_MANY, 'RelationTest_Item_Assignment', 'element_id', 'order' => 'display_order asc'),
			'items' => array(self::HAS_MANY, 'RelationTest_Item', 'item_id', 'through' => 'item_assignments', 'order' => 'display_order asc'),
		);
	}
}

class RelationTest_Item_Assignment extends BaseActiveRecord
{
	public function rules()
	{
		return array(
			array('item_id, display_order', 'safe'),
		);
	}

	public function tableName()
	{
		return 'relationtest_item_assignment';
	}
}

class RelationTest_Element_ManyMany extends BaseActiveRecord
{
	public $auto_update_relations;

	public function rules()
	{
		return array(
			array('items', 'safe'),
		);
	}

	public function tableName()
	{
		return 'relationtest_element';
	}

	public function relations()
	{
		return array(
			'items' => array(self::MANY_MANY, 'RelationTest_Item', 'relationtest_item_assignment(element_id, item_id)', 'order' => 'display_order asc'),
		);
	}
}
