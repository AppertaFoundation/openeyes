<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class ProceduresController.
 */
class ProcedureController extends BaseAdminController
{
    /**
     * @var holds the Admin() object as the generic admin view refers $this->admin
     */
    public $admin;

    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 30;

    /**
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $criteria = new CDbCriteria();
        $search = \Yii::app()->request->getPost('search', ['query' => '', 'active' => '']);

        if (Yii::app()->request->isPostRequest) {
            if ($search['query']) {
                $criteria->params[':query'] = $search['query'];

                $criteria->with = array(
                    'opcsCodes' => array(
                        'select' => false,
                        'joinType' => 'INNER JOIN',
                    )
                );
                $criteria->together = true;

                $criteria->addCondition('term = :query', 'OR');
                $criteria->addCondition('snomed_code = :query', 'OR');
                $criteria->addCondition('opcsCodes.name = :query', 'OR');
                $criteria->addCondition('default_duration = :query', 'OR');
                $criteria->addCondition('aliases = :query', 'OR');
            }

            if ($search['active'] == 1) {
                $criteria->addCondition('t.active = 1');
            } elseif ($search['active'] != '') {
                $criteria->addCondition('t.active != 1');
            }
        }

        $procedure = Procedure::model();

        $this->render('/oeadmin/procedure/index', [
            'pagination' => $this->initPagination($procedure, $criteria),
            'procedures' => $procedure->findAll($criteria),
            'search' => $search,
        ]);
    }

    /**
     * Edits or adds a Procedure.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $procedure = Procedure::model();
        $errors = [];

        $criteria = new CDbCriteria();
        $criteria->with = ['opcsCodes', 'benefits', 'complications'];
        $criteria->together = true;

        $procedure_object = $procedure->findByPk($id, $criteria);

        if (!$procedure_object) {
            $this->redirect('/oeadmin/procedure/list/');
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST
            $user_data = \Yii::app()->request->getPost('Procedure');
            $user_opcs_cods = \Yii::app()->request->getPost('opcs_codes', []);
            $user_benefits = \Yii::app()->request->getPost('benefits');
            $user_complications = \Yii::app()->request->getPost('complications');
            $user_notes = \Yii::app()->request->getPost('notes', []);

            // set user data
            $procedure_object->term = $user_data['term'];
            $procedure_object->short_format = $user_data['short_format'];
            $procedure_object->default_duration = $user_data['default_duration'];
            $procedure_object->snomed_code = $user_data['snomed_code'];
            $procedure_object->aliases = $user_data['aliases'];
            $procedure_object->unbooked = $user_data['unbooked'];
            $procedure_object->active = $user_data['active'];

            // set notes
            $notes = [];
            if (isset($user_notes)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_notes));
                $notes = ElementType::model()->findAll($criteria);
            }
            $procedure_object->operationNotes = $notes;

            // set opcs_cods
            $opcsCodes = [];
            if (isset($user_opcs_cods)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_opcs_cods));
                $opcsCodes = OPCSCode::model()->findAll($criteria);
            }
            $procedure_object->opcsCodes = $opcsCodes;

            // set benefits
            $benefits = [];
            if (isset($user_benefits)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_benefits));
                $benefits = Benefit::model()->findAll($criteria);
            }
            $procedure_object->benefits = $benefits;

            // set complications
            $complications = [];
            if (isset($user_complications)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_complications));
                $complications = Benefit::model()->findAll($criteria);
            }
            $procedure_object->complications = $complications;

            // try saving the data
            if (!$procedure_object->save()) {
                $errors = $procedure_object->getErrors();
            } else {
                $this->redirect('/oeadmin/procedure/list/');
            }
        }

        $this->render('/oeadmin/procedure/edit', array(
            'procedure' => $procedure_object,
            'opcs_code' => OPCSCode::model()->findAll(),
            'benefits' => Benefit::model()->findAll(),
            'complications' => Complication::model()->findAll(),
            'notes' => ElementType::model()->findAll(),
            'errors' => $errors,
        ));
    }

    /**
     * Deletes rows from the model.
     */
    public function actionDelete()
    {
        $procedures = \Yii::app()->request->getPost('select', []);

        foreach ($procedures as $procedure_id) {
            $procedure = Procedure::model()->findByPk($procedure_id);

            if ($procedure) {
                $procedure->specialties = [];
                $procedure->subspecialtySubsections = [];
                $procedure->opcsCodes = [];
                $procedure->additional = [];
                $procedure->benefits = [];
                $procedure->complications = [];

                try {
                    $procedure->save();
                    $procedure->delete();
                } catch (Exception $e) {
                    echo "error";
                    return;
                }
            }
        }
        echo 1;
    }
}
