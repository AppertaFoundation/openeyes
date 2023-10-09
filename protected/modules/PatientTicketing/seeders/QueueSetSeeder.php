<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\seeders;

use CDbCriteria;
use OE\seeders\BaseSeeder;
use OELog;
use OEModule\PatientTicketing\models\QueueSet;
use OEModule\PatientTicketing\models\QueueSetCategory;

class QueueSetSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $with_institution = $this->getSeederAttribute('with_institution');
        $one_queue_institution_assigned = $this->getSeederAttribute('one_queue_institution_assigned');

        if ($with_institution) {
            $queueset_category = $this->createCategoryAndQueueSetWithInstitution($this->app_context->getSelectedInstitution());
        } elseif ($one_queue_institution_assigned) {
            $queueset_category = $this->createCategoryAndTwoQueueSetOneWithInstitution($this->app_context->getSelectedInstitution());
        } else {
            $queueset_category = $this->createCategoryAndQueueSet();
        }

        return [
            'queueset_category_id' => $queueset_category->id,
            'queueset_category_name' => $queueset_category->name,
            'queueset_name' => $queueset_category->queuesets[0]->name,
            'queuesets_without_institution_names' => $this->getQueueSetsWithoutInstitutionName($queueset_category->id)
        ];
    }

    private function createCategoryAndQueueSet()
    {
        return QueueSetCategory::factory()
        ->withQueueSet()
        ->create();
    }

    private function createCategoryAndQueueSetWithInstitution($institution)
    {
        return QueueSetCategory::factory()
        ->withInstitution($institution)
        ->withQueueSet($institution)
        ->create();
    }

    private function createCategoryAndTwoQueueSetOneWithInstitution($institution)
    {
        $category = $this->createCategoryAndQueueSetWithInstitution($institution);

        QueueSet::factory()
            ->forCategory($category)
            ->create();

        return $category;
    }

    private function getQueueSetsWithoutInstitutionName($category_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition("category_id=:category_id");
        $criteria->addCondition("id NOT IN (SELECT queueset_id FROM patientticketing_queueset_institution)");
        $criteria->params = [":category_id" => $category_id];
        $queuesets = QueueSet::model()->findAll($criteria);

        return array_column($queuesets, "name");
    }
}
