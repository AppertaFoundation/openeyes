<?php

Yii::import('application.vendors.*');
require_once('Zend/Ldap.php');

class UserIdentityTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User'
	);

	public function testInvalidAuthSource()
	{
		Yii::app()->params['auth_source'] = 'INVALID_AUTH_SOURCE';

		$userIdentity = new UserIdentity(
			'JoeBloggs',
			'password'
		);

		try {
			$this->assertFalse($userIdentity->authenticate());
		} catch (Exception $e) {
			return;
		}

		$this->fail('Failed to recognise invalid auth_source.');
	}

	public function testInvalidUser()
	{
		Yii::app()->params['auth_source'] = 'BASIC';

		$userIdentity = new UserIdentity(
			'wronguser',
			'password'
		);

		$this->assertFalse($userIdentity->authenticate());
		$this->assertEquals(
			$userIdentity->errorCode,
			UserIdentity::ERROR_USERNAME_INVALID
		);
	}

	public function testInvalidPassword()
	{
		Yii::app()->params['auth_source'] = 'BASIC';

		$userIdentity = new UserIdentity(
			'JoeBloggs',
			'wrongpassword'
		);

		$this->assertFalse($userIdentity->authenticate());
		$this->assertEquals(
			$userIdentity->errorCode,
			UserIdentity::ERROR_PASSWORD_INVALID
		);
	}

	public function testUserInactive()
	{
		Yii::app()->params['auth_source'] = 'BASIC';

		$userIdentity = new UserIdentity(
			'icabod',
			'password'
		);

		$this->assertFalse($userIdentity->authenticate());
		$this->assertEquals(
			$userIdentity->errorCode,
			UserIdentity::ERROR_USER_INACTIVE
		);
	}

	public function testBasicLogin()
	{
		Yii::app()->params['auth_source'] = 'BASIC';

		$userIdentity = new UserIdentity(
			'JoeBloggs',
			'password'
		);

		$this->assertTrue($userIdentity->authenticate());
	}

	public function testLdapLogin()
	{
		Yii::app()->params['auth_source'] = 'LDAP';

        $ZendLdapStub = $this->getMockBuilder('Zend_Ldap')
                     ->disableOriginalConstructor()
                     ->getMock();

        $ZendLdapStub->expects($this->any())
             ->method('bind')
             ->will($this->returnValue(true));

        $ZendLdapStub->expects($this->any())
             ->method('getEntry')
             ->will($this->returnValue(array(
				 'givenname' => array('stub'),
				 'sn' => array('stub'),
				 'mail' => array('stub@stub.com')
			 )));

		$userIdentity = $this->getMockBuilder('UserIdentity')
					->setConstructorArgs(array('JoeBloggs', 'password'))
					->setMethods(array('getLdap'))
                    ->getMock();

		$userIdentity->expects($this->any())
                ->method('getLdap')
                ->will($this->returnValue($ZendLdapStub));

		$this->assertTrue($userIdentity->authenticate());
	}

	public function testInvalidLdapLogin()
	{
		Yii::app()->params['auth_source'] = 'LDAP';

        $ZendLdapStub = $this->getMockBuilder('Zend_Ldap')
                     ->disableOriginalConstructor()
                     ->getMock();

        $ZendLdapStub->expects($this->any())
             ->method('bind')
             ->will($this->throwException(new Exception));

        $ZendLdapStub->expects($this->any())
             ->method('getEntry')
             ->will($this->returnValue(array(
				 'givenname' => array('stub'),
				 'sn' => array('stub'),
				 'mail' => array('stub@stub.com')
			 )));

		$userIdentity = $this->getMockBuilder('UserIdentity')
					->setConstructorArgs(array('JoeBloggs', 'password'))
					->setMethods(array('getLdap'))
                    ->getMock();

		$userIdentity->expects($this->any())
                ->method('getLdap')
                ->will($this->returnValue($ZendLdapStub));

		$this->assertFalse($userIdentity->authenticate());
		$this->assertEquals(
			$userIdentity->errorCode,
			UserIdentity::ERROR_USERNAME_INVALID
		);
	}

	public function testGetId()
	{
		Yii::app()->params['auth_source'] = 'BASIC';

		$userIdentity = new UserIdentity(
			'JoeBloggs',
			'password'
		);

		$this->assertTrue($userIdentity->authenticate());
		$this->assertEquals($userIdentity->getId(), 1);
	}
}