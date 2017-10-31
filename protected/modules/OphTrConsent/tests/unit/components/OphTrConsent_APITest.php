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
class OphTrConsent_APITest extends CDbTestCase
{
    public $fixtures = array(
        'el_procedure' => 'Element_OphTrConsent_Procedure',
        'proclist' => 'EtOphtrconsentProcedureProceduresProcedures',
        'procs' => 'Procedure',
        'events' => 'Event',
        'episodes' => 'Episode',
    );

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
    }

    public function testGetFooterProcedures_Just2()
    {
        $api = Yii::app()->moduleAPI->get('OphTrConsent');

        $this->assertEquals('Procedure(s): Foobar Procedure, Test Procedure', $api->getFooterProcedures(13));
    }

    public function testGetFooterProcedures_MoreThan2()
    {
        $api = Yii::app()->moduleAPI->get('OphTrConsent');

        $this->assertEquals('Procedure(s): Foobar Procedure, Test Procedure...', $api->getFooterProcedures(14));
    }
}
