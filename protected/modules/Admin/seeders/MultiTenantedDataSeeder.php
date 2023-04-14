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
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\Admin\seeders;

use CommonOphthalmicDisorder;
use CommonOphthalmicDisorderGroup;
use CommonSystemicDisorder;
use CommonSystemicDisorderGroup;
use ElementType;
use OE\factories\models\EventFactory;
use OE\seeders\BaseSeeder;
use OE\seeders\resources\SeededEventResource;
use OEModule\OphCiExamination\models\Element_OphCiExamination_History;
use OEModule\OphCiExamination\models\OphCiExamination_Attribute;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeElement;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeOption;
use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule;
use OEModule\OphCiExamination\models\SystemicDiagnoses;
use SecondaryToCommonOphthalmicDisorder;

/**
 * A seeder used in the multitenancy/test_assignments test.
 * Creates the models for testing multitenanted assignments in a generic way.
 * Please see the aforementioned test if this functionality needs to be expanded.
 */
class MultiTenantedDataSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $control_institution = \Institution::model()->findByPk(1);
        $control_site = \Site::model()->findByPk(1);
        $control_subspecialty = \Subspecialty::model()->findByPk(1);

        $test_institution = \Institution::factory()
            ->isTenanted()
            ->create();
        $test_site = \Site::factory()->create(['institution_id' => $test_institution->id]);
        $test_firm = \Firm::factory()->create(['institution_id' => $test_institution->id]);

        $test_user = \User::factory()->withAuthItems(['admin', 'User', 'Edit', 'View clinical'])->withLocalAuthForInstitution($test_institution)->create();
        $test_user_auth = $test_user->authentications[0];

        $control_user = \User::model()->findByPk(1);
        $control_user_auth = $control_user->authentications[0];

        $test_event = EventFactory::forModule('OphCiExamination')->create();
        SystemicDiagnoses::factory()->create(['event_id' => $test_event->id]);
        Element_OphCiExamination_History::factory()->create(['event_id' => $test_event->id]);

        $test_event_resource = SeededEventResource::from($test_event)->toArray();

        $common_ophthalmic_disorder = CommonOphthalmicDisorder::factory()->useExisting()->create();
        $admin_secondary_common_ophthalmic_disorders = [
            'url' => '/admin/editsecondarytocommonophthalmicdisorder?parent_id=' . $common_ophthalmic_disorder->id,
            'data_test' => 'diagnosis-name',
            'value' => SecondaryToCommonOphthalmicDisorder::factory()->create(['parent_id' => $common_ophthalmic_disorder->id])->disorder->term,
        ];


        $workflows = $this->generateWorkflows($test_institution->id);

        return [
            'user_logins' => [
                'restricted_to_institution_user' => [
                    'username' => $test_user_auth->username,
                    'password' => "password",
                    'site_id' => $test_site->id,
                    'institution_id' => $test_institution->id,
                ],
                'installation_user' => [
                    'username' => $control_user_auth->username,
                    'password' => "admin",
                    'site_id' => $control_site->id,
                    'institution_id' => $control_institution->id,
                ],
            ],
            'event_resource' => $test_event_resource,
            'values' => [
                $this->generateElementAttributes($test_institution->id, $control_institution->id),
                $this->generateCommonOphthalmicDisorders($test_institution->id, $control_institution->id, $control_subspecialty->id),
                $this->generateCommonOphthalmicDisorderGroups($test_institution->id, $control_institution->id),
                $this->generateCommonSystemicDisorders($test_institution->id, $control_institution->id, $control_subspecialty->id),
                $this->generateCommonSystemicDisorderGroups($test_institution->id, $control_institution->id, $control_subspecialty->id),
                $workflows['workflows'],
                $workflows['workflow_rules']
            ],
        ];
    }

    private function generateElementAttributes($test_institution_id, $control_institution_id): array
    {
        $element_attribute_value = OphCiExamination_Attribute::factory()
            ->withInstitution($test_institution_id)
            ->withOptionCount(1)
            ->forElementType(Element_OphCiExamination_History::class)
            ->create();

        $element_attribute_element = OphCiExamination_AttributeElement::model()->findByAttributes(
            [
                "attribute_id" => $element_attribute_value->id,
                "element_type_id" => ElementType::model()->findByAttributes(["class_name" => Element_OphCiExamination_History::class])->id
            ]
        );

        $element_attribute_option_value = OphCiExamination_AttributeOption::model()->findByAttributes(["attribute_element_id" => $element_attribute_element->id]);

        $installation_element_attribute_value = OphCiExamination_Attribute::factory()
            ->withInstallation()
            ->withOptionCount(1)
            ->forElementType(Element_OphCiExamination_History::class)
            ->create();

        $installation_element_attribute_element = OphCiExamination_AttributeElement::model()->findByAttributes(
            [
                "attribute_id" => $installation_element_attribute_value->id,
                "element_type_id" => ElementType::model()->findByAttributes(["class_name" => Element_OphCiExamination_History::class])->id
            ]
        );

        $installation_element_attribute_option_value = OphCiExamination_AttributeOption::model()->findByAttributes(["attribute_element_id" => $installation_element_attribute_element->id]);

        $element_attributes = [
            'admin' => [
                'test_url' => '/oeadmin/ExaminationElementAttributes/list?institution_id=' . $test_institution_id,
                'control_url' => '/oeadmin/ExaminationElementAttributes/list?institution_id=' . $control_institution_id,
                'data_test' => 'name',
                'value' => $element_attribute_value->name,
            ],
            'event' => [
                'data_test' => 'add-to-history',
                'element_type' => 'adder',
                'value' => $element_attribute_option_value->value,
                'installation_value' => $installation_element_attribute_option_value->value,
            ],
        ];

        return $element_attributes;
    }

    private function generateCommonOphthalmicDisorders($test_institution_id, $control_institution_id, $control_subspecialty_id): array
    {
        $common_ophthalmic_disorders =  CommonOphthalmicDisorder::factory()->withInstitution($test_institution_id)->create(['subspecialty_id' => $control_subspecialty_id])->disorder;
        $common_ophthalmic_disorders = [
            'admin' => [
                'test_url' => '/admin/editcommonophthalmicdisorder?institution_id=' . $test_institution_id . '&subspecialty_id=' . $control_subspecialty_id,
                'control_url' => '/admin/editcommonophthalmicdisorder?institution_id=' . $control_institution_id . '&subspecialty_id=' . $control_subspecialty_id,
                'data_test' => 'disorder-term',
                'value' => $common_ophthalmic_disorders->term,
            ],
        ];

        return $common_ophthalmic_disorders;
    }

    private function generateCommonOphthalmicDisorderGroups($test_institution_id, $control_institution_id): array
    {
        $common_ophthalmic_disorder_groups_value = CommonOphthalmicDisorderGroup::factory()->withInstitution($test_institution_id)->create();
        $common_ophthalmic_disorder_groups = [
            'admin' => [
                'test_url' => '/admin/editcommonophthalmicdisordergroups?institution_id=' . $test_institution_id,
                'control_url' => '/admin/editcommonophthalmicdisordergroups?institution_id=' . $control_institution_id,
                'data_test' => 'group-name',
                'element_type' => 'input',
                'value' => $common_ophthalmic_disorder_groups_value->name,
            ],
        ];

        return $common_ophthalmic_disorder_groups;
    }

    private function generateCommonSystemicDisorders($test_institution_id, $control_institution_id): array
    {
        $common_systemic_disorders_value = CommonSystemicDisorder::factory()
            ->withInstitution($test_institution_id)
            ->create()
            ->disorder;
        $common_systemic_disorders = [
            'admin' => [
                'test_url' => '/oeadmin/CommonSystemicDisorder/list?institution_id=' . $test_institution_id,
                'control_url' => '/oeadmin/CommonSystemicDisorder/list?institution_id=' . $control_institution_id,
                'data_test' => 'disorder-term',
                'value' => $common_systemic_disorders_value->term,
            ],
            'event' => [
                'data_test' => 'add-systemic-diagnoses-button',
                'element_type' => 'adder',
                'value' => $common_systemic_disorders_value->term,
            ],
        ];

        return $common_systemic_disorders;
    }

    private function generateCommonSystemicDisorderGroups($test_institution_id, $control_institution_id): array
    {
        $common_systemic_disorder_groups_value = CommonSystemicDisorderGroup::factory()
            ->withInstitution($test_institution_id)
            ->create();
        $common_systemic_disorder_groups = [
            'admin' => [
                'test_url' => '/oeadmin/CommonSystemicDisorderGroup/list?institution_id=' . $test_institution_id,
                'control_url' => '/oeadmin/CommonSystemicDisorderGroup/list?institution_id=' . $control_institution_id,
                'data_test' => 'group-name',
                'element_type' => 'input',
                'value' => $common_systemic_disorder_groups_value->name,
            ],
        ];

        return $common_systemic_disorder_groups;
    }

    private function generateWorkflows($test_institution_id): array
    {
        $workflows_value = OphCiExamination_Workflow::factory()->create(['institution_id' => $test_institution_id]);
        //This is needed to create events
        $element_set = OphCiExamination_ElementSet::factory()->create(['workflow_id' => $workflows_value->id]);
        $workflows = [
            'admin' => [
                'url' => '/OphCiExamination/admin/viewWorkflows',
                'data_test' => 'workflow-name',
                'value' => $workflows_value->name,
            ],
        ];

        $workflow_rules_value = OphCiExamination_Workflow_Rule::factory()
            ->forFirm(null)
            ->forSubspecialty(null)
            ->create(['workflow_id' => $workflows_value->id]);
        $workflow_rules = [
            'admin' => [
               'url' => '/OphCiExamination/admin/viewWorkflowRules',
               'data_test' => 'workflow-name',
               'value' => $workflow_rules_value->workflow->name,
            ]
        ];

        return [
            'workflows' => $workflows,
            'workflow_rules' => $workflow_rules
        ];
    }
}
