<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DataTemplateTest extends PHPUnit_Framework_TestCase
{
    public function generateDataProvider()
    {
        return $this->getTemplates('generate-match');
    }

    public function matchDataProvider()
    {
        return array_merge($this->getTemplates('generate-match'), $this->getTemplates('match-only'));
    }

    public function matchFailureDataProvider()
    {
        return $this->getTemplates('match-failure');
    }

    private function getTemplates($dir)
    {
        $data = array();

        foreach (glob(__DIR__.'/'.__CLASS__."/{$dir}/*.template.json") as $template_path) {
            preg_match('|([^/]+)\.template\.json$|', $template_path, $m);
            $name = $m[1];

            $structure_path = str_replace('.template.json', '.structure.json', $template_path);
            $structure = json_decode(file_get_contents($structure_path));

            $values_path = str_replace('.template.json', '.values.json', $template_path);
            $values = file_exists($values_path) ? json_decode(file_get_contents($values_path), true) : array();

            $consts_path = str_replace('.template.json', '.consts.json', $template_path);
            $consts = file_exists($consts_path) ? json_decode(file_get_contents($consts_path), true) : array();

            $data[] = array($name, DataTemplate::fromJsonFile($template_path, $consts), $structure, $values);
        }

        return $data;
    }

    /**
     * @covers       DataTemplate
     * @dataProvider generateDataProvider
     * @param $name
     * @param $template
     * @param $structure
     * @param $values
     */
    public function testGenerate($name, $template, $structure, $values)
    {
        $this->assertEquals($structure, $template->generate($values));
    }

    /**
     * @covers       DataTemplate
     * @dataProvider matchDataProvider
     * @param $name
     * @param $template
     * @param $structure
     * @param $values
     */
    public function testMatch($name, $template, $structure, $values)
    {
        Yii::log(CVarDumper::dumpAsString($template));
        $this->assertEquals($values, $template->match($structure));
    }

    /**
     * @covers       DataTemplate
     * @dataProvider matchFailureDataProvider
     * @param $name
     * @param $template
     * @param $structure
     */
    public function testMatchFailure($name, $template, $structure)
    {
        $this->assertNull($template->match($structure));
    }

    /**
     * @covers DataTemplate
     */
    public function testMissingConstant()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Missing template constant: \'const1\'');
        DataTemplate::fromJsonFile(__DIR__.'/'.__CLASS__.'/generate-match/const.template.json');
    }
}
