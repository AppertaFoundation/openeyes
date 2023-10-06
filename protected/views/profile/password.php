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
?>
  <h2>Change password</h2>
<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'profile-form',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
)) ?>
<?php if (!Yii::app()->params['profile_user_can_change_password']) { ?>
  <div class="alert-box alert">
    User changing of passwords is administratively disabled.
    <?php if (PasswordUtils::testStatus($user_auth, "expired")) { ?>
      Your password has expired, please contact your administrator.
      <?php } ?>
    </div>
<?php } ?>
<?php $this->renderPartial('//base/_messages') ?>
<?php $this->renderPartial('//elements/form_errors', array('errors' => $errors)) ?>
<table class="standard">
  <tbody>
  <tr>
    <td>
        <?php echo $form->passwordChangeField(
            $user_auth,
            'Current Password',
            'UserAuthentication[password_old]',
            array('readonly' => !Yii::app()->params['profile_user_can_change_password'])
        ) ?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->passwordChangeField(
            $user_auth,
            'New Password',
            'UserAuthentication[password_new]',
            array('readonly' => !Yii::app()->params['profile_user_can_change_password'])
        ) ?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->passwordChangeField(
            $user_auth,
            'Confirm',
            'UserAuthentication[password_confirm]',
            array('readonly' => !Yii::app()->params['profile_user_can_change_password'])
        ) ?>
    </td>
  </tr>
  </tbody>
</table>
<?php if (Yii::app()->params['profile_user_can_change_password']) { ?>
  <div class="profile-actions">
      <?php echo EventAction::button('Save', 'save', array(), array('class' => 'button large hint green'))->toHtml() ?>
    <i class="spinner" title="Loading..." style="display: none;"></i>
  </div>
<?php } ?>
<?php $this->endWidget() ?>
