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
class PatientTest extends CDbTestCase
{
	public $model;
	public $fixtures = array(
		'patients' => 'Patient',
		'addresses' => 'Address',
		'Disorder',
		'SecondaryDiagnosis',
		'Specialty',
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('first_name' => 'Katherine', 'last_name' => 'muller', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0,), 1, array('patient3')),
			array(array('last_name' => 'jones', 'first_name' => 'muller', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0,), 1, array('patient2')), /* case insensitivity test */
			array(array('hos_num' => 12345, 'last_name' => 'test lastname', 'first_name' => 'test firstname', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0,), 1, array('patient1')),
			array(array('first_name' => 'John', 'last_name' => 'jones', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0,), 2, array('patient1', 'patient2')),
		);
	}

	// 'first_name', 'last_name', 'dob', 'title', 'city', 'postcode', 'telephone', 'mobile', 'email', 'address1', 'address2'
	public function dataProvider_Pseudo()
	{
		return array(
			array(array('hos_num' => 5550101, 'first_name' => 'Rod', 'last_name' => 'Flange', 'dob' => '1979-09-08', 'title' => 'MR', 'primary_phone' => '0208 1111111', 'address_id' => 1)),
			array(array('hos_num' => 5550101, 'first_name' => 'Jane', 'last_name' => 'Hinge', 'dob' => '2000-01-02', 'title' => 'Ms.', 'primary_phone' => '0207 1111111', 'address_id' => 2)),
			array(array('hos_num' => 5550101, 'first_name' => 'Freddy', 'last_name' => 'Globular', 'dob' => '1943-04-05', 'title' => 'WC', 'primary_phone' => '0845 11111111', 'address_id' => 3)),
		);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->model = new Patient;
	}

	/**
	 * @covers Patient::model
	 * @todo   Implement testModel().
	 */
	public function testModel()
	{
		$this->assertEquals('Patient', get_class(Patient::model()), 'Class name should match model.');
	}

	/**
	 * @covers Patient::noPas
	 * @todo   Implement testNoPas().
	 */
	public function testNoPas()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::attributeLabels
	 */
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'pas_key' => 'PAS Key',
			'dob' => 'Date of Birth',
			'date_of_death' => 'Date of Death',
			'gender' => 'Gender',
			'ethnic_group_id' => 'Ethnic Group',
			'hos_num' => 'Hospital Number',
			'nhs_num' => 'NHS Number',
		);

		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @covers Patient::search_nr
	 * @todo   Implement testSearch_nr().
	 */
	public function testSearch_nr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{

		$patient = new Patient;
		$patient->setAttributes($searchTerms);
		$results = $patient->search($searchTerms);

		$data = $results->getData();


		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->patients($key);
			}
		}
		if (isset($data[0])) {
			$this->assertEquals($expectedResults, array('0' => $data[0]->getAttributes()));
		}
	}

	/**
	 * @covers Patient::beforeSave
	 * @todo   Implement testBeforeSave().
	 */
	public function testBeforeSave()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getOrderedEpisodes
	 * @todo   Implement testGetOrderedEpisodes().
	 */
	public function testGetOrderedEpisodes()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAge
	 * @todo   Implement testGetAge().
	 */
	public function testGetAge()
	{
		Yii::app()->params['pseudonymise_patient_details'] = false;

		$attributes = array(
			'hos_num' => 5550101,
			'first_name' => 'Rod',
			'last_name' => 'Flange',
			'dob' => '1979-09-08',
			'title' => 'MR',
			'primary_phone' => '0208 1111111',
			'address_id' => 1);

		$patient = new Patient;
		$patient->setAttributes($attributes);
		$patient->save();

		$age = date('Y') - 1979;
		if (date('md') < '0908') {
			$age--; // have not had a birthday yet
		}

		$this->assertEquals($age, $patient->getAge());
	}

	public function testRandomData_ParamSetOff_ReturnsFalse()
	{
		Yii::app()->params['pseudonymise_patient_details'] = false;

		$attributes = array(
			'hos_num' => 5550101,
			'first_name' => 'Rod',
			'last_name' => 'Flange',
			'dob' => '1979-09-08',
			'title' => 'MR',
			'primary_phone' => '0208 1111111',
			'address_id' => 1);

		$patient = new Patient;
		$patient->setAttributes($attributes);
		$patient->save();


		$this->assertEquals($attributes['hos_num'], $patient->getAttribute('hos_num'), 'Data should not have changed.');
	}

	/**
	 * @covers Patient::isChild
	 * @todo   Implement testIsChild().
	 */
	public function testIsChild()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::hasAllergy
	 * @todo   Implement testHasAllergy().
	 */
	public function testHasAllergy()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::isDeceased
	 * @todo   Implement testIsDeceased().
	 */
	public function testIsDeceased()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAddressName
	 * @todo   Implement testGetAddressName().
	 */
	public function testGetAddressName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getSalutationName
	 * @todo   Implement testGetSalutationName().
	 */
	public function testGetSalutationName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getFullName
	 * @todo   Implement testGetFullName().
	 */
	public function testGetFullName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getDisplayName
	 * @todo   Implement testGetDisplayName().
	 */
	public function testGetDisplayName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getEpisodeForCurrentSubspecialty
	 * @todo   Implement testGetEpisodeForCurrentSubspecialty().
	 */
	public function testGetEpisodeForCurrentSubspecialty()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getOphInfo
	 * @todo   Implement testGetOphInfo().
	 */
	public function testGetOphInfo()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getSub
	 * @todo   Implement testGetSub().
	 */
	public function testGetSub()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPro
	 * @todo   Implement testGetPro().
	 */
	public function testGetPro()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getEpd
	 * @todo   Implement testGetEpd().
	 */
	public function testGetEpd()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getEps
	 * @todo   Implement testGetEps().
	 */
	public function testGetEps()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getGenderString
	 * @todo   Implement testGetGenderString().
	 */
	public function testGetGenderString()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getEthnicGroupString
	 * @todo   Implement testGetEthnicGroupString().
	 */
	public function testGetEthnicGroupString()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getObj
	 * @todo   Implement testGetObj().
	 */
	public function testGetObj()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getOpl
	 * @todo   Implement testGetOpl().
	 */
	public function testGetOpl()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getOpr
	 * @todo   Implement testGetOpr().
	 */
	public function testGetOpr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getOps
	 * @todo   Implement testGetOps().
	 */
	public function testGetOps()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPos
	 * @todo   Implement testGetPos().
	 */
	public function testGetPos()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getTitle
	 * @todo   Implement testGetTitle().
	 */
	public function testGetTitle()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getFirst_name
	 * @todo   Implement testGetFirst_name().
	 */
	public function testGetFirst_name()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getLast_name
	 * @todo   Implement testGetLast_name().
	 */
	public function testGetLast_name()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getNick_name
	 * @todo   Implement testGetNick_name().
	 */
	public function testGetNick_name()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPrimary_phone
	 * @todo   Implement testGetPrimary_phone().
	 */
	public function testGetPrimary_phone()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPre
	 * @todo   Implement testGetPre().
	 */
	public function testGetPre()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getSummaryAddress
	 * @todo   Implement testGetSummaryAddress().
	 */
	public function testGetSummaryAddress()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAllergiesString
	 * @todo   Implement testGetAllergiesString().
	 */
	public function testGetAllergiesString()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::addAllergy
	 * @todo   Implement testAddAllergy().
	 */
	public function testAddAllergy()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::removeAllergy
	 * @todo   Implement testRemoveAllergy().
	 */
	public function testRemoveAllergy()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::assignAllergies
	 * @todo   Implement testAssignAllergies().
	 */
	public function testAssignAllergies()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAdm
	 * @todo   Implement testGetAdm().
	 */
	public function testGetAdm()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getSystemicDiagnoses
	 * @todo   Implement testGetSystemicDiagnoses().
	 */
	public function testGetSystemicDiagnoses()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getOphthalmicDiagnoses
	 * @todo   Implement testGetOphthalmicDiagnoses().
	 */
	public function testGetOphthalmicDiagnoses()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getSpecialtyCodes
	 * @todo   Implement testGetSpecialtyCodes().
	 */
	public function testGetSpecialtyCodes()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::addDiagnosis
	 * @todo   Implement testAddDiagnosis().
	 */
	public function testAddDiagnosis()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::removeDiagnosis
	 * @todo   Implement testRemoveDiagnosis().
	 */
	public function testRemoveDiagnosis()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::editOphInfo
	 */
	public function testEditOphInfo_Success()
	{
		$cvi_status = ComponentStubGenerator::generate('PatientOphInfoCviStatus', array('id' => 1));
		$this->assertTrue($this->patients('patient1')->editOphInfo($cvi_status, '2000-01-01'));
	}

	public function testEditOphInfo_ValidationFailure()
	{
		$cvi_status = ComponentStubGenerator::generate('PatientOphInfoCviStatus', array('id' => 1));
		$errors = $this->patients('patient1')->editOphInfo($cvi_status, '2000-42-35');
		$this->assertEquals(array('cvi_status_date' => array('This is not a valid date')), $errors);
	}

	/**
	 * @covers Patient::getHpc
	 * @todo   Implement testGetHpc().
	 */
	public function testGetHpc()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getIpb
	 * @todo   Implement testGetIpb().
	 */
	public function testGetIpb()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getIpl
	 * @todo   Implement testGetIpl().
	 */
	public function testGetIpl()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getIpp
	 * @todo   Implement testGetIpp().
	 */
	public function testGetIpp()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getIpr
	 * @todo   Implement testGetIpr().
	 */
	public function testGetIpr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAsb
	 * @todo   Implement testGetAsb().
	 */
	public function testGetAsb()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAsl
	 * @todo   Implement testGetAsl().
	 */
	public function testGetAsl()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAsp
	 * @todo   Implement testGetAsp().
	 */
	public function testGetAsp()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAsr
	 * @todo   Implement testGetAsr().
	 */
	public function testGetAsr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPsb
	 * @todo   Implement testGetPsb().
	 */
	public function testGetPsb()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPsl
	 * @todo   Implement testGetPsl().
	 */
	public function testGetPsl()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPsp
	 * @todo   Implement testGetPsp().
	 */
	public function testGetPsp()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPsr
	 * @todo   Implement testGetPsr().
	 */
	public function testGetPsr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getVbb
	 * @todo   Implement testGetVbb().
	 */
	public function testGetVbb()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getVbl
	 * @todo   Implement testGetVbl().
	 */
	public function testGetVbl()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getVbp
	 * @todo   Implement testGetVbp().
	 */
	public function testGetVbp()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getVbr
	 * @todo   Implement testGetVbr().
	 */
	public function testGetVbr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getCon
	 * @todo   Implement testGetCon().
	 */
	public function testGetCon()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getMan
	 * @todo   Implement testGetMan().
	 */
	public function testGetMan()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getContactAddress
	 * @todo   Implement testGetContactAddress().
	 */
	public function testGetContactAddress()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getNhsnum
	 * @todo   Implement testGetNhsnum().
	 */
	public function testGetNhsnum()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::hasLegacyLetters
	 * @todo   Implement testHasLegacyLetters().
	 */
	public function testHasLegacyLetters()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAdd
	 * @todo   Implement testGetAdd().
	 */
	public function testGetAdd()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getAdl
	 * @todo   Implement testGetAdl().
	 */
	public function testGetAdl()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::audit
	 * @todo   Implement testAudit().
	 */
	public function testAudit()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getChildPrefix
	 * @todo   Implement testGetChildPrefix().
	 */
	public function testGetChildPrefix()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getSdl
	 */
	public function testGetSdl()
	{
		$this->assertEquals('left myopia, right retinal lattice degeneration and bilateral posterior vitreous detachment', $this->patients('patient2')->getSdl());
	}

	public function testGetSyd()
	{
		$this->assertEquals('diabetes mellitus type 1 and essential hypertension', $this->patients('patient2')->getSyd());
	}

	/**
	 * @covers Patient::addPreviousOperation
	 * @todo   Implement testAddPreviousOperation().
	 */
	public function testAddPreviousOperation()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::addFamilyHistory
	 * @todo   Implement testAddFamilyHistory().
	 */
	public function testAddFamilyHistory()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::currentContactLocationIDS
	 * @todo   Implement testCurrentContactLocationIDS().
	 */
	public function testCurrentContactLocationIDS()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getPrefix
	 * @todo   Implement testGetPrefix().
	 */
	public function testGetPrefix()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getEpc
	 * @todo   Implement testGetEpc().
	 */
	public function testGetEpc()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::getEpv
	 * @todo   Implement testGetEpv().
	 */
	public function testGetEpv()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::updateMedication
	 * @todo   Implement testUpdateMedication().
	 */
	public function testUpdateMedication()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Patient::addMedication
	 * @todo   Implement testAddMedication().
	 */
	public function testAddMedication()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
