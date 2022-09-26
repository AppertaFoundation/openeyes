<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class BaseControllerTest.
 *
 * @group controllers
 */
class BaseControllerTest extends OEDbTestCase
{
    private $controller;

    public $fixtures = array(
        'user' => 'User',
    );

    /**
     * @throws ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = $this->getMockForAbstractClass('BaseController', array('BaseControllerTest'));
    }

    /**
     * @covers BaseController
     */
    public function testFetchModelSuccess()
    {
        $this->assertEquals(
            $this->user('user1'),
            $this->controller->fetchModel('User', $this->user('user1')->id)
        );
    }

    /**
     * @covers BaseController
     */
    public function testFetchModelNotFound()
    {
        $this->expectException('CHttpException');
        $this->expectExceptionMessage('User with PK \'foo\' not found');
        $this->controller->fetchModel('User', 'foo');
    }

    /**
     * @covers BaseController
     */
    public function testFetchModelEmptyPkDontCreate()
    {
        $this->expectException('CHttpException');
        $this->expectExceptionMessage('User with PK \'\' not found');
        $this->controller->fetchModel('User', null);
    }

    /**
     * @covers BaseController
     */
    public function testFetchModelEmptyPkCreate()
    {
        $user = $this->controller->fetchModel('User', null, true);
        $this->assertInstanceOf('User', $user);
        $this->assertTrue($user->isNewRecord);
    }
}
