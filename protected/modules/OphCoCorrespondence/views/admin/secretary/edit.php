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

$modulePath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OphCoCorrespondence.assets'), true);
Yii::app()->clientScript->registerScriptFile($modulePath . '/js/siteSecretary.js');
?>

    <div class="row divider">
        <h2>Contact Numbers</h2>
    </div>
<?php
$deleteForm = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'deleteSecretaryForm',
    'action' => '/OphCoCorrespondence/admin/deleteSiteSecretary/',
    'enableAjaxValidation' => false,
)) ?>

<?php $this->endWidget() ?>

<?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'editSecretaryForm',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
)) ?>
    <table class="standard cols-full">
        <thead>
        <tr>
            <td>Site</td>
            <td>Direct Line</td>
            <td>Fax</td>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($siteSecretaries as $id => $siteSecretary) : ?>
            <tr class="secretaryFormRow">
                <td>
                    <?=\CHtml::activeHiddenField($siteSecretary, "[$id]firm_id"); ?>
                    <?=\CHtml::activeHiddenField($siteSecretary, "[$id]id"); ?>
                    <?=\CHtml::activeDropDownList($siteSecretary, "[$id]site_id", CHtml::listData(Site::model()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => '- None -')) ?>
                </td>
                <td>
                    <?=\CHtml::activeTextField($siteSecretary, "[$id]direct_line", array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
                </td>
                <td>
                    <?=\CHtml::activeTextField($siteSecretary, "[$id]fax", array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
                </td>
                <td>
                    <button type="submit" form="deleteSecretaryForm" name="id" class="small"
                            value="<?php echo $siteSecretary->id ?>"><i class="oe-i trash-blue "></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
                <button class="button hint green js-showBlankRow" type="button"><i class="oe-i plus pro-theme"></i></button> <!-- show new row -->
            </td>
        </tr>
        <tr class="secretaryFormRow js-addNewRow">
            <td>
                <?=\CHtml::activeHiddenField($newSiteSecretary, "[new]firm_id", ["value" => $_GET['id']]); ?>
                <?=\CHtml::activeHiddenField($newSiteSecretary, "[new]id"); ?>
                <?=\CHtml::activeDropDownList($newSiteSecretary, "[new]site_id", CHtml::listData(Site::model()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => '- None -')) ?>
            </td>
            <td><?=\CHtml::activeTextField($newSiteSecretary, "[new]direct_line", array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?></td>
            <td><?=\CHtml::activeTextField($newSiteSecretary, "[new]fax", array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?></td>
            <td><button class="addButton small">Add</button></td>
        </tr>

        <tr>
            <td>
                <?php echo $form->formActions(); ?>
            </td>
        </tr>
        </tbody>
    </table>

<?php $this->endWidget() ?>
