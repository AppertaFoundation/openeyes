<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Required for LDAP authentication.
 *
 * @group undefined
 */
require_once 'Zend/Ldap.php';

class UserIdentityTest extends OEDbTestCase
{
    public $fixtures = array(
        'users' => 'User',
    );

    /**
     * @covers UserIdentity
     */
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

    /**
     * @covers UserIdentity
     */
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

    /**
     * @covers UserIdentity
     */
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

    /**
     * @covers UserIdentity
     */
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

    /**
     * @covers UserIdentity
     */
    public function testBasicLogin_WithGlobalFirmRights()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $userIdentity = new UserIdentity(
            'demo',
            'demo'
        );

        $this->assertTrue((bool) $this->users['user1']['global_firm_rights']);

        $this->assertTrue($userIdentity->authenticate());
    }

    /**
     * @covers UserIdentity
     */
    public function testBasicLogin_WithoutGlobalFirmRights()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $user = $this->users('user1');
        $user->global_firm_rights = false;
        $user->save(false);

        $userIdentity = new UserIdentity(
            'JoeBloggs',
            'secret'
        );

        $this->assertFalse((bool) $user->global_firm_rights);

        $this->assertTrue($userIdentity->authenticate());
    }

    /**
     * @covers UserIdentity
     */
    public function testLdapLogin()
    {
        Yii::app()->params['auth_source'] = 'LDAP';

        $ZendLdapStub = $this->getMock('Zend_Ldap', array(), array(), '', false);

        $ZendLdapStub->expects($this->any())
             ->method('bind')
             ->will($this->returnValue(true));

        $ZendLdapStub->expects($this->any())
             ->method('getEntry')
             ->will($this->returnValue(array(
                 'givenname' => array('stub'),
                 'sn' => array('stub'),
                 'mail' => array('stub@stub.com'),
             )));

        $userIdentity = $this->getMock('UserIdentity', array('getLdap'), array('JoeBloggs', 'password'));
        $userIdentity->expects($this->any())
                ->method('getLdap')
                ->will($this->returnValue($ZendLdapStub));

        $this->assertTrue($userIdentity->authenticate());
    }

    /**
     * @covers UserIdentity
     */
    public function testInvalidLdapLogin()
    {
        Yii::app()->params['auth_source'] = 'LDAP';

        $ZendLdapStub = $this->getMock('Zend_Ldap', array(), array(), '', false);
        $ZendLdapStub->expects($this->any())
             ->method('bind')
             ->will($this->throwException(new Exception()));

        $ZendLdapStub->expects($this->any())
             ->method('getEntry')
             ->will($this->returnValue(array(
                 'givenname' => array('stub'),
                 'sn' => array('stub'),
                 'mail' => array('stub@stub.com'),
             )));

        $userIdentity = $this->getMock('UserIdentity', array('getLdap'), array('JoeBloggs', 'password'));
        $userIdentity->expects($this->any())
                ->method('getLdap')
                ->will($this->returnValue($ZendLdapStub));

        $this->assertFalse($userIdentity->authenticate());
        $this->assertEquals(
            $userIdentity->errorCode,
            UserIdentity::ERROR_USERNAME_INVALID
        );
    }

    /**
     * @covers UserIdentity
     */
    public function testGetId()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $userIdentity = new UserIdentity(
            'JoeBloggs',
            'secret'
        );

        $this->assertTrue($userIdentity->authenticate());
        $this->assertEquals($this->users['user1']['id'], $userIdentity->getId());
    }

    /**
     * @covers UserIdentity
     */
    public function testSAMLLogin()
    {
        Yii::app()->params['auth_source'] = 'SAML';

        $userIdentity = new UserIdentity(
            'JoeBloggs',
            'password'
        );

        $this->assertTrue($userIdentity->authenticate(true));
    }

    /**
     * @covers UserIdentity
     */
    public function testOIDCLogin()
    {
        Yii::app()->params['auth_source'] = 'OIDC';

        $userIdentity = new UserIdentity(
            'JoeBloggs',
            'password'
        );

        $this->assertTrue($userIdentity->authenticate(true));
    }
}
