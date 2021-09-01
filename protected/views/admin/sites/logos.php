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
        <h2>Edit System Default logos</h2>
    </div>
    <?= $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'CActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
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
            <td>Primary logo</td>
            <td>
                <?php
                    echo $form->fileField($logo, 'primary_logo');
                if (!empty($default_urls['primaryLogo'])) {
                    echo '<div style=" margin-top: 5px; position: relative; ">';
                        echo "<img src='". $default_urls['primaryLogo']."' style='width:100%;'>";
                        echo '<br>'.CHtml::button( '',
                            array('submit' => array('admin/deletelogo/'),
                            'params' => array(
                                'deletePrimaryLogo' => true,
                            ),
                            'csrf' => true,
                            'class' =>'remove-logo oe-i remove-circle small',
                            'confirm' => 'Are you sure you want to delete the default primary logo?',
                            'data-method'=>"POST"
                        ));
                        echo '</div>';
                } else {
                    echo "<div class='alert-box info'>No default primary logo</div>";
                }

                ?>
            </td>
        </tr>
        <tr>
            <td>Secondary logo</td>
            <td>
                <?php
                    echo $form->fileField($logo, 'secondary_logo');
                if (!empty($default_urls['secondaryLogo'])) {
                    echo '<div style=" margin-top: 5px; position: relative; ">';
                        echo "<img src='". $default_urls['secondaryLogo']."' style='width:100%;'>";
                        echo '<br>'.CHtml::button( '',
                            array('submit' => array('admin/deletelogo/'),
                            'params' => array(
                                'deleteSecondaryLogo' => true,
                            ),
                            'csrf' => true,
                            'class' =>'remove-logo oe-i remove-circle small',
                            'confirm' => 'Are you sure you want to delete the default secondary logo?',
                            'data-method'=>"POST"
                        ));
                        echo '</div>';
                } else {
                    echo "<div class='alert-box info'>No default secondary logo</div>";
                }
                ?>
            </td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="4">
                <?= CHtml::submitButton('Save')?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>