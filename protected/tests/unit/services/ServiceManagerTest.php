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

namespace services;

use Exception;
use PHPUnit\Framework\MockObject\Generator;
use PHPUnit_Framework_TestCase;
use ReflectionException;

class ServiceManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $name
     * @param $extends
     * @throws ReflectionException
     */
    private static function generateMockClass($name, $extends)
    {
        $mock_class = (new Generator())->generate($extends, null, $name);
        eval($mock_class['code']);
    }

    private $manager;

    public function setUp()
    {
        parent::setUp();
        $this->manager = new ServiceManager();
        $this->manager->internal_services = array(
            'services\ServiceManagerTest_InternalResourceService',
            'services\ServiceManagerTest_InternalAmbiguousResourceService',
            'services\ServiceManagerTest_NonFhirResourceService',
        );
        $this->manager->init();
    }

    /**
     * @covers \services\ServiceManager
     */
    public function test__get_NonexistentService()
    {
        $this->expectException(Exception::class);
        $this->manager->Aardvark;
    }

    /**
     * @covers \services\ServiceManager
     */
    public function test__get_InternalService()
    {
        $this->assertInstanceOf('services\ServiceManagerTest_InternalResourceService', $this->manager->{'ServiceManagerTest_InternalResource'});
    }

    /**
     * @covers \services\ServiceManager
     * @throws Exception
     */
    public function testGetService_NonexistentService()
    {
        $this->assertNull($this->manager->getService('Aardvark'));
    }

    /**
     * @covers \services\ServiceManager
     * @throws Exception
     */
    public function testGetService_InternalService()
    {
        $this->assertInstanceOf('services\ServiceManagerTest_InternalResourceService', $this->manager->getService('ServiceManagerTest_InternalResource'));
    }

    /**
     * @covers \services\ServiceManager
     * @throws ProcessingNotSupported
     */
    public function testGetFhirService_NotFound()
    {
        $this->expectException(NotFound::class);
        $this->manager->getFhirService('Caterpillar', array());
    }

    /**
     * @covers \services\ServiceManager
     * @throws NotFound
     * @throws ProcessingNotSupported
     */
    public function testGetFhirService_Unambiguous()
    {
        $this->assertInstanceOf(
            'services\ServiceManagerTest_InternalResourceService',
            $this->manager->getFhirService('FhirResourceA', array())
        );
    }

    /**
     * @covers \services\ServiceManager
     * @throws NotFound
     */
    public function testGetFhirService_AmbiguousNoProfile()
    {
        $this->expectException(ProcessingNotSupported::class);
        $this->manager->getFhirService('FhirResourceB', array());
    }

    /**
     * @covers \services\ServiceManager
     * @throws NotFound
     * @throws ProcessingNotSupported
     */
    public function testGetFhirService_AmbiguousValidProfile()
    {
        $this->assertInstanceOf(
            'services\ServiceManagerTest_InternalAmbiguousResourceService',
            $this->manager->getFhirService('FhirResourceB', array('bar'))
        );
    }

    /**
     * @covers \services\ServiceManager
     * @throws NotFound
     * @throws ProcessingNotSupported
     */
    public function testGetFhirService_AmbiguousInvalidProfile()
    {
        $this->assertNull($this->manager->getFhirService('FhirResourceB', array('baz')));
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testFhirIdToReference_NoPrefix()
    {
        $ref = $this->manager->fhirIdToReference('FhirResourceA', 42);
        $this->assertEquals('ServiceManagerTest_InternalResource', $ref->getServiceName());
        $this->assertEquals(42, $ref->getId());
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testFhirIdToReference_Prefix()
    {
        $ref = $this->manager->fhirIdToReference('FhirResourceB', 'foo-43');
        $this->assertEquals('ServiceManagerTest_InternalAmbiguousResource', $ref->getServiceName());
        $this->assertEquals(43, $ref->getId());
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testFhirIdToReference_UnknownFhirType()
    {
        $this->assertNull($this->manager->fhirIdToReference('Foo', 44));
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testFhirIdToReference_MissingPrefix()
    {
        $this->assertNull($this->manager->fhirIdToReference('FhirResourceB', 45));
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testFhirIdToReference_InvalidPrefix()
    {
        $this->assertNull($this->manager->fhirIdToReference('FhirResourceB', 'baz-46'));
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testServiceAndIdToFhirUrl_UnknownService()
    {
        $this->expectException(Exception::class);
        $this->manager->serviceAndIdToFhirUrl('Walrus', 47);
    }

    /**
     * @covers \services\ServiceManager
     */
    public function testServiceAndIdToFhirUrl_NonFhirService()
    {
        $this->expectException(Exception::class);
        $this->manager->serviceAndIdToFhirUrl('ServiceManagerTest_NonFhirResource', 48);
    }

    /**
     * @covers \services\ServiceManager
     * @throws Exception
     */
    public function testServiceAndIdToFhirUrl_NoPrefix()
    {
        $this->assertEquals('FhirResourceA/49', $this->manager->serviceAndIdToFhirUrl('ServiceManagerTest_InternalResource', 49));
    }

    /**
     * @covers \services\ServiceManager
     * @throws Exception
     */
    public function testServiceAndIdToFhirUrl_Prefix()
    {
        $this->assertEquals('FhirResourceB/foo-50', $this->manager->serviceAndIdToFhirUrl('ServiceManagerTest_InternalAmbiguousResource', 50));
    }
}

class ServiceManagerTest_InternalResourceService extends InternalService
{
}

class ServiceManagerTest_InternalResource extends Resource
{
    public static function getFhirType()
    {
        return 'FhirResourceA';
    }
}

class ServiceManagerTest_InternalAmbiguousResourceService extends InternalService
{
}

class ServiceManagerTest_InternalAmbiguousResource extends Resource
{
    public static function getFhirType()
    {
        return 'FhirResourceB';
    }

    public static function getFhirPrefix()
    {
        return 'foo';
    }

    public static function getOeFhirProfile()
    {
        return 'bar';
    }
}

class ServiceManagerTest_NonFhirResourceService extends InternalService
{
}

class ServiceManagerTest_NonFhirResource extends Resource
{
    public static function getFhirType()
    {
    }
}
