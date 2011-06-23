<?php

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
