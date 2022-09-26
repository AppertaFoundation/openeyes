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
require_once __DIR__.DIRECTORY_SEPARATOR.'HelperTestNS.php';

class HelperTest extends CTestCase
{
    private static string $tz;

    public static function setUpBeforeClass(): void
    {
        self::$tz = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    public static function tearDownAfterClass(): void
    {
        date_default_timezone_set(self::$tz);
    }

    public function mysqlDate2JsTimestampDataProvider()
    {
        $dates = array(
            array(null, null),
            array('1969-12-31 23:59:59', -1000),
            array('1970-01-01 00:00:00', 0),
            array('2013-12-18 10:14:33', 1387361673000),
        );

        if (PHP_INT_SIZE === 8) { // 64 bit
            $dates[] = array('9999-12-31 23:59:59', 253402300799000);
        }

        return $dates;
    }

    /**
     * @covers Helper
     * @dataProvider mysqlDate2JsTimestampDataProvider
     * @param $input
     * @param $output
     */
    public function testMysqlDateToJsTimestamp($input, $output)
    {
        $this->assertEquals($output, Helper::mysqlDate2JsTimestamp($input));
    }

    public function getAgeDataProvider()
    {
        return array(
            array('Unknown', null),
            array(49, date('Y-m-d', strtotime('-50 years +1 day'))),
            array(50, date('Y-m-d', strtotime('-50 years'))),
            array(50, date('Y-m-d', strtotime('-50 years -1 day'))),
            array(49, '1925-06-01', '1975-01-01'),
            array(50, '1925-06-01', '1975-06-01'),
            array(50, '1925-06-01', '1975-12-01'),
            array(74, '1925-06-01', null, '2000-01-01'),
            array(75, '1925-06-01', null, '2000-06-01'),
            array(75, '1925-06-01', null, '2000-12-01'),
            array(49, '1925-06-01', '1975-01-01', '2000-01-01'),
            array(50, '1925-06-01', '1975-06-01', '2000-06-01'),
            array(50, '1925-06-01', '1975-12-01', '2000-12-01'),
            array(49, '1925-06-01', '2000-01-01', '1975-01-01'),
            array(50, '1925-06-01', '2000-06-01', '1975-06-01'),
            array(50, '1925-06-01', '2000-12-01', '1975-12-01'),
        );
    }

    /**
     * @covers Helper
     * @dataProvider getAgeDataProvider
     * @param $expected
     * @param $dob
     * @param null $date_of_death
     * @param null $check_date
     * @throws Exception
     */
    public function testGetAge($expected, $dob, $date_of_death = null, $check_date = null)
    {
        $this->assertEquals($expected, Helper::getAge($dob, $date_of_death, $check_date));
    }

    /**
     * @covers Helper
     * @throws ReflectionException
     */
    public function testExtractValues()
    {
        $objects = array(
            array('disorder' => array('term' => 'term1')),
            array('disorder' => array('term' => 'term2')),
            array('disorder' => array()),
            array(),
            ComponentStubGenerator::generate(
                'SecondaryDiagnosis',
                array('disorder' => ComponentStubGenerator::generate('Disorder', array('term' => 'term3')))
            ),
            ComponentStubGenerator::generate(
                'SecondaryDiagnosis',
                array('disorder' => ComponentStubGenerator::generate('Disorder', array('term' => 'term4')))
            ),
            ComponentStubGenerator::generate(
                'SecondaryDiagnosis',
                array('disorder' => ComponentStubGenerator::generate('Disorder', array('term' => null)))
            ),
            ComponentStubGenerator::generate(
                'SecondaryDiagnosis',
                array()
            ),
        );

        $expected = array(
            'term1',
            'term2',
            'termDefault',
            'termDefault',
            'term3',
            'term4',
            'termDefault',
            'termDefault',
        );

        $this->assertEquals($expected, Helper::extractValues($objects, 'disorder.term', 'termDefault'));
    }

    /**
     * @covers Helper
     */
    public function testFormatList_Empty()
    {
        $this->assertEquals('', Helper::formatList(array()));
    }

    /**
     * @covers Helper
     */
    public function testFormatList_One()
    {
        $this->assertEquals('foo', Helper::formatList(array('foo')));
    }

    /**
     * @covers Helper
     */
    public function testFormatList_Two()
    {
        $this->assertEquals('foo and bar', Helper::formatList(array('foo', 'bar')));
    }

    /**
     * @covers Helper
     */
    public function testFormatList_Three()
    {
        $this->assertEquals('foo, bar and baz', Helper::formatList(array('foo', 'bar', 'baz')));
    }

    /**
     * @covers Helper
     * @throws ReflectionException
     */
    public function testGetNSShortname()
    {
        $test = new HelperTestNS\models\HelperTestNS();

        $this->assertEquals('HelperTestNS', Helper::getNSShortname($test));
    }

    public function getDateForAgeProvider()
    {
        return array(
            array('2015-05-12', '2004-05-12', 11),
            array('2015-05-12', '2004-05-12', 11, '2016-04-11'),
            array(null, '2004-05-12', 11, '2009-04-11'),
            array(null, null, 11, '2009-04-11'),
        );
    }

    /**
     * @covers Helper
     * @dataProvider getDateForAgeProvider
     * @param $expected
     * @param $dob
     * @param $age
     * @param null $date_of_death
     * @throws Exception
     */
    public function testGetDateForAge($expected, $dob, $age, $date_of_death = null)
    {
        $this->assertEquals($expected, Helper::getDateForAge($dob, $age, $date_of_death));
    }

    public function lineLimitProvider()
    {
        return array(
            array("foo\nbar\ncow, dog", array("foo\nbar\ncow\ndog", 3)),
            array("foo\nbar\ncow\ndog", array("foo\nbar\ncow\ndog", 5)),
            array("foo, bar, cow, dog", array("foo\nbar\ncow\ndog", 1)),
            array('', array("foo\nbar\ncow\ndog", 1, 2), 'InvalidArgumentException'),
            array('foo bar cow', array("foo bar cow", 1)),
            array('', array("foo bar car", 0), 'InvalidArgumentException'),
            array("foo\nbar, cow\ndog", array("foo\nbar\ncow\ndog", 3, 1))
        );
    }

    /**
     * @covers Helper
     * @dataProvider lineLimitProvider
     * @param $expected
     * @param $args
     * @param null $exception
     */
    public function testLineLimit($expected, $args, $exception = null)
    {
        if ($exception) {
            $this->expectException($exception);
            forward_static_call_array(array('Helper', 'lineLimit'), $args);
        } else {
            $this->assertEquals($expected, forward_static_call_array(array('Helper', 'lineLimit'), $args));
        }
    }

    public function elementFinderProvider()
    {
        return array(
            array('foo', array('foo', array('foo' => 'foo')) ),
            array('foo', array('foo.bar', array('foo' => array('bar' => 'foo'))) ),
            array('foo', array('foo.bar', array('foo' => array('bar' => 'foo')) ) ),
            array(null, array('foo.bar', array('foo' => array('bar' => 'foo')), ':')),
            array(null, array('foobar', array('foo' => array('bar' => 'foo'))) ),
            array(array('car' => 'wilson'), array('foo.bar', array('foo' => array('bar' => array('car' => 'wilson')) ) )),
            array('wilson', array('foo.bar.car', array('foo' => array('bar' => array('car' => 'wilson')) ) ) )
        );
    }

    /**
     * @covers Helper
     * @dataProvider elementFinderProvider
     * @param $expected
     * @param $args
     */
    public function testElementFinder($expected, $args)
    {
        $this->assertEquals($expected, forward_static_call_array(array('Helper', 'elementFinder'), $args));
        // we use the CAttributeCollection in places, so want to check it works with this as well
        $collection = new CAttributeCollection($args[1]);
        $args[1] = $collection;
        $this->assertEquals($expected, forward_static_call_array(array('Helper', 'elementFinder'), $args));
    }

    /**
     * @covers Helper
     */
    public function testMd5Verified()
    {
        $random_string = '';
        for ($i = 0; $i < 5; $i++) {
            $random_string .= substr(md5(mt_rand()), 0, mt_rand(1, 32));
        }

        $checksum = md5($random_string);

        $pass_test = $random_string . $checksum;
        $this->assertEquals($random_string, Helper::md5Verified($pass_test));
        $fail_test = $random_string . 'a' . $checksum;
        $this->assertNull(Helper::md5Verified($fail_test));
    }

    public function getWeekdays()
    {
        return array(
            array('day' => 1, 'expected' => 'Monday'),
            array('day' => 2, 'expected' => 'Tuesday'),
            array('day' => 3, 'expected' => 'Wednesday'),
            array('day' => 4, 'expected' => 'Thursday'),
            array('day' => 5, 'expected' => 'Friday'),
            array('day' => 6, 'expected' => 'Saturday'),
            array('day' => 7, 'expected' => 'Sunday'),
            array('day' => 8, 'expected' => null),
        );
    }

    /**
     * @covers Helper
     * @dataProvider getWeekdays
     * @param $day
     * @param $expected
     */
    public function testGetWeekdayText($day, $expected)
    {
        $this->assertEquals($expected, Helper::getWeekdayText($day));
    }

    /**
     * @covers Helper
     */
    public function testConvertToBytes()
    {
        $val = '2KB';
        $expected = 2048;
        $this->assertEquals($expected, Helper::convertToBytes($val));

        $val = 8;
        $expected = 8;
        $this->assertEquals($expected, Helper::convertToBytes($val));

        $val = 'invalid';
        $this->assertNull(Helper::convertToBytes($val));
    }

    /**
     * @covers Helper
     */
    public function testConvertDate2NHS()
    {
        $expected = '20 Jul 2020';
        $this->assertEquals($expected, Helper::convertDate2NHS('20-07-2020'));
        $this->assertEquals($expected, Helper::convertDate2NHS($expected));

        $expected = '-';
        $this->assertEquals($expected, Helper::convertDate2NHS(null));
    }

    /**
     * @covers Helper
     */
    public function testTimestampToDB()
    {
        $date = new DateTime();
        $expected = date('Y-m-d H:i:s', $date->getTimestamp());
        $this->assertEquals($expected, Helper::timestampToDB($date->getTimestamp()));
    }

    /**
     * @covers Helper
     */
    public function testReturn_bytes()
    {
        $suffixes = array('k', 'm', 'g');
        $base = 1024;
        foreach ($suffixes as $i => $suffix) {
            $expected = pow($base, $i + 1);
            $this->assertEquals($expected, Helper::return_bytes('1' . $suffix));
        }
    }

    /**
     * @covers Helper
     */
    public function testConvertFuzzyDate2HTML()
    {
        $expected = "<span class='day'>20 </span><span class='mth'>Jul </span><span class='yr'>2020</span>";
        $this->assertEquals($expected, Helper::convertFuzzyDate2HTML('2020-07-20'));

        $expected = "<span class='day'> </span><span class='mth'>Jul </span><span class='yr'>2020</span>";
        $this->assertEquals($expected, Helper::convertFuzzyDate2HTML('2020-07-00'));

        $expected = "<span class='day'> </span><span class='mth'> </span><span class='yr'>2020</span>";
        $this->assertEquals($expected, Helper::convertFuzzyDate2HTML('2020-00-00'));
    }

    public function getEyeData()
    {
        return array(
            'Both eyes' => array(
                'eyes' => array(
                    'left_eye' => array(
                        'test 1'
                    ),
                    'right_eye' => array(
                        'test 2'
                    ),
                ),
                'expected' => Eye::BOTH
            ),
            'Left eye' => array(
                'eyes' => array(
                    'left_eye' => array(
                        'test 1'
                    ),
                ),
                'expected' => Eye::LEFT
            ),
            'Right eye' => array(
                'eyes' => array(
                    'right_eye' => array(
                        'test 2'
                    ),
                ),
                'expected' => Eye::RIGHT
            ),
            'N/A' => array(
                'eyes' => array(
                    'na_eye' => array(
                        'test 3'
                    )
                ),
                'expected' => -9
            ),
        );
    }

    /**
     * @covers Helper
     * @dataProvider getEyeData
     * @param $eyes
     * @param $expected
     */
    public function testGetEyeIdFromArray($eyes, $expected)
    {
        $this->assertEquals($expected, Helper::getEyeIdFromArray($eyes));
    }

    /**
     * @covers Helper
     */
    public function testConvertMySQL2NHS()
    {
        $data = '2020-07-21';
        $expected = '21 Jul 2020';

        $this->assertEquals($expected, Helper::convertMySQL2NHS($data));

        $data = null;
        $expected = '-';

        $this->assertEquals($expected, Helper::convertMySQL2NHS($data));
    }

    public function getMonths()
    {
        return array(
            array('month' => 1, 'text' => 'January'),
            array('month' => 2, 'text' => 'February'),
            array('month' => 3, 'text' => 'March'),
            array('month' => 4, 'text' => 'April'),
            array('month' => 5, 'text' => 'May'),
            array('month' => 6, 'text' => 'June'),
            array('month' => 7, 'text' => 'July'),
            array('month' => 8, 'text' => 'August'),
            array('month' => 9, 'text' => 'September'),
            array('month' => 10, 'text' => 'October'),
            array('month' => 11, 'text' => 'November'),
            array('month' => 12, 'text' => 'December'),
        );
    }

    /**
     * @covers Helper
     * @dataProvider getMonths
     * @param $month
     * @param $text
     */
    public function testGetMonthText($month, $text)
    {
        $expected = substr($text, 0, 3);
        $this->assertEquals($expected, Helper::getMonthText($month));
        $this->assertEquals($text, Helper::getMonthText($month, true));
    }

    /**
     * @covers Helper
     * @throws Exception
     */
    public function testCombineMySQLDateAndDateTime()
    {
        $date = '2020-07-21';
        $time = '11:00:00';
        $expected = '2020-07-21 11:00:00';

        $this->assertEquals($expected, Helper::combineMySQLDateAndDateTime($date, $time));

        $date = '21-07-2020';
        $time = '11:00:00';

        $this->expectException('Exception');
        Helper::combineMySQLDateAndDateTime($date, $time);
    }

    /**
     * @covers Helper
     */
    public function testFormatFuzzyDate()
    {
        $expected = '20 Jul 2020';
        $this->assertEquals($expected, Helper::formatFuzzyDate('2020-07-20'));

        $expected = 'Jul 2020';
        $this->assertEquals($expected, Helper::formatFuzzyDate('2020-07'));

        $expected = '2020';
        $this->assertEquals($expected, Helper::formatFuzzyDate('2020'));

        $expected = 'Undated';
        $this->assertEquals($expected, Helper::formatFuzzyDate(null));
    }

    /**
     * @covers Helper
     */
    public function testOeDateAsStr()
    {
        $expected = '<span class="oe-date"><span class="day">20</span><span class="mth">Jul</span><span class="yr">2020</span></span>';
        $this->assertEquals($expected, Helper::oeDateAsStr('20 Jul 2020'));

        $expected = '<span class="oe-date"><span class="day"></span><span class="mth">Jul</span><span class="yr">2020</span></span>';
        $this->assertEquals($expected, Helper::oeDateAsStr('Jul 2020'));

        $expected = '<span class="oe-date"><span class="day"></span><span class="mth"></span><span class="yr">2020</span></span>';
        $this->assertEquals($expected, Helper::oeDateAsStr('2020'));

        $expected = 'Undated';
        $this->assertEquals($expected, Helper::oeDateAsStr('Undated'));

        $expected = "";
        $this->assertEquals($expected, Helper::oeDateAsStr(""));
    }

    /**
     * @covers Helper
     */
    public function testConvertDate2FullYear()
    {
        $expected = '20/07/2020';
        $this->assertEquals($expected, Helper::convertDate2FullYear('20-07-20'));
        $this->assertEquals($expected, Helper::convertDate2FullYear('20-07-2020'));

        $expected = '-';
        $this->assertEquals($expected, Helper::convertDate2FullYear(null));
    }

    /**
     * @covers Helper
     */
    public function testGenerateUuid()
    {
        $regex = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

        $this->assertRegExp($regex, Helper::generateUuid());
    }

    /**
     * @covers Helper
     */
    public function testPadFuzzyDate()
    {
        $day = '21';
        $month = '7';
        $year = '2020';
        $expected = '0021-07-2020';
        $this->assertEquals($expected, Helper::padFuzzyDate($day, $month, $year));

        $day = null;
        $month = '7';
        $year = '2020';
        $expected = '0000-07-2020';
        $this->assertEquals($expected, Helper::padFuzzyDate($day, $month, $year));

        $day = null;
        $month = null;
        $year = '2020';
        $expected = '0000-00-2020';
        $this->assertEquals($expected, Helper::padFuzzyDate($day, $month, $year));
    }

    /**
     * @covers Helper
     */
    public function testConvertDate2HTML()
    {
        $expected = '<span class="day">20</span><span class="mth">Jul</span><span class="yr">2020</span>';
        $this->assertEquals($expected, Helper::convertDate2HTML('20-07-2020'));
        $this->assertEquals($expected, Helper::convertDate2HTML('20-07-20'));

        $this->assertEquals('-', Helper::convertDate2HTML(null));
    }

    /**
     * @covers Helper
     */
    public function testMysqlDate2JsTimestamp()
    {
        $date = '2020-07-21 00:00:00';
        $expected = 1595289600000;

        $this->assertEquals($expected, Helper::mysqlDate2JsTimestamp($date));
    }

    /**
     * @covers Helper
     */
    public function testArray_dump_html()
    {
        $values = array(
            array('Test 1', 'Test 2'),
            'Test 3',
            array(
                'Test 4',
                array('Test 5'),
                array('Test 6')
            )
        );

        $expected = 'Test 1<br>Test 2<br><br>Test 3<br>Test 4<br>Test 5<br><br>Test 6<br><br><br>';

        $this->assertEquals($expected, Helper::array_dump_html($values));
    }

    /**
     * @covers Helper
     */
    public function testIsValidDateTime()
    {
        $date = '21-07-2020';
        $this->assertTrue(Helper::isValidDateTime($date) !== false);

        $date = 'invalid';
        $this->assertTrue(Helper::isValidDateTime($date) === false);
    }

    /**
     * @covers Helper
     */
    public function testConvertDate2Short()
    {
        $expected = '20/07/20';
        $this->assertEquals($expected, Helper::convertDate2Short('20-07-2020'));

        $expected = '-';
        $this->assertEquals($expected, Helper::convertDate2Short(null));
    }

    /**
     * @covers Helper
     */
    public function testConvertMySQL2HTML()
    {
        $date = '2020-07-21';
        $expected = '<span class="day">21</span><span class="mth">Jul</span><span class="yr">2020</span>';
        $this->assertEquals($expected, Helper::convertMySQL2HTML($date));

        $date = null;
        $expected = '-';
        $this->assertEquals($expected, Helper::convertMySQL2HTML($date));
    }

    /**
     * @covers Helper
     */
    public function testConvertNHS2MySQL()
    {
        $date = '21 Jul 2020';
        $expected = '2020-07-21';
        $this->assertEquals($expected, Helper::convertNHS2MySQL($date));

        $date = array('21 Jul 2020', '20 Jul 2020');
        $expected = array('2020-07-21', '2020-07-20');
        $this->assertEquals($expected, Helper::convertNHS2MySQL($date));

        $date = array(array('21 Jul 2020'), '20 Jul 2020');
        $expected = array(array('2020-07-21'), '2020-07-20');
        $this->assertEquals($expected, Helper::convertNHS2MySQL($date));

        $date = null;
        $expected = null;
        $this->assertEquals($expected, Helper::convertNHS2MySQL($date));
    }
}
