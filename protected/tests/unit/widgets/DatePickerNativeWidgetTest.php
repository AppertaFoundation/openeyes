<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use PHPUnit\Framework\MockObject\Builder\Stub;


/**
 * @covers DatePickerNative
 * @group sample-data
 * @group widgets
 * @group date-picker-native
 */
class DatePickerNativeWidgetTest extends OEDbTestCase
{
    use CreatesWidgets;
    use \WithFaker;

    protected $controller_cls = BaseController::class;
    protected $expected_date_input_format = 'Y-m-d';

    public function maxAndMinDateDataProvider()
    {
        return [
            "'today' string returns today's date in $this->expected_date_input_format" => [
                [
                    'maxDate' => 'today',
                    'minDate' => 'today'
                ],
                date('Y-m-d')
            ],
            "OE formatted date returned in $this->expected_date_input_format" => [
                [
                    'maxDate' => '26 Sep 1978',
                    'minDate' => '26 Sep 1978'
                ],
                '1978-09-26'
            ],
            "Y-m-d formattted date is returned in $this->expected_date_input_format" => [
                [
                    'maxDate' => '1978-09-26',
                    'minDate' => '1978-09-26'
                ],
                '1978-09-26'
            ],
            "unset dates return null" => [
                [
                    []
                ],
                null
            ],
            "empty string dates return null" => [
                [
                    'maxDate' => "",
                    'minDate' => ""
                ],
                null
            ],
            "null dates return null" => [
                [
                    'maxDate' => null,
                    'minDate' => null
                ],
                null
            ]
        ];
    }

    /**
     * @test
     * @dataProvider maxAndMinDateDataProvider
     */
    public function get_max_and_min_date_returns_correct_data($options, $expected)
    {
        $widget = $this->createWidgetWithProps(
            DatePickerNative::class,
            ['options' => $options]
        );

        if ($expected) {
            $this->assertEquals($expected, $widget->getMaxDate());
            $this->assertEquals($expected, $widget->getMinDate());
        } else {
            $this->assertNull($widget->getMaxDate());
            $this->assertNull($widget->getMinDate());
        }
    }

    public function htmlOptionsDataProvider()
    {
        return [
            "set htmlOptions property returns correct value" => [
                [
                    'foo' => 'bar'
                ],
                'bar'
            ],
            "unset htmlOptions property returns null" => [
                [
                    []
                ],
                null
            ],
            "empty string htmlOptions property returns empty string" => [
                [
                    'foo' => "",
                ],
                ""
            ],
            "null htmlOptions property returns null" => [
                [
                    'foo' => null,
                ],
                null
            ]
        ];
    }

    /**
     * @test
     * @dataProvider htmlOptionsDataProvider
     */
    public function get_html_option_returns_correct_data($html_options, $expected)
    {
        $widget = $this->createWidgetWithProps(
            DatePickerNative::class,
            ['htmlOptions' => $html_options]
        );

        if ($expected) {
            $this->assertEquals($expected, $widget->getHtmlOption('foo'));
        } else {
            $this->assertNull($widget->getHtmlOption('foo'));
        }
    }

    public function inputIdDataProvider()
    {
        $element = ComponentStubGenerator::generate('BaseElement', []);
        $field = "Foo";

        return [
            "htmlOptions property 'id' overrides derived id" => [
                [
                    'id' => 'Foo'
                ],
                $element,
                $field,
                'Foo'
            ],
            "default id is derived from the element class and field name" => [
                [],
                $element,
                $field,
                CHtml::modelName($element) . "_" . $field . "_0"
            ]
        ];
    }

    /**
     * @test
     * @dataProvider inputIdDataProvider
     */
    public function get_input_id_returns_correct_data($html_options, $element, $field, $expected)
    {
        $widget = $this->createWidgetWithProps(
            DatePickerNative::class,
            ['htmlOptions' => $html_options]
        );
        $widget->element = $element;
        $widget->field = $field;

        $this->assertEquals($expected, $widget->getInputId());
    }

    public function valueDataProvider()
    {
        return [
            "'today' string returns today's date in $this->expected_date_input_format" => [
                'today',
                date('Y-m-d')
            ],
            "OE formatted date returned in $this->expected_date_input_format" => [
                '3 Mar 1981',
                '1981-03-03'
            ],
            "Y-m-d formattted date is returned in $this->expected_date_input_format" => [
                '1978-09-26',
                '1978-09-26'
            ],
            "empty date value returns null" => [
                "",
                null
            ]
        ];
    }

    /**
     * @test
     * @dataProvider valueDataProvider
     * */
    public function get_value_returns_correctly_formatted_date($value, $expected)
    {
        $widget = $this->createWidgetWithProps(
            DatePickerNative::class
        );
        $widget->value = $value;

        if ($expected) {
            $this->assertEquals($expected, $widget->getValue());
        } else {
            $this->assertNull($widget->getValue());
        }
    }

    /** @test */
    public function value_defaults_to_today()
    {
        $widget = $this->createWidgetWithProps(
            DatePickerNative::class,
            [
                'field' => 'foo'
            ]
        );
        $widget->element = ComponentStubGenerator::generate('BaseElement', ['foo' => null]);

        ob_start();
        $widget->run();
        ob_end_clean();

        $this->assertEquals(date('Y-m-d'), $widget->getValue());
    }

    /** @test */
    public function value_correct_set_by_element_value()
    {
        $expected_value = $this->faker->date();

        $widget = $this->createWidgetWithProps(
            DatePickerNative::class
        );
        $widget->field = "foo";
        $widget->element = ComponentStubGenerator::generate('BaseElement', [
            'foo' => $expected_value
        ]);

        ob_start();
        $widget->run();
        ob_end_clean();

        $this->assertEquals($expected_value, $widget->getValue());
    }

    /**
     * @test
     * @dataProvider valueDataProvider
     * */
    public function value_correctly_set_by_post_request($value, $expected)
    {
        $widget = $this->createWidgetWithPost(
            DatePickerNative::class,
            [
                'name' => 'foo_bar'
            ],
            [
                'foo_bar' => $value
            ]
        );
        $widget->element = ComponentStubGenerator::generate('BaseElement', []);

        ob_start();
        $widget->run();
        ob_end_clean();

        if ($expected) {
            $this->assertEquals($expected, $widget->getValue());
        } else {
            $this->assertNull($widget->getValue());
        }
    }

    protected function createWidgetWithPost($cls, $props, $posted)
    {
        $request = $this->createMockPostRequest($posted);
        Yii::app()->setComponent('request', $request);
        $widget = $this->createWidgetWithProps($cls, $props);

        return $widget;
    }

    protected function createMockPostRequest($posted_data = [])
    {
        $request = $this->getMockBuilder(\CHttpRequest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPost', 'getIsPostRequest'])
            ->getMock();

        $request->method('getPost')
            ->will($this->returnCallback(function ($key, $default) use ($posted_data) {
                return isset($posted_data[$key]) ? $posted_data[$key] : $default;
            }));
        $request->method('getIsPostRequest')
            ->will($this->returnValue(true));

        return $request;
    }
}
