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
class ProcedureController extends BaseController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'roles' => array('OprnViewClinical'),
            ),
        );
    }

    protected function beforeAction($action)
    {
        // Sample code to be used when RBAC is fully implemented.
//      if (!Yii::app()->user->checkAccess('admin')) {
//          throw new CHttpException(403, 'You are not authorised to perform this action.');
//      }

        return parent::beforeAction($action);
    }

    /**
     * Lists all disorders for a given search term.
     */
    public function actionAutocomplete()
    {
        echo CJavaScript::jsonEncode(Procedure::getList($_GET['term'], @$_GET['restrict']));
    }

    public function actionDetails()
    {
        $name = \Yii::app()->request->getParam('name');
        $proc = $name ? Procedure::model()->findByAttributes(['term' => $name]) : null;
        if ($proc) {
            $this->renderPartial(
                '_ajaxProcedure',
                array(
                    'proc' => $proc,
                    'durations' => \Yii::app()->request->getParam('durations'),
                    'identifier' => \Yii::app()->request->getParam('identifier'),
                )
            );
        }
    }

    public function actionList()
    {
        if (!empty($_POST['subsection'])) {
            $criteria = new CDbCriteria();
            $criteria->select = 't.id, term, short_format';
            $criteria->join = 'LEFT JOIN proc_subspecialty_subsection_assignment pssa ON t.id = pssa.proc_id';
            $criteria->compare('pssa.subspecialty_subsection_id', $_POST['subsection']);
            $criteria->compare('pssa.institution_id', Yii::app()->session['selected_institution_id']);
            $criteria->order = 'term asc';

            $procedures = Procedure::model()->active()->findAll($criteria);
            $view = '_procedureOptions';
            if (!empty($_POST['dialog'])) {
                $view = '_procedureDialogOptions';
            }

            $this->renderPartial($view, array('procedures' => $procedures), false, false);
        }
    }

    public function actionBenefits($id)
    {
        if (!Procedure::model()->findByPk($id)) {
            throw new Exception("Unknown procedure: $id");
        }

        $benefits = array();

        foreach (Yii::app()->db->createCommand()
            ->select('b.name')
            ->from('benefit b')
            ->join('procedure_benefit pb', 'pb.benefit_id = b.id')
            ->where("pb.proc_id = $id and b.active = 1")
            ->order('b.name asc')
            ->queryAll() as $row) {
            $benefits[] = $row['name'];
        }

        $this->renderJSON($benefits);
    }

    public function actionComplications($id)
    {
        if (!Procedure::model()->findByPk($id)) {
            throw new Exception("Unknown procedure: $id");
        }

        $complications = array();

        foreach (Yii::app()->db->createCommand()
            ->select('b.name')
            ->from('complication b')
            ->join('procedure_complication pb', 'pb.complication_id = b.id')
            ->where("pb.proc_id = $id and b.active = 1")
            ->order('b.name asc')
            ->queryAll() as $row) {
            $complications[] = $row['name'];
        }

        $this->renderJSON($complications);
    }
}
