<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CacheBusterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $cacheBuster = Yii::app()->cacheBuster;
        if (!$cacheBuster->time) {
            $cacheBuster->time = date('YmdH');
        }
    }

    /**
     * @covers CacheBuster
     */
    public function testCreateUrl()
    {
        $cacheBuster = Yii::app()->cacheBuster;

        $url1 = '/css/style.css';
        $url2 = '/css/style.css?cats=rule';

        $bustedUrl1 = $cacheBuster->createUrl($url1);
        $bustedUrl2 = $cacheBuster->createUrl($url2);
        $bustedUrl3 = $cacheBuster->createUrl($url1, 'hello');

        $urlMatch1 = preg_quote($url1, '/');
        $urlMatch2 = preg_quote($url2, '/');
        $urlMatch3 = preg_quote($url1, '/');

        // Test we have a question mark following by at least 1 char at end the url.
        $this->assertTrue(
            (bool) preg_match('/('.$urlMatch1.')\?[^\?]+$/', $bustedUrl1),
            'The URL contains the cache busting string'
        );

        // Test we have an ampersand followed by at least 1 char at end of url.
        $this->assertTrue(
            (bool) preg_match('/('.$urlMatch2.')&[^&]+$/', $bustedUrl2),
            'The URL with query string params contains the cache busting'
        );

        // Test we can specify the time string when creating the URL.
        $this->assertTrue(
            (bool) preg_match('/('.$urlMatch3.')\?hello$/', $bustedUrl3),
            'The URL should contain the correct cache busting string when specifying the time when creating the URL'
        );

        // Test that the custom time string is not added globally.
        $this->assertTrue(
            $cacheBuster->time !== 'hello',
            'The custom time string should not be added globally. The global time string should be set in the config'
        );
    }
}
