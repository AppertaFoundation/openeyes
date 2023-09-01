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

namespace OEModule\OphCiExamination\tests\feature;

/**
 * A safety test that validates all the examination elements can be loaded dynamically
 * @group sample-data
 * @group examination
 */
class ElementLoadingTest extends \OEDbTestCase
{
    use \HasEventTypeElementAssertions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \WithTransactions;

    public function elementClassListProvider()
    {
        return [
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_History::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_AdnexalComorbidity::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_PosteriorPole::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Investigation::class],
            // [\OEModule\OphCiExamination\models\Element_OphCiExamination_Conclusion::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Dilation::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Comorbidities::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Gonioscopy::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_OpticDisc::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Management::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_GlaucomaRisk::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Risks::class],
            [\OEModule\OphCiExamination\models\PupillaryAbnormalities::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_DRGrading::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_LaserManagement::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagement::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_InjectionManagementComplex::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_OCT::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_BlebAssessment::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_PostOpComplications::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Fundus::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_HistoryRisk::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_OptomComments::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_CXL_History::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Keratometry::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Specular_Microscopy::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Slit_Lamp::class],
            [\OEModule\OphCiExamination\models\MedicalLids::class],
            [\OEModule\OphCiExamination\models\FamilyHistory::class],
            [\OEModule\OphCiExamination\models\Allergies::class],
            [\OEModule\OphCiExamination\models\SocialHistory::class],
            [\OEModule\OphCiExamination\models\PastSurgery::class],
            [\OEModule\OphCiExamination\models\SurgicalLids::class],
            [\OEModule\OphCiExamination\models\SystemicDiagnoses::class],
            [\OEModule\OphCiExamination\models\HistoryRisks::class],
            [\OEModule\OphCiExamination\models\HistoryMedications::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Observations::class],
            [\OEModule\OphCiExamination\models\VanHerick::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_CVI_Status::class],
            [\OEModule\OphCiExamination\models\HistoryIOP::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Contacts::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences::class],
            [\OEModule\OphCiExamination\models\OCT::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::class],
            // TODO: enable this once the mock user works with ESignPINFieldMedication
            // [\OEModule\OphCiExamination\models\MedicationManagement::class],
            [\OEModule\OphCiExamination\models\SystemicSurgery::class],
            [\OEModule\OphCiExamination\models\PostOpDiplopiaRisk::class],
            [\OEModule\OphCiExamination\models\BirthHistory::class],
            [\OEModule\OphCiExamination\models\HeadPosture::class],
            [\OEModule\OphCiExamination\models\ConvergenceAccommodation::class],
            [\OEModule\OphCiExamination\models\StereoAcuity::class],
            [\OEModule\OphCiExamination\models\SensoryFunction::class],
            [\OEModule\OphCiExamination\models\PrismReflex::class],
            [\OEModule\OphCiExamination\models\CoverAndPrismCover::class],
            [\OEModule\OphCiExamination\models\ContrastSensitivity::class],
            [\OEModule\OphCiExamination\models\Synoptophore::class],
            [\OEModule\OphCiExamination\models\RedReflex::class],
            [\OEModule\OphCiExamination\models\NinePositions::class],
            [\OEModule\OphCiExamination\models\Retinoscopy::class],
            [\OEModule\OphCiExamination\models\CorrectionGiven::class],
            [\OEModule\OphCiExamination\models\PrismFusionRange::class],
            [\OEModule\OphCiExamination\models\StrabismusManagement::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Retinopathy::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Maculopathy::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_DrugAdministration::class],
            [\OEModule\OphCiExamination\models\FreehandDraw::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Pain::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_AE_RedFlags::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicProcedures::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_Triage::class],
            [\OEModule\OphCiExamination\models\Element_OphCiExamination_NextSteps::class],
            [\OEModule\OphCiExamination\models\AdviceGiven::class],
            [\OEModule\OphCiExamination\models\Lacrimal::class]
        ];
    }

    /**
     * @test
     * @dataProvider elementClassListProvider
     */
    public function element_class_can_be_loaded($class_name)
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $element_type = \ElementType::factory()->useExisting(['class_name' => $class_name])->create();
        $patient = \Patient::factory()->create();
        $episode = \Episode::factory()->create(['patient_id' => $patient]);

        $this->mockCurrentContext($episode->firm, null, $institution);

        $this->actingAs($user, $institution)
            ->get("/OphCiExamination/Default/ElementForm?id={$element_type->id}&patient_id={$patient->id}")
            ->assertSuccessful("$class_name element could not be loaded for examination event.");
    }
}
