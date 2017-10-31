<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
require_once Yii::app()->basePath.'/commands/ProcessHscicDataCommand.php';

class ProcessHscicDataCommandTest extends CDbTestCase
{
    protected $processHscicDataCommand;

    public function setUp()
    {
        $this->processHscicDataCommand = new \ProcessHscicDataCommand();

        $this->processHscicDataCommand->path = \Yii::app()->basePath.'/tests/fixtures/data/hscic';
        $this->processHscicDataCommand->tempPath = $this->processHscicDataCommand->path.'/temp';
    }

    public function testImport()
    {
        /*Gp::model()->deleteAll("id > 3)");
            Yii::app()->db->createCommand("ALTER TABLE gp AUTO_INCREMENT = 4")->execute();
            
            return ;*/

            $gp = Gp::model()->findAll();

        $this->assertEquals(3, count($gp));

        $this->processHscicDataCommand->force = true;
        $this->processHscicDataCommand->actionImport('gp', 'monthly');

        $gp = Gp::model()->findAll();
        $this->assertEquals(103, count($gp));
    }

    public function tearDown()
    {
        Gp::model()->deleteAll('id > 3)');
        Yii::app()->db->createCommand('ALTER TABLE gp AUTO_INCREMENT = 4')->execute();
    }
}
