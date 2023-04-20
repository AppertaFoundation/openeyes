<?php

class ContextController extends BaseAdminController
{
    // public $defaultAction = 'firms';

    public function actionIndex()
    {
        Audit::add('admin-Firm', 'list');
        $criteria = new \CDbCriteria();
        $search = [];
        $search['query'] = \Yii::app()->request->getQuery('query');
        $search['active'] = \Yii::app()->request->getQuery('active');
        $search['institution_id'] = \Yii::app()->request->getQuery('institution_id');
        if (isset($search['query'])) {
            if (is_numeric($search['query'])) {
                $criteria->addCondition('id = :id');
                $criteria->params[':id'] = $search['query'];
            } else {
                $criteria->addSearchCondition('pas_code', $search['query'], true, 'OR');
                $criteria->addSearchCondition('cost_code', $search['query'], true, 'OR');
                $criteria->addSearchCondition('name', $search['query'], true, 'OR');
            }
        }
        if (isset($search['active'])) {
            if ((int)$search['active'] === 1) {
                $criteria->addCondition('active = 1');
            } elseif ($search['active'] !== '') {
                $criteria->addCondition('active != 1');
            }
        } else {
            $search['active'] = 1;
            $criteria->addCondition('active = 1');
        }

        $current_institution = Yii::app()->session->getSelectedInstitution();

        if (!$this->checkAccess('admin')) {
            $search['institution_id'] = isset($current_institution) ? $current_institution->id : null;
        }

        $reference_level = $search['institution_id'] ? ReferenceData::LEVEL_INSTITUTION : ReferenceData::LEVEL_INSTALLATION;

        // eliminate uncessary DB query if search institution is the same as current institution
        if (isset($current_institution) && $current_institution->id == $search['institution_id']) {
            $institution = $current_institution;
        } else {
            if (isset($search['institution_id'])) {
                $institution = Institution::model()->findByPk($search['institution_id']);
            } else {
                $institution = null;
            }
        }

        $this->render('index', array(
            'pagination' => $this->initPagination(
                Firm::model(),
                Firm::model()->getCriteriaForLevels($reference_level, $criteria, $institution)
            ),
            'firms' => Firm::model()->findAllAtLevels($reference_level, $criteria, $institution),
            'search' => $search
        ));
    }

    /**
     * @throws Exception
     */
    public function actionAdd()
    {
        $firm = new Firm();

        if (!empty($_POST)) {
            $firm->attributes = $_POST['Firm'];

            if (!$this->checkAccess('admin')) {
                $firm->institution_id = Yii::app()->session['selected_institution_id'];
            }

            if (!$firm->validate()) {
                $errors = $firm->getErrors();
            } else {
                if (!$firm->save()) {
                    throw new Exception('Unable to save firm: ' . print_r($firm->getErrors(), true));
                }
                Audit::add('admin-Firm', 'add', $firm->id);
                $this->redirect('/Admin/context/' . ceil($firm->id / $this->items_per_page));
            }
        }

        $this->render('edit', array(
            'firm' => $firm,
            'errors' => @$errors,
            'subspecialties_list_data' => CHtml::listData(Subspecialty::model()->findAll(['order' => 'name']), 'id', 'name'),
            'consultant_list_data' => CHtml::listData(User::model()->findAll(['order' => 'first_name,last_name']), 'id', 'fullName'),
        ));
    }

    /**
     * @throws Exception
     */
    public function actionEdit($id)
    {
        $firm = Firm::model()->findByPk($id);
        if (!$firm) {
            throw new Exception("Firm not found: $id");
        }

        $firm->subspecialty_id = $firm->getSubspecialtyID();

        if (!empty($_POST)) {
            $firm->attributes = $_POST['Firm'];
            if (!$firm->validate()) {
                $errors = $firm->getErrors();
            } else {
                if (!$firm->save()) {
                    throw new Exception('Unable to save firm: ' . print_r($firm->getErrors(), true));
                }
                Audit::add('admin-Firm', 'edit', $firm->id);
                $this->redirect('/Admin/context/' . ceil($firm->id / $this->items_per_page));
            }
        } else {
            Audit::add('admin-Firm', 'view', $id);
        }

        $siteSecretaries = array();
        if (isset(Yii::app()->modules['OphCoCorrespondence'])) {
            $firmSiteSecretaries = new FirmSiteSecretary();
            $site_secretaries = $firmSiteSecretaries->findSiteSecretaryForFirm($id);
            $firmSiteSecretaries->firm_id = $id;
            $siteSecretaries[] = $firmSiteSecretaries;
        }

        $this->render('edit', array(
            'firm' => $firm,
            'errors' => @$errors,
            'siteSecretaries' => $site_secretaries,
            'subspecialties_list_data' => CHtml::listData(Subspecialty::model()->findAll(['order' => 'name']), 'id', 'name'),
            'consultant_list_data' => CHtml::listData(User::model()->findAll(['order' => 'first_name,last_name']), 'id', 'fullName'),
            'newSiteSecretary' => new FirmSiteSecretary(),
        ));
    }
}
