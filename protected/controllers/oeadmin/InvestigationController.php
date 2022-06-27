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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
*/

/**
 * Class InvestigationsController.
 */
class InvestigationController extends BaseAdminController
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

    public $group = 'Investigation Management';

    /**
     * Lists investigations.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $criteria = new CDbCriteria();
        $search = \Yii::app()->request->getPost('search', ['query' => '']);

        if (Yii::app()->request->isPostRequest) {
            $query = trim($search['query']);
            if ($search['query']) {
                $criteria->together = true;

                $criteria->addSearchCondition('name', $query, true, 'OR');
                $criteria->addSearchCondition('snomed_code', $query, true, 'OR');
                $criteria->addSearchCondition('snomed_term', $query, true, 'OR');
                $criteria->addSearchCondition('ecds_code', $query, true, 'OR');
                $criteria->addSearchCondition('specialty_id', $query, true, 'OR');
            }
        }
        $investigation = \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model();

        $this->render('/oeadmin/investigation/index', [
            'pagination' => $this->initPagination($investigation, $criteria),
            'investigations' => $investigation->findAll($criteria),
            'search' => $search,
        ]);
    }

    /**
     * @param \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes $investigation - investigation to look for dependencies
     * @return bool|int - true if there are no tables depending on the given investigation
     */
    protected function isInvestigationDeletable(\OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes $investigation)
    {
        $check_dependencies = 1;

        $options = [':id' => $investigation->id];
        $check_dependencies &= !\OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry::model()->count('investigation_code = :id', $options);
        return $check_dependencies;
    }

    /**
     * Edits or adds an Investigation.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $errors = [];
        $criteria = new CDbCriteria();
        $criteria->together = true;

        $investigation = \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model()->findByPk($id, $criteria);
        if (!$investigation) {
            $investigation = new \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes();
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST
            $user_data = Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_Investigation_Codes');
            // set user data
            $investigation->name = $user_data['name'];
            $investigation->ecds_code = $user_data['ecds_code'];
            $investigation->specialty_id = $user_data['specialty_id'];
            $investigation->snomed_code = $user_data['snomed_code'];
            $investigation->snomed_term = $user_data['snomed_term'];

            $user_comments_data = Yii::app()->request->getPost('OEModule_OphCiExamination_models_InvestigationComments');

            // before saving the comments, delete all the existing comments
            \OEModule\OphCiExamination\models\InvestigationComments::model()->deleteAll('investigation_code = :investigation_code', array(':investigation_code' => $id));

            foreach ($user_comments_data['comments'] as $comment) {
                $investigationComments = new \OEModule\OphCiExamination\models\InvestigationComments();
                $investigationComments->investigation_code = $id;
                $investigationComments->comments = $comment;
                $investigationComments->save();
            }

            // try saving the data
            if (!$investigation->save()) {
                $errors = $investigation->getErrors();
            } else {
                $this->redirect('/oeadmin/investigation/list/');
            }
        }

        $this->render('/oeadmin/investigation/edit', array(
            'investigation' => $investigation,
            'errors' => $errors,
        ));
    }


    /**
     * Deletes rows from the model.
     */
    public function actionDelete()
    {
        $investigations = \Yii::app()->request->getPost('select', []);

        foreach ($investigations as $investigation_id) {
            $investigation = \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model()->findByPk($investigation_id);

            if ($investigation && $this->isInvestigationDeletable($investigation)) {
                if (!$investigation->save()) {
                    echo 'Could not save investigation.';
                    return;
                }
                if (!$investigation->delete()) {
                    echo 'Could not delete investigation.';
                    return;
                }
            } else {
                echo 'Investigation Code cannot be deleted. Other tables depend on it.';
            }
        }
        echo 1;
    }
}
