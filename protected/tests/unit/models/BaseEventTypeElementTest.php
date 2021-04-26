<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BaseEventTypeElementTestNS.php';

class BaseEventTypeElementTest extends CDbTestCase
{
    /**
     * @covers BaseEventTypeElement::getElementType
     * @todo   Implement testGetElementType().
     */
    public function testGetElementType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::canCopy
     */
    public function test_can_copy_default_is_false()
    {
        $instance = $this->getMockInstance();
        $this->assertFalse($instance->canCopy());
    }

    /**
     * @covers BaseEventTypeElement::getChildren
     * @todo   Implement testGetChildren().
     */
    public function testGetChildren()
    {

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::loadFromExisting
     * @todo   Implement testLoadFromExisting().
     */
    public function testLoadFromExisting()
    {

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::render
     * @todo   Implement testRender().
     */
    public function testRender()
    {

        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::getSetting
     * @todo   Implement testGetSetting().
     */
    public function testGetSetting()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::parseSetting
     * @todo   Implement testParseSetting().
     */
    public function testParseSetting()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::setBaseOptions
     * @todo   Implement testSetBaseOptions().
     */
    public function testSetBaseOptions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::setDefaultOptions
     * @todo   Implement testSetDefaultOptions().
     */
    public function testSetDefaultOptions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::setUpdateOptions
     * @todo   Implement testSetUpdateOptions().
     */
    public function testSetUpdateOptions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::getInfoText
     * @todo   Implement testGetInfoText().
     */
    public function testGetInfoText()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::getDefaultView
     */
    public function testGetDefaultView()
    {
        $test = $this->getMockInstance();

        $r = new ReflectionClass($test);
        $this->assertEquals($r->getShortName(), $test->getDefaultView());

        $ns_test = new BaseEventTypeElementTestNS\models\NamespacedElement();
        $this->assertEquals('NamespacedElement', $ns_test->getDefaultView());
    }

    /**
     * @covers BaseEventTypeElement::getCreate_view
     * @todo   Implement testGetCreate_view().
     */
    public function testGetCreate_view()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::getUpdate_view
     * @todo   Implement testGetUpdate_view().
     */
    public function testGetUpdate_view()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::getView_view
     * @todo   Implement testGetView_view().
     */
    public function testGetView_view()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::getPrint_view
     * @todo   Implement testGetPrint_view().
     */
    public function testGetPrint_view()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::isEditable
     * @todo   Implement testIsEditable().
     */
    public function testIsEditable()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers BaseEventTypeElement::requiredIfSide
     * @todo   Implement testRequiredIfSide().
     */
    public function testRequiredIfSide()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function test_widget_class_is_null_for_element_with_no_widget_class()
    {
        $instance = new ElementWithNoWidgetClass();
        $this->assertNull($instance->getWidgetClass());
    }

    public function test_widget_class_is_not_null_for_element_with_widget_class_set()
    {
        $instance = new ElementWithWidgetClass();
        $this->assertNotNull($instance->getWidgetClass());
        $this->assertEquals(BaseEventElementWidget::class, $instance->getWidgetClass());
    }

    protected function getMockInstance()
    {
        return $this->getMockBuilder('\BaseEventTypeElement')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }
}

class ElementWithNoWidgetClass extends BaseEventTypeElement
{
    protected $widgetClass = null;

    public function __construct($scenario = 'insert')
    {
    }
}

class ElementWithWidgetClass extends BaseEventTypeElement
{
    protected $widgetClass = BaseEventElementWidget::class;

    public function __construct($scenario = 'insert')
    {
    }
}
