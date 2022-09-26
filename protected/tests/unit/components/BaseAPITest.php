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
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
require_once dirname(__FILE__).'/NamespacedBaseAPI.php';

class BaseAPITest extends OEDbTestCase
{
    public $fixtures = array(
        'event_types' => 'EventType',
        'events' => 'Event',
        'episodes' => 'Episode',
        'patients' => 'Patient'
    );

    public function setUp(): void
    {
        parent::setUp();
        $this->generateMockTableAndData('test_element_mock_table');
    }
    public function tearDown(): void
    {
        $this->destroyMockTableAndData('test_element_mock_table');
        parent::tearDown();
    }

    /**
     * @covers BaseAPI
     * @throws ReflectionException
     */
    public function testgetModuleClass_unnamespaced()
    {
        $test = $this->getMockBuilder('BaseAPI')
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->setMockClassName('TestModule_API')
                ->getMock();

        $r = new ReflectionClass('BaseAPI');
        $m = $r->getMethod('getModuleClass');
        $m->setAccessible(true);

        $this->assertEquals('TestModule', $m->invoke($test));
    }

    /**
     * @covers BaseAPI
     * @throws ReflectionException
     */
    public function testgetModuleClass_namespaced()
    {
        $test = new TestModule_API();
        $r = new ReflectionClass($test);
        $m = $r->getMethod('getModuleClass');
        $m->setAccessible(true);

        $this->assertEquals('TestModule', $m->invoke($test));
    }

    /**
     * @covers BaseAPI
     * @throws ReflectionException
     */
    public function testGetEventType()
    {
        $test = $this->getMockBuilder('BaseAPI')
                ->disableOriginalConstructor()
                ->setMethods(array('getModuleClass'))
                ->getMock();

        $test->expects($this->once())
            ->method('getModuleClass')
            ->will($this->returnValue($this->event_types('event_type1')->class_name));

        $r = new ReflectionClass('BaseAPI');
        $m = $r->getMethod('getEventType');
        $m->setAccessible(true);

        $this->assertEquals($this->event_types('event_type1'), $m->invoke($test));
    }

    /**
     * @covers BaseAPI
     */
    public function testGetMostRecentEventInEpisode()
    {
        $test = $this->getBaseApiMock();

        $event = $this->events('event2');
        $expectedEvent = $this->events('event3');
        $resultEvent = $test->getMostRecentEventInEpisode($event->episode_id, $event->event_type_id);
        $this->assertEquals($expectedEvent->info, $resultEvent->info);
    }

    /**
     * @covers BaseAPI
     */
    public function testGetMostRecentElementInEpisode()
     {
        $test = $this->getBaseApiMock();
        $event = $this->events('event2');

        $resultElement = $test->getMostRecentElementInEpisode($event->episode_id, $event->event_type_id, ElementMock_TestClass::model());
        $this->assertEquals(3, $resultElement->id);
    }

    /**
     * @covers BaseAPI
     */
    public function testGetLatestElement()
    {
        $patient = $this->patients('patient1');

        $found_element = $this->getBaseApiMock()
            ->getLatestElement(ElementMock_TestClass::class, $patient);
        $this->assertEquals(3, $found_element->id);
    }

    /**
     * @covers BaseAPI
     * @group strabismus
     */
    public function testGetLatestElementBefore()
    {
        $found_element = $this->getBaseApiMock()
            ->getLatestElement(
                ElementMock_TestClass::class,
                $this->patients('patient1'),
                false,
                date('Y-m-d 00:00:00', strtotime('-1 days'))
            );
        $this->assertEquals(2, $found_element->id);
    }

    /**
     * @covers BaseAPI
     * @group strabismus
     */
    public function testGetLatestElementAfter()
    {
        $found_element = $this->getBaseApiMock()
            ->getLatestElement(
                ElementMock_TestClass::class,
                $this->patients('patient1'),
                false,
                null,
                date('Y-m-d 00:00:00', strtotime('-1 days'))
            );
        $this->assertEquals(3, $found_element->id);
    }

    /**
     * @covers BaseAPI
     * @group strabismus
     */
    public function testGetLatestElementAfterAndBefore()
    {
        $found_element = $this->getBaseApiMock()
            ->getLatestElement(
                ElementMock_TestClass::class,
                $this->patients('patient1'),
                false,
                date('Y-m-d 00:00:00', strtotime('-1 days')),
                date('Y-m-d 00:00:00', strtotime('-3 days'))
            );
        $this->assertEquals(2, $found_element->id);
    }

    /**
     * @covers BaseAPI
     * @group strabismus
     */
    public function testGetLatestElementAfterReturnNull()
    {
        $found_element = $this->getBaseApiMock()
            ->getLatestElement(
                ElementMock_TestClass::class,
                $this->patients('patient1'),
                false,
                date('Y-m-d 00:00:00', strtotime('-1 days')),
                date('Y-m-d 00:00:00', strtotime('-2 days'))
            );
        $this->assertNull($found_element);
    }

    /**
     * Simple abstraction for base api method calls
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getBaseApiMock()
    {
        return $this->getMockBuilder('BaseAPI')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->setMockClassName('TestModule_API')
            ->getMock();
    }

    private function generateMockTableAndData($table_name)
    {
        $this->getFixtureManager()->dbConnection->createCommand(
            "create temporary table $table_name (id int unsigned primary key, name varchar(63), event_id int unsigned not null default 1) engine=innodb"
        )->execute();

        $this->getFixtureManager()->dbConnection->commandBuilder->createMultipleInsertCommand(
            $table_name,
            array(
                array('id' => 1, 'name' => 'foo', 'event_id' => 1),
                array('id' => 2, 'name' => 'bar', 'event_id' => 2),
                array('id' => 3, 'name' => 'baz', 'event_id' => 3),
                array('id' => 4, 'name' => 'qux', 'event_id' => 4),
            )
        )->execute();
    }

    private function destroyMockTableAndData($table_name)
    {
        $this->getFixtureManager()->dbConnection->createCommand("drop temporary table $table_name")->execute();
    }
}

class ElementMock_TestClass extends BaseEventTypeElement
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'test_element_mock_table';
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
        );
    }
}
