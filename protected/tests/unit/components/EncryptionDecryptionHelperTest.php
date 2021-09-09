<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class EncryptionDecryptionHelperTest extends CTestCase
{
    private function generateKey() : string
    {
        return sodium_crypto_secretbox_keygen();
    }

    private function getMockedHelper() : EncryptionDecryptionHelper
    {
        $mock = $this->getMockBuilder(EncryptionDecryptionHelper::class)
                    ->setMethods(["getCryptoKey"])->getMock();
        $mock->method("getCryptoKey")->willReturn($this->generateKey());
        return $mock;
    }

    public function testEncryptData()
    {
        $input = "Hello World";
        $helper = $this->getMockedHelper();
        $encrypted = $helper->encryptData($input);
        $this->assertIsString($encrypted);
        $this->assertNotEquals($input, $encrypted);
    }

    public function testDecryptData()
    {
        $input = "Hello World";
        $helper = $this->getMockedHelper();
        $encrypted = $helper->encryptData($input);
        $decrypted = $helper->decryptData($encrypted);
        $this->assertEquals($decrypted, $input);
    }
}