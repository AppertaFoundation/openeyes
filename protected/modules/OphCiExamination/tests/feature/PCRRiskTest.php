<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\feature;

use CHtml;
use DoctorGrade;
use OEModule\OphCiExamination\models\Element_OphCiExamination_PcrRisk;

/**
 * @group sample-data
 * @group pcr-risk
 */
class PCRRiskTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \HasDatabaseAssertions;

    /** @test */
    public function element_can_be_saved()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;

        $this->mockCurrentContext($episode->firm, null, $institution);

        $element_with_data = Element_OphCiExamination_PcrRisk::factory()
            ->bothSided()
            ->withAllAnswers()
            ->make([
                'event_id' => null,
                'right_doctor_grade_id' => DoctorGrade::model()->findAll()[0]->id,
                'left_doctor_grade_id' => DoctorGrade::model()->findAll()[0]->id
            ]);

        $form_data = [
            CHtml::modelName($element_with_data) => [
                $element_with_data->getAttributes()
            ],
            'patient_id' => $patient->id
        ];

        $response = $this->actingAs($user, $institution)
            ->post('/OphCiExamination/Default/create', $form_data);

        $response->assertRedirectContains('view', 'Expected to redirect to a view of the created event');
        $this->assertEventTypeElementCreatedFor($patient, Element_OphCiExamination_PcrRisk::class, $this->getExpectedElementData($element_with_data->getAttributes()));
    }

    protected function createUserWithInstitution()
    {
        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = \Institution::factory()
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }

    protected function getExpectedElementData($data)
    {
        // quick filter to just extract the sided data fields from the given data
        return array_filter($data, function ($key) {
            return str_starts_with($key, 'right') || str_starts_with($key, 'left');
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function assertEventTypeElementCreatedFor(\Patient $patient, $element_class, $element_data)
    {
        $table = $element_class::model()->tableName();
        $query = $this->generateDatabaseCountQuery($table, $element_data);
        $query->join = 'join event ev on ' . $table . '.event_id = ev.id join episode ep on ev.episode_id = ep.id';
        $query->andWhere('ep.patient_id = :et_patient_id', [':et_patient_id' => $patient->id]);

        $this->assertGreaterThanOrEqual(1, $query->queryScalar(), "$table does not contain " . print_r($element_data, true) . "for given patient.");
    }
}
