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
class AutoSaveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers AutoSave
     */
    public function testAutoSave()
    {
        $key = 'test_key';
        $value = 'test_value';

        AutoSave::add($key, $value);

        $this->assertEquals(Autosave::get($key), $value);
    }

    /**
     * @covers AutoSave
     */
    public function testAutoSaveRemove()
    {
        $key = 'test_key';
        $value = 'test_value';

        AutoSave::add($key, $value);
        AutoSave::remove($key);

        $this->assertNull(Autosave::get($key));
    }

    /**
     * @covers AutoSave
     */
    public function testAutoSaveRemoveByPrefix()
    {
        AutoSave::add('red', 'value');
        AutoSave::add('really', 'value');
        AutoSave::add('reaper', 'value');

        AutoSave::removeAllByPrefix('rea');

        $this->assertEquals(Autosave::get('red'), 'value');
        $this->assertNull(Autosave::get('really'));
        $this->assertNull(Autosave::get('reaper'));
    }
}
