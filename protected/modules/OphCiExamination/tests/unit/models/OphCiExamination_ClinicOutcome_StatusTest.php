<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphCiExamination_ClinicOutcome_StatusTest extends ActiveRecordTestCase
{
    protected $model;
    public $fixtures = array(
        'clinicOutcomeStatus' => 'OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status',
        'clinicOutcomeStatusOptions' => ':ophciexamination_clinicoutcome_status_options',
        'subspecialty' => '\Subspecialty',
        'institution' => '\Institution',
    );

    public function getModel()
    {
        return $this->model;
    }

    public function setUp()
    {
        parent::setUp();
        $this->model = new OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status();
    }

    public function testBySubspecialty()
    {
        $statusesBySubspecialty = OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status::model()
            ->bySubspecialty($this->subspecialty('subspecialty1'))->findAll();

        $this->assertCount(3, $statusesBySubspecialty);

        foreach ($statusesBySubspecialty as $statusBySubspecialty) {
            $this->assertNotEquals('Not active option', $statusBySubspecialty->name);
        }
    }

    public function testByInstitution()
    {
        $statusesByInstitution = $this->model->byInstitution($this->institution('moorfields'))->findAll();

        $this->assertCount(3, $statusesByInstitution);

        foreach ($statusesByInstitution as $status) {
            $this->assertNotEquals('Not active option', $status->name);
        }
    }
}
