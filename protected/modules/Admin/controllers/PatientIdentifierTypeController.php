<?php

class PatientIdentifierTypeController extends BaseAdminController
{
    public function actionIndex()
    {
        Audit::add('admin-PatientIdentifierType', 'list');

        $criteria = new \CDbCriteria();
        $search = [];
        $search['institution'] = \Yii::app()->request->getQuery('institution', '');
        $search['site'] = \Yii::app()->request->getQuery('site', '');

        if ($search['institution']) {
            $criteria->addCondition('institution_id = :institution_id');
            $criteria->params[':institution_id'] = $search['institution'];
        }

        if ($search['site']) {
            $criteria->addCondition('site_id = :site_id');
            $criteria->params[':site_id'] = $search['site'];
        }

        $patient_identifier_type_model = PatientIdentifierType::model();
        $pagination = $this->initPagination($patient_identifier_type_model, $criteria);

        $this->render('index', [
            'patient_identifier_types' => $patient_identifier_type_model->findAll($criteria),
            'pagination' => $pagination,
            'element' => $patient_identifier_type_model,
            'search' => $search
        ]);
    }

    public function actionEdit()
    {
        $patient_identifier_type_id = Yii::app()->request->getParam('patient_identifier_type_id');
        $patient_identifier_type = PatientIdentifierType::model()->findByPk($patient_identifier_type_id);
        if (!$patient_identifier_type) {
            throw new Exception('Patient identifier type not found: ' . $patient_identifier_type_id);
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $patient_identifier_type->attributes = Yii::app()->request->getPost('PatientIdentifierType');

            if (!$patient_identifier_type->save()) {
                $errors = $patient_identifier_type->getErrors();
            }

            if (empty($errors)) {
                Audit::add('admin-PatientIdentifierType', 'edit', $patient_identifier_type_id);
                $this->redirect('/Admin/PatientIdentifierType/index');
            }
        } else {
            Audit::add('admin-PatientIdentifierType', 'view', $patient_identifier_type_id);
        }

        $this->render('edit', [
            'patient_identifier_type' => $patient_identifier_type,
            'errors' => $errors,
        ]);
    }

    public function actionAdd()
    {
        $errors = [];
        $patient_identifier_type = new PatientIdentifierType();

        if (Yii::app()->request->isPostRequest) {
            $patient_identifier_type->attributes = Yii::app()->request->getPost('PatientIdentifierType');

            if (!$patient_identifier_type->save()) {
                $errors = $patient_identifier_type->getErrors();
            }

            if (empty($errors)) {
                Audit::add('admin-PatientIdentifierType', 'add', $patient_identifier_type->id);
                $this->redirect('/Admin/PatientIdentifierType/index');
            }
        }

        $this->render('edit', [
            'patient_identifier_type' => $patient_identifier_type,
            'errors' => $errors,
        ]);
    }

    public function actionDelete()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', Yii::app()->request->getPost('patient_identifier_types'));

        $transaction = Yii::app()->db->beginTransaction();
        try {
            foreach (PatientIdentifierType::model()->findAll($criteria) as $pit) {
                if (!$pit->delete()) {
                    $transaction->rollback();
                    echo '0';
                    return;
                }
            }
            $transaction->commit();
        } catch (Exception $exception){
            $transaction->rollback();
            echo '0';
            return;


        }

        Audit::add('admin-PatientIdentifierType', 'delete');

        echo '1';
    }

}
