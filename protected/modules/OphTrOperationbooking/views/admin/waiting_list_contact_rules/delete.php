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
<?php
/**
 * @todo : refactor the html
 */
?>

<div class="cols-7">
    <div class="row divider">
        <h3 class="georgia">Delete waiting list contact rule</h3>
    </div>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'lcr_deleteform',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('class' => 'sliding'),
        'focus' => '#contactname',
    )) ?>

    <input type="hidden" name="delete" value="1"/>

    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr>
            <td><?php echo $rule->getAttributeLabel('parent_rule_id') ?>:</td>
            <td><?php echo $rule->parent ? $rule->parent->treeName : 'None' ?></td>
        </tr>
        <tr>
            <td><?php echo $rule->getAttributeLabel('institution_id') ?>:</td>
            <td><?php echo Institution::model()->getCurrent()->name ?></td>
        </tr>
        <tr>
            <td><?php echo $rule->getAttributeLabel('site_id') ?>:</td>
            <td><?php echo $rule->site ? $rule->site->name : 'Not set' ?></td>
        </tr>
        <tr>
            <td><?php echo $rule->getAttributeLabel('firm_id') ?>:</td>
            <td><?php echo $rule->firm ? $rule->firm->name : 'Not set' ?></td>
        </tr>
        <tr>
            <td><?php echo $rule->getAttributeLabel('service_id') ?>:</td>
            <td><?php echo $rule->service ? $rule->service->name : 'Not set' ?></td>
        </tr>
        <tr>
            <td><?php echo $rule->getAttributeLabel('name') ?>:</td>
            <td><?php echo $rule->name ?></td>
        </tr>
        <tr>
            <td><?php echo $rule->getAttributeLabel('telephone') ?>:</td>
            <td><?php echo $rule->telephone ?></td>
        </tr>
        </tbody>
    </table>


    <?php $this->endWidget()?>

    <?php if ($rule->children) { ?>
        <div>
            <p style="font-size: 15px; margin: 0; padding: 0; margin-top: 10px; margin-bottom: 10px;"><strong>
                <span style="color: #f00;">WARNING:</span>
                this rule has one or more descendants, if you proceed these will all be deleted.
            </strong></p>
            <?php
            $this->widget('CTreeView', array(
                'data' => OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAllAsTree($rule, true, 'textPlain'),
            )) ?>
        </div>
    <?php } ?>
    <div>
        <p style="font-size: 15px; margin: 0; padding: 0; margin-top: 10px; margin-bottom: 10px;"><strong>Are you sure you want to delete this rule
            <?php if ($rule->children) {
                ?> and its descendants
            <?php } ?>?
        </strong></p>
    </div>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <div>
        <?= CHtml::submitButton(
            'Delete',
            [
                'class' => 'button large',
                'name' => 'delete',
                'id' => "et_delete",
                'colour' => 'green'
            ]
        ); ?>
        <?= \CHtml::submitButton(
            'Cancel',
            [
                'class' => 'button large',
                'level' => 'cancel',
                'name' => 'cancel',
                'id' => 'et_cancel'
            ]
        ); ?>
        <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
             alt="loading..." style="display: none;"/>
    </div>
</div>

<script type="text/javascript">
    handleButton($('#et_cancel'), function () {
        console.log("CANCLE PRESS");
        window.location.href = baseUrl + '/OphTrOperationbooking/admin/edit' + OE_rule_model + '/<?php echo $rule->id?>';
    });
    handleButton($('#et_delete'), function () {
        $('#lcr_deleteform').submit();
    });
</script>
