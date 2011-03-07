<?php

Yii::import('application.services.*');

class ClinicalServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => ':service',
		'specialties' => ':specialty',
		'serviceSpecialtyAssignment' => ':service_specialty_assignment',
		'firms' => ':firm',
		'eventTypes' => ':event_type',
		'elementTypes' => ':element_type',
		'possibleElementTypes' => ':possible_element_type',
		'siteElementTypes' => ':site_element_type'
	);

	public function testgetSiteElementTypeObjects()
	{
		$firm = Firm::Model()->findByPk(1);

		$siteElementTypes = ClinicalService::getSiteElementTypeObjects(
				1,
				$firm
		);

		$this->assertEquals(count($siteElementTypes), 1);
		$this->assertEquals(get_class($siteElementTypes[0]), 'SiteElementType');
	}
}