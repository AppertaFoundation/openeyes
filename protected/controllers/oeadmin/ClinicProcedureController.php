<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class ProceduresController.
 */
use OEModule\OphCiExamination\models\OphCiExamination_ClinicProcedure;

class ClinicProcedureController extends BaseAdminController
{
    public $admin;

    public $layout = 'admin';

    public $itemsPerPage = 30;

    public $group = 'Procedure Management';

    public function actionList()
    {
        $procedures = Procedure::model()->findAll(['condition' => 'is_clinic_proc = 1', 'order' => 'term asc']);

        $this->render('/oeadmin/clinicprocedures/index', [
            'procedures' => $procedures,
        ]);
    }

    public function actionEdit($id)
    {
        $errors = [];

        $procedure = Procedure::model()->findByPk($id);
        $clinic_prcedure = $procedure->clinic_procedure;
        if (!$clinic_prcedure) {
            $clinic_prcedure = new OphCiExamination_ClinicProcedure();
        }

        if (isset($_POST[CHtml::modelName($clinic_prcedure)])) {
            $clinic_prcedure->proc_id = $id;
            $clinic_prcedure->attributes = $_POST[CHtml::modelName($clinic_prcedure)];

            if ($clinic_prcedure->save()) {
                Audit::add('admin', 'update', serialize($clinic_prcedure->attributes), false, array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_ClinicProcedure'));
                Yii::app()->user->setFlash('success', 'Clinic Procedure updated');
                $this->redirect(array('list'));
            }
        }

        $this->render('/oeadmin/clinicprocedures/edit', [
            'procedure' => $procedure,
            'clinic_procedure' => $clinic_prcedure,
            'errors' => $errors,
        ]);
    }
}
