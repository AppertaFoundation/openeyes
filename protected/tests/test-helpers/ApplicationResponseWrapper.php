<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use PHPUnit\Framework\Assert as PHPUnit;

class ApplicationResponseWrapper
{
    public ?string $response;
    public ?ApplicationRedirectWrapper $redirect;

    public ?Exception $exception;

    public function __construct(?string $response = null, ?ApplicationRedirectWrapper $redirect = null, ?Exception $exception = null)
    {
        $this->response = $response;
        $this->redirect = $redirect;
        $this->exception = $exception;
    }

    public static function fromOutputString(string $string): ApplicationResponseWrapper
    {
        return new self($string);
    }

    public static function fromRedirect(ApplicationRedirectWrapper $redirect): ApplicationResponseWrapper
    {
        return new self(null, $redirect);
    }

    public static function fromException(Exception $exception): ApplicationResponseWrapper
    {
        return new self(null, null, $exception);
    }

    public function assertRedirect($url = null, bool $showResponseOnFail = false)
    {

        PHPUnit::assertTrue(
            $this->isRedirect(),
            "Response is not a redirect" . $showResponseOnFail ? ":\n" . $this->response : ""
        );

        if (!is_null($url)) {
            PHPUnit::assertEquals($url, $this->redirect->url, "Received redirect {$this->redirect->url} does not equal $url");
        }
    }

    public function assertRedirectContains(string $partial, ?string $message)
    {
        $this->assertRedirect();

        PHPUnit::assertStringContainsString($partial, $this->redirect->url, $message);
    }

    public function assertException(?string $expected_class = null, ?array $expected_properties = null)
    {
        PHPUnit::assertNotNull($this->exception);
        if ($expected_class) {
            PHPUnit::assertInstanceOf($expected_class, $this->exception);
        }
        if ($expected_properties) {
            foreach ($expected_properties as $property => $expected_value) {
                PHPUnit::assertEquals($expected_value, $this->exception->$property, print_r($this->exception, true));
            }
        }
    }

    protected function isRedirect()
    {
        return !is_null($this->redirect);
    }
}
