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
class ProfileController extends BaseController
{
    public $layout = 'profile';
    public $items_per_page = 30;

    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    protected function beforeAction($action)
    {
        if (!Yii::app()->params['profile_user_can_edit']) {
            $this->redirect('/');
        }
        Yii::app()->assetManager->registerCssFile('css/admin.css');
        Yii::app()->assetManager->registerScriptFile('js/profile.js');
        $this->jsVars['items_per_page'] = $this->items_per_page;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->redirect(array('/profile/info'));
    }

    public function actionInfo()
    {
        if (!Yii::app()->params['profile_user_can_edit']) {
            $this->redirect(array('/profile/password'));
        }
        $errors = array();
        $user = User::model()->findByPk(Yii::app()->user->id);
        if (!empty($_POST)) {
            if (Yii::app()->params['profile_user_can_edit']) {
                foreach (array('title', 'first_name', 'last_name', 'email', 'qualifications') as $field) {
                    if (isset($_POST['User'][$field])) {
                        $user->{$field} = $_POST['User'][$field];
                    }
                }
                if (!$user->save()) {
                    $errors = $user->getErrors();
                } else {
                    Yii::app()->user->setFlash('success', 'Your profile has been updated.');
                }
            }
        }
        $this->render('/profile/info', array(
            'user' => $user,
            'errors' => $errors,
        ));
    }

    public function actionPassword()
    {
        if (!Yii::app()->params['profile_user_can_change_password']) {
            $this->redirect(array('/profile/sites'));
        }
        $errors = array();
        $user = User::model()->findByPk(Yii::app()->user->id);
        if (!empty($_POST)) {
            if (Yii::app()->params['profile_user_can_change_password']) {
                if (empty($_POST['User']['password_old'])) {
                    $errors['Current password'] = array('Please enter your current password');
                } elseif ($user->password !== md5($user->salt . $_POST['User']['password_old'])) {
                    $errors['Current password'] = array('Password is incorrect');
                }

                if (empty($_POST['User']['password_new'])) {
                    $errors['New password'] = array('Please enter your new password');
                }

                if (empty($_POST['User']['password_confirm'])) {
                    $errors['Confirm password'] = array('Please confirm your new password');
                }

                if ($_POST['User']['password_new'] != $_POST['User']['password_confirm']) {
                    $errors['Confirm password'] = array("Passwords don't match");
                }

                if (empty($errors)) {
                    $user->password = $user->password_repeat = $_POST['User']['password_new'];
                    if (!$user->save()) {
                        $errors = $user->getErrors();
                    } else {
                        Yii::app()->user->setFlash('success', 'Your password has been changed.');
                    }
                }
            }
            unset($_POST['User']['password_old']);
            unset($_POST['User']['password_new']);
            unset($_POST['User']['password_confirm']);
        }
        $this->render('/profile/password', array(
            'user' => $user,
            'errors' => $errors,
        ));
    }

    public function actionSites()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $this->render('/profile/sites', array(
            'user' => $user,
        ));
    }

    /**
     * Sites deletion from user profile
     *
     * @throws Exception
     */
    public function actionDeleteSites()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $transaction = Yii::app()->db->beginTransaction();
        if (!empty($_POST['sites'])) {
            foreach ($_POST['sites'] as $site_id) {
                if ($us = UserSite::model()->find('user_id=? and site_id=?', array($user->id, $site_id))) {
                    if (!$us->delete()) {
                        throw new Exception('Unable to delete UserSite: ' . print_r($us->getErrors(), true));
                    }
                }
            }
            echo 'success';
        }
        $transaction->commit();
    }

    public function actionAddSite()
    {
        if (@$_POST['site_id'] == 'all') {
            $sites = Institution::model()->getCurrent()->sites;
        } else {
            $sites = Site::model()->findAllByPk(@$_POST['site_id']);
        }
        foreach ($sites as $site) {
            if (!$us = UserSite::model()->find('site_id=? and user_id=?', array($site->id, Yii::app()->user->id))) {
                $us = new UserSite();
                $us->site_id = $site->id;
                $us->user_id = Yii::app()->user->id;
                if (!$us->save()) {
                    throw new Exception('Unable to save UserSite: ' . print_r($us->getErrors(), true));
                }
            }
        }
        echo '1';
    }

    /**
     * Firm view in user profile
     */
    public function actionFirms()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $this->render('/profile/firms', array(
            'user' => $user,
        ));
    }


    /**
     * Firm deletion from user profile
     *
     * @throws Exception
     */
    public function actionDeleteFirms()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $firm_transaction = Yii::app()->db->beginTransaction();
        if (!empty($_POST['firms'])) {

            foreach ($_POST['firms'] as $firm_id) {

                if ($uf = UserFirm::model()->find('user_id=? and firm_id=?', array($user->id, $firm_id))) {
                    if (!$uf->delete()) {
                        throw new Exception('Unable to delete UserFirm: ' . print_r($uf->getErrors(), true));
                    }
                }
            }

            if (!UserFirm::model()->find('user_id=?', array(Yii::app()->user->id))) {
                $user = User::model()->findByPk(Yii::app()->user->id);
                if ($user->has_selected_firms) {
                    $user->has_selected_firms = 0;
                    if (!$user->save()) {
                        throw new Exception('Unable to save user: ' . print_r($user->getErrors(), true));
                    }
                }
            }
            echo "success";
        }
        $firm_transaction->commit();
    }

    public function actionAddFirm()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);

        if (@$_POST['firm_id'] == 'all') {
            $firms = $user->getAvailableFirms();
        } else {
            $firms = Firm::model()->findAllByPk(@$_POST['firm_id']);
        }

        foreach ($firms as $firm) {
            if (!$us = UserFirm::model()->find('firm_id=? and user_id=?', array($firm->id, Yii::app()->user->id))) {
                $us = new UserFirm();
                $us->firm_id = $firm->id;
                $us->user_id = Yii::app()->user->id;
                if (!$us->save()) {
                    throw new Exception('Unable to save UserFirm: ' . print_r($us->getErrors(), true));
                }

                $user->has_selected_firms = 1;
                if (!$user->save()) {
                    throw new Exception('Unable to save user: ' . print_r($user->getErrors(), true));
                }
            }
        }

        echo "1";
    }

    public function actionSignature()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);


        $this->render('/profile/signature', array(
            'user' => $user,
        ));
    }

    public function actionGetSignatureFromPortal()
    {
        if (Yii::app()->user->id) {
            // TODO: query the portal here:
            // TODO: get current unique ID for the user
            // TODO: query the portal with the current unique ID
            // TODO: if successfull save the signature as a ProtectedFile
            // from the portal we receive binary data:

            $user = User::model()->findByPk(Yii::app()->user->id);

            $portal_conn = new OptomPortalConnection();
            if ($portal_conn) {
                $signature_data = $portal_conn->signatureSearch(null,
                    $user->generateUniqueCodeWithChecksum($this->getUniqueCodeForUser()));

                if (is_array($signature_data) && isset($signature_data["image"])) {
                    $signature_file = $portal_conn->createNewSignatureImage($signature_data["image"],
                        Yii::app()->user->id);
                    if ($signature_file) {
                        $user->signature_file_id = $signature_file->id;
                        if ($user->save()) {
                            echo true;
                        }
                    }
                }
            }


        }
        echo false;

    }

    public function actionShowSignature()
    {
        if (Yii::app()->user->id && Yii::app()->getRequest()->getParam("signaturePin")) {
            $user = User::model()->findByPk(Yii::app()->user->id);
            if ($user->signature_file_id) {
                $decodedImage = $user->getDecryptedSignature(Yii::app()->getRequest()->getParam("signaturePin"));
                if ($decodedImage) {
                    echo base64_encode($decodedImage);
                }
            }
        }
        echo false;
    }

    public function actionGenerateSignatureQR()
    {
        if (Yii::app()->user->id) {

            $QRSignature = new SignatureQRCodeGenerator();
            // TODO: need to get a unique code for the user and add a key here!

            $user = User::model()->findByPk(Yii::app()->user->id);
            $user_code = $this->getUniqueCodeForUser();
            if (!$user_code) {
                throw new CHttpException('Could not get unique code for user - unique codes might need to be generated');
            }
            $finalUniqueCode = $user->generateUniqueCodeWithChecksum($user_code);

            $QRimage = $QRSignature->createQRCode("@U:1@code:" . $finalUniqueCode . "@key:" . md5(Yii::app()->user->id),
                250);

            // Output and free from memory
            header('Content-Type: image/jpeg');

            imagejpeg($QRimage);
            imagedestroy($QRimage);

        }
    }

}
