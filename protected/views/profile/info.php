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
    <h2>Basic information</h2>
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'profile-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

        <?php if (!Yii::app()->params['profile_user_can_edit'] || !Yii::app()->params['profile_user_show_menu']) {?>
            <div class="alert-box alert">
                User editing of basic information is administratively disabled.
            </div>
        <?php }?>

        <?php $this->renderPartial('//base/_messages')?>
        <?php $this->renderPartial('//elements/form_errors', array('errors' => $errors))?>

<table class="standard">
  <tbody>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'title',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu'])),
            null,
            array('field' => 2)
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'first_name',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'last_name',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'email',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'qualifications',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
      <div class="data-group flex-layout flex-left cols-full">
        <div class="cols-2">
          <label for="User_qualifications">Display Theme:</label>
        </div>
        <div class="cols-5">
            <?=\CHtml::dropDownList('display_theme', $display_theme, array(null => 'Default', 'light' => 'Light', 'dark' => 'Dark')); ?>
        </div>
      </div>
    </td>
  </tr>
  </tbody>
</table>
<?php if (Yii::app()->params['profile_user_can_edit']) {?>
      <div class="profile-actions">
          <?php echo EventAction::button('Update', 'save',null, array('class'=>'button large hint green'))->toHtml()?>
        <i class="spinner" title="Loading..." style="display: none;"></i>
      </div>
        <?php }?>

    <?php $this->endWidget()?>


