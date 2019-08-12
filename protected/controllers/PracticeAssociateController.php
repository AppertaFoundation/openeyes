<?php

class PracticeAssociateController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
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
                'allow', // allow anyone to search for contact labels
                'actions' => array('create','getGpWithPractice'),
                'users' => array('*')
            ),
        );
    }

    /**
     * This function is called at the final step of Add New Contact/Referring Practitioner and saves all the data
     * (i.e. Contact, Gp and Contact Practice Associate)
     */
    public function actionCreate(){
        if (isset($_POST['Contact'])) {
            $contact_practice_associate = new ContactPracticeAssociate();
            $contact_practice_associate->practice_id = $_POST['PracticeAssociate']['practice_id'];

            if ($contact_practice_associate->validate(array('practice_id'))) {

                $contactFormData = $_POST['Contact'];
                $gp = new Gp();
                $contact = new Contact('manage_gp');
                $contact->attributes = $contactFormData;

                list($contact, $gp) = $this->performGpSave($contact, $gp,  true);
                $contact_practice_associate->gp_id = $gp->getPrimaryKey();

                if ($contact_practice_associate->save()) {
                    echo CJSON::encode(array(
                        'gp_id' => $contact_practice_associate->gp_id,
                    ));
                }
            } else {
                echo CJSON::encode(array('error' => $contact_practice_associate->getError('practice_id')));
            }
        }
    }

    public function actionGetGpWithPractice($gp_id){
        $return_array = array('gp_id'=>$gp_id,'content'=>'');
        $practice_contact_associate = ContactPracticeAssociate::model()->findByAttributes(array('gp_id'=>$gp_id));
        if (isset($practice_contact_associate)){
            $gp = $practice_contact_associate->gp;
            $practice = $practice_contact_associate->practice;
            $role = $gp->getGPROle()?' - '.$gp->getGPROle():'';
            $practiceNameAddress = $practice->getPracticeNames() ? ' - '.$practice->getPracticeNames():'';

            $return_array['content'] =  '<li><span class="js-name" style="text-align:justify">'.$gp->getCorrespondenceName().$role.$practiceNameAddress.'</span><i id="js-remove-extra-gp-'.$gp->id.'" class="oe-i remove-circle small-icon pad-left"></i><input type="hidden" name="ExtraContact[gp_id][]" class="js-extra-gps" value="'.$gp_id.'"></li>';
            $return_array['label'] = $gp->getCorrespondenceName().$role.$practiceNameAddress;
            $return_array['practice_id'] = $practice->id;
        }else{
            $gp = Gp::model()->findByPk($gp_id);
            $return_array['content'] = '<li><span class="js-name" style="text-align:justify">'.$gp->getCorrespondenceName().'</span><i id="js-remove-extra-gp-'.$gp->id.'" class="oe-i remove-circle small-icon pad-left"></i><input type="hidden" class="js-extra-gps" name="ExtraContact[gp_id][]" value="'.$gp_id.'"></li>';
        }
        echo CJSON::encode($return_array);
    }

    /**
     * @param Contact $contact
     * @param Gp $gp
     * @param bool $isAjax
     * @return array
     * @throws CException
     */
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
                        throw new CHttpException(400,"Unable to save Practitioner contact");
                    }
                    $transaction->rollback();
                }
            } else {
                if ($isAjax) {
                    throw new CHttpException(400,CHtml::errorSummary($contact));
                }
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
            if ($isAjax) {
                if (strpos($ex->getMessage(),'errorSummary')){
                    echo $ex->getMessage();
                }else{
                    echo "<div class=\"errorSummary\"><p>Unable to save Practitioner information, please contact your support.</p></div>";
                }
            }
        }
        return array($contact, $gp);
    }

}