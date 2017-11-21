<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class AdminController extends BaseAdminController
{
    public $layout = 'admin';
    public $items_per_page = 30;

    /**
     * @var int
     */
    public $displayOrder = 0;

    public function actionIndex()
    {
        $this->redirect(array('/admin/users'));
    }

    public function actionEditPreviousOperation()
    {
        $this->genericAdmin('Edit Previous Ophthalmic Surgery Choices', 'CommonPreviousOperation');
    }

    public function actionEditCommonOphthalmicDisorderGroups()
    {
        $this->genericAdmin('Common Ophthalmic Disorder Groups', 'CommonOphthalmicDisorderGroup');
    }

    public function actionEditCommonOphthalmicDisorder()
    {
        $models = CommonOphthalmicDisorderGroup::model()->findAll();
        $data = array_map(create_function('$model','return $model->getAttributes(array("id", "name"));'), $models);
        $this->jsVars['common_ophthalmic_disorder_group_options'] = $data;

        $errors = array();
        $subspecialty_id = Yii::app()->request->getParam('subspecialty_id', 1);

        if( Yii::app()->request->isPostRequest){

            $transaction = Yii::app()->db->beginTransaction();

            $display_orders = Yii::app()->request->getParam('display_order', array());
            $disorders = Yii::app()->request->getParam('CommonOphthalmicDisorder', array());

            $ids = array();
            foreach($disorders as $key => $disorder){

                if(!$common_ophtalmic_disorder = CommonOphthalmicDisorder::model()->findByPk($disorder['id'])){
                    $common_ophtalmic_disorder = new CommonOphthalmicDisorder;
                    $disorder['id'] = null;
                }

                $common_ophtalmic_disorder->attributes = $disorder;
                $common_ophtalmic_disorder->display_order = $display_orders[$key];

                //$_GET['subspecialty_id'] must be present, we do not use the default value 1
                $common_ophtalmic_disorder->subspecialty_id = isset($_GET['subspecialty_id']) ? $_GET['subspecialty_id'] : null;

                if(!$common_ophtalmic_disorder->save()){
                    $errors[] = $common_ophtalmic_disorder->getErrors();
                }

                $ids[$common_ophtalmic_disorder->id] = $common_ophtalmic_disorder->id;
            }

            if(empty($errors)){

                //Delete items
                $criteria = new CDbCriteria();

                if ($ids) {
                    $criteria->addNotInCondition('id', array_map(function ($id) {
                        return $id;
                    }, $ids));
                }

                $criteria->compare('subspecialty_id', $subspecialty_id);

                $to_delete = CommonOphthalmicDisorder::model()->findAll($criteria);
                foreach ($to_delete as $item) {
                    if (!$item->delete()) {
                        throw new Exception("Unable to delete CommonOphthalmicDisorder:{$item->primaryKey}");
                    }
                    Audit::add('admin', 'delete', $item->primaryKey, null, array(
                        'module' => (is_object($this->module)) ? $this->module->id : 'core',
                        'model' => CommonOphthalmicDisorder::getShortModelName(),
                    ));
                }

                $transaction->commit();

                Yii::app()->user->setFlash('success', 'List updated.');

            } else {

                foreach($errors as $error){
                    foreach($error as $attribute => $error_array){
                        $display_errors = '<strong>'.$common_ophtalmic_disorder->getAttributeLabel($attribute) . ':</strong> ' . implode(', ', $error_array);
                        Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                    }
                }

                $transaction->rollback();

            }
            $this->redirect(Yii::app()->request->url);
        }

        // end of handling the POST


        $generic_admin = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/GenericAdmin.js');
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.DiagnosesSearch.js'), ClientScript::POS_END);

        $criteria = new CDbCriteria();
        $criteria->compare('subspecialty_id', $subspecialty_id);

        $this->render('editcommonophthalmicdisorder',array(
            'dataProvider' => new CActiveDataProvider('CommonOphthalmicDisorder', array(
                'criteria' => $criteria,
                'pagination' => false,
            )),
            'subspecialty_id' => $subspecialty_id,
            'subspecialty' => Subspecialty::model()->findAll()
        ));
    }

    public function actionEditSecondaryToCommonOphthalmicDisorder()
    {
        $errors = array();
        $parent_id = Yii::app()->request->getParam('parent_id', 1);

        if( Yii::app()->request->isPostRequest){
            $transaction = Yii::app()->db->beginTransaction();

            $display_orders = Yii::app()->request->getParam('display_order', array());
            $disorders = Yii::app()->request->getParam('SecondaryToCommonOphthalmicDisorder', array());

            $ids = array();
            foreach($disorders as $key => $disorder){

                if(!$common_ophtalmic_disorder = SecondaryToCommonOphthalmicDisorder::model()->findByPk($disorder['id'])){
                    $common_ophtalmic_disorder = new SecondaryToCommonOphthalmicDisorder;
                    $disorder['id'] = null;
                }

                $common_ophtalmic_disorder->attributes = $disorder;
                $common_ophtalmic_disorder->display_order = $display_orders[$key];

                //$_GET['parent_id'] must be present, we do not use the default value 1
                $common_ophtalmic_disorder->parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;

                if(!$common_ophtalmic_disorder->save()){
                    $errors[] = $common_ophtalmic_disorder->getErrors();
                }

                $ids[$common_ophtalmic_disorder->id] = $common_ophtalmic_disorder->id;
            }

            if(empty($errors)){

                //Delete items
                $criteria = new CDbCriteria();

                if ($ids) {
                    $criteria->addNotInCondition('id', array_map(function ($id) {
                        return $id;
                    }, $ids));
                }

                $criteria->compare('parent_id', $parent_id);

                $to_delete = SecondaryToCommonOphthalmicDisorder::model()->findAll($criteria);
                foreach ($to_delete as $item) {
                    if (!$item->delete()) {
                        throw new Exception("Unable to delete SecondaryToCommonOphthalmicDisorder:{$item->primaryKey}");
                    }
                    Audit::add('admin', 'delete', $item->primaryKey, null, array(
                        'module' => (is_object($this->module)) ? $this->module->id : 'core',
                        'model' => SecondaryToCommonOphthalmicDisorder::getShortModelName(),
                    ));
                }

                $transaction->commit();

                Yii::app()->user->setFlash('success', 'List updated.');

            } else {

                foreach($errors as $error){
                    foreach($error as $attribute => $error_array){
                        $display_errors = '<strong>'.$common_ophtalmic_disorder->getAttributeLabel($attribute) . ':</strong> ' . implode(', ', $error_array);
                        Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                    }
                }

                $transaction->rollback();

            }
            $this->redirect(Yii::app()->request->url);
        }

        $generic_admin = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/GenericAdmin.js');
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.DiagnosesSearch.js'), ClientScript::POS_END);

        $criteria = new CDbCriteria();
        $criteria->compare('parent_id', $parent_id);

        $this->render('editSecondaryToCommonOphthalmicdisorder',array(
            'dataProvider' => new CActiveDataProvider('SecondaryToCommonOphthalmicDisorder', array(
                'criteria' => $criteria,
                'pagination' => false,
            )),
            'parent_id' => $parent_id,
        ));
    }

    public function actionManageFindings()
    {
        $this->genericAdmin(
            'Findings',
            'Finding',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'subspecialties',
                        'type' => 'multilookup',
                        'noSelectionsMessage' => 'All Subspecialties',
                        'htmlOptions' => array(
                            'empty' => '- Please Select -',
                            'nowrapper' => true,
                        ),
                        'options' => \CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name'),
                    ),
                    array(
                        'field' => 'requires_description',
                        'type' => 'boolean',
                    ),
                ),
            )
        );
    }

    public function actionDrugs()
    {
        $criteria = new CDbCriteria();
        if (isset($_REQUEST['search'])) {
            $criteria->compare('name', $_REQUEST['search'], true);
        }
        $pagination = $this->initPagination(Drug::model(), $criteria);
        $this->render('/admin/drugs', array(
            'drugs' => Drug::model()->findAll($criteria),
            'pagination' => $pagination,
        ));
    }

    public function actionAddDrug()
    {
        return; //disabled OE-4474
        $drug = new Drug('create');

        if (!empty($_POST)) {
            $drug->attributes = $_POST['Drug'];

            if (!$drug->validate()) {
                $errors = $drug->getErrors();
            } else {
                if (!$drug->save()) {
                    throw new Exception('Unable to save drug: ' . print_r($drug->getErrors(), true));
                }

                if (isset($_POST['allergies'])) {
                    $posted_allergy_ids = $_POST['allergies'];

                    //add new allergy mappings
                    foreach ($posted_allergy_ids as $asign) {
                        $allergy_assignment = new DrugAllergyAssignment();
                        $allergy_assignment->drug_id = $drug->id;
                        $allergy_assignment->allergy_id = $asign;
                        $allergy_assignment->save();
                    }
                }

                $this->redirect('/admin/drugs/' . ceil($drug->id / $this->items_per_page));
            }
        }

        $this->render('/admin/adddrug', array(
            'drug' => $drug,
            'errors' => @$errors,
        ));
    }

    public function actionEditDrug($id)
    {
        return; //disabled OE-4474
        if (!$drug = Drug::model()->findByPk($id)) {
            throw new Exception("Drug not found: $id");
        }
        $drug->scenario = 'update';

        if (!empty($_POST)) {
            $drug->attributes = $_POST['Drug'];

            if (!$drug->validate()) {
                $errors = $drug->getErrors();
            } else {
                if (!$drug->save()) {
                    throw new Exception('Unable to save drug: ' . print_r($drug->getErrors(), true));
                }

                $posted_allergy_ids = array();

                if (isset($_POST['allergies'])) {
                    $posted_allergy_ids = $_POST['allergies'];
                }

                $criteria = new CDbCriteria();
                $criteria->compare('drug_id', $drug->id);
                $allergy_assignments = DrugAllergyAssignment::model()->findAll($criteria);

                $allergy_assignment_ids = array();
                foreach ($allergy_assignments as $allergy_assignment) {
                    $allergy_assignment_ids[] = $allergy_assignment->allergy_id;
                }

                $allergy_assignment_ids_to_delete = array_diff($allergy_assignment_ids, $posted_allergy_ids);
                $posted_allergy_ids_to_assign = array_diff($posted_allergy_ids, $allergy_assignment_ids);

                //add new allergy mappings
                foreach ($posted_allergy_ids_to_assign as $asign) {
                    $allergy_assignment = new DrugAllergyAssignment();
                    $allergy_assignment->drug_id = $drug->id;
                    $allergy_assignment->allergy_id = $asign;
                    $allergy_assignment->save();
                }

                //delete redundant allergy mappings
                foreach ($allergy_assignments as $asigned) {
                    if (in_array($asigned->allergy_id, $allergy_assignment_ids_to_delete)) {
                        $asigned->delete();
                    }
                }

                $this->redirect('/admin/drugs/' . ceil($drug->id / $this->items_per_page));
            }
        }

        $this->render('/admin/editdrug', array(
            'drug' => $drug,
            'errors' => @$errors,
        ));
    }

    public function actionUserFind()
    {
        $res = array();
        if (Yii::app()->request->isAjaxRequest && !empty($_REQUEST['search'])) {
            $criteria = new CDbCriteria();
            $criteria->compare('LOWER(username)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(first_name)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(last_name)', strtolower($_REQUEST['search']), true, 'OR');
            foreach (User::model()->findAll($criteria) as $user) {
                $res[] = array(
                    'id' => $user->id,
                    'label' => $user->getFullName() . '(' . $user->username . ')',
                    'value' => $user->getFullName(),
                    'username' => $user->username,
                );
            }
        }
        echo CJSON::encode($res);
    }

    public function actionUsers($id = false)
    {
        Audit::add('admin-User', 'list');

        $criteria = new CDbCriteria();
        if (!empty($_GET['search'])) {
            $criteria->compare('LOWER(username)', strtolower($_GET['search']), true, 'OR');
            $criteria->compare('LOWER(first_name)', strtolower($_GET['search']), true, 'OR');
            $criteria->compare('LOWER(last_name)', strtolower($_GET['search']), true, 'OR');
            $criteria->compare('LOWER(id)', $_GET['search'], false, 'OR');
        }

        $pagination = $this->initPagination(User::model(), $criteria);
        $search = !empty($_GET['search']) ? $_GET['search'] : '';
        $this->render('/admin/users', array(
            'users' => User::model()->findAll($criteria),
            'pagination' => $pagination,
            'search' => $search,
        ));
    }

    /**
     * Add a user
     *
     * @throws Exception
     */
    public function actionAddUser()
    {
        $user = new User();
        $request = Yii::app()->getRequest();

        if ($request->getIsPostRequest()) {
            $userAtt = $request->getPost('User');

            $user->attributes = $userAtt;

            if (!$user->validate()) {
                $errors = $user->getErrors();
            } else {
                if (!$user->save()) {
                    throw new Exception('Unable to save user: ' . print_r($user->getErrors(), true));
                }

                if (!$contact = $user->contact) {
                    $contact = new Contact();
                }

                $contact->title = $user->title;
                $contact->first_name = $user->first_name;
                $contact->last_name = $user->last_name;
                $contact->qualifications = $user->qualifications;

                if (!$contact->save()) {
                    throw new Exception('Unable to save user contact: ' . print_r($contact->getErrors(), true));
                }

                if (!$user->contact) {
                    $user->contact_id = $contact->id;

                    if (!$user->save()) {
                        throw new Exception('Unable to save user: ' . print_r($user->getErrors(), true));
                    }
                }

                Audit::add('admin-User', 'add', $user->id);

                if (!isset($userAtt['roles']) || ( empty($userAtt['roles']))) {
                    $userAtt['roles'] = array();
                }

                if (!array_key_exists('firms', $userAtt) || !is_array($userAtt['firms'])) {
                    $userAtt['firms'] = array();
                }

                $user->saveRoles($userAtt['roles']);

                try {
                    $user->saveFirms($userAtt['firms']);
                    $this->redirect('/admin/users/' . ceil($user->id / $this->items_per_page));
                } catch (FirmSaveException $e) {
                    $user->addError('global_firm_rights', 'When no global firm rights is set, a firm must be selected');
                    $errors = $user->getErrors();
                }
            }
        }

        $user->password = '';

        $this->render('/admin/adduser', array(
            'user' => $user,
            'errors' => @$errors,
        ));
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function actionEditUser($id)
    {
        if (!$user = User::model()->findByPk($id)) {
            throw new Exception("User not found: $id");
        }

        $request = Yii::app()->getRequest();

        if ($request->getIsPostRequest()) {
            $userAtt = $request->getPost('User');

            if (empty($userAtt['password'])) {
                unset($userAtt['password']);
            }
            $user->attributes = $userAtt;

            if (!$user->validate()) {
                $errors = $user->getErrors();
            } else {

                if (!$user->save()) {
                    throw new Exception('Unable to save user: ' . print_r($user->getErrors(), true));
                }

                if (!$contact = $user->contact) {
                    $contact = new Contact();
                }

                $contact->title = $user->title;
                $contact->first_name = $user->first_name;
                $contact->last_name = $user->last_name;
                $contact->qualifications = $user->qualifications;

                if (!$contact->save()) {
                    throw new Exception('Unable to save user contact: ' . print_r($contact->getErrors(), true));
                }

                if (!$user->contact) {
                    $user->contact_id = $contact->id;

                    if (!$user->save()) {
                        throw new Exception('Unable to save user: ' . print_r($user->getErrors(), true));
                    }
                }

                Audit::add('admin-User', 'edit', $user->id);

                if (!isset($userAtt['roles']) || (empty($userAtt['roles']))) {
                    $userAtt['roles'] = array();
                }

                if (!array_key_exists('firms', $userAtt) || !is_array($userAtt['firms'])) {
                    $userAtt['firms'] = array();
                }

                $user->saveRoles($userAtt['roles']);

                try {
                    $user->saveFirms($userAtt['firms']);
                    $this->redirect('/admin/users/' . ceil($user->id / $this->items_per_page));
                } catch (FirmSaveException $e) {
                    $user->addError('global_firm_rights', 'When no global firm rights is set, a firm must be selected');
                    $errors = $user->getErrors();
                }
            }
        } else {
            Audit::add('admin-User', 'view', $id);
        }

        $user->password = '';

        $this->render('/admin/edituser', array(
            'user' => $user,
            'errors' => @$errors,
        ));
    }

    public function actionDeleteUsers()
    {
        $result = 1;

        if (!empty($_POST['users'])) {
            foreach (User::model()->findAllByPk($_POST['users']) as $user) {
                try {
                    if (!$user->delete()) {
                        $result = 0;
                    }
                } catch (Exception $e) {
                    $result = 0;
                }

                if ($result) {
                    Audit::add('admin-User', 'delete');
                }
            }
        }

        echo $result;
    }

    /**
     * @param bool $id
     *
     * @throws Exception
     */
    public function actionFirms()
    {
        Audit::add('admin-Firm', 'list');
        $search = new ModelSearch(Firm::model());
        $search->addSearchItem('name', array(
            'type' => 'compare',
            'compare_to' => array(
                'id',
                'pas_code',
                'consultant.first_name',
                'consultant.last_name',
                'serviceSubspecialtyAssignment.subspecialty.name',
            ),
        ));
        $search->addSearchItem('active', array('type' => 'boolean'));

        $this->render('/admin/firms', array(
            'pagination' => $search->initPagination(),
            'firms' => $search->retrieveResults(),
            'search' => $search,
            'displayOrder' => $this->displayOrder,
        ));
    }

    /**
     * @throws Exception
     */
    public function actionAddFirm()
    {
        $firm = new Firm();

        if (!empty($_POST)) {
            $firm->attributes = $_POST['Firm'];

            if (!$firm->validate()) {
                $errors = $firm->getErrors();
            } else {
                if (!$firm->save()) {
                    throw new Exception('Unable to save firm: ' . print_r($firm->getErrors(), true));
                }
                Audit::add('admin-Firm', 'add', $firm->id);
                $this->redirect('/admin/firms/' . ceil($firm->id / $this->items_per_page));
            }
        }

        $this->render('/admin/editfirm', array(
            'firm' => $firm,
            'errors' => @$errors,
        ));
    }

    public function actionEditFirm($id)
    {
        if (!$firm = Firm::model()->findByPk($id)) {
            throw new Exception("Firm not found: $id");
        }

        if (!empty($_POST)) {
            $firm->attributes = $_POST['Firm'];

            if (!$firm->validate()) {
                $errors = $firm->getErrors();
            } else {
                if (!$firm->save()) {
                    throw new Exception('Unable to save firm: ' . print_r($firm->getErrors(), true));
                }
                Audit::add('admin-Firm', 'edit', $firm->id);
                $this->redirect('/admin/firms/' . ceil($firm->id / $this->items_per_page));
            }
        } else {
            Audit::add('admin-Firm', 'view', $id);
        }

        $siteSecretaries = array();
        if (isset(Yii::app()->modules['OphCoCorrespondence'])) {
            $firmSiteSecretaries = new FirmSiteSecretary();
            $siteSecretaries = $firmSiteSecretaries->findSiteSecretaryForFirm($id);
            $firmSiteSecretaries->firm_id = $id;
            $siteSecretaries[] = $firmSiteSecretaries;
        }

        $this->render('/admin/editfirm', array(
            'firm' => $firm,
            'errors' => @$errors,
            'siteSecretaries' => $siteSecretaries,
        ));
    }

    public function actionLookupUser()
    {
        Yii::app()->event->dispatch('lookup_user', array('username' => $_GET['username']));

        if ($user = User::model()->find('username=?', array($_GET['username']))) {
            echo $user->id;
        } else {
            echo 'NOTFOUND';
        }
    }

    public function actionContacts($id = false)
    {
        $contacts = $this->searchContacts();
        Audit::add('admin-Contact', 'list');

        $this->render('/admin/contacts', array('contacts' => @$contacts));
    }

    public function actionContactlabels($id = false)
    {
        Audit::add('admin-ContactLabel', 'list');

        $criteria = new CDbCriteria();
        $pagination = $this->initPagination(ContactLabel::model(), $criteria);

        $this->render('/admin/contactlabels', array(
            'contactlabels' => ContactLabel::model()->findAll($criteria),
            'pagination' => $pagination,
        ));
    }

    public function searchContacts()
    {
        $criteria = new CDbCriteria();
        Audit::add('admin-Contact', 'search', @$_GET['q']);

        $ex = explode(' ', @$_GET['q']);

        if (empty($ex)) {
            throw new Exception("Empty search query string, this shouldn't happen");
        }

        $criteria->addCondition('t.first_name != :blank or t.last_name != :blank');
        $criteria->params[':blank'] = '';

        if (count($ex) == 1) {
            $criteria->addSearchCondition('lower(`t`.first_name)', strtolower(@$_GET['q']), false);
            $criteria->addSearchCondition('lower(`t`.last_name)', strtolower(@$_GET['q']), false, 'OR');
        } elseif (count($ex) == 2) {
            $criteria->addSearchCondition('lower(`t`.first_name)', strtolower(@$ex[0]), false);
            $criteria->addSearchCondition('lower(`t`.last_name)', strtolower(@$ex[1]), false);
        } elseif (count($ex) >= 3) {
            $criteria->addSearchCondition('lower(`t`.title)', strtolower(@$ex[0]), false);
            $criteria->addSearchCondition('lower(`t`.first_name)', strtolower(@$ex[1]), false);
            $criteria->addSearchCondition('lower(`t`.last_name)', strtolower(@$ex[2]), false);
        }

        if (@$_GET['label']) {
            $criteria->compare('contact_label_id', @$_GET['label']);
        }

        $criteria->order = 'title, first_name, last_name';
        $pagination = $this->initPagination(Contact::model(), $criteria);

        $contacts = Contact::model()->findAll($criteria);

        if (count($contacts) == 1) {
            foreach ($contacts as $contact) {
            }
            $this->redirect(array('/admin/editContact?contact_id=' . $contact->id));

            return;
        }

        return array(
            'contacts' => $contacts,
            'pagination' => $pagination,
        );
    }

    public function actionEditContact($id = null)
    {
        if ($id == null) {
            $id = @$_GET['contact_id'];
        }

        if (!$contact = Contact::model()->findByPk($id)) {
            throw new Exception('Contact not found: ' . $id);
        }

        if (!empty($_POST)) {
            $contact->attributes = $_POST['Contact'];

            if (!$contact->validate()) {
                $errors = $contact->getErrors();
            } else {
                if (!$contact->save()) {
                    throw new Exception('Unable to save contact: ' . print_r($contact->getErrors(), true));
                }
                Audit::add('admin-Contact', 'edit', $contact->id);
                $this->redirect('/admin/contacts?q=' . $contact->fullName);
            }
        } else {
            Audit::add('admin-Contact', 'view', $id);
        }

        $this->render('/admin/editcontact', array(
            'contact' => $contact,
            'errors' => @$errors,
        ));
    }

    public function actionContactLocation()
    {
        if (!$cl = ContactLocation::model()->findByPk(@$_GET['location_id'])) {
            throw new Exception('ContactLocation not found: ' . @$_GET['location_id']);
        }

        Audit::add('admin-ContactLocation', 'view', @$_GET['location_id']);

        $this->render('/admin/contactlocation', array(
            'location' => $cl,
        ));
    }

    public function actionRemoveLocation()
    {
        if (!$cl = ContactLocation::model()->findByPk(@$_POST['location_id'])) {
            throw new Exception('ContactLocation not found: ' . @$_POST['location_id']);
        }

        if (count($cl->patients) > 0) {
            echo '0';

            return;
        }

        if (!$cl->delete()) {
            echo '-1';

            return;
        }

        Audit::add('admin-ContactLocation', 'delete', @$_POST['location_id']);

        return '1';
    }

    public function actionAddContactLocation()
    {
        if (!$contact = Contact::model()->findByPk(@$_GET['contact_id'])) {
            throw new Exception('Contact not found: ' . @$_GET['contact_id']);
        }

        $errors = array();
        $sites = array();

        if (!empty($_POST)) {
            if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
                $errors['institution_id'] = array('Please select an institution');
            } else {
                $sites = $institution->sites;
            }

            if (empty($errors)) {
                $cl = new ContactLocation();
                $cl->contact_id = $contact->id;

                if ($site = Site::model()->findByPk(@$_POST['site_id'])) {
                    $cl->site_id = $site->id;
                } else {
                    $cl->institution_id = $institution->id;
                }

                if (!$cl->save()) {
                    $errors = array_merge($errors, $cl->getErrors());
                } else {
                    Audit::add('admin-ContactLocation', 'add', $cl->id);
                    $this->redirect(array('/admin/editContact?contact_id=' . $contact->id));
                }
            }
        }

        $this->render('/admin/addcontactlocation', array(
            'contact' => $contact,
            'errors' => $errors,
            'sites' => $sites,
        ));
    }

    public function actionGetInstitutionSites()
    {
        if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
            throw new Exception('Institution not found: ' . @$_GET['institution_id']);
        }

        Audit::add('admin-Institution>Site', 'view', @$_GET['institution_id']);

        echo json_encode(CHtml::listData($institution->sites, 'id', 'name'));
    }

    public function actionInstitutions($id = false)
    {
        Audit::add('admin-Institution', 'list');

        $search = new ModelSearch(Institution::model());
        $search->addSearchItem('name', array(
            'type' => 'compare',
            'compare_to' => array(
                'remote_id',
                'short_name',
            ),
        ));
        $search->addSearchItem('active', array('type' => 'boolean'));

        $this->render('/admin/institutions', array(
            'pagination' => $search->initPagination(),
            'institutions' => $search->retrieveResults(),
            'search' => $search,
        ));
    }

    public function actionAddInstitution()
    {
        $institution = new Institution();
        $address = new Address();

        $errors = array();

        if (!empty($_POST)) {
            $institution->attributes = $_POST['Institution'];

            if (!$institution->validate()) {
                $errors = $institution->getErrors();
            }

            $address->attributes = $_POST['Address'];

            if ($address->validate()) {
                $errors = array_merge($errors, $address->getErrors());
            }

            if (empty($errors)) {
                if (!$institution->save()) {
                    throw new Exception('Unable to save institution: ' . print_r($institution->getErrors(), true));
                }

                $address->contact_id = $institution->contact_id;

                if (!$address->save()) {
                    throw new Exception('Unable to save institution address: ' . print_r($address->getErrors(), true));
                }
                $institution->addAddress($address);

                if (!$institution->contact->save()) {
                    throw new Exception('Institution contact could not be saved: ' . print_r($institution->contact->getErrors(),
                            true));
                }

                Audit::add('admin-Institution', 'add', $institution->id);

                $this->redirect(array('/admin/editInstitution?institution_id=' . $institution->id));
            }
        }

        $this->render('/admin/addinstitution', array(
            'institution' => $institution,
            'address' => $address,
            'errors' => @$errors,
        ));
    }

    public function actionEditInstitution()
    {
        if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
            throw new Exception('Institution not found: ' . @$_GET['institution_id']);
        }

        $errors = array();
        $address = $institution->contact->address;
        if (!$address) {
            $address = new Address();
        }
        if (!empty($_POST)) {
            $institution->attributes = $_POST['Institution'];

            if (!$institution->validate()) {
                $errors = $institution->getErrors();
            }

            $address = $institution->contact->address;

            $address->attributes = $_POST['Address'];

            if (!$address->validate()) {
                $errors = array_merge(@$errors, $address->getErrors());
            }

            if (empty($errors)) {
                if (!$institution->save()) {
                    throw new Exception('Unable to save institution: ' . print_r($institution->getErrors(), true));
                }
                if (!$address->save()) {
                    throw new Exception('Unable to save institution address: ' . print_r($address->getErrors(), true));
                }

                Audit::add('admin-Institution', 'edit', $institution->id);

                $this->redirect('/admin/institutions');
            }
        } else {
            Audit::add('admin-Institution', 'view', @$_GET['institution_id']);
        }

        $this->render('/admin/editinstitution', array(
            'institution' => $institution,
            'address' => $address,
            'errors' => $errors,
        ));
    }

    public function actionSites($id = false)
    {
        Audit::add('admin-Site', 'list');

        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN contact ON contact_id = contact.id'
            . ' join address on address.contact_id = contact.id';

        if (!empty($_REQUEST['search'])) {
            $criteria->compare('LOWER(name)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(short_name)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(remote_id)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(postcode)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(address1)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(address2)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(city)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(county)', strtolower($_REQUEST['search']), true, 'OR');
        }

        $pagination = $this->initPagination(Site::model(), $criteria);

        $this->render('/admin/sites', array(
            'sites' => Site::model()->findAll($criteria),
            'pagination' => $pagination,
        ));
    }

    public function actionAddSite()
    {
        $errors = array();
        $site = new Site();
        $contact = new Contact();
        $address = new Address();

        /*
         * Set default blank contact to fulfill the current relationship with a site
         */

        $contact->nick_name = 'NULL';
        $contact->primary_phone = 'NULL';
        $contact->title = null;
        $contact->first_name = '';
        $contact->last_name = '';
        $contact->qualifications = null;

        $contact->save();

        $site->contact_id = $contact->id;
        $address->contact_id = $contact->id;

        if (!empty($_POST)) {
            $site->attributes = $_POST['Site'];

            if (!$site->validate()) {
                $errors = $site->getErrors();
            }

            $address->attributes = $_POST['Address'];

            if (!$address->validate()) {
                $errors = array_merge($errors, $address->getErrors());
            }

            if (!$errors) {
                if (!$site->save()) {
                    throw new Exception('Unable to save contact: ' . print_r($site->getErrors(), true));
                }

                if (!$address->save()) {
                    throw new Exception('Unable to save address: ' . print_r($address->getErrors(), true));
                }

                Audit::add('admin-Site', 'add', $site->id);

                $this->redirect(array('/admin/editSite?site_id=' . $site->id));
            }
        }

        $this->render('/admin/addsite', array(
            'site' => $site,
            'errors' => $errors,
            'address' => $address,
            'contact' => $contact,
        ));
    }

    public function actionEditsite()
    {
        if (!$site = Site::model()->findByPk(@$_GET['site_id'])) {
            throw new Exception('Site not found: ' . @$_GET['site_id']);
        }

        $errors = array();

        if (!empty($_POST)) {
            $site->attributes = $_POST['Site'];

            if (!$site->validate()) {
                $errors = $site->getErrors();
            }

            $address = $site->contact->address;

            $address->attributes = $_POST['Address'];

            if (!$address->validate()) {
                $errors = array_merge($errors, $address->getErrors());
            }

            if (empty($errors)) {
                if (!$site->save()) {
                    throw new Exception('Unable to save site: ' . print_r($site->getErrors(), true));
                }
                if (!$address->save()) {
                    throw new Exception('Unable to save site address: ' . print_r($address->getErrors(), true));
                }

                Audit::add('admin-Site', 'edit', $site->id);

                $this->redirect('/admin/sites');
            }
        } else {
            Audit::add('admin-Site', 'view', @$_GET['site_id']);
        }

        $this->render('/admin/editsite', array(
            'site' => $site,
            'address' => $site->contact->address,
            'errors' => $errors,
        ));
    }

    public function actionAddContact()
    {
        $contact = new Contact();

        if (!empty($_POST)) {
            $contact->attributes = $_POST['Contact'];

            if (!$contact->validate()) {
                $errors = $contact->getErrors();
            } else {
                if (!$contact->save()) {
                    throw new Exception('Unable to save contact: ' . print_r($contact->getErrors(), true));
                }
                Audit::add('admin-Contact', 'add', $contact->id);

                $this->redirect(array('/admin/editContact?contact_id=' . $contact->id));
            }
        }

        $this->render('/admin/addcontact', array(
            'contact' => $contact,
            'errors' => @$errors,
        ));
    }

    public function actionAddContactLabel()
    {
        $contactlabel = new ContactLabel();

        if (!empty($_POST)) {
            $contactlabel->attributes = $_POST['ContactLabel'];

            if (!$contactlabel->validate()) {
                $errors = $contactlabel->getErrors();
            } else {
                if (!$contactlabel->save()) {
                    throw new Exception('Unable to save contactlabel: ' . print_r($contactlabel->getErrors(), true));
                }
                Audit::add('admin-ContactLabel', 'add', $contactlabel->id);
                $this->redirect('/admin/contactlabels/' . ceil($contactlabel->id / $this->items_per_page));
            }
        }

        $this->render('/admin/addcontactlabel', array(
            'contactlabel' => $contactlabel,
            'errors' => @$errors,
        ));
    }

    public function actionEditContactLabel($id)
    {
        if (!$contactlabel = ContactLabel::model()->findByPk($id)) {
            throw new Exception("ContactLabel not found: $id");
        }

        if (!empty($_POST)) {
            $contactlabel->attributes = $_POST['ContactLabel'];

            if (!$contactlabel->validate()) {
                $errors = $contactlabel->getErrors();
            } else {
                if (!$contactlabel->save()) {
                    throw new Exception('Unable to save contactlabel: ' . print_r($contactlabel->getErrors(), true));
                }
                Audit::add('admin-ContactLabel', 'edit', $contactlabel->id);

                $this->redirect('/admin/contactlabels/' . ceil($contactlabel->id / $this->items_per_page));
            }
        } else {
            Audit::add('admin-ContactLabel', 'view', $id);
        }

        $this->render('/admin/editcontactlabel', array(
            'contactlabel' => $contactlabel,
            'errors' => @$errors,
        ));
    }

    public function actionDeleteContactLabel()
    {
        if (!$contactlabel = ContactLabel::model()->findByPk(@$_POST['contact_label_id'])) {
            throw new Exception('ContactLabel not found: ' . @$_POST['contact_label_id']);
        }

        $count = Contact::model()->count('contact_label_id=?', array($contactlabel->id));

        if ($count == 0) {
            if (!$contactlabel->delete()) {
                throw new Exception('Unable to delete ContactLabel: ' . print_r($contactlabel->getErrors(), true));
            }

            Audit::add('admin-ContactLabel', 'delete', @$_POST['contact_label_id']);
        }

        echo $count;
    }

    public function actionDataSources()
    {
        Audit::add('admin-DataSource', 'list');
        $this->render('/admin/datasources');
    }

    public function actionEditDataSource($id)
    {
        if (!$source = ImportSource::model()->findByPk($id)) {
            throw new Exception("Source not found: $id");
        }

        if (!empty($_POST)) {
            $source->attributes = $_POST['ImportSource'];

            if (!$source->validate()) {
                $errors = $source->getErrors();
            } else {
                if (!$source->save()) {
                    throw new Exception('Unable to save source: ' . print_r($source->getErrors(), true));
                }
                Audit::add('admin-DataSource', 'edit', $id);
                $this->redirect('/admin/datasources/' . ceil($source->id / $this->items_per_page));
            }
        } else {
            Audit::add('admin-DataSource', 'view', $id);
        }

        $this->render('/admin/editdatasource', array(
            'source' => $source,
            'errors' => @$errors,
        ));
    }

    public function actionAddDataSource()
    {
        $source = new ImportSource();

        if (!empty($_POST)) {
            $source->attributes = $_POST['ImportSource'];

            if (!$source->validate()) {
                $errors = $source->getErrors();
            } else {
                if (!$source->save()) {
                    throw new Exception('Unable to save data source: ' . print_r($source->getErrors(), true));
                }
                Audit::add('admin-DataSource', 'add', $source->id);
                $this->redirect('/admin/datasources');
            }
        }

        $this->render('/admin/editdatasource', array(
            'source' => $source,
            'errors' => @$errors,
        ));
    }

    public function actionDeleteDataSources()
    {
        if (!empty($_POST['source'])) {
            foreach ($_POST['source'] as $source_id) {
                if (Institution::model()->find('source_id=?', array($source_id))) {
                    echo '0';

                    return;
                }
                if (Site::model()->find('source_id=?', array($source_id))) {
                    echo '0';

                    return;
                }
                if (Person::model()->find('source_id=?', array($source_id))) {
                    echo '0';

                    return;
                }
            }

            foreach ($_POST['source'] as $source_id) {
                if ($source = ImportSource::model()->findByPk($source_id)) {
                    if (!$source->delete()) {
                        throw new Exception('Unable to delete import source: ' . print_r($source->getErrors(), true));
                    }
                }
            }

            Audit::add('admin-DataSource', 'delete');
        }

        echo '1';
    }

    public function actionDeleteFirms()
    {
        $result = 1;

        if (!empty($_POST['firms'])) {
            foreach (Firm::model()->findAllByPk($_POST['firms']) as $firm) {
                try {
                    $firm_id = $firm->id;
                    if (!$firm->delete()) {
                        $result = 0;
                    } else {
                        Audit::add('admin-Firm', 'delete', $firm_id);
                    }
                } catch (Exception $e) {
                    $result = 0;
                }
            }
        }

        echo $result;
    }

    public function actionCommissioning_bodies()
    {
        Audit::add('admin-CommissioningBody', 'list');
        $this->render('commissioning_bodies');
    }

    public function actionEditCommissioningBody()
    {
        if (isset($_GET['commissioning_body_id'])) {
            if (!$cb = CommissioningBody::model()->findByPk(@$_GET['commissioning_body_id'])) {
                throw new Exception('CommissioningBody not found: ' . @$_GET['commissioning_body_id']);
            }
            if (!$address = $cb->contact->address) {
                $address = new Address();
                $address->country_id = 1;
            }
        } else {
            $cb = new CommissioningBody();
            $address = new Address();
            $address->country_id = 1;
        }

        $errors = array();

        if (!empty($_POST)) {
            $cb->attributes = $_POST['CommissioningBody'];

            if (!$cb->validate()) {
                $errors = $cb->getErrors();
            }

            $address->attributes = $_POST['Address'];

            if (empty($errors)) {

                $transaction = Yii::app()->db->beginInternalTransaction();
                try {

                    if (!$contact = $cb->contact) {
                        $contact = new Contact();
                        if (!$contact->save()) {
                            $errors = array_merge($errors, $contact->getErrors());
                        }
                    }

                    $cb->contact_id = $contact->id;

                    $method = $cb->id ? 'edit' : 'add';

                    $audit = $_POST;

                    if ($method == 'edit') {
                        $audit['id'] = $cb->id;
                    }

                    if (!$cb->save()) {
                        $errors = array_merge($errors, $cb->getErrors());
                    }

                    $address->contact_id = $contact->id;

                    if (!$address->save()) {
                        $errors = array_merge($errors, $address->getErrors());
                    }

                    if(empty($errors)){
                        $transaction->commit();
                        Audit::add('admin-CommissioningBody', $method, $cb->id);
                        $this->redirect('/admin/commissioning_bodies');
                    } else {
                        $transaction->rollback();
                    }

                } catch (Exception $e) {
                    OELog::log($e->getMessage());
                    $transaction->rollback();
                }
            }
        } else {
            Audit::add('admin-CommissioningBody', 'view', @$_GET['commissioning_body_id']);
        }

        $this->render('/admin/editCommissioningBody', array(
            'cb' => $cb,
            'address' => $address,
            'errors' => $errors,
        ));
    }

    public function actionAddCommissioning_Body()
    {
        return $this->actionEditCommissioningBody();
    }

    public function actionVerifyDeleteCommissioningBodies()
    {
        foreach (CommissioningBody::model()->findAllByPk(@$_POST['commissioning_body']) as $cb) {
            if (!$cb->canDelete()) {
                echo '0';

                return;
            }
        }

        echo '1';
    }

    public function actionDeleteCommissioningBodies()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('commissioning_body_id', @$_POST['commissioning_body']);

        foreach (CommissioningBodyService::model()->findAll($criteria) as $cbs) {
            $cbs->commissioning_body_id = null;
            if (!$cbs->save()) {
                throw new Exception('Unable to save commissioning body service: ' . print_r($cbs->getErrors(), true));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', @$_POST['commissioning_body']);

        if (CommissioningBody::model()->deleteAll($criteria)) {
            echo '1';
            Audit::add('admin-CommissioningBody', 'delete');
        } else {
            echo '0';
        }
    }

    public function actionCommissioning_body_types()
    {
        Audit::add('admin-CommissioningBodyType', 'list');
        $this->render('commissioning_body_types');
    }

    public function actionEditCommissioningBodyType()
    {
        if (isset($_GET['commissioning_body_type_id'])) {
            if (!$cbt = CommissioningBodyType::model()->findByPk(@$_GET['commissioning_body_type_id'])) {
                throw new Exception('CommissioningBody not found: ' . @$_GET['commissioning_body_type_id']);
            }
        } else {
            $cbt = new CommissioningBodyType();
        }

        $errors = array();

        if (!empty($_POST)) {
            $cbt->attributes = $_POST['CommissioningBodyType'];

            if (!$cbt->validate()) {
                $errors = $cbt->getErrors();
            }

            if (empty($errors)) {
                $method = $cbt->id ? 'edit' : 'add';

                $audit = $_POST;

                if ($method == 'edit') {
                    $audit['id'] = $cbt->id;
                }

                if (!$cbt->save()) {
                    throw new Exception('Unable to save CommissioningBodyType : ' . print_r($cbt->getErrors(), true));
                }
                Audit::add('admin-CommissioningBodyType', $method, $cbt->id);
                $this->redirect('/admin/commissioning_body_types');
            }
        }

        $this->render('/admin/editCommissioningBodyType', array(
            'cbt' => $cbt,
            'errors' => $errors,
        ));
    }

    public function actionAddCommissioningBodyType()
    {
        $this->actionEditCommissioningBodyType();
    }

    public function actionVerifyDeleteCommissioningBodyTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('commissioning_body_type_id', @$_POST['commissioning_body_type']);

        foreach (CommissioningBody::model()->findAll($criteria) as $cb) {
            if (!$cb->canDelete()) {
                echo '0';

                return;
            }
        }

        echo '1';
    }

    public function actionDeleteCommissioningBodyTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', @$_POST['commissioning_body_type']);

        foreach (CommissioningBodyType::model()->findAll($criteria) as $cbt) {
            if (!$cbt->delete()) {
                echo '0';

                return;
            }
        }

        Audit::add('admin-CommissioningBodyType', 'delete');

        echo '1';
    }

    public function actionCommissioning_Body_Services()
    {
        Audit::add('admin-CommissioningBodyService', 'list');
        $commissioningBodyId = Yii::app()->request->getQuery('commissioning_body_id');
        $this->render('commissioning_body_services', array("commissioningBody" => $commissioningBodyId));
    }

    public function actionEditCommissioningBodyService()
    {
        $address = new Address();
        $contact = new Contact();
        $address->country_id = 1;
        // to allow the commissioning body type list to be filtered
        $commissioning_bt = null;
        $commissioning_bst = null;

        if ($cbs_id = $this->getApp()->request->getQuery('commissioning_body_service_id')) {
            if (!$cbs = CommissioningBodyService::model()->findByPk($cbs_id)) {
                throw new Exception('CommissioningBody not found: ' . $cbs_id);
            }

            if ($cbs->contact) {
                $contact = $cbs->contact;
                if ($cbs->contact->address) {
                    $address = $cbs->contact->address;
                }
            }
        } else {
            $cbs = new CommissioningBodyService;
            if ($commissioning_bt_id = Yii::app()->request->getQuery('commissioning_body_type_id')) {
                if (!$commissioning_bt = CommissioningBodyType::model()->findByPk($commissioning_bt_id)) {
                    throw new CHttpException(404, 'Unrecognised Commissioning Body Type ID');
                }
            }
            if ($service_type_id = Yii::app()->request->getQuery('service_type_id')) {
                if (!$commissioning_bst = CommissioningBodyServiceType::model()->findByPk($service_type_id)) {
                    throw new CHttpException(404, 'Unrecognised Service Type ID');
                };
                $cbs->setAttribute('commissioning_body_service_type_id', $service_type_id);
            }
        }

        $errors = array();

        if (!$return_url = Yii::app()->request->getQuery('return_url')) {
            $return_url = '/admin/commissioning_body_services';
        }

        if (!empty($_POST)) {
            $cbs->attributes = $_POST['CommissioningBodyService'];

            if (!$cbs->validate()) {
                $errors = $cbs->getErrors();
            }
            $contact->attributes = $_POST['Contact'];
            if (!$contact->validate()) {
                $errors = array_merge($errors, $contact->getErrors());
            }

            $address->attributes = $_POST['Address'];

            if (!$address->validate()) {
                $errors = array_merge($errors, $address->getErrors());
            }

            if (empty($errors)) {
                $transaction = Yii::app()->db->beginInternalTransaction();
                try {
                    if (!$contact->save()) {
                        throw new Exception('Unable to save contact: ' . print_r($contact->getErrors(), true));
                    }

                    if (!$address->id) {
                        $cbs->contact_id = $contact->id;
                        $address->contact_id = $contact->id;
                    }

                    $method = $cbs->id ? 'edit' : 'add';

                    if (!$cbs->save()) {
                        throw new Exception('Unable to save CommissioningBodyService: ' . print_r($cbs->getErrors(),
                                true));
                    }

                    if (!$address->save()) {
                        throw new Exception('Unable to save CommissioningBodyService address: ' . print_r($address->getErrors(),
                                true));
                    }

                    Audit::add('admin-CommissioningBodyService', $method, $cbs->id);
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw $e;
                }


                $this->redirect($return_url);
            }
        }

        $this->render('//admin/editCommissioningBodyService', array(
            'commissioning_bt' => $commissioning_bt,
            'commissioning_bst' => $commissioning_bst,
            'cbs' => $cbs,
            'address' => $address,
            'errors' => $errors,
            'return_url' => $return_url
        ));
    }

    public function actionAddCommissioningBodyService()
    {
        $this->actionEditCommissioningBodyService();
    }

    public function actionVerifyDeleteCommissioningBodyServices()
    {
        // Currently no foreign keys to this table
        echo '1';
    }

    public function actionDeleteCommissioningBodyServices()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', @$_POST['commissioning_body_service']);

        foreach (CommissioningBodyService::model()->findAll($criteria) as $cbs) {
            if (!$cbs->delete()) {
                echo '0';

                return;
            }
        }

        Audit::add('admin-CommissioningBodyService', 'delete');

        echo '1';
    }

    public function actionCommissioning_Body_Service_Types()
    {
        $this->render('commissioning_body_service_types');
    }

    public function actionEditCommissioningBodyServiceType()
    {
        if (isset($_GET['commissioning_body_service_type_id'])) {
            if (!$cbs = CommissioningBodyServiceType::model()->findByPk(@$_GET['commissioning_body_service_type_id'])) {
                throw new Exception('CommissioningBodyServiceType not found: ' . @$_GET['commissioning_body_service_type_id']);
            }
        } else {
            $cbs = new CommissioningBodyServiceType();
        }

        $errors = array();

        if (!empty($_POST)) {
            $cbs->attributes = $_POST['CommissioningBodyServiceType'];

            if (!$cbs->validate()) {
                $errors = $cbs->getErrors();
            }

            $method = $cbs->id ? 'edit' : 'add';

            $audit = $_POST;

            if ($method == 'edit') {
                $audit['id'] = $cbs->id;
            }

            if (empty($errors)) {
                if (!$cbs->save()) {
                    throw new Exception('Unable to save CommissioningBodyServiceType: ' . print_r($cbs->getErrors(),
                            true));
                }

                Audit::add('admin-CommissioningBodyServiceType', $method, $cbs->id);

                $this->redirect('/admin/commissioning_body_service_types');
            }
        }

        $this->render('/admin/editCommissioningBodyServiceType', array(
            'cbs' => $cbs,
            'errors' => $errors,
        ));
    }

    public function actionAddCommissioningBodyServiceType()
    {
        $this->actionEditCommissioningBodyServiceType();
    }

    public function actionVerifyDeleteCommissioningBodyServiceTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('commissioning_body_service_type_id', @$_POST['commissioning_body_service_type']);

        if (CommissioningBodyService::model()->find($criteria)) {
            echo '0';
        } else {
            echo '1';
        }
    }

    public function actionDeleteCommissioningBodyServiceTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', @$_POST['commissioning_body_service_type']);

        if (!$er = CommissioningBodyServiceType::model()->deleteAll($criteria)) {
            throw new Exception('Unable to delete CommissioningBodyServiceTypes: ' . print_r($er->getErrors(), true));
        }

        Audit::add('admin-CommissioningBodyServiceType', 'delete');

        echo '1';
    }

    public function actionEventDeletionRequests()
    {
        $this->render('/admin/event_deletion_requests', array(
            'events' => Event::model()->findAll(array(
                'order' => 'last_modified_date asc',
                'condition' => 'delete_pending = 1',
            )),
        ));
    }

    public function actionApproveEventDeletionRequest($id)
    {
        if (!$event = Event::model()->find('id=? and delete_pending=?', array($id, 1))) {
            throw new Exception("Event not found: $id");
        }

        $requested_by_user_id = $event->last_modified_user_id;
        $requested_by_datetime = $event->last_modified_date;

        $event->softDelete();

        $event->audit('event', 'delete-approved', serialize(array(
            'requested_by_user_id' => $requested_by_user_id,
            'requested_by_datetime' => $requested_by_datetime,
        )));

        echo '1';
    }

    public function actionRejectEventDeletionRequest($id)
    {
        if (!$event = Event::model()->find('id=? and delete_pending=?', array($id, 1))) {
            throw new Exception("Event not found: $id");
        }

        $requested_by_user_id = $event->last_modified_user_id;
        $requested_by_datetime = $event->last_modified_date;

        $event->delete_pending = 0;
        $event->delete_reason = null;

        if (!$event->save()) {
            throw new Exception('Unable to reject deletion request for event: ' . print_r($event->getErrors(), true));
        }

        $event->audit('event', 'delete-rejected', serialize(array(
            'requested_by_user_id' => $requested_by_user_id,
            'requested_by_datetime' => $requested_by_datetime,
        )));

        echo '1';
    }

    public function actionEpisodeSummaries($subspecialty_id = null)
    {
        $this->render(
            '/admin/episodeSummaries',
            array(
                'subspecialty_id' => $subspecialty_id,
                'enabled_items' => EpisodeSummaryItem::model()->enabled($subspecialty_id)->findAll(),
                'available_items' => EpisodeSummaryItem::model()->available($subspecialty_id)->findAll(),
            )
        );
    }

    public function actionUpdateEpisodeSummary()
    {
        $item_ids = @$_POST['item_ids'] ? explode(',', $_POST['item_ids']) : array();
        $subspecialty_id = @$_POST['subspecialty_id'] ?: null;

        $tx = Yii::app()->db->beginTransaction();
        EpisodeSummaryItem::model()->assign($item_ids, $subspecialty_id);
        $tx->commit();

        $this->redirect(array('/admin/episodeSummaries', 'subspecialty_id' => $subspecialty_id));
    }

    /**
     * Allows the upload of images for correspondence.
     *
     * @throws CException
     */
    public function actionLogo()
    {

        if (!isset(Yii::app()->params['letter_logo_upload']) || !Yii::app()->params['letter_logo_upload']) {
            throw new CHttpException(404);
        }

        $logo = new Logo();
        if (isset($_FILES['Logo'])) {
            $savePath = Yii::app()->basePath . '/runtime/';
            $fileFormats = array('jpg', 'jpeg', 'png', 'gif');
            $filter = array_filter($_FILES['Logo']['name']);

            foreach ($filter as $logoKey => $logoName) {
                $uploadLogo = CUploadedFile::getInstance($logo, $logoKey);
                $fileInfo = pathinfo($logoName);
                foreach (glob($savePath . $logoKey) as $existingLogo) {
                    unlink($savePath . $existingLogo);
                }
                if (in_array($fileInfo['extension'], $fileFormats, true)) {
                    if ($logoKey === 'header_logo') {
                        $logoTemp = $_FILES['Logo']['tmp_name']['header_logo'];
                        list($width, $height) = getimagesize($logoTemp);
                        $condition = $height . '==100 && ' . $width . '==500';
                    }
                    if ($logoKey === 'secondary_logo') {
                        $logoTemp = $_FILES['Logo']['tmp_name']['secondary_logo'];
                        list($width, $height) = getimagesize($logoTemp);
                        $condition = $height . '==100 && ' . $width . '==120';
                    }

                    if ($condition) {
                        $uploadLogo->saveAs($savePath . $logoKey . '.' . $fileInfo['extension']);
                        Yii::app()->user->setFlash('success', 'Logo Saved Successfully');
                    } else {
                        Yii::app()->user->setFlash('error', ' logo size must be defined dimension');
                    }
                } else {
                    Yii::app()->user->setFlash('error', 'Upload valid image formats (jpg,jpeg,png,gif)');
                }
            }

            $this->redirect(array('/admin/logo'));
        }
        $this->render('/admin/logo', array('model' => $logo));
    }

    public function actionDeleteLogo()
    {
        $deleteHeaderLogo = @$_GET['header_logo'];
        $deleteSecondaryLogo = @$_GET['secondary_logo'];

        if (!empty($deleteHeaderLogo)) {
            @unlink(Yii::app()->basePath . '/runtime/' . $deleteHeaderLogo);
            Yii::app()->user->setFlash('success', 'Logo Deleted Successfully');
            $this->redirect(array('/admin/logo'));
        } elseif (!empty($deleteSecondaryLogo)) {
            @unlink(Yii::app()->basePath . '/runtime/' . $deleteSecondaryLogo);
            Yii::app()->user->setFlash('success', 'Logo Deleted Successfully');
            $this->redirect(array('/admin/logo'));
        }
    }

    public function actionSettings()
    {
        $this->render('/admin/settings');
    }

    public function actionEditSetting()
    {
        if (!$metadata = SettingMetadata::model()->find('`key`=?', array(@$_GET['key']))) {
            $this->redirect(array('/admin/settings'));
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            foreach (SettingMetadata::model()->findAll('element_type_id is null') as $metadata) {
                if (@$_POST['hidden_' . $metadata->key] || @$_POST[$metadata->key]) {
                    if (!$setting = $metadata->getSetting($metadata->key, null, true)) {
                        $setting = new SettingInstallation();
                        $setting->key = $metadata->key;
                    }
                    $setting->value = @$_POST[$metadata->key];
                    if (!$setting->save()) {
                        $errors = $setting->errors;
                    } else {
                        $this->redirect(array('/admin/settings'));
                    }
                }
            }
        }
        $this->render('/admin/edit_setting', array('metadata' => $metadata, 'errors' => $errors));
    }

    /**
     * Lists and allows editing of AnaestheticAgent records.
     *
     * @throws Exception
     */
    public function actionViewAnaestheticAgent()
    {
        $this->genericAdmin('Edit Anaesthetic Agents', 'AnaestheticAgent');

        /*Audit::add('admin', 'list', null, null, array('model'=>'AnaestheticAgent'));

        $this->render('anaestheticagent');*/
    }

    public function actionAddAnaestheticAgent()
    {
        $agent = new AnaestheticAgent();
        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $agent->attributes = Yii::app()->request->getPost('AnaestheticAgent');

            if (!$agent->validate()) {
                $errors = $agent->getErrors();
            } else {
                if (!$agent->save()) {
                    throw new CHttpException(500, 'Unable to save Anaesthetic Agent: ' . $agent->name);
                }

                Audit::add('admin', 'add', $agent->id, null, array('model' => 'AnaestheticAgent'));
                $this->redirect('/admin/viewAnaestheticAgent');
            }
        }

        $this->render('/admin/editanaestheticagent', array(
            'agent' => $agent,
            'errors' => $errors,
        ));
    }

    public function actionEditAnaestheticAgent($id)
    {
        $agent = AnaestheticAgent::model()->findByPk($id);
        $errors = array();

        if (!$agent) {
            throw new CHttpException(404, 'Anaesthetic Agent not found: ' . $id);
        }

        if (Yii::app()->request->isPostRequest) {
            $agent->attributes = Yii::app()->request->getPost('AnaestheticAgent');

            if (!$agent->validate()) {
                $errors = $agent->getErrors();
            } else {
                if (!$agent->save()) {
                    throw new CHttpException(500, 'Unable to save Anaesthetic Agent: ' . $agent->name);
                }

                Audit::add('admin', 'edit', $id, null, array('model' => 'AnaestheticAgent'));
                $this->redirect('/admin/viewAnaestheticAgent');
            }
        }

        Audit::add('admin', 'view', $id, null, array('model' => 'AnaestheticAgent'));
        $this->render('/admin/editanaestheticagent', array(
            'agent' => $agent,
            'errors' => $errors,
        ));
    }

    public function actionDeleteAnaestheticAgent($id)
    {
        $agent = AnaestheticAgent::model()->findByPk($id);

        if (!$agent) {
            throw new CHttpException(404, 'Anaesthetic Agent not found: ' . $id);
        }

        if (Yii::app()->request->isPostRequest) {
            $agent->active = 0;
            if (!$agent->save()) {
                throw new CHttpException(500, 'Unable to delete Anaesthetic Agent: ' . $agent->name);
            }

            Audit::add('admin', 'delete', $id, null, array('model' => 'AnaestheticAgent'));
            $this->redirect('/admin/viewAnaestheticAgent');
        }

        Audit::add('admin', 'view', $id, null, array('model' => 'AnaestheticAgent'));
        $this->render('/admin/deleteanaestheticagent', array(
            'agent' => $agent,
        ));
    }

    public function actionPatientShortcodes()
    {
        $this->genericAdmin('Edit Shortcodes', 'PatientShortcode', array(
            'description' => 'You may alter the shortcode for this installation below. Otherwise this screen is purely for information',
            'cannot_add' => true,
            'cannot_delete' => true,
            'label_field' => 'code',
            'extra_fields' => array(
                array(
                    'field' => 'default_code',
                    'type' => 'text',
                    'htmlOptions' => array(
                        'disabled' => true,
                        'size' => 4
                    )
                ),
                array(
                    'field' => 'description',
                    'type' => 'textarea'
                ),
                array(
                    'field' => 'event_type_id',
                    'type' => 'lookup',
                    'model' => 'EventType',
                    'htmlOptions' => array(
                        'disabled' => true
                    )
                ),
                array(
                    'field' => 'method',
                    'type' => 'text',
                    'htmlOptions' => array(
                        'disabled' => true,
                    )
                ),
                array(
                    'field' => 'codedoc',
                    'type' => 'textdisplay',
                    'htmlOptions' => array(
                        'disabled' => true,
                    )
                )
            )
        ));
    }
}
