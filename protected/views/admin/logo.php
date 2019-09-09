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


    <div class="row divider">
    <h2>Logo</h2>
    </div>
    <?php
    $form = $this->beginWidget(
            'BaseEventTypeCActiveForm', array(
        'id' => 'upload-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
            )
    );

    echo $form->error($model, 'header_logo');
    echo $form->error($model, 'secondary_logo');

    $path = Yii::app()->basePath.'/runtime/';
    $yourImageUrl = Yii::app()->assetManager->publish($path);
    $imageLists = scandir($path, 1);

    foreach ($imageLists as $imageList) {
        if (strpos($imageList, 'header') !== false) {
            $headerLogo = $imageList;
        }
        if (strpos($imageList, 'secondary') !== false) {
            $secondaryLogo = $imageList;
        }
    }
    ?>


    <table class="standard">
        <tbody>
            <tr>
                <td><?php echo $form->labelEx($model, 'Header Logo'); ?> (recommended dimensions is less than 500x100 pixels)</td>
                <td>
                    <?php
                    if (!empty($headerLogo)) { ?>
                        <img src="<?php echo $yourImageUrl.'/'.$headerLogo; ?>"  />
                        <?=\CHtml::link('Remove', '#', array('submit' => array('admin/deleteLogo/', 'header_logo' => $headerLogo), 'confirm' => 'Are you sure to delete header logo?', 'csrf' => true)); ?><?php echo '<br/><br/><br/>';
                    } ?>
                    <?php echo $form->fileField($model, 'header_logo'); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $form->labelEx($model, 'Secondary Logo'); ?> (recommended dimensions is less than dimensions 120x100 pixels)</td>
                <td><?php
                if (!empty($secondaryLogo)) { ?>
                        <img src="<?php echo $yourImageUrl.'/'.$secondaryLogo; ?>" >
                        <?=\CHtml::link('Remove', '#', array('submit' => array('admin/deleteLogo/', 'secondary_logo' => $secondaryLogo), 'confirm' => 'Are you sure to delete secondary logo?', 'csrf' => true)); ?>
                        <?php echo '<br/><br/><br/>';
                } ?> 
                    <?php echo $form->fileField($model, 'secondary_logo'); ?>
                </td>
            </tr>
        </tbody>
    </table>
<?php echo $form->formActions(array('cancel-uri' => '/admin/logo')); ?>
<?php $this->endWidget() ?>

<script type="text/javascript">

    $(".flash-success").delay(3000).fadeOut("slow");
    $(".error").delay(5000).fadeOut("slow");
</script>