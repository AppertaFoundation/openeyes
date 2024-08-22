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
                $criteria->together = true;

                $criteria->addSearchCondition('term', $query, true, 'OR');
                $criteria->addSearchCondition('snomed_code', $query, true, 'OR');
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
        $criteria->with = ['benefits', 'complications'];
        $criteria->together = true;

        $procedure = OphTrConsent_Extra_Procedure::model()->findByPk($id, $criteria);

        if (!$procedure) {
            $procedure = new OphTrConsent_Extra_Procedure();
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST

            $extra_proc_data = \Yii::app()->request->getPost('OphTrConsent_Extra_Procedure');
            $extra_proc_benefits = \Yii::app()->request->getPost('benefits');
            $extra_proc_complications = \Yii::app()->request->getPost('complications');

            // set extra proc data
            $procedure->term = $extra_proc_data['term'];
            $procedure->short_format = $extra_proc_data['short_format'];
            $procedure->snomed_code = $extra_proc_data['snomed_code'];
            $procedure->snomed_term = $extra_proc_data['snomed_term'];
            $procedure->aliases = $extra_proc_data['aliases'];
            // set benefits
            $benefits = [];
            if (isset($extra_proc_benefits)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($extra_proc_benefits));
                $benefits = Benefit::model()->findAll($criteria);
                \Yii::log(\CVarDumper::dumpAsString($benefits));
            }
            $procedure->benefits = $benefits;

            // set complications
            $complications = [];
            if (isset($extra_proc_complications)) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', array_values($extra_proc_complications));
                $complications = Complication::model()->findAll($criteria);
            }
            $procedure->complications = $complications;

            // try saving the data
            if (!$procedure->save()) {
                $errors = $procedure->getErrors();
            } else {
                $this->redirect('/OphTrConsent/oeadmin/ExtraProcedures/list/index/');
            }
        }

        $this->render('/oeadmin/ExtraProcedures/edit', array(
            'procedure' => $procedure,
            'benefits' => Benefit::model()->findAll(),
            'complications' => Complication::model()->findAll(),
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
        $check_dependencies &= !OphTrConsent_Extra_Procedure_subspecialty_assignment::model()->count('extra_proc_id = :id', $options);
        $check_dependencies &= !OphTrConsent_Procedure_Extra_Assignment::model()->count('extra_proc_id = :id', $options);

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

    public function actionEditSubspecialty()
    {
        $procedures = OphTrConsent_Extra_Procedure::model()->findAll(['order' => 'term']);
        $procedure_options = array_map(
            function ($procedure) {
                return $procedure->getAttributes(["id", "term"]);
            },
            $procedures
        );
        $this->jsVars['procedure_options'] = $procedure_options;

        if ($this->checkAccess('admin')) {
            $institutions = Institution::model()->getTenanted();
            $institution_options = array_map(
                static function ($institution) {
                    return $institution->getAttributes(["id", "name"]);
                },
                $institutions
            );
        } else {
            $institution = Institution::model()->getCurrent();
            $institution_options = [$institution->getAttributes(["id", "name"])];
        }

        $this->jsVars['institution_options'] = $institution_options;

        $subspecialty_id = Yii::app()->getRequest()->getParam('subspecialty_id', null);

        if (Yii::app()->request->isPostRequest) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $display_orders = Yii::app()->request->getParam('display_order', []);
                $assignments = Yii::app()->request->getParam('OphTrConsent_Extra_Procedure_subspecialty_assignment', []);

                $ids = [];
                foreach ($assignments as $key => $assignment) {
                    $procedureSubspecialtyAssignment = OphTrConsent_Extra_Procedure_subspecialty_assignment::model()->findByPk($assignment['id']);
                    if (!$procedureSubspecialtyAssignment) {
                        $procedureSubspecialtyAssignment = new OphTrConsent_Extra_Procedure_subspecialty_assignment();
                        $procedureSubspecialtyAssignment['id'] = null;
                    }

                    $procedureSubspecialtyAssignment->extra_proc_id = $assignment['extra_proc_id'];
                    $procedureSubspecialtyAssignment->display_order = $display_orders[$key];
                    $procedureSubspecialtyAssignment->subspecialty_id = Yii::app()->request->getParam('subspecialty_id', null);

                    if ($this->checkAccess('admin')) {
                        $procedureSubspecialtyAssignment->institution_id = $assignment['institution_id'];
                    } else {
                        $procedureSubspecialtyAssignment->institution_id = Institution::model()->getCurrent()->id;
                    }
                    if (!$procedureSubspecialtyAssignment->save()) {
                        $errors[] = $procedureSubspecialtyAssignment->getErrors();
                    }

                    $ids[] = $procedureSubspecialtyAssignment->id;
                }

                // Delete items
                $criteria = new CDbCriteria();

                if ($ids) {
                    $criteria->addNotInCondition('id', $ids);
                }

                $criteria->compare('subspecialty_id', $subspecialty_id);

                if (!$this->checkAccess('admin')) {
                    $criteria->compare('institution_id', Institution::model()->getCurrent()->id);
                }
                $to_delete = OphTrConsent_Extra_Procedure_subspecialty_assignment::model()->findAll($criteria);

                foreach ($to_delete as $item) {
                    if (!$item->delete()) {
                        $errorMessage = "Model ProcedureSubspecialtyAssignment could not be deleted";
                        $errors[] = ['id' => [$errorMessage]];
                        \OELog::log($errorMessage . " (ID = $item->id )");
                    } else {
                        Audit::add('admin', 'delete', $item->primaryKey, null, [
                            'module' => (is_object($this->module)) ? $this->module->id : 'core',
                            'model' => OphTrConsent_Extra_Procedure_subspecialty_assignment::getShortModelName(),
                        ]);
                    }
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                \OELog::log($errorMessage);
                $errors[] = ['id' => [$errorMessage]];
            }

            if (empty($errors)) {
                $transaction->commit();
                Yii::app()->user->setFlash('success', 'List updated.');
            } else {
                $transaction->rollback();
                foreach ($errors as $error) {
                    foreach ($error as $attribute => $error_array) {
                        $display_errors = '<strong>' . $procedureSubspecialtyAssignment->getAttributeLabel($attribute) .
                            ':</strong> ' . implode(', ', $error_array);
                        Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                    }
                }
            }
            $this->redirect(Yii::app()->request->url);
        }

        $generic_admin = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/GenericAdmin.js', true);
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        $criteria = new CDbCriteria();
        $criteria->order = 'display_order';
        $criteria->compare('subspecialty_id', $subspecialty_id);
        if (!$this->checkAccess('admin')) {
            $criteria->compare('institution_id', Institution::model()->getCurrent()->id);
        }

        if ((int)OphTrConsent_Extra_Procedure_subspecialty_assignment::model()->count($criteria) === 0) {
            $criteria->condition = '';
            $criteria->params = array();
            $criteria->compare('subspecialty_id', $subspecialty_id);
        }
        $this->render('/oeadmin/ExtraProcedures/edit_ExtraProcedureSubspecialtyAssignment', [
            'dataProvider' => new CActiveDataProvider('OphTrConsent_Extra_Procedure_subspecialty_assignment', [
                'criteria' => $criteria,
                'pagination' => false,
            ]),
            'subspecialty_id' => $subspecialty_id,
            'subspecialities' => Subspecialty::model()->findAll(),
            'procedure_list' => $procedures,
            'field_name' => 'OphTrConsent_Extra_Procedure_subspecialty_assignment'
        ]);
    }
}
