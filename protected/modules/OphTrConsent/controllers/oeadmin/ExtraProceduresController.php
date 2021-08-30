<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class ExtraProcedures.
 */

class ExtraProceduresController extends BaseAdminController
{

    public $group = 'Consent form';

    /**
     * Lists Extra procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {

        $search = \Yii::app()->request->getPost('search', ['query' => '', 'active' => '']);
        $criteria = new CDbCriteria();
        if (Yii::app()->request->isPostRequest) {
            $query = trim($search['query']);
            if ($search['query']) {
                $criteria->params[':query'] = $query;

                $criteria->with = array(
                    'opcsCodes' => array(
                        'select' => false,
                    )
                );
                $criteria->together = true;

                $criteria->addSearchCondition('term', $query, true, 'OR');
                $criteria->addSearchCondition('snomed_code', $query, true, 'OR');
                $criteria->addSearchCondition('opcsCodes.name', $query, true, 'OR');
                $criteria->addCondition('default_duration != 0 AND default_duration  = :query', 'OR');
                $criteria->addSearchCondition('aliases', $query, true, 'OR');
            }

            if ($search['active'] == 1) {
                $criteria->addCondition('t.active = 1');
            } elseif ($search['active'] != '') {
                $criteria->addCondition('t.active != 1');
            }
        }
        $procedure = OphTrConsent_Extra_Procedure::model(); //model for extra procedure

        $this->render('/oeadmin/ExtraProcedures/index', [
            'pagination' => $this->initPagination($procedure, $criteria),
            'procedures' => $procedure->findAll($criteria),
            'search' => $search,
        ]);
    }

    /**
     * Edit or add a Procedure.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $errors = [];
        $criteria = new CDbCriteria();
        $criteria->with = ['opcsCodes', 'benefits', 'complications', 'risks'];
        $criteria->together = true;

        $procedure = OphTrConsent_Extra_Procedure::model()->findByPk($id, $criteria);

        if (!$procedure) {
            $procedure = new OphTrConsent_Extra_Procedure();
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST

            $user_data = \Yii::app()->request->getPost('OphTrConsent_Extra_Procedure');
            $user_opcs_cods = \Yii::app()->request->getPost('opcs_codes', []);
            $user_benefits = \Yii::app()->request->getPost('benefits');
            $user_complications = \Yii::app()->request->getPost('complications');
            $user_notes = \Yii::app()->request->getPost('notes', []);
            $user_risks = \Yii::app()->request->getPost('risks', []);

            // set user data
            $procedure->term = $user_data['term'];
            $procedure->short_format = $user_data['short_format'];
            $procedure->default_duration = $user_data['default_duration'];
            $procedure->snomed_code = $user_data['snomed_code'];
            $procedure->snomed_term = $user_data['snomed_term'];
            $procedure->aliases = $user_data['aliases'];
            $procedure->unbooked = $user_data['unbooked'];
            $procedure->active = $user_data['active'];
            $procedure->institution_id = \Yii::app()->request->getPost('Institutions');
            $procedure->proc_id = \Yii::app()->request->getPost('Procedure');


            // set notes
            $notes = [];
            if (isset($user_notes)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_notes));
                $notes = ElementType::model()->findAll($criteria);
            }
            $procedure->operationNotes = $notes;

            // set opcs_cods
            $opcsCodes = [];
            if (isset($user_opcs_cods)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_opcs_cods));
                $opcsCodes = OPCSCode::model()->findAll($criteria);
            }
            $procedure->opcsCodes = $opcsCodes;

            // set benefits
            $benefits = [];
            if (isset($user_benefits)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_benefits));
                $benefits = Benefit::model()->findAll($criteria);
            }
            $procedure->benefits = $benefits;

            // set complications
            $complications = [];
            if (isset($user_complications)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_complications));
                $complications = Complication::model()->findAll($criteria);
            }
            $procedure->complications = $complications;

            $risks = [];
            if (isset($user_risks)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($user_risks));
                $risks = \OEModule\OphCiExamination\models\OphCiExaminationRisk::model()->findAll($criteria);
            }
            $procedure->risks = $risks;

            // try saving the data
            if (!$procedure->save()) {
                $errors = $procedure->getErrors();
            } else {
                $this->redirect('/OphTrConsent/oeadmin/ExtraProcedures/list/index/');
            }
        }

        $this->render('/oeadmin/ExtraProcedures/edit', array(
            'procedure' => $procedure,
            'opcs_code' => OPCSCode::model()->findAll(),
            'benefits' => Benefit::model()->findAll(),
            'procedurelist' => Procedure::model()->findAll(),
            'complications' => Complication::model()->findAll(),
            'risks' => \OEModule\OphCiExamination\models\OphCiExaminationRisk::model()->findAll(),
            'notes' => ElementType::model()->findAll('event_type_id=?', array(EventType::model()->find('class_name=?', array('OphTrOperationnote'))->id)),
            'errors' => $errors,
        ));
    }

    /**
     * @param Procedure $procedure - procedure to look for dependencies
     * @return bool|int - true if there are no tables depending on the given procedure
     */
    protected function isProcedureDeletable(OphTrConsent_Extra_Procedure $procedure)
    {
        $check_dependencies = 1;

        $options = [':id' => $procedure->id];
        $check_dependencies &= !Element_OphTrOperationnote_GenericProcedure::model()->count('proc_id = :id', $options);
        $check_dependencies &= !EtOphtrconsentProcedureProceduresProcedures::model()->count('proc_id = :id', $options);
        $check_dependencies &= !EtOphtrconsentProcedureAddProcsAddProcs::model()->count('proc_id = :id', $options);
        $check_dependencies &= !OphTrOperationbooking_Operation_Procedures::model()->count('proc_id = :id', $options);
        $check_dependencies &= !OphTrLaser_LaserProcedure::model()->count('procedure_id = :id', $options);
        $check_dependencies &= !OphTrLaser_LaserProcedureAssignment::model()->count('procedure_id = :id', $options);

        return $check_dependencies;
    }

    /**
     * Deletes rows from the model.
     */
    public function actionDelete()
    {
        $procedures = \Yii::app()->request->getPost('select', []);
        print_r($procedures);
        exit();
        foreach ($procedures as $procedure_id) {
            $procedure = OphTrConsent_Extra_Procedure::model()->findByPk($procedure_id);

            if ($procedure && $this->isProcedureDeletable($procedure)) {
                $procedure->specialties = [];
                $procedure->subspecialtySubsections = [];
                $procedure->opcsCodes = [];
                $procedure->additional = [];
                $procedure->benefits = [];
                $procedure->complications = [];

                if (!$procedure->save()) {
                    echo 'Could not save procedure.';
                    return;
                }
                if (!$procedure->delete()) {
                    echo 'Could not delete procedure.';
                    return;
                }
            } else {
                echo 'Procedure cannot be deleted. Other tables depend on it.';
            }
        }
        echo 1;
    }
}
