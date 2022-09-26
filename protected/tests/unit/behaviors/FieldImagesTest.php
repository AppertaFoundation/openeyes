<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class FieldImagesTest extends OEDbTestCase
{
    private $yiiMock;
    private $assetManagerMock;
    public function setUp(): void
    {
        $this->getFixtureManager()->dbConnection->createCommand(
            'create temporary table test_field_images_table (id int unsigned primary key, field varchar(63)) engine=innodb'
        )->execute();

        $this->getFixtureManager()->dbConnection->commandBuilder->createMultipleInsertCommand(
            'test_field_images_table',
            array(
                array('id' => 1, 'field' => 'foo'),
                array('id' => 2, 'field' => 'bar'),
                array('id' => 3, 'field' => 'baz'),
                array('id' => 4, 'field' => 'qux'),
            )
        )->execute();

        $this->assetManagerMock = $this->createMock('AssetManager');
        // Configure the stub.
        $this->assetManagerMock->expects($this->any())
            ->method('getPublishedPathOfAlias')
            ->will($this->returnValue(''));
    }

    public function tearDown(): void
    {
        $this->getFixtureManager()->dbConnection->createCommand('drop temporary table test_field_images_table')->execute();
    }

    /**
     * @covers FieldImages
     */
    public function testGetFieldImagesExceptionWhenFieldImagesMethodNotExist()
    {
        $this->expectException('FieldImagesException');
        $results = FiledImagesExceptionTest_TestClass::model()
            ->getFieldImages();
        $this->assertNull($results);
    }

    /**
     * @covers FieldImages
     */
    public function testGetFieldImagesNotPresent()
    {
        $yiiMock = new FieldImagesTest_FileHelper_NotPresent();

        $results = FiledImagesTest_TestClass::model()->getFieldImages($yiiMock, $this->assetManagerMock);
        $this->assertEmpty($results);
    }

    /**
     * @covers FieldImages
     */
    public function testGetFieldImages()
    {
        $yiiMock = new FieldImagesTest_FileHelper_Present();

        $results = FiledImagesTest_TestClass::model()->getFieldImages($yiiMock, $this->assetManagerMock);
        $this->assertInternalType('array', $results);
        $this->assertEquals(2, count($results));
        $this->assertTrue(isset($results['3']));
        $this->assertTrue(isset($results['5']));
        $this->assertEquals(DIRECTORY_SEPARATOR.'FiledImagesTest_TestClass-field-3.jpg', $results['3']);
        $this->assertEquals(DIRECTORY_SEPARATOR.'FiledImagesTest_TestClass-field-5.jpg', $results['5']);
    }
}

// CFileHelper subclass to allow mocking a static method with hardcoded data.
class FieldImagesTest_FileHelper_Present extends CFileHelper
{
    public static function findFiles($dir, $options = array())
    {
        return array('somepath/somefile.jpg', 'somepath/FiledImagesTest_TestClass-field-3.jpg', 'somepath/FiledImagesTest_TestClass-field-5.jpg');
    }
}

// CFileHelper subclass to allow mocking a static method with no data.
class FieldImagesTest_FileHelper_NotPresent extends CFileHelper
{
    public static function findFiles($dir, $options = array())
    {
        return array();
    }
}

class FiledImagesTest_TestClass extends BaseActiveRecord
{
    public function tableName()
    {
        return 'test_field_images_table';
    }

    public function behaviors()
    {
        return array('FieldImages' => 'FieldImages');
    }

    public function defaultScope()
    {
        return array('order' => 'id');
    }

    public function fieldImages()
    {
        return array('field');
    }
}

class FiledImagesExceptionTest_TestClass extends BaseActiveRecord
{
    public function tableName()
    {
        return 'test_field_images_table';
    }

    public function behaviors()
    {
        return array('FieldImages' => 'FieldImages');
    }

    public function defaultScope()
    {
        return array('order' => 'id');
    }
}
