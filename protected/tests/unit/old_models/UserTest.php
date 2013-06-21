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

class UserTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('username' => 'Joe'), 1, array('user1')),
			array(array('username' => 'Jane'), 1, array('user2')),
			array(array('last_name' => 'bloggs'), 2, array('user1','user2')),  /* case insensitivity test - needs _ci column collation */
			array(array('username' => 'no-one'), 0, array()),
		);
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$user = new User;
		$searchTerms['global_firm_rights'] = null; // ignore what setting global_firm_rights has
		$user->setAttributes($searchTerms, true);
		$results = $user->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->users($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}

//	public function testBasicCreate()
//	{
//		Yii::app()->params['auth_source'] = 'BASIC';
//
//		$user = new User;
//		$user->setAttributes(array(
//			'username' => 'testtest',
//			'first_name' => 'test',
//			'last_name' => 'test',
//			'email' => 'test@test.com',
//			'password' => 'testtest',
//			'password_repeat' => 'testtest',
//			'active' => 1
//		));
//
//		$this->assertTrue($user->save(true));
//		$this->assertNotEquals('', $user->salt);
//	}
//
//	public function testLdapCreate()
//	{
//		Yii::app()->params['auth_source'] = 'LDAP';
//
//		$user = new User;
//		$user->setAttributes(array(
//			'username' => 'testtest',
//			'first_name' => 'test',
//			'last_name' => 'test',
//			'email' => 'test@test.com',
//			'password' => 'testtest',
//			'password_repeat' => 'testtest',
//			'active' => 1
//		));
//
//		$this->assertTrue($user->save(true));
//		$this->assertEquals('', $user->salt);
//	}
//
//	public function testInvalidAuthSourceCreate()
//	{
//		Yii::app()->params['auth_source'] = 'INVALID';
//
//		try {
//			$user = new User;
//			$user->setAttributes(array(
//				'username' => 'testtest',
//				'first_name' => 'test',
//				'last_name' => 'test',
//				'email' => 'test@test.com',
//				'password' => 'testtest',
//				'password_repeat' => 'testtest',
//				'active' => 1
//			));
//
//			$this->assertTrue($user->save(true));
//		} catch (SystemException $e) {
//			return;
//		}
//
//		$this->fail('Failed to throw exception when using invalid auth_source.');
//	}
//
//	public function testUpdate()
//	{
//		Yii::app()->params['auth_source'] = 'BASIC';
//
//		$user = User::model()->find(
//			'username = :username', array(':username' => 'icabod')
//		);
//
//		$salt = $user->salt;
//		$user->active = 0;
//		$user->password = 'testtest2';
//		$user->password_repeat = 'testtest2';
//
//		$this->assertTrue($user->save(true));
//
//		$this->assertTrue($user->validatePassword('testtest2'));
//
//		// test salt is still the same
//		$this->assertEquals($user->salt, $salt);
//	}
//
//	public function testActiveTextYes()
//	{
//		$user = User::model()->find(
//			'username = :username', array(':username' => 'JoeBloggs')
//		);
//
//		$this->assertEquals($user->getActiveText(), 'Yes');
//	}
//
//	public function testActiveTextNo()
//	{
//		$user = User::model()->find(
//			'username = :username', array(':username' => 'icabod')
//		);
//
//		$this->assertEquals($user->getActiveText(), 'No');
//		$this->assertEquals($this->users('user3'), $user);
//	}
//
//        public function testGlobalFirmRightsTextYes()
//        {
//                $user = User::model()->find(
//                        'username = :username', array(':username' => 'icabod')
//                );
//
//                $this->assertEquals($user->getGlobalFirmRightsText(), 'Yes');
//        }
//
//        public function testGetGlobalFirmRightsTextNo()
//        {
//                $user = User::model()->find(
//                        'username = :username', array(':username' => 'admin')
//                );
//
//                $this->assertEquals($user->getGlobalFirmRightsText(), 'No');
//        }
}
