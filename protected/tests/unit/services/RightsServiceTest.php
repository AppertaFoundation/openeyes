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


class RightsServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'firms' => 'Firm',
		'services' => 'Service',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'userFirmRights' => 'UserFirmRights',
		'userServiceRights' => 'UserServiceRights'
	);

	protected $service;

	protected function setUp()
	{
		$this->service = new RightsService(1);
		parent::setUp();
	}

        public function testLoadRights_InvalidUser_ReturnsEmptyArray()
        {
                $fakeId = 12345;
		$this->service->userId = $fakeId;

		$rights = $this->getBlankRights();

                $returnedRights = $this->service->loadRights();

		$this->assertEquals($rights, $returnedRights);
        }

        public function testLoadRights_ValidUser_ReturnsValidArray()
        {
                $rights = $this->getPopulatedRights();

                $returnedRights = $this->service->loadRights();

                $this->assertEquals($rights, $returnedRights);
        }

	public function testSaveRights()
	{
		$_POST['Rights']['firm'][1] = 1;
		
		$_POST['Rights']['service'][1] = 1;

		$result = $this->service->saveRights();

		$this->assertTrue($result);
	}

	public function getPopulatedRights()
	{
		$rights = $this->getBlankRights();

                $rights[1]['checked'] = true;
                $rights[2]['checked'] = true;
                $rights[3]['checked'] = true;
                $rights[4]['checked'] = true;

                $rights[1]['firms'][1]['checked'] = true;
                $rights[2]['firms'][2]['checked'] = true;
                $rights[3]['firms'][3]['checked'] = true;

		return $rights;
	}

	public function getBlankRights()
	{
		$rights = array();

                foreach ($this->services as $result) {
                        $rights[$result['id']] = array(
                                'id' => $result['id'],
                                'name' => $result['name'],
                                'label' => 'Rights[service][' . $result['id'] . ']',
                                'firms' => array(),
                                'checked' => false
                        );
                }

                foreach ($this->firms as $result) {
                        $firm = Firm::model()->findByPk($result['id']);

                        $service_id = $firm->serviceSpecialtyAssignment->service_id;

                        $rights[$service_id]['firms'][$result['id']] = array(
                                'id' => $result['id'],
                                'name' => $result['name'],
                                'label' => 'Rights[firm][' . $result['id'] . ']',
                                'checked' => false
                        );
                }
	
		return $rights;
	}
}
