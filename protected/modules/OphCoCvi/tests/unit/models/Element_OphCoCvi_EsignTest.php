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

use OEModule\OphCoCvi\models\Element_OphCoCvi_Esign;

/**
 * @group sample-data
 * @group cvi
 */

class Element_OphCoCvi_EsignTest extends ModelTestCase
{
    use WithTransactions;

    protected $element_cls = Element_OphCoCvi_Esign::class;
    private $signature_file_id;

    public function setUp(): void
    {
        parent::setUp();
        /*
         * It is necessary to have a signature file, but it doesn't matter who the signer is.
         */
        $this->signature_file_id = $this->getDbConnection()->createCommand("SELECT signature_file_id FROM user WHERE signature_file_id IS NOT NULL")->queryScalar();
    }

    /** @test */
    public function signed_by_patient()
    {
        $esign_element = Element_OphCoCvi_Esign::factory()
            ->withSignatureType(OphCoCvi_Signature::TYPE_PATIENT, OphCoCvi_Signature::SIGNATORY_PERSON_PATIENT, $this->signature_file_id)
            ->create();
        $this->assertTrue($esign_element->isSignedByPatient());
    }

    /** @test */
    public function signed_by_consultant()
    {
        $esign_element = Element_OphCoCvi_Esign::factory()
            ->withSignatureType(OphCoCvi_Signature::TYPE_LOGGEDIN_USER, OphCoCvi_Signature::SIGNATORY_CONSULTANT, $this->signature_file_id)
            ->create();
        $this->assertTrue($esign_element->isSignedByConsultant());
    }

    /** @test */
    public function signed()
    {
        $esign_element = Element_OphCoCvi_Esign::factory()
        ->withSignatureType(OphCoCvi_Signature::TYPE_LOGGEDIN_USER, OphCoCvi_Signature::SIGNATORY_CONSULTANT, $this->signature_file_id)
        ->withSignatureType(OphCoCvi_Signature::TYPE_PATIENT, OphCoCvi_Signature::SIGNATORY_PERSON_PATIENT, $this->signature_file_id)
        ->create();
        $this->assertTrue($esign_element->isSigned());
    }

    /** @test */
    public function get_patient_signature()
    {
        $esign_element = Element_OphCoCvi_Esign::factory()
        ->withSignatureType(OphCoCvi_Signature::TYPE_PATIENT, OphCoCvi_Signature::SIGNATORY_PERSON_PATIENT, $this->signature_file_id)
        ->create();
        $this->assertEquals(OphCoCvi_Signature::TYPE_PATIENT, $esign_element->getSignatureByType(OphCoCvi_Signature::TYPE_PATIENT)->type);
    }

    /** @test */
    public function get_consultant_signature()
    {
        $esign_element = Element_OphCoCvi_Esign::factory()
        ->withSignatureType(OphCoCvi_Signature::TYPE_LOGGEDIN_USER, OphCoCvi_Signature::SIGNATORY_CONSULTANT, $this->signature_file_id)
        ->create();
        $this->assertEquals(OphCoCvi_Signature::TYPE_LOGGEDIN_USER, $esign_element->getSignatureByType(OphCoCvi_Signature::TYPE_LOGGEDIN_USER)->type);
    }
}
