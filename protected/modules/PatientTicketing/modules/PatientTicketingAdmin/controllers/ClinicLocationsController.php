<?php

use OEModule\PatientTicketing\models\QueueSet;

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ClinicLocationsController extends BaseAdminController
{
    public $group = 'PatientTicketing';

    public function actionIndex()
    {
        // for admin it returns the requested Institution,
        // for non-admins it returns the current institution
        $institution = $this->getInstitutionFromRequest();
        $queueset_id = \Yii::app()->request->getParam('queueset_id');

        if (\Yii::app()->request->isPostRequest) {
            $models = $this->save();
        } else {
            $criteria = new \CDbCriteria();
            $criteria->with = ['institutions'];
            $criteria->addCondition('institutions.id = :institution_id');
            $criteria->params = [':institution_id' => $institution->id];

            if ($queueset_id) {
                $criteria->addCondition('t.queueset_id = :queueset_id');
                $criteria->params[':queueset_id'] = $queueset_id;
            } else {
                $criteria->addCondition('t.queueset_id IS NULL');
            }

            $models = OEModule\PatientTicketing\models\ClinicLocation::model()->findAll($criteria);
        }

        $this->render('/cliniclocation/index', [
            'options' => $models,
            'institution' => $institution,
            'queueset_id' => $queueset_id
        ]);
    }

    public function save()
    {
        $queueset_id = \Yii::app()->request->getParam('queueset_id');
        $data = \Yii::app()->request->getParam('OEModule_PatientTicketing_models_ClinicLocation', []);
        $institution_id = \Yii::app()->request->getParam('institution_id');
        $current_institution = \Institution::model()->getCurrent();

        if (!$this->checkAccess('admin') && ($institution_id != $current_institution->id)) {
            $this->redirect(['/PatientTicketing/PatientTicketingAdmin/ClinicLocations/index']);
        }

        $criteria = new \CDbCriteria();
        $criteria->with = ['institutions'];
        $criteria->addCondition('institutions.id = :institution_id');
        $criteria->addCondition('t.queueset_id = :queueset_id');
        $criteria->params = [':institution_id' => $institution_id];
        $criteria->params[':queueset_id'] = $queueset_id;
        $original_options = OEModule\PatientTicketing\models\ClinicLocation::model()->findAll($criteria);

        $errors = [];
        $models = [];
        $transaction = Yii::app()->db->beginTransaction();
        $step = 0;
        foreach ($data as $entry) {
            $model = OEModule\PatientTicketing\models\ClinicLocation::model()->findOrNew($entry['id'] ?? null);
            $model->attributes = $entry;
            $model->display_order = $step;
            $models[] = $model;
            if ($model->save()) {
                $model->addToInstitution($institution_id);
            } else {
                $errors[] = $model->getErrors();
            }
            $step++;
        }
        if (!empty($errors)) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }

        // we only delete if we know the institution_id for sure
        if ($institution_id && $original_options) {
            $collection = new \ModelCollection($original_options);
            $to_delete = $collection->diff(array_column($data, 'id'));

            foreach ($to_delete as $model) {
                $model->delete();
            }
        }

        return $models;
    }

    private function getInstitutionFromRequest()
    {
        $current_institution = \Institution::model()->getCurrent();
        $institution_id =
            $this->checkAccess('admin')
                ? \Yii::app()->request->getParam('institution_id', $current_institution->id) :
                $current_institution->id;

        return Institution::model()->findByPk($institution_id);
    }

    /**
     * Get QueueSets by Institution
     *
     * @param int $institution_id
     * @return QueueSet[]
     */
    public function getQueueSets(int $institution_id): array
    {
        $criteria = new \CDbCriteria();
        $criteria->with = ['institutions'];
        $criteria->addCondition('institutions.id = :institution_id');
        $criteria->params = [':institution_id' => $institution_id];

        return QueueSet::model()->findAll($criteria);
    }
}
