<?php
/**
 *  OpenEyes.
 *
 *  (C) Moorfields  Eye Hospital    NHS Foundation  Trust,  2008-2011
 *  (C) OpenEyes    Foundation, 2011-2013
 *  This    file    is  part    of  OpenEyes.
 *  OpenEyes    is  free    software:   you can redistribute    it  and/or  modify  it  under   the terms   of  the GNU General Public  License as  published   by  the Free    Software    Foundation, either  version 3   of  the License,    or  (at your    option) any later   version.
 *  OpenEyes    is  distributed in  the hope    that    it  will    be  useful, but WITHOUT ANY WARRANTY;   without even    the implied warranty    of  MERCHANTABILITY or  FITNESS FOR A   PARTICULAR  PURPOSE.    See the GNU General Public  License for more    details.
 *  You should  have    received    a   copy    of  the GNU General Public  License along   with    OpenEyes    in  a   file    titled  COPYING.    If  not,    see <http://www.gnu.org/licenses/>.
 *
 *  @link   http://www.openeyes.org.uk
 *
 *  @author OpenEyes    <info@openeyes.org.uk>
 *  @copyright  Copyright   (c) 2008-2011,  Moorfields  Eye Hospital    NHS Foundation  Trust
 *  @copyright  Copyright   (c) 2011-2013,  OpenEyes    Foundation
 *  @license    http://www.gnu.org/licenses/agpl-3.0.html   The GNU General Public  License V3.0
 */
class SplitEventTypeElementTest extends CDbTestCase
{
    /**
     * @var SplitEventTypeElement
     */
    protected $object;

    public $fixtures = array(
        'eye' => 'Eye',
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->db->createCommand()->createTable('split_element_test', array('id' => 'pk',
            'title' => 'string NOT NULL',
            'eye_id' => 'int(10) unsigned NOT NULL',
            ' CONSTRAINT `split_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)', ));
        parent::setUpBeforeClass();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->object = new SplitEventTypeElementMock();
    }

    /**
     * @covers SplitEventTypeElement
     */
    public function testHasLeft()
    {
        $this->object->eye = $this->eye('eyeLeft');
        $this->assertTrue($this->object->hasLeft());
    }

    /**
     * @covers SplitEventTypeElement
     */
    public function testHasRight()
    {
        $this->object->eye = $this->eye('eyeRight');
        $this->assertTrue($this->object->hasRight());
    }

    /**
     * @covers SplitEventTypeElement
     */
    public function testSetDefaultOptions()
    {
        $this->object->eye = $this->eye('eyeBoth');
        $this->object->setDefaultOptions();
        //var_dump($this->object);
        $this->assertEquals('NoName', $this->object->left_name);
        $this->assertEquals('NoName', $this->object->right_name);
        $this->assertEquals(0, $this->object->left_number);
        $this->assertEquals(0, $this->object->right_number);
    }

    /**
     * @covers SplitEventTypeElement
     *
     */
    public function testSetUpdateOptions()
    {
        $this->object->eye = $this->eye('eyeLeft');
        $this->object->setUpdateOptions();
        $this->assertEquals('NoName', $this->object->right_name);
        $this->assertEquals(0, $this->object->right_number);
    }

    public static function tearDownAfterClass()
    {
        Yii::app()->db->createCommand()->dropTable('split_element_test');
        parent::tearDownAfterClass();
    }
}

class SplitEventTypeElementMock extends SplitEventTypeElement
{
    public $eye, $left_name, $right_name, $left_number, $right_number;

    public function tableName()
    {
        return 'split_element_test';
    }

    public function sidedFields()
    {
        return array('name', 'number');
    }
    public function sidedDefaults()
    {
        return array('name' => 'NoName', 'number' => 0);
    }
}
