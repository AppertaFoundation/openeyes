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

use Symfony\Component\DomCrawler\Crawler;

trait MakesApplicationRequests
{
    use MocksSession;

    protected function actingAs($user, $for_institution = null)
    {
        $this->mockCurrentUser($user);
        if ($for_institution) {
            $this->mockCurrentInstitution($for_institution);
        }

        return $this;
    }

    protected function get($url, $crawl_result = true)
    {
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit'; // this is used in the main layout template
        $_SERVER['REQUEST_URI'] = $url;
        $requestMock = $this->getMockBuilder(\CHttpRequest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCsrfToken', 'getPathInfo'])
            ->getMock();

        $requestMock->method('getCsrfToken')
            ->willReturn('foo');
        $requestMock->method('getPathInfo')
            ->willReturn($url);

        \Yii::app()->setComponent('request', $requestMock);

        ob_start();
        \Yii::app()->run();
        $result = ob_get_contents();
        ob_end_clean();

        return $crawl_result ? $this->crawl($result) : $result;
    }

    protected function post($url, $form_data = [])
    {
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit'; // this is used in the main layout template
        $_SERVER['REQUEST_URI'] = $url;
        $_POST = $form_data;
        $_REQUEST = $form_data;

        $requestMock = $this->getMockBuilder(\CHttpRequest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCsrfToken', 'getPathInfo', 'redirect'])
            ->getMock();

        $redirected = null;

        $requestMock->method('getCsrfToken')
            ->willReturn('foo');
        $requestMock->method('getPathInfo')
            ->willReturn($url);
        $requestMock->method('redirect')
            ->willReturnCallback(function (...$args) use (&$redirected) {
                $redirected = new ApplicationRedirectWrapper(...$args);
            });

        \Yii::app()->setComponent('request', $requestMock);

        ob_start();
        \Yii::app()->run();
        $output = ob_get_contents();
        ob_end_clean();

        return new ApplicationResponseWrapper($output, $redirected);
    }

    protected function crawl(string $contents)
    {
        return new Crawler($contents);
    }
}
