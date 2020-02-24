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

$logoHelper = new LogoHelper();
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
</style>
<?php if (Yii::app()->user->hasFlash('success')) : ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>

<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('error')) : ?>
    <div class="error">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>

<?php endif; ?>

<div class="cols-7">
    <div class="row divider">
        <h2>
           <?php
            if(!empty($site)){
                if(!empty($site->logo)){
                    echo "Edit logos for site: ".$site->name;
                }
                else{
                    echo "Add logos for site: ".$site->name;
                }
            }
            else{
               echo "Edit System Default logos";
            }
            ?>
        </h2>
    </div>
    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'layoutColumns' => array(
                'label' => 3,
                'field' => 7,
            ),
            'method'=> "POST",
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        ]
    ) ?>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-1">
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>

        <tbody>              
        <tr>
            <td>Primary logo</td>
            <td>
                <?php 
                if(empty($logo->primary_logo)){
                    echo "Currently using system default logo<br><br>";
                }
                echo "<img src='". $logo->getImageUrl()."' width='200'>";
                ?>
            </td>
            <td>
                <?php echo $form->fileField($logo, 'primary_logo'); ?>
            </td>
        </tr>              
        <tr>
            <td>Secondary logo</td>
            <td>
                <?php 
                    if(empty($logo->secondary_logo)){                      
                        echo "Currently Using system default logo<br><br>";     
                    }                   
                    echo "<img src='". $logo->getImageUrl('true')."' width='200'>";
                ?>
            </td>            
            <td>
                <?php echo $form->fileField($logo, 'secondary_logo'); ?>
            </td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="3">            
                <?php echo $form->formActions(array('cancel'=>'Back to Sites','cancel-uri' => '/admin/sites')); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>