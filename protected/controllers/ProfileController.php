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
        $user_auth = Yii::app()->session['user_auth'];
        $display_theme_setting = SettingUser::model()->find(
            'user_id = :user_id AND `key` = "display_theme"',
            array('user_id' => $user->id)
        );
        $user_out_of_office = UserOutOfOffice::model()->find('user_id=?', array($user->id));
        if (!$user_out_of_office) {
            $user_out_of_office = new UserOutOfOffice();
            $user_out_of_office->user_id = $user->id;
        }

        if (!empty($_POST)) {
            if (Yii::app()->params['profile_user_can_edit']) {
                foreach (array('title', 'first_name', 'last_name', 'email', 'qualifications', 'correspondence_sign_off_user_id') as $field) {
                    if (isset($_POST['User'][$field])) {
                        $user->{$field} = $_POST['User'][$field];
                    }
                }

                $fields = $_POST['UserOutOfOffice'];

                $user_out_of_office->enabled = $fields['enabled'];
                // strtotime converts empty string to 1970-01-01
                if (!empty($fields['from_date'])) {
                    $user_out_of_office->from_date = date("Y-m-d", strtotime($fields['from_date']));
                } else {
                    // Required to give validation error to user
                    $user_out_of_office->from_date = '';
                }
                if (!empty($fields['to_date'])) {
                    $user_out_of_office->to_date = date("Y-m-d", strtotime($fields['to_date']));
                } else {
                    // Required to give validation error to user
                    $user_out_of_office->to_date = '';
                }
                $user_out_of_office->alternate_user_id = $fields['alternate_user_id'];

                if (!$user->save()) {
                    $errors = $user->getErrors();
                } elseif (!$user_out_of_office->save()) {
                    $errors = $user_out_of_office->getErrors();
                } else {
                    Yii::app()->user->setFlash('success', 'Your profile has been updated.');
                }

                $display_theme_setting = self::changeDisplayTheme($user->id, $_POST['display_theme']);

                // make sure session has the latest data
                $user->refresh();
                Yii::app()->session['user'] = $user;
            }
        }

        $this->render('/profile/info', array(
            'user' => $user,
            'user_auth' => $user_auth,
            'errors' => $errors,
            'display_theme' => $display_theme_setting ? $display_theme_setting->value : null,
            'user_out_of_office' => $user_out_of_office,
        ));
    }

    public function actionPassword()
    {
        if (!Yii::app()->params['profile_user_can_change_password']) {
            $this->redirect(array('/profile/sites'));
        }
        $errors = array();
        $user_auth = Yii::app()->session['user_auth'];
        if (!empty($_POST)) {
            if (Yii::app()->params['profile_user_can_change_password']) {
                if (empty($_POST['UserAuthentication']['password_old'])) {
                    $errors['Current password'] = array('Please enter your current password');
                } elseif (!$user_auth->verifyPassword($_POST['UserAuthentication']['password_old'])) {
                    $errors['Current password'] = array('Password is incorrect');
                }

                if (empty($_POST['UserAuthentication']['password_new'])) {
                    $errors['New password'] = array('Please enter your new password');
                }

                if (empty($_POST['UserAuthentication']['password_confirm'])) {
                    $errors['Confirm password'] = array('Please confirm your new password');
                }

                if ($_POST['UserAuthentication']['password_new'] != $_POST['UserAuthentication']['password_confirm']) {
                    $errors['Confirm password'] = array("Passwords don't match");
                }

                if (empty($errors)) {
                    if ($user_auth->institutionAuthentication->user_authentication_method === 'LOCAL') {
                        if ($user_auth->password_status==="stale"||$user_auth->password_status==="expired") {// this user pw is now current
                            $user_auth->password_status = 'current';
                        }
                        //reset pw checks
                        $user_auth->password_last_changed_date = date('Y-m-d H:i:s');
                        $user_auth->password_failed_tries = 0;
                    }
                    $user_auth->password = $user_auth->password_repeat = $_POST['UserAuthentication']['password_new'];
                    if (!$user_auth->save()) {
                        $errors = $user_auth->getErrors();
                    } else {
                        Yii::app()->user->setFlash('success', 'Your password has been changed.');
                    }
                }
            }
            unset($_POST['UserAuthentication']['password_old']);
            unset($_POST['UserAuthentication']['password_new']);
            unset($_POST['UserAuthentication']['password_confirm']);
        }
        $this->render('/profile/password', array(
            'user_auth' => $user_auth,
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

    public function actionInstitutions()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $this->render('/profile/institutions', array(
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
            'recapture' => filter_var(Yii::app()->request->getParam("recapture"), FILTER_VALIDATE_BOOLEAN)
        ));
    }

    public function actionUploadSignature()
    {
        if(!$user = User::model()->findByPk(Yii::app()->user->id)) {
            $this->renderJSON([
                "success" => false,
                "message" => "User not found"
            ]);
        }
        if(!$img = Yii::app()->request->getPost("image")) {
            $this->renderJSON([
                "success" => false,
                "message" => "Image not provided"
            ]);
        }
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $file = ProtectedFile::createForWriting("user_signature_".$user->id);
        // Looks like something must be set here
        $file->title = "Signature";
        file_put_contents($file->getPath(), $img);
        if($file->save()) {
            $user->signature_file_id = $file->id;
            $user->save(false, ["signature_file_id"]);
            $this->renderJSON([
                "success" => true
            ]);
        }
        else {
            $this->renderJSON([
                "success" => false,
                "message" => "An error occurred while saving the signature."
            ]);
        }
    }

    /**
     * Changes the display theme of the current user and redirects them to teh page they were previously on
     *
     * @param string $display_theme What to set the user's theme to
     */
    public function actionChangeDisplayTheme($display_theme)
    {
        self::changeDisplayTheme(Yii::app()->user->id, $display_theme);
    }

    /**
     * Changes the display theme of the given user and returns the SettingUser object (if it exists)
     *
     * @param int $user_id The ID of the user to change the display theme for
     * @param string $display_theme What to set the user's theme to
     * @return SettingUser The setting if the theme was set (otherwise null)
     */
    public static function changeDisplayTheme($user_id, $display_theme)
    {
        $display_theme_setting = SettingUser::model()->find(
            'user_id = :user_id AND `key` = "display_theme"',
            array('user_id' => $user_id)
        );

        if ($display_theme) {
            if ($display_theme_setting === null) {
                $display_theme_setting = new SettingUser();
                $display_theme_setting->user_id = $user_id;
                $display_theme_setting->key = 'display_theme';
            }
            $display_theme_setting->value = $display_theme;
            $display_theme_setting->save();
        } elseif ($display_theme_setting) {
            # If the theme isn't set, but the setting already exists
            # then remove the display theme entirely so the global setting will take precedence
            $display_theme_setting->delete();
            $display_theme_setting = null;
        }

        return $display_theme_setting;
    }

    public function actionUsersettings()
    {
        $element_type = ElementType::model()->find('class_name = :class_name', array(':class_name' => 'Element_OphTrOperationnote_Cataract'));
        $setting_metadata = \Yii::app()->request->getPost('SettingMetadata');
        if ($setting_metadata) {
            SettingUser::model()->deleteAll('user_id = :user_id AND element_type_id = :element_type_id', array(':user_id' => Yii::app()->user->id, ':element_type_id' => $element_type->id));
            foreach ($setting_metadata as $key => $value) {
                $cataract_op_note_setting = new SettingUser();
                $cataract_op_note_setting->user_id = Yii::app()->user->id;
                $cataract_op_note_setting->element_type_id = $element_type->id;
                $cataract_op_note_setting->key = $key;
                $cataract_op_note_setting->value = $value;
                if (!$cataract_op_note_setting->save()) {
                    $errors = $cataract_op_note_setting->getErrors();
                }
                Yii::app()->cache->delete('op_note_user_settings');
            }
        }

        $settings_user = $this->getUserSettings($element_type);
        $settings_metadata = SettingMetadata::model()->byDisplayOrder()->findAll('element_type_id = :element_type_id', array(':element_type_id' => $element_type->id));
        $user_settings = CHtml::listData($settings_user, 'key', 'value');
        if ($settings_user) {
            foreach ($settings_metadata as $value) {
                if (isset($user_settings[$value->key])) {
                    $value->default_value = $user_settings[$value->key];
                }
            }
        }
        $errors = array();
        $this->render('/profile/user_settings', array(
            'errors' => $errors,
            'settings' => $settings_metadata,
        ));
    }

    public function getUserSettings($elementType)
    {
        return SettingUser::model()->findAll('user_id = :user_id AND element_type_id = :element_type_id', array(':user_id' => Yii::app()->user->id, ':element_type_id' => $elementType->id));
    }
}
