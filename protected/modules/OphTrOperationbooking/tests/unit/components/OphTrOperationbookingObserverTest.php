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
class OphTrOperationbookingObserverTest extends CTestCase
{
    public static function setUpBeforeClass(): void
    {
        Yii::import('application.modules.OphTrOperationbooking.components.*');
    }

    private $observer;
    private $op;

    public function setUp(): void
    {
        parent::setUp();
        $this->observer = new OphTrOperationbookingObserver();
    }

    public function testResetSearch()
    {
        Yii::app()->session['theatre_searchoptions'] = array(
            'firm-id' => 1,
            'specialty-id' => 1,
            'site-id' => 1,
            'date-filter' => 1,
            'date-start' => '2012-01-01',
            'date-end' => '2012-12-31',
        );

        $this->observer->resetSearch(null);

        $so = Yii::app()->session['theatre_searchoptions'];

        $this->assertEquals(null, @$so['firm-id']);
        $this->assertEquals(null, @$so['specialty-id']);
        $this->assertEquals(null, @$so['site-id']);
        $this->assertEquals(null, @$so['date-filter']);
        $this->assertEquals(null, @$so['date-start']);
        $this->assertEquals(null, @$so['date-end']);
    }
}
