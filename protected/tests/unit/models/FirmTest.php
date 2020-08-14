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
class FirmTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'services' => 'Service',
        'specialties' => 'Specialty',
        'serviceSubspecialtyAssignment' => 'ServiceSubspecialtyAssignment',
        'subspecialties' => 'Subspecialty',
        'firms' => 'Firm',
        'FirmUserAssignments' => 'FirmUserAssignment',
        'users' => 'User',
        'contacts' => 'Contact',
    );

    public function getModel()
    {
        return Firm::model();
    }

    /**
     * @covers Firm
     *
     */
    public function testModel()
    {
        $this->assertEquals('Firm', get_class(Firm::model()), 'Class name should match model.');
    }

    /**
     * @covers Firm
     */
    public function testGetServiceText()
    {
        $firm = $this->firms('firm1');
        $this->assertEquals($firm->getServiceText(), $firm->serviceSubspecialtyAssignment->service->name);
    }


    /**
     * @covers Firm
     */
    public function testGetConsultantName()
    {
        $this->assertEquals('Mr Jim Aylward', $this->firms('firm1')->getConsultantName());
    }

    /**
     * @covers Firm
     */
    public function testGetConsultantName_NoConsultant()
    {
        $this->assertEquals('NO CONSULTANT', $this->firms('firm2')->getConsultantName());
    }

    /**
     * @covers Firm
     */
    public function testGetReportDisplay()
    {
        $this->assertEquals('Aylward Firm (Subspecialty 1)', $this->firms('firm1')->getReportDisplay());
    }

    /**
     * @covers Firm
     */
    public function testGetNameAndSubspecialty()
    {
        $this->assertEquals('Aylward Firm (Subspecialty 1)', $this->firms('firm1')->getNameAndSubspecialty());
    }

    /**
     * @covers Firm
     */
    public function testIsSupportServicesFirm_False()
    {
        $this->assertFalse(Firm::model()->findByPk(1)->isSupportServicesFirm());
    }

    /**
     * @covers Firm
     */
    public function testIsSupportServicesFirm_True()
    {
        $this->assertTrue(Firm::model()->findByPk(4)->isSupportServicesFirm());
    }
}
