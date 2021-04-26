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

class BaseEventElementWidgetTest extends OEDbTestCase
{
    use CreatesWidgets;
    use WithFaker;

    protected $controller_cls = BaseEventTypeController::class;

    public function test_update_data_only_sets_safe_attributes_on_element()
    {
        $posted_data = [
            'foo' => $this->faker->word(),
            'bar' => $this->faker->word(),
            'baz' => $this->faker->word()
        ];

        $element = $this->createMockElementExpectingOnlySafeAttributesToBeSet(['foo', 'baz'], $posted_data);

        $this->createWidgetWithProps(
            BaseEventElementWidget::class,
            [
                'patient' => $this->getMockBuilder(\Patient::class)
                    ->disableOriginalConstructor()
                    ->getMock(),
                'mode' => BaseEventElementWidget::$EVENT_EDIT_MODE,
                'element' => $element,
                'data' => $posted_data
            ]
        );
    }

    protected function createMockElementExpectingOnlySafeAttributesToBeSet($attrs, $posted_data)
    {
        $element = $this->getMockBuilder(BaseEventTypeElement::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSafeAttributeNames', 'setAttributes', 'setAttribute', 'getMetaData'])
            ->getMock();

        // prevent base class inspecting the db for existence of attributes
        $element->method('setAttribute')
            ->willReturn(false);
        $element->method('getMetaData')
            ->willReturn($this->getMockClass(CActiveRecordMetaData::class));

        $element->expects($this->once())
            ->method('getSafeAttributeNames')
            ->willReturn($attrs);

        $element->expects($this->once())
            ->method('setAttributes')
            ->with(
                array_filter(
                    $posted_data,
                    function ($k) use ($attrs) {
                        return in_array($k, $attrs);
                    },
                    ARRAY_FILTER_USE_KEY
                )
            );

        return $element;
    }
}
