<?php

class SupplementaryConsentController extends BaseAdminController
{

    public $group = 'Consent form';

    /**
     * Lists Ophtrconsent_SupplementaryConsentQuestions.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $criteria = new CDbCriteria();
        $search = \Yii::app()->request->getPost('search', ['query' => '', 'active' => '']);

        $query_lowercase = strtolower($search['query']);

        $criteria->distinct = true;

        $criteria->join = 'LEFT JOIN ophtrconsent_sup_consent_question_assignment qa ON qa.question_id = t.id ' .
                        'LEFT JOIN ophtrconsent_sup_consent_question_answer qan ON qan.question_assignment_id = qa.id ' .
                        'LEFT JOIN institution i ON i.id = qa.institution_id ' .
                        'LEFT JOIN site st ON st.id = qa.site_id ' .
                        'LEFT JOIN subspecialty sbs ON sbs.id = qa.subspecialty_id ' .
                        'LEFT JOIN ophtrconsent_type_type ft ON ft.id = qa.form_id';

        $criteria->addSearchCondition('LOWER(`t`.`name`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`t`.`description`)', $query_lowercase, true, 'OR');

        $criteria->addSearchCondition('LOWER(`qa`.`question_text`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`qa`.`question_info`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`qa`.`question_output`)', $query_lowercase, true, 'OR');

        $criteria->addSearchCondition('LOWER(`qan`.`name`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`qan`.`display`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`qan`.`answer_output`)', $query_lowercase, true, 'OR');

        $criteria->addSearchCondition('LOWER(`i`.`name`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`st`.`name`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`sbs`.`name`)', $query_lowercase, true, 'OR');
        $criteria->addSearchCondition('LOWER(`ft`.`name`)', $query_lowercase, true, 'OR');

        if ($search['active'] !== '') {
            $criteria->compare('`qa`.`active`', (int)$search['active']);
        }

        $suppleConsent = Ophtrconsent_SupplementaryConsentQuestion::model();

        $this->render('/oeadmin/supplementaryconsent/index', [
            'pagination' => $this->initPagination($suppleConsent, $criteria),
            'suppleConsent' => $suppleConsent->findAll($criteria),
            'search' => $search,
        ]);
    }

    /**
     * Edits or adds a SupplementaryConsent.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEditAnswer($id = false)
    {
        $errors = [];

        $q_assign = Ophtrconsent_SupplementaryConsentQuestionAnswer::model()->findByPk($id);


        if (!$q_assign) {
            $q_assign = new Ophtrconsent_SupplementaryConsentQuestionAnswer();

            $q_assign->question_assignment_id = \Yii::app()->request->getParam('question_assignment_id');

            $existing = Ophtrconsent_SupplementaryConsentQuestionAnswer::model()->findAll('question_assignment_id=?', array($q_assign->question_assignment_id));

            $max_order = array_reduce($existing, static function($order, $answer) {
                return max($order, $answer->display_order);
            }, 0);

            $q_assign->display_order = $max_order + 1;
        }

        if (Yii::app()->request->isPostRequest) {
            $assignment_data = \Yii::app()->request->getPost('Ophtrconsent_SupplementaryConsentQuestionAnswer');

            $q_assign->attributes = $assignment_data;

            // try saving the data
            if (!$q_assign->save()) {
                $errors = $q_assign->getErrors();
            } else {
                $this->redirect('/OphTrConsent/oeadmin/SupplementaryConsent/editAssignment/' . $q_assign->question_assignment_id);
            }
        }

        $this->render('/oeadmin/supplementaryconsent/editAnswer', array(
            'q_assign' => $q_assign,
            'errors' => $errors,
        ));
    }

    /**
     * Edits or adds a Ophtrconsent_SupplementaryConsentQuestionAssignment
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEditAssignment($id = false)
    {
        $errors = [];

        $q_assign = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->findByPk($id);

        if (!$q_assign) {
            $q_assign = new Ophtrconsent_SupplementaryConsentQuestionAssignment();

            $q_assign->question_id = \Yii::app()->request->getParam('question_id');
        }

        if (Yii::app()->request->isPostRequest) {
            $assignment_data = \Yii::app()->request->getPost('Ophtrconsent_SupplementaryConsentQuestionAssignment');

            $q_assign->attributes = $assignment_data;

            // try saving the data
            if (!$q_assign->save()) {
                $errors = $q_assign->getErrors();
            } else {
                $this->redirect('/OphTrConsent/oeadmin/SupplementaryConsent/edit/' . $q_assign->question_id);
            }
        }

        $this->render('/oeadmin/supplementaryconsent/editAssignment', array(
            'q_assign' => $q_assign,
            'errors' => $errors,
        ));
    }
    /**
     * Edits or adds a Ophtrconsent_SupplementaryConsentQuestion.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $errors = [];

        $suppleconsent = Ophtrconsent_SupplementaryConsentQuestion::model()->findByPk($id);


        if (!$suppleconsent) {
            $suppleconsent = new Ophtrconsent_SupplementaryConsentQuestion();
        }
        if (Yii::app()->request->isPostRequest) {
            $user_data = \Yii::app()->request->getPost('Ophtrconsent_SupplementaryConsentQuestion');

            $suppleconsent->attributes = $user_data;

            $suppleconsent->question_type_id = $user_data['question_type_id'];

            // try saving the data
            if (!$suppleconsent->save()) {
                $errors = $suppleconsent->getErrors();
            } else {
                $this->redirect('/OphTrConsent/oeadmin/SupplementaryConsent/list/');
            }
        }
        $this->render('/oeadmin/supplementaryconsent/edit', array(
            'suppleconsent' => $suppleconsent,
            'errors' => $errors,
        ));
    }

    public function getNameFromID($id, $modelName)
    {

        return $modelName::model()->findByAttributes(
            array(
                'id' => $id,
            )
        )->name;
    }
}
