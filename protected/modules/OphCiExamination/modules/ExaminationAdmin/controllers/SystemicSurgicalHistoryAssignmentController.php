<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

use OEModule\OphCiExamination\models\SystemicSurgerySet;
use OEModule\OphCiExamination\modules\ExaminationAdmin\controllers\BaseAssignmentController;

class SystemicSurgicalHistoryAssignmentController extends BaseAssignmentController
{
    public $entry_model_name = 'OEModule\OphCiExamination\models\SystemicSurgerySetEntry';
    public $set_model_name = 'OEModule\OphCiExamination\models\SystemicSurgerySet';

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            ['allow', 'users' => ['@']],
        ];
    }

    public function actionIndex()
    {
        $model = new SystemicSurgerySet();
        $model->unsetAttributes();
        $this->render('/systemicsurgicalhistoryassignment/index', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $errors = false;
        $model = new SystemicSurgerySet();

        if (\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/systemicsurgicalhistoryassignment/edit', [
            'model' => $model,
            'errors' => $errors,
            'title' => 'Create Required Systemic Surgical History Set',
        ]);
    }

    public function actionUpdate($id)
    {
        $errors = false;
        $model = $this->loadModel($id);

        if (\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/systemicsurgicalhistoryassignment/edit', [
            'model' => $model,
            'errors' => $errors,
            'title' => 'Edit Required Systemic Surgical History Set',
        ]);
    }

    /**
     * @param $id
     * @return \CActiveRecord
     * @throws \CHttpException
     */
    private function loadModel($id)
    {
        $model = SystemicSurgerySet::model()->findByPk($id);
        if ($model === null) {
            throw new \CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }
}
