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

    public function actionCreate(){
        if (isset($_POST['PracticeAssociate'])) {
            $contact_practice_associate = new ContactPracticeAssociate();
            $contact_practice_associate->gp_id = $_POST['PracticeAssociate']['gp_id'];
            $contact_practice_associate->practice_id = $_POST['PracticeAssociate']['practice_id'];
            if ($contact_practice_associate->save()){
                echo CJSON::encode(array(
                    'gp_id'    => $contact_practice_associate->gp_id,
                ));
            }else{
                echo CJSON::encode(array('error'=>$contact_practice_associate->getError('practice_id')));
            }
        }
    }

    public function actionGetGpWithPractice($gp_id){
        $return_array = array('gp_id'=>$gp_id,'content'=>'');
        $practice_contact_associate = ContactPracticeAssociate::model()->findByAttributes(array('gp_id'=>$gp_id));
        if (isset($practice_contact_associate)){
            $gp = $practice_contact_associate->gp;
            $practice = $practice_contact_associate->practice;
            $practiceDetails = $practice?' - '.$gp->getAssociatedPractice($gp->id)['first_name']:'';
            $role = $gp->getGPROle()?' - '.$gp->getGPROle():'';

            $return_array['content'] =  '<li><span class="js-name">'.$gp->getCorrespondenceName().$role.$practiceDetails.'</span><i id="js-remove-extra-gp-'.$gp->id.'" class="oe-i remove-circle small-icon pad-left"></i><input type="hidden" name="ExtraContact[gp_id][]" class="js-extra-gps" value="'.$gp_id.'"></li>';
            $return_array['label'] = $gp->getCorrespondenceName().$role.$practiceDetails;
            $return_array['practice_id'] = $practice->id;
        }else{
            $gp = Gp::model()->findByPk($gp_id);
            $return_array['content'] = '<li><span class="js-name">'.$gp->getCorrespondenceName().'</span><i id="js-remove-extra-gp-'.$gp->id.'" class="oe-i remove-circle small-icon pad-left"></i><input type="hidden" class="js-extra-gps" name="ExtraContact[gp_id][]" value="'.$gp_id.'"></li>';
        }
        echo CJSON::encode($return_array);
    }

}