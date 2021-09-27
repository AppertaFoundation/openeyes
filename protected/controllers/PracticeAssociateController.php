<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2021
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

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
     * @throws CException
     */
    public function actionCreate() {
        if (isset($_POST['Contact'], $_POST['gp_data_retrieved'])) {
            $contact_practice_associate = new ContactPracticeAssociate();
            $contact_practice_associate->practice_id = $_POST['PracticeAssociate']['practice_id'];
            $contact_practice_associate->provider_no = !empty($_POST['ContactPracticeAssociate']['provider_no']) ? $_POST['ContactPracticeAssociate']['provider_no'] : null;

            if ($contact_practice_associate->validate(array('practice_id'))) {
                $contactFormData = $_POST['Contact'];
                $gp = new Gp();
                $contact = new Contact('manage_gp');
                $contact->attributes = $contactFormData;

                // This variable stores the gp details that were entered in the pop-up (in first step)
                $gpDetails = json_decode($_POST['gp_data_retrieved']);

                if ((int)$gpDetails->gpId !== -1) {
                    // Use the existing gp id and look for duplicates.
                    // check for duplicate
                    $query = Yii::app()->db->createCommand()
                        ->select('cpa.id')
                        ->from('contact_practice_associate cpa')
                        ->where('cpa.gp_id = :gp_id and cpa.practice_id = :practice_id',
                            array(':gp_id'=> $gpDetails->gpId,':practice_id'=> $contact_practice_associate->practice_id))
                        ->queryAll();

                    $isDuplicate = count($query);

                    if ($isDuplicate === 0) {
                        $contact_practice_associate->gp_id = $gpDetails->gpId;
                    } else {
                        echo CJSON::encode(array('error' => 'This practitioner is already associated with the selected practice.'));
                        Yii::app()->end();
                    }
                } else {
                    // New Gp id will be created
                    list($contact, $gp) = $this->performGpSave($contact, $gp, true);
                    $contact_practice_associate->gp_id = $gp->getPrimaryKey();
                }
            }

            if ($contact_practice_associate->save()) {
                echo CJSON::encode(array(
                    'gp_id' => $contact_practice_associate->gp_id,
                    'practice_id' => $contact_practice_associate->practice_id,
                ));
            } else {
                echo CJSON::encode(array('error' => $contact_practice_associate->getError('practice_id')));
            }
        }
    }

    public function actionGetGpWithPractice($id, $gp_id, $practice_id){
        $return_array = array('gp_id'=>$gp_id,'practice_id'=>$practice_id,'content'=>'');
        $practice_contact_associate = ContactPracticeAssociate::model()->findByAttributes(array('gp_id'=>$gp_id,'practice_id'=>$practice_id));
        if (isset($practice_contact_associate)){
            $gp = $practice_contact_associate->gp;
            $practice = $practice_contact_associate->practice;
            $providerNo = isset($practice_contact_associate->provider_no) ? ' ('.$practice_contact_associate->provider_no.') ' : '';
            $role = $gp->getGPROle()?' - '.$gp->getGPROle():'';
            $practiceNameAddress = $practice->getPracticeNames() ? ' - '.$practice->getPracticeNames():'';
            $inputGpElement = '';
            $inputPracticeElement = '';
            if($id !== 'js-selected_gp') {
                $inputGpElement = '<input type="hidden" name="ExtraContact[gp_id][]" class="js-extra-gps" value="'.$gp_id.'">';
                $inputPracticeElement = '<input type="hidden" name="ExtraContact[practice_id][]" class="js-extra-practices" value="'.$practice_id.'">';
            }
            $return_array['content'] = '<li><span class="js-name" style="text-align:justify">'.$gp->getCorrespondenceName().$providerNo.$role.$practiceNameAddress.'</span><i id="js-remove-extra-gp-'.$gp->id.'-'.$practice->id.'" class="oe-i remove-circle small-icon pad-left"></i>'.$inputGpElement.$inputPracticeElement.'</li>';
            $return_array['label'] = $gp->getCorrespondenceName().$providerNo.$role.$practiceNameAddress;
        }else{
            $gp = Gp::model()->findByPk($gp_id);
            $inputGpElement = '';
            if($id !== 'js-selected_gp') {
                $inputGpElement = '<input type="hidden" name="ExtraContact[gp_id][]" class="js-extra-gps" value="'.$gp_id.'">';
            }
            $return_array['content'] = '<li><span class="js-name" style="text-align:justify">'.$gp->getCorrespondenceName().'</span><i id="js-remove-extra-gp-'.$gp->id.'" class="oe-i remove-circle small-icon pad-left"></i>'.$inputGpElement.'</li>';
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
}
