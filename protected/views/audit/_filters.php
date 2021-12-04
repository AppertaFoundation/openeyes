<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<nav class="oe-full-side-panel audit-filters">
    <input type="hidden" id="page" name="page" value="1" />
    <div class="row">
        <table class="standard last-right">
            <colgroup>
                <col class="cols-3">
            </colgroup>
            <tr>
                <td>
                    Institution
                </td>
                <td>
                    <?= Yii::app()->user->checkAccess('Institution Audit') ?
                        \CHtml::dropDownList('institution_id', @$_POST['institution_id'], Institution::model()->getList(false), array('class'=>'cols-full', 'empty' => 'All institutions')) :
                        \CHtml::dropDownList('institution_id', Yii::app()->session['selected_institution_id'], Institution::model()->getList(true), array('class'=>'cols-full', 'disabled' => 'disabled')) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Site
                </td>
                <td>
                    <?php $site_list = Site::model()->getListForAllInstitutions();
                        echo Yii::app()->user->checkAccess('Institution Audit') ?
                            \CHtml::dropDownList('site_id', @$_POST['site_id'], $site_list['list'], array('class'=>'cols-full', 'empty' => 'All sites', 'options' => $site_list['options'])) :
                            \CHtml::dropDownList('site_id', @$_POST['site_id'], Site::model()->getListForCurrentInstitution(), array('empty' => 'All sites','class'=>'cols-full')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Context
                </td>
                <td>
                    <?=\CHtml::dropDownList('firm_id', @$_POST['firm_id'], Firm::model()->getList(), array('empty' => 'All firms', 'class'=>'cols-full'))?>
                </td>
            </tr>
        </table>
    </div>
    <h4>Action</h4>
    <?=\CHtml::dropDownList('action', @$_POST['action'], CHtml::listData(AuditAction::model()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => 'All actions', 'class' => 'cols-full'))?>
    <h4>Target</h4>
    <?=\CHtml::dropDownList('target_type', @$_POST['target_type'], CHtml::listData(AuditType::model()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => 'All targets', 'class' => 'cols-full'))?>
    <h4>Event Types</h4>
    <?=\CHtml::dropDownList('event_type_id', @$_POST['event_type_id'], EventType::model()->getEventTypeInUseList(), array('empty' => 'All event types', 'class' => 'cols-full'))?>
    <h4>User</h4>
    <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
    <h4>Patient Identifier</h4>
    <?=\CHtml::textField('patient_identifier_value', Yii::app()->request->getPost('patient_identifier_value'), array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'search cols-full', 'placeholder'=>'Enter Patient Identifier'))?>
    <h3>Filter by Date</h3>
    <div class="flex-layout">
        <fieldset>
        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'date_from',
            'id' => 'date_from',
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
            ),
            'value' => @$_POST['date_from'],
            'htmlOptions' => array(
                'class' => 'cols-5',
                'placeholder' => 'from'
            ),
        ))?>
        <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'name' => 'date_to',
            'id' => 'date_to',
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
            ),
            'value' => @$_POST['date_to'],
            'htmlOptions' => array(
                'class' => 'cols-5',
                'placeholder' => 'to'
            ),
        ))?>
        </fieldset>
    </div>
    <div class="row">
        <?=\CHtml::link('Reset all Filters', array('audit/'), array('class' => 'cols-full'))?>
    </div>
    <table class="standard last-right">
        <tbody>
        <tr>
            <td>Auto update</td>
            <td><?=\CHtml::link('Auto update on', '#', array('class' => 'inline', 'id' => 'auto_update_toggle'))?></td>
        </tr>
        </tbody>
    </table>
    <div class="row">
        <img class="loader hidden" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" alt="loading..." style="margin-right:10px" />
        <button type="submit" class="green hint cols-full">Create Audit</button>
    </div>
</nav>
