<?php

/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DefaultController extends BaseController
{
    public array $filters = [];

    public $layout = '//layouts/worklist';

    public function accessRules()
    {
        return [
            ['allow', 'roles' => ['OprnViewPharmacyWorklist'] ],
        ];
    }

    public function actionIndex()
    {
        $is_admin = \Yii::app()->user->checkAccess('admin');
        $this->setFilters();

        $this->render('index', [
            'filters' => $this->filters,
            'institutions' => Institution::model()->getList(!$is_admin),
            'sites' => $this->filters['institution_id']
                ? Site::model()->findAll('institution_id = ?', [$this->filters['institution_id']])
                : Site::model()->getListForAllInstitutions()['list'] ?? [],
            'firms' => Firm::model()->getList($this->filters['institution_id']),
            'data_provider' => $this->getSearchResult(),
            'adder_itemset' => $this->getAdderItemSet(),

            'dispense_condition' => $this->getDispenseCondition(),
            'dispense_location' => $this->getDispenseLocation(),
        ]);
    }

    private function setFilters(): void
    {
        $is_admin = \Yii::app()->user->checkAccess('admin');
        $is_post = \Yii::app()->request->isPostRequest;
        $filters = \Yii::app()->request->getPost('filters', []);
        $institution_id = Institution::model()->getCurrent()->id;
        $clear_filters = \Yii::app()->request->getPost('clear_filters');
        $dynamic_filters = \Yii::app()->request->getPost('dynamic_filters', []);

        // clear filters on institution change, as some filters vary by institution
        if ($clear_filters) {
            $filters = ['institution_id' => $institution_id];
            $dynamic_filters = [];
        }

        if (!\Yii::app()->request->isPostRequest) {
            $value = Yii::app()->request->cookies['filters']->value ?? null;

            if ($is_admin || (int)$institution_id === Institution::model()->getCurrent()->id) {
                $this->filters = $value ? unserialize($value) : [];

                if ($this->filters) {
                    return;
                }
            }
        }

        $site_id = $filters['site_id'] ?? null;
        $firm_id = $filters['firm_id'] ?? null;
        $dispense_condition_id = $filters['dispense_condition_id'] ?? 1;
        $dispense_location_id = $filters['dispense_location_id'] ?? 2;

        $institution_id = (!$is_admin || !$is_post)
            ? Institution::model()->getCurrent()->id
            : ($filters['institution_id'] ?? null);

        $this->filters = [
            'institution_id' => $institution_id,
            'site_id' => $site_id,
            'firm_id' => $firm_id,
            'dispense_condition_id' => $dispense_condition_id,
            'dispense_location_id' => $dispense_location_id,
            'dynamic_filters' => $dynamic_filters,
        ];

        if (\Yii::app()->request->isPostRequest) {
            \Yii::app()->request->cookies['filters'] = new CHttpCookie('filters', serialize($this->filters));
            \Yii::app()->request->cookies['filters']->expire = time() + (3650 * 24 * 60 * 60); // 10 years

        }
    }

    private function getAdderItemSet(): array
    {
        if ($this->filters['institution_id']) {
            $institution = Institution::model()->findByPk($this->filters['institution_id']);
            $signatories = Element_OphDrPrescription_Esign::model()->getSecondarySignatures($institution);

            $collection = new \ModelCollection($signatories);
            $signatories = $collection->pluck('signatory_role');
        } else {
            $collection = new \ModelCollection(SecondarySignatory::model()->findAll());
            $signatories = $collection->pluck('name');
        }

        $itemset = [];
        foreach ($signatories as $signatory) {
            $type = str_replace(" ", "", strtolower($signatory));
            $itemset[] = [
                "id" => "{$type}Parameter",
                "type" => "{$type}Parameter",
                "label" => $signatory,
            ];
        }

        return [
            [
                "options" => ["header" => "Signatory"],
                "itemset" => $itemset,
            ],
            [
                "itemset" => [
                    ["label" => "signed"],
                    ["label" => "not signed"],
                ]
            ],
            [
                "itemset" => [
                    ["label" => "AND"],
                    ["label" => "OR"],
                ]
            ],
        ];
    }

    private function getSearchResult(): CActiveDataProvider
    {
        $institution = Institution::model()->getCurrent();
        $criteria = new CDbCriteria;
        $criteria->with = ['signatures', 'event.episode'];
        $criteria->together = true;

        foreach (['institution_id', 'site_id', 'firm_id'] as $filter_item) {
            if ($this->filters[$filter_item]) {
                $criteria->compare("event.$filter_item", $this->filters[$filter_item]);
            }
        }

        $dynamic_filters = $_POST['dynamic_filters'] ?? [];

        $filter_conditions = [];

        foreach ($dynamic_filters as $k => $filter) {
            $subquery = "(SELECT 1 FROM ophdrprescription_signature sub_sig WHERE sub_sig.element_id = t.id AND sub_sig.signatory_role = :signatory_role_$k)";

            if ($filter['value'] === 'NOT SIGNED') {
                $subquery = "NOT EXISTS $subquery";
            }

            $criteria->params[":signatory_role_$k"] = $filter['type'];

            $filter_conditions[] = $subquery;

            if ($k < count($dynamic_filters)-1) {
                $filter_conditions[] = $filter['operation'] ?? 'AND';
            }
        }

        if (!empty($filter_conditions)) {
            $dynamic_filter_condition = '(' . implode(" ", $filter_conditions) . ')';
            $criteria->addCondition($dynamic_filter_condition);
        }
        $signatories = Element_OphDrPrescription_Esign::model()->getSecondarySignatures($institution);
        $collection = new \ModelCollection($signatories);
        $signatory_roles = $collection->pluck('signatory_role');

        $all_roles_complete_query = "(SELECT COUNT(*) FROM ophdrprescription_signature sub_sig
              WHERE sub_sig.element_id = t.id AND sub_sig.signatory_role IN ('" . implode("','", $signatory_roles) . "'))";

        $criteria->addCondition("$all_roles_complete_query < " . count($signatory_roles));

        if ($this->filters['dispense_location_id'] || $this->filters['dispense_condition_id']) {
            $criteria->join = 'JOIN event_medication_use ON event_medication_use.event_id = t.event_id';

            if ($this->filters['dispense_location_id']) {
                $criteria->compare('event_medication_use.dispense_location_id', $this->filters['dispense_location_id']);
            }

            if ($this->filters['dispense_condition_id']) {
                $criteria->compare('event_medication_use.dispense_condition_id', $this->filters['dispense_condition_id']);
            }
        }

        $default_greater_than_date = strtotime(\SettingMetadata::model()->getSetting('pharmacy_worklists_ignore_before_date'));

        if ($default_greater_than_date !== false) {
            $criteria->compare("event.event_date", ">=" . date('Y-m-d', $default_greater_than_date));
        }

        $pagination = new CPagination(Element_OphDrPrescription_Esign::model()->count($criteria));
        $pagination->pageSize = 25;

        return new \CActiveDataProvider(Element_OphDrPrescription_Esign::class, [
            'criteria' => $criteria,
            'sort'=>[
                'defaultOrder'=>'event.event_date ASC',
            ],
            'pagination' => $pagination,
        ]);
    }
    public function actionGetDispenseByInstitution($institution_id)
    {
        $institution = Institution::model()->findByPk($institution_id);

        $dispense_conditions = OphDrPrescription_DispenseCondition::model()
            ->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, null, $institution);

        if (!$dispense_conditions) {
            $dispense_conditions = OphDrPrescription_DispenseCondition::model()->findAll();
        }

        $dispense_location = OphDrPrescription_DispenseLocation::model()
            ->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, null, $institution);

        if (!$dispense_location) {
            $dispense_location = OphDrPrescription_DispenseLocation::model()->findAll();
        }

        $dispense_conditions = array_map(function($m) { return ["id" => $m->id, "name" => $m->name];},  $dispense_conditions);
        $dispense_location = array_map(function($m) { return ["id" => $m->id, "name" => $m->name];},  $dispense_location);

        $this->renderJSON([
            'dispense_conditions' => $dispense_conditions,
            'dispense_location' => $dispense_location
        ]);
    }

    private function getDispenseCondition(): array
    {
        if ($this->filters['institution_id']) {
            $institution = Institution::model()->findByPk($this->filters['institution_id']);

            return OphDrPrescription_DispenseCondition::model()
                ->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, null, $institution);
        } else {
            return OphDrPrescription_DispenseCondition::model()->findAll();
        }
    }

    private function getDispenseLocation(): array
    {
        if ($this->filters['institution_id']) {
            $institution = Institution::model()->findByPk($this->filters['institution_id']);

            return OphDrPrescription_DispenseLocation::model()
                ->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, null, $institution);
        } else {
            return OphDrPrescription_DispenseLocation::model()->findAll();
        }
    }
}
