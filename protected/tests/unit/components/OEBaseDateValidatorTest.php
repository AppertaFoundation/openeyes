<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OEBaseDateValidatorTest extends PHPUnit_Framework_TestCase
{
    public function dateProvider()
    {
        return array(
            array('2015-02-29', false),
            array('2016-02-29', new DateTime('2016-02-29')),
            array('2015-01-12 10:61', false),
            array('2015-01-12 10:54', new DateTime('2015-01-12 10:54')),
            array('2015-01-12 07:54', new DateTime('2015-01-12 07:54')),
            array('2015-01-12 07:54:12', new DateTime('2015-01-12 07:54:12')),
            array('2015-01-12 07:54:2', false),
            array('garbage', false),
        );
    }

    /**
     * @covers OEBaseDateValidator
     * @dataProvider dateProvider
     */
    public function test_date_parsing($value, $expected_result)
    {
        $validator = new TestOEBaseDateValidator();

        $this->assertEquals($expected_result, $validator->parseDateValue($value));
    }

    public function datetimeProvider()
    {
        return array(
            array('2015-02-29', false),
            array('2016-02-29', false),
            array('2015-01-12 10:61', false),
            array('2015-01-12 10:54', new DateTime('2015-01-12 10:54')),
            array('2015-01-12 07:54', new DateTime('2015-01-12 07:54')),
            array('2015-01-12 07:54:12', new DateTime('2015-01-12 07:54:12')),
            array('2015-01-12 07:54:2', false),
            array('garbage', false),
        );
    }

    /**
     * @covers OEBaseDateValidator
     * @dataProvider datetimeProvider
     */
    public function test_datetime_parsing($value, $expected_result)
    {
        $validator = new TestOEBaseDateValidator();
        $validator->time_required = true;

        $this->assertEquals($expected_result, $validator->parseDateValue($value));
    }
}

class TestOEBaseDateValidator extends OEBaseDateValidator
{
    protected function validateAttribute($object, $attribute)
    {
    }
}
