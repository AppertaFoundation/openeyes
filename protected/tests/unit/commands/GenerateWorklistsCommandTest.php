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
class GenerateWorklistsCommandTest extends PHPUnit_Framework_TestCase
{
    private static $dateFormat = 'Y-m-d';

    public function getMockCmd($manager, $methods = array())
    {
        $name = 'GenerateWorklists';
        $runner = $this->getMockBuilder('CConsoleCommandRunner')
            ->disableOriginalConstructor()
            ->getMock();

        return $this->getMockBuilder('GenerateWorklistsCommand')
            ->setConstructorArgs(array($name, $runner, $manager))
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @covers GenerateWorklistsCommand
     */
    public function test_actionGenerate()
    {
        $horizon = '3 months';

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('generateAllAutomaticWorklists'))
            ->getMock();

        $cmd = $this->getMockCmd($manager, array('getDateLimit'));

        $test_limit = new DateTime();

        $cmd->expects($this->once())
            ->method('getDateLimit')
            ->with($horizon)
            ->willReturn($test_limit);

        $manager->expects($this->once())
            ->method('generateAllAutomaticWorklists')
            ->with($test_limit)
            ->willReturn(5);

        $cmd->actionGenerate(null, $horizon);
    }

    /**
     * @covers GenerateWorklistsCommand
     */
    public function test_actionGenerate_errors()
    {
        $horizon = '3 months';

        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('generateAllAutomaticWorklists', 'getErrors'))
            ->getMock();

        $cmd = $this->getMockCmd($manager, array('getDateLimit', 'finish', 'error'));

        $test_limit = new DateTime();

        $fake_errs = array('1', '2');

        $cmd->expects($this->once())
            ->method('getDateLimit')
            ->with($horizon)
            ->willReturn($test_limit);

        $cmd->expects($this->exactly(count($fake_errs)))
            ->method('error');

        $cmd->expects($this->once())
            ->method('finish');

        $manager->expects($this->once())
            ->method('generateAllAutomaticWorklists')
            ->with($test_limit)
            ->willReturn(false);

        $manager->expects($this->once())
            ->method('getErrors')
            ->willReturn($fake_errs);

        $cmd->actionGenerate(null, $horizon);
    }

    public function getDateLimitProvider()
    {
        return array(
            array(null, (new DateTime())->format(self::$dateFormat)),
            array('6 months', (new DateTime())->add(DateInterval::createFromDateString('6 months'))->format(self::$dateFormat)),
        );
    }

    /**
     * @dataProvider getDateLimitProvider
     * @covers GenerateWorklistsCommand
     *
     * @param $horizon
     * @param $expected
     */
    public function test_getDateLimit($horizon, $expected)
    {
        $manager = $this->getMockBuilder('WorklistManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getGenerationTimeLimitDate'))
            ->getMock();

        if ($horizon) {
            $manager->expects($this->never())
                ->method('getGenerationTimeLimitDate');
        } else {
            $manager->expects($this->once())
                ->method('getGenerationTimeLimitDate')
                ->willReturn(DateTime::createFromFormat(self::$dateFormat, $expected));
        }

        $cmd = $this->getMockCmd($manager, array('usageError'));

        if (is_null($expected)) {
            // null indicates we don't care about result from method because an exception will terminate the script.
            $this->expectException(PHPUnit_Framework_Error_Warning::class);
            $cmd->getDateLimit($horizon);
        } else {
            $cmd->expects($this->never())
                ->method('usageError');
            $res = $cmd->getDateLimit($horizon);

            $this->assertInstanceOf(\DateTime::class, $res);
            $this->assertEquals($expected, $res->format(self::$dateFormat));
        }
    }
}
