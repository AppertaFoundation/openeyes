<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Class RiskController.
 */
class InternalReferralSettingsController extends ModuleAdminController
{

    public function actionSettings()
    {
        $this->render('/admin/settings',array(
            'settings' => OphcocorrespondenceInternalReferralSettings::model()->findAll(),
            'to_locations' => OphCoCorrespondence_InternalReferral_ToLocation::model()->findAll(),
        ));
    }

    public function actionEditSetting()
    {
        if (!$metadata = OphcocorrespondenceInternalReferralSettings::model()->find('`key`=?', array(@$_GET['key']))) {
            $this->redirect(array('/OphCoCorrespondence/oeadmin/internalReferralSettings/settings'));
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            foreach (OphcocorrespondenceInternalReferralSettings::model()->findAll() as $metadata) {
                if (@$_POST['hidden_' . $metadata->key] || @$_POST[$metadata->key]) {
                    if (!$setting = $metadata->getSetting($metadata->key, null, true)) {
                        $setting = new SettingInternalReferral();
                        $setting->key = $metadata->key;
                    }
                    $setting->value = @$_POST[$metadata->key];
                    if (!$setting->save()) {
                        $errors = $setting->errors;
                    } else {
                        $this->redirect(array('/OphCoCorrespondence/oeadmin/internalReferralSettings/settings'));
                    }
                }
            }
        }

        $this->render('/admin/edit_setting', array('metadata' => $metadata, 'errors' => $errors));
    }


    public function actionUpdateToLocationList()
    {
        $return = array('success' => false);

        $site_ids = array();
        $locations_post = Yii::app()->request->getPost('OphCoCorrespondence_InternalReferral_ToLocation', array());

        foreach($locations_post as $location_post){
            $site_ids[] = $location_post['site_id'];
        }

        $transaction = Yii::app()->db->beginTransaction();

        try
        {

            //make sure no duplications are saved
            $site_ids = array_unique($site_ids);

            // remove all site that's ids were not posted back - means they were removed
            $criteria = new CDbCriteria();
            $criteria->addNotInCondition('site_id', $site_ids);

            OphCoCorrespondence_InternalReferral_ToLocation::model()->deleteAll($criteria);

            $is_ok = true;

            //now we save the new ones
            foreach($site_ids as $site_id){
                $site = OphCoCorrespondence_InternalReferral_ToLocation::model()->findByAttributes(array('site_id' => $site_id));

                if(!$site){
                    $site = new OphCoCorrespondence_InternalReferral_ToLocation();
                    $site->site_id = $site_id;

                    if( !$site->save()){
                        $is_ok = false;
                    }
                }
            }

            if($is_ok){
                $transaction->commit();
                $return = array('success' => true);
            } else {
                $transaction->rollback();
                $return = array('success' => false);
            }

        }
        catch(Exception $e)
        {
            $transaction->rollback();
            $return = array('success' => false);
        }

        echo CJSON::encode($return);
        Yii::app()->end();


    }


}
