<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace services;

class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{
	static private function generateMockClass($name, $extends)
	{
		$mock_class = \PHPUnit_Framework_MockObject_Generator::generate($extends, null, $name);
		eval($mock_class['code']);
	}

	private $manager;

	public function setUp()
	{
		$this->manager = new ServiceManager;
		$this->manager->internal_services = array(
			'services\ServiceManagerTest_InternalResourceService',
			'services\ServiceManagerTest_InternalAmbiguousResourceService',
			'services\ServiceManagerTest_NonFhirResourceService',
		);
		$this->manager->init();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Service 'Aardvark' not defined
	 */
	public function test__get_NonexistentService()
	{
		$this->manager->Aardvark;
	}

	public function test__get_InternalService()
	{
		$this->assertInstanceOf('services\ServiceManagerTest_InternalResourceService', $this->manager->{'ServiceManagerTest_InternalResource'});
	}

	public function testGetService_NonexistentService()
	{
		$this->assertNull($this->manager->getService('Aardvark'));
	}

	public function testGetService_InternalService()
	{
		$this->assertInstanceOf('services\ServiceManagerTest_InternalResourceService', $this->manager->getService('ServiceManagerTest_InternalResource'));
	}

	/**
	 * @expectedException services\NotFound
	 * @expectedExceptionMessage Unsupported resource type: 'Caterpillar'
	 */
	public function testGetFhirService_NotFound()
	{
		$this->manager->getFhirService('Caterpillar', array());
	}

	public function testGetFhirService_Unambiguous()
	{
		$this->assertInstanceOf('services\ServiceManagerTest_InternalResourceService', $this->manager->getFhirService('FhirResourceA', array()));
	}

	/**
	 * @expectedException services\ProcessingNotSupported
	 * @expectedExceptionMessage A profile must be specified for resources of type 'FhirResourceB'
	 */
	public function testGetFhirService_AmbiguousNoProfile()
	{
		$this->manager->getFhirService('FhirResourceB', array());
	}

	public function testGetFhirService_AmbiguousValidProfile()
	{
		$this->assertInstanceOf(
			'services\ServiceManagerTest_InternalAmbiguousResourceService',
			$this->manager->getFhirService('FhirResourceB', array('bar'))
		);
	}

	public function testGetFhirService_AmbiguousInvalidProfile()
	{
		$this->assertNull($this->manager->getFhirService('FhirResourceB', array('baz')));
	}

	public function testFhirIdToReference_NoPrefix()
	{
		$ref = $this->manager->fhirIdToReference('FhirResourceA', 42);
		$this->assertEquals('ServiceManagerTest_InternalResource', $ref->getServiceName());
		$this->assertEquals(42, $ref->getId());
	}

	public function testFhirIdToReference_Prefix()
	{
		$ref = $this->manager->fhirIdToReference('FhirResourceB', 'foo-43');
		$this->assertEquals('ServiceManagerTest_InternalAmbiguousResource', $ref->getServiceName());
		$this->assertEquals(43, $ref->getId());
	}

	public function testFhirIdToReference_UnknownFhirType()
	{
		$this->assertNull($this->manager->fhirIdToReference('Foo', 44));
	}

	public function testFhirIdToReference_MissingPrefix()
	{
		$this->assertNull($this->manager->fhirIdToReference('FhirResourceB', 45));
	}

	public function testFhirIdToReference_InvalidPrefix()
	{
		$this->assertNull($this->manager->fhirIdToReference('FhirResourceB', 'baz-46'));
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Unknown service: 'Walrus'
	 */
	public function testServiceAndIdToFhirUrl_UnknownService()
	{
		$this->manager->serviceAndIdToFhirUrl('Walrus', 47);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage No FHIR resource type configured for service 'ServiceManagerTest_NonFhirResource'
	 */
	public function testServiceAndIdToFhirUrl_NonFhirService()
	{
		$this->manager->serviceAndIdToFhirUrl('ServiceManagerTest_NonFhirResource', 48);
	}

	public function testServiceAndIdToFhirUrl_NoPrefix()
	{
		$this->assertEquals('FhirResourceA/49', $this->manager->serviceAndIdToFhirUrl('ServiceManagerTest_InternalResource', 49));
	}

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
	static public function getFhirType()
	{
		return 'FhirResourceA';
	}
}

class ServiceManagerTest_InternalAmbiguousResourceService extends InternalService
{
}

class ServiceManagerTest_InternalAmbiguousResource extends Resource
{
	static public function getFhirType()
	{
		return 'FhirResourceB';
	}

	static public function getFhirPrefix()
	{
		return 'foo';
	}

	static public function getOeFhirProfile()
	{
		return 'bar';
	}
}

class ServiceManagerTest_NonFhirResourceService extends InternalService
{
}

class ServiceManagerTest_NonFhirResource extends Resource
{
	static public function getFhirType()
	{
		return null;
	}
}
