<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2011-2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<h2>User Settings - Settings for Cataract op-note</h2>
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'user-settings-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

    <?php if (!Yii::app()->params['profile_user_can_edit'] || !Yii::app()->params['profile_user_show_menu']) {?>
      <div class="alert-box alert">
        User editing of user settings is administratively disabled.
      </div>
    <?php }?>

    <?php $this->renderPartial('//base/_messages')?>
    <?php $this->renderPartial('//elements/form_errors', array('errors' => $errors))?>

<table class="standard">
  <tbody>
        <?php
        foreach ($settings as $setting) { ?>
            <tr>
                <td>
            <?php switch ($setting->field_type_id) {
                case 2:
                    if (!in_array($setting->key, ['surgeon_position_right_eye', 'surgeon_position_left_eye'])) {
                        $setting_data = unserialize($setting->data);
                    } else {
                        $array_keys = array_keys(unserialize($setting->data));
                        $setting_data = array_combine($array_keys, $array_keys);
                    }
                    echo $form->dropDownList($setting, 'default_value', $setting_data, array('name' =>  CHtml::modelName($setting) . "[" . $setting->key . "]", 'label' => $setting->name, 'class' => 'cols-full'));
                    break;

                default:
                    echo $form->textField(
                        $setting,
                        'default_value',
                        array(
                        'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'readonly' => (!Yii::app()->params['profile_user_can_edit']
                        || !Yii::app()->params['profile_user_show_menu']),
                        'name' =>  CHtml::modelName($setting) . "[" . $setting->key . "]",
                        'label' => $setting->name
                        ),
                        null
                    );
                    break;
            } ?>
            </td>
        </tr>
        <?php } ?>
  </tbody>
</table>
<?php if (Yii::app()->params['profile_user_can_edit']) {?>
      <div class="profile-actions">
          <?php echo EventAction::button('Update', 'save', null, array('id' => 'user-settings-save', 'class' => 'button large hint green'))->toHtml()?>
        <i class="spinner" title="Loading..." style="display: none;"></i>
      </div>
<?php }?>

    <?php $this->endWidget()?>
