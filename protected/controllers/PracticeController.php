<?php

class PracticeController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                // allow users with either the TaskViewPractice or TaskCreatePractice roles to view Practice data
                'actions' => array('index', 'view'),
                'roles' => array('TaskViewPractice', 'TaskCreatePractice'),
            ),
            array(
                'allow',
                // allow users with either the TaskCreatePractice or TaskAddPatient roles to perform 'create' actions
                'actions' => array('create', 'createAssociate'),
                'roles' => array('TaskCreatePractice', 'TaskAddPatient'),
            ),
            array(
                'allow', // allow users with the TaskCreatePractice role to perform 'update' actions
                'actions' => array('update'),
                'roles' => array('TaskCreatePractice'),
            ),
            array(
                'deny',  // deny all other users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('practice_id = '.$id);
        $dataProvider = new CActiveDataProvider('ContactPracticeAssociate', array(
            'criteria' => $criteria,
        ));

        $this->render('view', array(
            'model' => $this->loadModel($id),
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $context string The context through which create action is invoked. Is either 'AJAX' or null.
     */
    public function actionCreate($context = null)
    {
        $duplicateCheckOutput = null;
        $isDuplicateProviderNo = false;
        $contact = new Contact('manage_practice');
        $address = new Address('manage_practice');
        $practice = new Practice('manage_practice');

        $gp = new Gp('manage_practice');
        // this array contains the arrays of gp id, provider_no and count of
        // the rows in the contact_practice_associate table with that provider no
        $gpIdProviderNoList = array();
        if (isset($_POST['Gp'])) {
            // Assuming the Gp id and ContactPracticeAssociate Provider_no arrays have the same length.
            for ($i=0; $i<sizeof($_POST['Gp']['id']); $i++) {
                $count=0;
                $gpId = $_POST['Gp']['id'][$i];
                $providerNo = $_POST['ContactPracticeAssociate']['provider_no'][$i];
                $providerNoDuplicateCheck = ContactPracticeAssociate::model()->findAllByAttributes(array('provider_no'=>$providerNo), "provider_no IS NOT NULL AND provider_no != ''");

                // If condition is executed when the provider no exists in the db
                if (count($providerNoDuplicateCheck) >=1 ) {
                    $isDuplicateProviderNo = true;
                    $count = count($providerNoDuplicateCheck);
                    $gpIdProviderNoList[] = array($gpId, $providerNo, $count);
                }
                // else condition makes sure that there is no duplicate within the form itself. (it excludes empty values).
                else {
                    for ($j=0; $j<count($gpIdProviderNoList); $j++) {
                        if ($gpIdProviderNoList[$j][1] != $providerNo || $providerNo == '' ) {
                            $count = 0;
                        } else {
                            $count = 1;
                            $isDuplicateProviderNo = true;
                            break;
                        }
                    }
                    $gpIdProviderNoList[] = array($gpId, $providerNo, $count);
                }
            }
        }

        $this->performAjaxValidation(array($practice, $contact, $address, $gp));
        if (isset($_POST['Contact'])) {
            $contact->first_name = $_POST['Contact']['first_name'];
            $address->attributes = $_POST['Address'];
            $practice->attributes = $_POST['Practice'];

            if ( $contact->validate(array('first_name')) and $practice->validate(array('phone')) and $address->validate(array('address1', 'city', 'postcode', 'country')) ) {
                // If there is no validation error, check for the duplicate practice based on practice name, phone, address1, city, postcode and country.
                $duplicateCheckOutput = Yii::app()->db->createCommand()
                    ->select('c1.first_name, p.phone, a.address1, a.city, a.postcode, a.country_id')
                    ->from('practice p')
                    ->join('contact c1', 'c1.id = p.contact_id')
                    ->join('address a', 'a.contact_id = c1.id')
                    ->where(
                        'LOWER(c1.first_name) = LOWER(:first_name) and LOWER(p.phone) = LOWER(:phone) and LOWER(a.address1) = LOWER(:address1) and LOWER(a.city) = LOWER(:city) and a.postcode = :postcode and a.country_id = :country_id',
                        array(':first_name'=> $contact->first_name, ':phone'=> $practice->phone,':address1'=>$address->address1,
                        ':city'=>$address->city,
                        ':postcode'=>$address->postcode,
                        ':country_id'=>$address->country_id)
                    )
                    ->queryAll();

                $isDuplicate = count($duplicateCheckOutput);

                if ($isDuplicate === 0 && !$isDuplicateProviderNo) {
                    list($contact, $practice, $address) = $this->performPracticeSave(
                        $contact,
                        $practice,
                        $address,
                        $gpIdProviderNoList,
                        false
                    );
                }
            } else {
                $contact->validate(array('first_name'));
                $practice->validate(array('phone'));
                $address->validate(array('address1', 'city', 'postcode', 'country'));
            }
        }

        $this->render('create', array(
            'model' => $practice,
            'address' => $address,
            'contact' => $contact,
            'gp' => $gp,
            'duplicateCheckOutput' => $duplicateCheckOutput,
            'gpIdProviderNoList' => $gpIdProviderNoList,
        ));
    }

    /**
     * This function is called at the final step of Add New Contact/Referring Practitioner when user wants to add new
     * practice (It uses Contact, Gp, Practice, Address and Contact Practice Associate models)
     * @throws CException
     * @throws Exception
     */
    public function actionCreateAssociate(){
        if (isset($_POST['Contact'], $_POST['gp_data_retrieved'])) {
            $contactPractice = new Contact('manage_practice');
            $address = new Address('manage_practice');
            $practice = new Practice('manage_practice');

            $contactPractice->first_name = $_POST['Contact']['first_name'];
            $address->attributes = $_POST['Address'];
            $practice->attributes = $_POST['Practice'];

            if ($contactPractice->validate(array('first_name')) and $practice->validate(array('phone')) and $address->validate(array('address1', 'city', 'postcode', 'country'))) {
                $practice_contact_associate = new ContactPracticeAssociate();
                $practice_contact_associate->provider_no = !empty($_POST['ContactPracticeAssociate']['provider_no']) ? $_POST['ContactPracticeAssociate']['provider_no'] : null;

                $gp = new Gp();
                $contact = new Contact('manage_gp');

                $contact->title = $_POST['Contact']['contact_title'];
                $contact->first_name = $_POST['Contact']['contact_first_name'];
                $contact->last_name = $_POST['Contact']['contact_last_name'];
                $contact->primary_phone = $_POST['Contact']['contact_primary_phone'];
                $contact->contact_label_id = $_POST['Contact']['contact_label_id'];

                // If there is no validation error, check for the duplicate practice based on practice name, phone, address1, city, postcode and country.
                $dataProvider = Yii::app()->db->createCommand()
                    ->select('c1.first_name, p.phone, a.address1, a.city, a.postcode, a.country_id')
                    ->from('practice p')
                    ->join('contact c1', 'c1.id = p.contact_id')
                    ->join('address a', 'a.contact_id = c1.id')
                    ->where('LOWER(c1.first_name) = LOWER(:first_name) and LOWER(p.phone) = LOWER(:phone) and LOWER(a.address1) = LOWER(:address1) and LOWER(a.city) = LOWER(:city) and LOWER(a.postcode) = LOWER(:postcode) and LOWER(a.country_id) = LOWER(:country_id)',
                        array(':first_name'=> $contactPractice->first_name, ':phone'=> $practice->phone,':address1'=>$address->address1,
                            ':city'=>$address->city, ':postcode'=>$address->postcode, ':country_id'=>$address->country_id))
                    ->queryAll();

                $isDuplicate = count($dataProvider);

                if ($isDuplicate === 0) {
                    // This variable stores the gp details that were entered in the pop-up (in first step)
                    $gpDetails = json_decode($_POST['gp_data_retrieved']);
                    if ((int)$gpDetails->gpId !== -1) {
                        $practice_contact_associate->gp_id = $gpDetails->gpId;
                    } else {
                        // User has selected one of the search results but modified the text in one of the fields.
                        list($contact, $gp) = $this->performGpSave($contact, $gp, true);
                        $practice_contact_associate->gp_id = $gp->getPrimaryKey();
                    }
                } else {
                    echo CJSON::encode(array('error' => 'Duplicate Practice detected. <br/> A practice with the same practice name, phone and address already exists. Please enter another practice or exit.'));
                    Yii::app()->end();
                }

                list($contactPractice, $practice, $address) = $this->performPracticeSave($contactPractice, $practice, $address, '', true);

                $practice_contact_associate->practice_id = $practice->id;

                if ($practice_contact_associate->save()) {
                    echo CJSON::encode(array(
                        'gp_id' => $practice_contact_associate->gp->getPrimaryKey(),
                        'practice_id' => $practice_contact_associate->practice_id,
                    ));
                }
            } else {
                echo CJSON::encode(array('error' =>  CHtml::errorSummary(array($contactPractice, $practice, $address))));
            }
        }
    }

    public function performGpSave(Contact $contact, Gp $gp, $isAjax = false)
    {
        $action = $gp->isNewRecord ? 'add' : 'edit';
        $transaction = Yii::app()->db->beginTransaction();

        try {
            if ($contact->save()) {
                // No need to re-set these values if they already exist.
                if ($gp->contact_id === null) {
                    $gp->contact_id = $contact->getPrimaryKey();
                }

                if ($gp->nat_id === null) {
                    $gp->nat_id = 0;
                }

                if ($gp->obj_prof === null) {
                    $gp->obj_prof = 0;
                }

                if ($gp->save()) {
                    $transaction->commit();
                    Audit::add('Gp', $action . '-gp', "Practitioner manually [id: $gp->id] {$action}ed.");
                    if (!$isAjax) {
                        $this->redirect(array('view','id'=>$gp->id));
                    }
                } else {
                    if ($isAjax) {
                        throw new CHttpException(400, "Unable to save Practitioner contact");
                    }
                    $transaction->rollback();
                }
            } else {
                if ($isAjax) {
                    throw new CHttpException(400, CHtml::errorSummary($contact));
                }
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
            if ($isAjax) {
                if (strpos($ex->getMessage(), 'errorSummary')) {
                    echo $ex->getMessage();
                } else {
                    echo "<div class=\"errorSummary\"><p>Unable to save Practitioner information, please contact your support.</p></div>";
                }
            }
        }
        return array($contact, $gp);
    }

    /**
     * @param Contact $contact
     * @param Practice $practice
     * @param Address $address
     * @param bool $isAjax
     * @return array
     * @throws CException
     */
    public function performPracticeSave(Contact $contact, Practice $practice, Address $address, $gpIdProviderNoList, $isAjax = false)
    {
        $action = $practice->isNewRecord ? 'add' : 'edit';
        $transaction = Yii::app()->db->beginTransaction();
        try {
            // Contact validation must be done before calling performPracticeSave
            if ($contact->save(false)) {
                $practice->contact_id = $contact->getPrimaryKey();
                $address->contact_id = $contact->id;
                if ($practice->save()) {
                    if (isset($address)) {
                        if ($address->save()) {
                            $transaction->commit();
                            Audit::add(
                                'Practice',
                                $action . '-practice',
                                "Practice manually [id: $practice->id] {$action}ed."
                            );
                            if (!$isAjax) {
                                if (count($gpIdProviderNoList) === 0) {
                                    $this->redirect(array('view', 'id' => $practice->id));
                                }
                                $isLastAssociate = false;
                                for ($i = 0; $i < count($gpIdProviderNoList); $i++) {
                                    $practice_contact_associate = new ContactPracticeAssociate();
                                    $practice_contact_associate->gp_id = $gpIdProviderNoList[$i][0];
                                    $practice_contact_associate->provider_no = !empty($gpIdProviderNoList[$i][1]) ? $gpIdProviderNoList[$i][1] : null;
                                    $practice_contact_associate->practice_id = $practice->id;
                                    $practice_contact_associate->save();
                                    if ($i == (count($gpIdProviderNoList)-1)) {
                                        $isLastAssociate = true;
                                    }
                                    if ($isLastAssociate) {
                                        $this->redirect(array('view', 'id' => $practice->id));
                                    }
                                }
                            }
                        } else {
                            $address->validate();
                            $address->clearErrors('contact_id');
                            if ($isAjax) {
                                throw new CHttpException(400, CHtml::errorSummary($address));
                            }
                            $transaction->rollback();
                        }
                    } else {
                        $transaction->commit();
                        Audit::add(
                            'Practice',
                            $action . '-practice',
                            "Practice manually [id: $practice->id] {$action}ed."
                        );
                        if (!$isAjax) {
                            $this->redirect(array('view', 'id' => $practice->id));
                        }
                    }
                } else {
                    $address->validate();
                    $address->clearErrors('contact_id');
                    if ($isAjax) {
                        throw new CHttpException(400, CHtml::errorSummary(array($practice,$address)));
                    }
                    $transaction->rollback();
                }
            } else {
                $practice->validate();
                $practice->clearErrors('contact_id');
                $address->validate();
                $address->clearErrors('contact_id');
                if ($isAjax) {
                    throw new CHttpException(400, CHtml::errorSummary(array($contact,$address)));
                }
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
            if ($isAjax) {
                echo $ex->getMessage();
            }
        }
        return array($contact, $practice, $address);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $duplicateCheckOutput = null;

        $model = $this->loadModel($id);
        $contact = $model->contact;
        $address = isset($contact->address) ? $contact->address : new Address();
        $contact->setScenario('manage_practice');
        $address->setScenario('manage_practice');
        $model->setScenario('manage_practice');

        $gp = new Gp('manage_practice');
        // this array contains the arrays of gp id, provider_no and count of
        // the rows in the contact_practice_associate table with that provider no
        $gpIdProviderNoList = array();
        $cpas = ContactPracticeAssociate::model()->findAllByAttributes(array('practice_id' => $id));

        foreach ($cpas as $cpa) {
            $gpId = $cpa['gp_id'];
            $providerNo = $cpa['provider_no'];
            $gpIdProviderNoList[] = array($gpId, $providerNo);
        }

        $this->performAjaxValidation(array($model, $contact, $address, $gp));

        if (isset($_POST['Address']) || isset($_POST['Contact'])) {
            $isDuplicateProviderNo = false;

            $contact->attributes = $_POST['Contact'];
            $address->attributes = $_POST['Address'];
            $model->attributes = $_POST['Practice'];

            // this array contains the arrays of gp id, provider_no and count of
            // the rows in the contact_practice_associate table with that provider no
            $gpIdProviderNoList = array();
            if (isset($_POST['Gp'])) {
                // Assuming the Gp id and ContactPracticeAssociate Provider_no arrays have the same length.
                for ($i=0; $i<sizeof($_POST['Gp']['id']); $i++) {
                    $count=0;
                    $gpId = $_POST['Gp']['id'][$i];
                    $providerNo = $_POST['ContactPracticeAssociate']['provider_no'][$i];
                    $providerNoDuplicateCheck = ContactPracticeAssociate::model()->findAllByAttributes(array('provider_no'=>$providerNo), "provider_no IS NOT NULL AND provider_no != '' AND practice_id !=".$id);
                    // If condition is executed when the provider no exists in the db
                    if (count($providerNoDuplicateCheck) >=1 ) {
                        $isDuplicateProviderNo = true;
                        $count = count($providerNoDuplicateCheck);
                        $gpIdProviderNoList[] = array($gpId, $providerNo, $count);
                    }
                    // else condition makes sure that there is no duplicate within the form itself. (it excludes empty values).
                    else {
                        for ($j=0; $j<count($gpIdProviderNoList); $j++) {
                            if ($gpIdProviderNoList[$j][1] != $providerNo || $providerNo == '' ) {
                                $count = 0;
                            } else {
                                $count = 1;
                                $isDuplicateProviderNo = true;
                                break;
                            }
                        }
                        $gpIdProviderNoList[] = array($gpId, $providerNo, $count);
                    }
                }
            }

            if ( $contact->validate(array('first_name')) and $model->validate(array('phone')) and $address->validate(array('address1', 'city', 'postcode', 'country'))) {
                // If there is no validation error, check for the duplicate practice based on practice name, phone, address1, city, postcode and country.
                $duplicateCheckOutput = Yii::app()->db->createCommand()
                    ->select('c1.first_name, p.phone, a.address1, a.city, a.postcode, a.country_id')
                    ->from('practice p')
                    ->join('contact c1', 'c1.id = p.contact_id')
                    ->join('address a', 'a.contact_id = c1.id')
                    ->where(
                        'LOWER(c1.first_name) = LOWER(:first_name) and LOWER(p.phone) = LOWER(:phone) and LOWER(a.address1) = LOWER(:address1) and LOWER(a.city) = LOWER(:city) and a.postcode = :postcode and a.country_id = :country_id and p.id != :id',
                        array(':first_name'=> $contact->first_name, ':phone'=> $model->phone, ':address1'=>$address->address1,
                        ':city'=>$address->city,
                        ':postcode'=>$address->postcode,
                        ':country_id'=>$address->country_id,
                        ':id'=>$id)
                    )
                    ->queryAll();

                $isDuplicate = count($duplicateCheckOutput);

                if ($isDuplicate === 0 && !$isDuplicateProviderNo) {
                    // If a single record exists for a practice in contact_practice_associate table,
                    // delete all the records from the contact_practice_associate table before populating.
                    ContactPracticeAssociate::model()->deleteAllByAttributes(array('practice_id'=>$id));

                    list($contact, $model, $address) = $this->performPracticeSave(
                        $contact,
                        $model,
                        $address,
                        $gpIdProviderNoList,
                        false
                    );
                }
            } else {
                $contact->validate(array('first_name'));
                $model->validate(array('phone'));
                $address->validate(array('address1', 'city', 'postcode', 'country'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'address' => $address,
            'contact' => $contact,
            'gp' => $gp,
            'duplicateCheckOutput' => $duplicateCheckOutput,
            'gpIdProviderNoList' => $gpIdProviderNoList,
        ));
    }

    /**
     * Lists all models.
     *
     * @param string $search_term
     */
    public function actionIndex($search_term = null)
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array('contact');
        $criteria->order = 'first_name';

        if ($search_term !== null) {
            $search_term = strtolower($search_term);
            $criteria->addSearchCondition('LOWER(last_name)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(first_name)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(phone)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(code)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(address1)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(address2)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(city)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(postcode)', $search_term, true, 'OR');
        }
        $dataProvider = new CActiveDataProvider('Practice', array(
            'criteria' => $criteria,
        ));
        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'search_term' => $search_term,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Practice|CActiveRecord the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Practice::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel|array $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'practice-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
