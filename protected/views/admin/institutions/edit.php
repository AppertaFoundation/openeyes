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
    .flash-success {
        border:1px solid #1DDD50;
        background: #C3FFD3;
        text-align: center;
        padding: 7px 15px ;
        color: #000000;
        margin-bottom: 20px;
    }

    .error {
        border:1px solid #ff6666;
        background: #ffe6e6;
        text-align: center;
        padding: 7px 15px ;
        color: #000000;
        margin-bottom: 20px;
    }

    .remove-logo {
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
<div class="cols-7">

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
            'method' => "POST",
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        ]
    ) ?>

    <div class="row divider">
        <h2>
            <?php
            if ($new) {
                echo "Add institution";
            } else {
                echo "Edit institution";
            }
            ?>
        </h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>

        <tbody>
            <?php foreach (['name', 'short_name'] as $field) : ?>
            <tr>
                <td><?= $institution->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $institution,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                        ]
                    ); ?>
                </td>
            </tr>            
            <?php endforeach; ?>
        <tr>
            <td><?= $institution->getAttributeLabel('remote_id'); ?></td>
            <td>
            <?php if (!$new) { ?>
                <?= htmlspecialchars($institution->remote_id) ?>
            <?php } else { ?>
                <div class="alert-box alert">Once added, this field will not be editable</div>
                <?= CHtml::activeTextField(
                    $institution,
                    'remote_id',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                    ]
                );
            } ?>
            </td>
        </tr>
        <tr>
            <td><?= $institution->getAttributeLabel('pas_key'); ?></td>
            <td>
                <?= CHtml::activeTextField(
                    $institution,
                    'pas_key',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                    ]
                ) ?>
            </td>
        </tr>
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
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
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
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                    ]
                ); ?>
            </td>
        </tr><tr>
            <td>Primary logo</td>
            <td>
                <?php
                echo $form->fileField($logo, 'primary_logo');
                if (empty($default_urls['primaryLogo']) && !($logo->primary_logo)) {
                    echo "<div class='alert-box info'>No uploaded secondary logo and no default logo</div>";
                } else {
                    if (!($logo) || !($logo->primary_logo)) {
                        echo "<div class='alert-box info'>Currently using system default logo</div>";
                        echo "<img src='" . $default_urls['primaryLogo'] . "' style='width:100%;'>";
                    } elseif (!$new) {
                        echo '<div style=" margin-top: 5px; position: relative; ">';
                        echo "<img src='" . $logo->getImageUrl() . "' style='width:100%;'>";
                        echo '<br>' . CHtml::button(
                            '',
                            array('submit' => array('admin/deletelogo/'),
                            'params' => array(
                                'institution_id' => $institution->id,
                                'deletePrimaryLogo' => true,
                            ),
                             'csrf' => true,
                             'class' => 'remove-logo oe-i remove-circle small',
                             'confirm' => 'Are you sure you want to delete the primary logo? You will lose all unsaved edits you have made to this institution.',
                             'data-method' => "POST"
                            )
                        );
                        echo '</div>';
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
                if (empty($default_urls['secondaryLogo']) && !($logo->secondary_logo)) {
                    echo "<div class='alert-box info'>No uploaded secondary logo and no default logo</div>";
                } else {
                    if (!($logo) || !($logo->secondary_logo)) {
                        echo "<div class='alert-box info'>Currently using system default logo</div>";
                        echo "<img src='" . $default_urls['secondaryLogo'] . "' style='width:100%;'>";
                    } elseif (!$new) {
                        echo '<div style="
                        margin-top: 5px;
                        position: relative;
                    ">';
                        echo "<img src='" . $logo->getImageUrl(true) . "' style='width:100%;'>";
                        echo '<br>' . CHtml::button(
                            '',
                            array('submit' => array('admin/deletelogo/'),
                            'params' => array(
                                'institution_id' => $institution->id,
                                'deleteSecondaryLogo' => true,
                            ),
                            'csrf' => true,
                            'class' => 'remove-logo oe-i remove-circle small',
                            'confirm' => 'Are you sure you want to delete the secondary logo? You will lose all unsaved edits you have made to this institution.',
                            'data-method' => "POST"
                            )
                        );
                        echo '</div>';
                    }
                }
                ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?= \CHtml::submitButton(
                    'Cancel',
                    [
                        'class' => 'button large',
                        'data-uri' => '/admin/institutions',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
<?php if (!$new) { ?>
    <br>

    <h2>Sites</h2>
    <hr class="divider">
    <form id="admin_institution_sites">
        <table class="standard">
            <thead>
            <tr>
                <th>ID</th>
                <th>Remote ID</th>
                <th>Name</th>
                <th>Address</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($institution->sites as $site) { ?>
                <tr class="clickable"
                    data-id="<?= $site->id ?>"
                    data-uri="admin/editsite?site_id=<?= $site->id ?>">
                    <td><?= $site->id ?></td>
                    <td><?= $site->remote_id ?>&nbsp;</td>
                    <td><?= $site->name ?>&nbsp;</td>
                    <td>
                        <?= $site->getLetterAddress(
                            array('delimiter' => ', ')
                        ) ?>&nbsp
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
<?php }?>
</div>
