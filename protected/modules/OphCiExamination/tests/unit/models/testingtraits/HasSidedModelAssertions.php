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

namespace OEModule\OphCiExamination\tests\unit\models\testingtraits;

trait HasSidedModelAssertions
{
    use \HasModelAssertions;

    public function assertSidedRelationValidated($cls, $related_cls, $side, $relation_name, $message_partial = 'invalid')
    {
        $related_instance = $this->getMockBuilder($related_cls)
            ->disableOriginalConstructor()
            ->setMethods(['validate', 'getErrors'])
            ->getMock();

        $related_instance->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));

        $related_instance->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(['foo' => [$message_partial]]));

        $test = $this->getMockBuilder($cls)
            ->disableOriginalConstructor()
            ->setMethods(array('hasEye', 'hasLeft', 'hasRight'))
            ->getMock();

        $test->expects($this->any())
            ->method('hasEye')
            ->will(
                $this->returnValueMap([
                    ['right', $side === 'right'],
                    ['left', $side === 'left']
                ])
            );

        $test->expects($this->any())
            ->method('hasLeft')
            ->will($this->returnValue($side === 'left'));
        $test->expects($this->any())
            ->method('hasRight')
            ->will($this->returnValue($side === 'right'));

        $test->$relation_name = array($related_instance);
        $this->assertAttributeInvalid($test, $relation_name, $message_partial);
    }
}