<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$logo_helper = new LogoHelper();
$default_urls = $logo_helper->getLogoURLs();
?>
<style>
    .flash-success{
        border:1px solid #1DDD50;
        background: #C3FFD3;
        text-align: center;
        padding: 7px 15px ;
        color: #000000;
        margin-bottom: 20px;
    }

    .error{
        border:1px solid #ff6666;
        background: #ffe6e6;
        text-align: center;
        padding: 7px 15px ;
        color: #000000;
        margin-bottom: 20px;
    }

    .remove-logo{
        display: block;
        position: absolute;
        top: 1px;
        right: 2px;
        padding: 11px 11px;
        background-color: rgba(255,255,255,.5);
    }
</style>
<?php if (Yii::app()->user->hasFlash('success')) : ?>
    <div class="flash-success">
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>

<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('error')) : ?>
    <div class="error">
        <?= Yii::app()->user->getFlash('error'); ?>
    </div>

<?php endif; ?>

<div class="cols-5">

    <div class="row divider">
        <h2>
            <?php
            if ($site->id) {
                echo "Edit site: ".$site->name;
            } else {
                echo "Add Site";
            }
            ?>
        </h2>
    </div>

    <?= $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
            'method'=> "POST",
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        ]
    ) ?>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>

        <tbody>
        <tr>
            <td>Institution</td>
            <td>
                <?php if ($this->checkAccess('admin')) {
                    echo \CHtml::activeDropDownList(
                        $site,
                        'institution_id',
                        CHtml::listData(Institution::model()->findAll(), 'id', 'name'),
                        ['class' => 'cols-full']
                    );
                } else {
                    $institution = Institution::model()->getCurrent();
                    echo $site->institution_id ? $site->institution->name : $institution->name;
                    echo \CHtml::activeHiddenField(
                        $site,
                        'institution_id',
                        ['value' => $site->institution_id ?? $institution->id]
                    );
                } ?>
            </td>
        </tr>
        <?php foreach (['name', 'short_name', 'remote_id', 'fp_10_code'] as $field) : ?>
            <tr>
                <td><?= $site->getAttributeLabel($field) ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $site,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete']
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php
        $address_fields = ['address1', 'address2', 'city', 'county', 'postcode'];
        foreach ($address_fields as $field) : ?>
            <tr>
                <td><?= $address->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $address,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete']
                        ]
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php foreach (['telephone', 'fax'] as $field) : ?>
            <tr>
                <td><?= $site->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $site,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete']
                        ]
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td>Country</td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $address,
                    'country_id',
                    CHtml::listData(Country::model()->findAll(), 'id', 'name'),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Primary logo</td>
            <td>
                <?php
                echo $form->fileField($logo, 'primary_logo');
                if (empty($default_urls['primaryLogo']) && !($parentlogo && $parentlogo->primary_logo) && !($logo->primary_logo)) {
                    echo "<div class='alert-box info'>No uploaded primary logo and no inherited or system primary logo.</div>";
                } else {
                    if ($logo&&$logo->primary_logo&&!$new) {
                        echo '<div style=" margin-top: 5px; position: relative; ">';
                        echo "<img src='". $logo->getImageUrl()."' style='width:100%;'>";
                        echo '<br>'.CHtml::button(
                            '',
                            array('submit' => array('admin/deletelogo/'),
                            'params' => array(
                                'site_id' => $site->id,
                                'deletePrimaryLogo' => true,
                            ),
                             'csrf' => true,
                             'class' =>'remove-logo oe-i remove-circle small',
                             'confirm' => 'Are you sure you want to delete the primary logo? You will lose all unsaved edits you have made to this site.',
                             'data-method'=>"POST"
                            )
                        );
                        echo '</div>';
                    } elseif ( $parentlogo &&  $parentlogo->primary_logo && !$new) {
                        echo "<div class='alert-box info'>Currently using inherited logo.</div>";
                        echo "<img src='". $logo->getImageUrl()."' style='width:100%;'>";
                    } elseif (!$new) {
                        echo "<div class='alert-box info'>Currently using system default logo.</div>";
                        echo "<img src='". $default_urls['primaryLogo']."' style='width:100%;'>";
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Secondary logo</td>
            <td>
                <?php
                echo $form->fileField($logo, 'secondary_logo');
                if (empty($default_urls['secondaryLogo']) && !($parentlogo && $parentlogo->secondary_logo) && !($logo->secondary_logo)) {
                    echo "<div class='alert-box info'>No uploaded secondary logo or system secondary logo.</div>";
                } else {
                    if ($logo && $logo->secondary_logo && !$new) {
                        echo '<div style="
                        margin-top: 5px;
                        position: relative;
                    ">';
                        echo "<img src='". $logo->getImageUrl(true) . "' style='width:100%;'>";
                        echo '<br>'.CHtml::button(
                            '',
                            array('submit' => array('admin/deletelogo/'),
                            'params' => array(
                                'site_id' => $site->id,
                                'deleteSecondaryLogo' => true,
                            ),
                            'csrf' => true,
                            'class' =>'remove-logo oe-i remove-circle small',
                            'confirm' => 'Are you sure you want to delete the secondary logo? You will lose all unsaved edits you have made to this site.',
                            'data-method'=>"POST"
                            )
                        );
                        echo '</div>';
                    } elseif ( $parentlogo && $parentlogo->secondary_logo  && !$new) {
                        echo "<div class='alert-box info'>Currently using inherited logo.</div>";
                        echo "<img src='". $logo->getImageUrl(true) . "' style='width:100%;'>";
                    } elseif (!$new) {
                        echo "<div class='alert-box info'>Currently using system default logo.</div>";
                        echo "<img src='". $default_urls['secondaryLogo'] . "' style='width:100%;'>";
                    }
                }
                ?>
            </td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="2">
            <?= $form->formActions(array('cancel'=>'Back to Sites','cancel-uri' => '/admin/sites'));?>
            </td>
        </tr>
        
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>