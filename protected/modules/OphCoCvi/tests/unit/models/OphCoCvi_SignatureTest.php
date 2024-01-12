<?php

/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group cvi
 */

class OphCoCvi_SignatureTest extends ModelTestCase
{
    use WithTransactions;

    private $signatory_file_id;
    protected $element_cls = OphCoCvi_Signature::class;

    public function setUp(): void
    {
        parent::setUp();
        /*
         * It is necessary to have a signature file, but it doesn't matter who the signer is.
         */
        $this->signatory_file_id =  $this->getDbConnection()->createCommand("SELECT signature_file_id FROM user WHERE signature_file_id IS NOT NULL")->queryScalar();
    }

    /** @test */
    public function display_patient_signatory_role()
    {
        $signature = OphCoCvi_Signature::factory()->asTypeAndRole(OphCoCvi_Signature::TYPE_PATIENT, OphCoCvi_Signature::SIGNATORY_PERSON_PATIENT)->create();
        $this->assertStringContainsString("the Patient", $signature->getDisplaySignatoryRole());
    }

    /** @test */
    public function display_patient_relative_signatory_role()
    {
        $signature = OphCoCvi_Signature::factory()->asTypeAndRole(OphCoCvi_Signature::TYPE_PATIENT, OphCoCvi_Signature::SIGNATORY_PERSON_PARENT_OR_GUARDIAN)->create();
        $this->assertStringContainsString("the Patient's Parent/Guardian", $signature->getDisplaySignatoryRole());
    }

    /** @test */
    public function display_patient_representative_signatory_role()
    {
        $signature = OphCoCvi_Signature::factory()->asTypeAndRole(OphCoCvi_Signature::TYPE_PATIENT, OphCoCvi_Signature::SIGNATORY_PERSON_REPRESENTATIVE)->create();
        $this->assertStringContainsString("the Patient's representative", $signature->getDisplaySignatoryRole());
    }
}
