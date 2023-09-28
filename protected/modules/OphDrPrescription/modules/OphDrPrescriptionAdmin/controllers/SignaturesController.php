<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class SignaturesController extends BaseAdminController
{
    public $group = 'Prescription';
    public ?\CActiveRecord $current_institution;

    public function beforeAction($action)
    {
        $this->setCurrentInstitutionVariable();
        return parent::beforeAction($action);
    }

    private function setCurrentInstitutionVariable()
    {
        $institution_id = $this->request->getParam('institution_id');
        $this->current_institution = Institution::model()->findByPk($institution_id) ?:
            (!$this->checkAccess('admin') ? Institution::model()->getCurrent() : null);
    }

    public function actionEdit()
    {
        if (\Yii::app()->request->isPostRequest) {
            $signatories = $this->updateSignaturesFromPost();
        } else {
            $signatories = \SecondarySignatory::model()->findAllAtLevels(
                $this->current_institution ? ReferenceData::LEVEL_INSTITUTION : ReferenceData::LEVEL_INSTALLATION,
                null,
                $this->current_institution
            );
        }

        $data_provider = new CActiveDataProvider('SecondarySignatory');
        $data_provider->setData($signatories);

        $this->render(
            '/signatures/edit', [
                'signatories' => $signatories,
                'data_provider' => $data_provider
            ]
        );
    }

    private function updateSignaturesFromPost()
    {
        $signatories_post = \Yii::app()->request->getPost('SecondarySignatory', []);
        $current_institution_id = \Yii::app()->request->getPost('institution_id');

        $signatories = [];
        $display_order = 1;
        $delete_except_ids = [];
        foreach($signatories_post as $signatory_post) {
            $signatory = SecondarySignatory::model()->findOrNew($signatory_post['id']);
            $signatory->name = $signatory_post['name'];
            $signatory->active = $signatory_post['active'] ?? 0;
            $signatory->display_order = $display_order;
            $signatory->institution_id = $current_institution_id;
            $is_saved = $signatory->save();

            if ($is_saved) {
                $delete_except_ids[] = $signatory->id;
            }

            $signatories[] = $signatory;
            $display_order++;
        }

        $this->deleteSignatures($delete_except_ids, $current_institution_id);

        return $signatories;
    }

    public function deleteSignatures($ids_top_keep, $institution_id)
    {
        $criteria = null;
        if ($ids_top_keep) {
            $criteria = new CDbCriteria();
            $criteria->addNotInCondition('id', array_map(function ($id) {
                return $id;
            }, $ids_top_keep));
        }

        $to_delete = SecondarySignatory::model()->findAllAtLevels(
            $institution_id ? ReferenceData::LEVEL_INSTITUTION : ReferenceData::LEVEL_INSTALLATION,
            $criteria,
            $this->current_institution
        );

        foreach ($to_delete as $item) {
            $item->delete();
        }
    }

    public function actions()
    {
        return [
            'sortConditions' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => SecondarySignatory::model(),
                'modelName' => 'SecondarySignatory',
            ],
        ];
    }
}
