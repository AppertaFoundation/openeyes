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

/**
 * Class SiteControllerTest
 * @group controllers
 */
class SiteControllerTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'firms' => 'Firm',
		'sites' => 'Site'
	);

	protected $controller;

	protected function setUp()
	{
		$this->controller = new SiteController('SiteController');
		parent::setUp();
	}

	public function test_MarkIncomplete()
	{
		$this->markTestIncomplete('Tests not implemented yet');
	}
	/*
	public function testStoreData_IndexAction_ValidFirmId_StoresValidData()
	{
		$firmId = $this->firms['firm1']['id'];
		$action = 'index';

		$mockController = $this->getMock('SiteController',
			array('getAction'),	array('SiteController'));
		$mockAction = $this->getMock('CAction', array('getId'), array($mockController, 'index'));
		$mockAction->expects($this->once())
			->method('getId')
			->will($this->returnValue($action));
		$mockController->expects($this->once())
			->method('getAction')
			->will($this->returnValue($mockAction));

		$mockController->selectedFirmId = $firmId;

		$this->assertNull(Yii::app()->session['selected_firm_id']);

		$firms = array();
		foreach ($this->firms as $name => $values) {
			$firms[$values['id']] = $values['name'] .
						' (' . $values['pas_code'] . ') (' .
						$this->firms($name)->serviceSpecialtyAssignment->service->name .')';
		}

		Yii::app()->session['firms'] = $firms;

		$_POST['selected_firm_id'] = $firmId;

		$mockController->storeData();
		$this->assertEquals($firmId, Yii::app()->session['selected_firm_id'], 'Firm Id should now be in the session.');
	}

	public function testStoreData_OtherAction_ValidFirmId_StoresNothing()
	{
		$firmId = $this->firms['firm1']['id'];
		$action = 'login';

		$mockController = $this->getMock('SiteController',
			array('getAction'), array('SiteController'));
		$mockAction = $this->getMock('CAction', array('getId'), array($mockController, 'index'));
		$mockAction->expects($this->once())
			->method('getId')
			->will($this->returnValue($action));
		$mockController->expects($this->once())
			->method('getAction')
			->will($this->returnValue($mockAction));

		$mockController->selectedFirmId = $firmId;

		$this->assertNull(Yii::app()->session['selected_firm_id']);

		$firms = array();
		foreach ($this->firms as $name => $values) {
			$firms[$values['id']] = $values['name'] .
						' (' . $values['pas_code'] . ') (' .
						$this->firms($name)->serviceSpecialtyAssignment->service->name .')';
		}

		Yii::app()->session['firms'] = $firms;

		$_POST['selected_firm_id'] = $firmId;

		$mockController->storeData();
		$this->assertNull(Yii::app()->session['selected_firm_id'], 'Firm Id should not be in the session.');
	}

	public function testStoreData_IndexAction_MissingFirmId_StoresNothing()
	{
		$firmId = $this->firms['firm1']['id'];
		$action = 'index';

		$mockController = $this->getMock('SiteController',
			array('getAction'),	array('SiteController'));
		$mockAction = $this->getMock('CAction', array('getId'), array($mockController, 'index'));
		$mockAction->expects($this->once())
			->method('getId')
			->will($this->returnValue($action));
		$mockController->expects($this->once())
			->method('getAction')
			->will($this->returnValue($mockAction));

		$mockController->selectedFirmId = $firmId;

		$this->assertNull(Yii::app()->session['selected_firm_id']);

		$firms = array();
		foreach ($this->firms as $name => $values) {
			$firms[$values['id']] = $values['name'] .
						' (' . $values['pas_code'] . ') (' .
						$this->firms($name)->serviceSpecialtyAssignment->service->name .')';
		}

		Yii::app()->session['firms'] = $firms;

		$mockController->storeData();
		$this->assertNull(Yii::app()->session['selected_firm_id'], 'Firm Id should not be in the session.');
	}

	public function testActions_ReturnsCorrectData()
	{
		$expected = array(
			'page'=>array(
				'class'=>'CViewAction',
			),
		);

		$this->assertEquals($expected, $this->controller->actions());
	}

	public function testActionIndex_RendersIndexView()
	{
		$mockController = $this->getMock('SiteController', array('render'),
			array('SiteController'));
		$mockController->expects($this->any())
			->method('render');
		$mockController->actionIndex();
	}

	public function testActionIndex_LoggedIn_RendersIndexView()
	{
		$userInfo = $this->users['user1'];
		$identity = new UserIdentity('JoeBloggs', 'secret');
		$identity->authenticate();
		Yii::app()->user->login($identity);

		$mockController = $this->getMock('SiteController', array('render', 'redirect'),
			array('SiteController'));
		$mockController->expects($this->once())
			->method('render')
			->with('index');
		$mockController->expects($this->never())
			->method('redirect');
		$mockController->actionIndex();
	}

	public function testActionLogout_LogsUserOut()
	{
		$userInfo = $this->users['user1'];
		$identity = new UserIdentity('JoeBloggs', 'secret');
		$identity->authenticate();
		Yii::app()->user->login($identity);

		$this->assertFalse(Yii::app()->user->isGuest);
		$userId = $this->users['user1']['id'];
		$mockController = $this->getMock('SiteController', array('redirect'),
			array('SiteController'));
		$mockController->expects($this->any())
			->method('redirect')
			->with(Yii::app()->homeUrl);

		$mockController->actionLogout();
	}

	public function testActionLogin_NonAjaxRequest_RendersLoginForm()
	{
		$_POST = array(
			'LoginForm' => array(
				'username' => 'JoeBloggs',
				'password' => 'secret',
				'siteId' => 1,
				'rememberMe' => false
			),
		);

		$model = new LoginForm;
		$model->attributes = $_POST['LoginForm'];
		$model->validate();
		$model->login();

		$mockController = $this->getMock('SiteController', array('redirect', 'render'),
			array('SiteController'));
		$mockController->expects($this->once())
			->method('redirect')
			->with(Yii::app()->user->returnUrl);
		$mockController->expects($this->once())
			->method('render')
			->with('login', array(
				'model' => $model,
				'sites' => CHtml::listData($this->sites, 'id', 'short_name')
			)
		);

		$this->assertNull($mockController->actionLogin());
	}
	*/
}
